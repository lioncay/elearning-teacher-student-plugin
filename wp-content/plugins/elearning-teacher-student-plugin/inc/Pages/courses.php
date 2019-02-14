<?php
global $wpdb;

echo '<p><button type="submit" onclick="document.location.href=\''.get_home_url().'/add-course\'">Neuen Kurs erstellen</button></p>';

$query = $wpdb->prepare(
    'SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_value` IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s) LIMIT 1',
    $postTitle = 'Alle Kurse'
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
                        <div class="paper_basket_icon" onclick="location.href=\'' . get_home_url() . '/delete-courseunitchapterentries?id=' . $item->ID . '&type=course\';"></div>
                        <div class="edit_icon" onclick="location.href=\'' . get_home_url() . '/edit-course?id=' . $item->ID . '\';"></div>
                        <div class="users_icon" onclick="location.href=\'' . get_home_url() . '/course-users?courseid=' . $item->ID.'\'"></div>
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