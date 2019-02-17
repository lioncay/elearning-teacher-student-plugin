<?php
global $wpdb;

$query = $wpdb->prepare(
    'SELECT * FROM courses WHERE pageid IN (SELECT courseid FROM users_of_course WHERE userid = %s)',
    $id = get_current_user_id()
);
$wpdb->query($query);
if ( $wpdb->num_rows ) {
    $items = $wpdb->last_result;
    $string = '<table class="ulcomponents">';
    foreach ($items as $item) {
        $string .= '<tr id="trofulcomponents">';
        $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/course?courseid='.$item->pageid.'\'">' . $item->name . '</td><td class="rightaction">
                        <div class="startcourse" onclick="location.href=\'course?courseid=' . $item->pageid . '\'"></div>
                    </td>';
        $string .= '</tr>';
    }

    $string .= '</table>';
    $string .= '';
    $string .= '';
    echo $string;
}else{
    echo '<h1 style="text-align: center;">Du wurdest leider noch keinen Kursen zugeteilt!</h1>';
}
?>