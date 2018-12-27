<?php
global $wpdb;

echo '<form><button type="button" onclick="document.location.href=\''.get_home_url().'/add-course\'">Create new Course</button></form>';

$query = $wpdb->prepare(
    'SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_value` IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s) LIMIT 1',
    $postTitle = 'All Courses'
);
$wpdb->query($query);

$id = $wpdb->last_result[0]->post_id;

global $wpdb;
$query = $wpdb->prepare(
    'SELECT `post_title` FROM ' . $wpdb->posts . ' WHERE `ID` IN (SELECT `meta_value` FROM ' . $wpdb->postmeta . ' WHERE `meta_key`= \'_menu_item_object_id\' && `post_id` IN (SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_value` = %s AND `meta_key`=\'_menu_item_menu_item_parent\'))',
    $parentid = '' . $id
);
$wpdb->query($query);
if ( $wpdb->num_rows ) {
    $items = $wpdb->last_result;
    $string = '<ul>';
    foreach ($items as $item) {
        $string .= '<a href="' . get_home_url() . '/'.$item->post_title.'"><li class="">';
        $string .= $item->post_title;
        $string .= '</li></a>';
    }

    $string .= '</ul>';
    $string .= '';
    $string .= '';
    echo $string;
}else{
    echo 'Es wurden noch keine Kurse erstellt!';
}
?>