<?php
global $wpdb;

if(isset($_GET['id'])){
    wp_delete_post($_GET['id'], true);
    echo "<script>window.location = window.location.pathname;</script>";
}

echo '<button type="submit" onclick="document.location.href=\''.get_home_url().'/add-course\'">Create new Course</button>';

$query = $wpdb->prepare(
    'SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_value` IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s) LIMIT 1',
    $postTitle = 'All Courses'
);
$wpdb->query($query);

$id = $wpdb->last_result[0]->post_id;

global $wpdb;
$query = $wpdb->prepare(
    'SELECT `post_title`,`ID` FROM ' . $wpdb->posts . ' WHERE `ID` IN (SELECT `meta_value` FROM ' . $wpdb->postmeta . ' WHERE `meta_key`= \'_menu_item_object_id\' && `post_id` IN (SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_value` = %s AND `meta_key`=\'_menu_item_menu_item_parent\'))',
    $parentid = '' . $id
);
$wpdb->query($query);
if ( $wpdb->num_rows ) {
    $items = $wpdb->last_result;
    $string = '<table class="ulcomponents">';
    foreach ($items as $item) {
        $string .= '<a href="' . get_home_url() . '/'.$item->post_title.'"><tr id="trofulcomponents">';
        $string .= '<td class="lefttitle">' . $item->post_title . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'all-courses?id=' . $item->ID . '\';"></div>
                        <div class="edit_icon" onclick="location.href=\'pageurl.html\';"></div>
                    </td>';
        $string .= '</tr></a>';
    }

    $string .= '</table>';
    $string .= '';
    $string .= '';
    echo $string;
}else{
    echo '<p>Es wurden noch keine Kurse erstellt!</p>';
}
?>