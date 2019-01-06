<?php
global $wpdb;

if(isset($_GET['id'])){
    $query = $wpdb->prepare(
        'SELECT `id` FROM `courses` WHERE  pageid=%d',
        $id = $_GET['id']
    );
    $wpdb->query( $query );
    $courseId = $wpdb->last_result[0]->id;
    wp_delete_post($_GET['id'], true);
    $query = $wpdb->prepare(
        'DELETE FROM `courses` WHERE  pageid=%d',
        $id = $_GET['id']
    );
    $wpdb->query( $query );
    $query = $wpdb->prepare(
        'DELETE FROM `units` WHERE  course_id=%d',
        $id = $courseId
    );
    $wpdb->query( $query );
    echo "<script>window.location = window.location.pathname;</script>";
}

echo '<p><button type="submit" onclick="document.location.href=\''.get_home_url().'/add-course\'">Neuen Kurs erstellen</button></p>';

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
        $string .= '<tr id="trofulcomponents">';
        $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/'.$item->post_title.'\'">' . $item->post_title . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'all-courses?id=' . $item->ID . '\';"></div>
                        <div class="edit_icon" onclick="location.href=\'' . get_home_url() . '/edit-course?id=' . $item->ID . '\';"></div>
                    </td>';
        $string .= '</tr>';
    }

    $string .= '</table>';
    $string .= '';
    $string .= '';
    echo $string;
}else{
    echo '<p>Es wurden noch keine Kurse erstellt!</p>';
}
?>