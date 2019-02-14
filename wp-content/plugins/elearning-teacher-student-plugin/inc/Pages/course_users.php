<?php
if (isset($_GET['courseid'])){
    global $wpdb;

    echo '<p><button type="submit" onclick="document.location.href=\''.get_home_url().'/add-user-to-course?courseid=' . $_GET['courseid'] . '\'">Neuen User hinzufügen</button></p>';
    echo '<p><button type="submit" onclick="document.location.href=\''.get_home_url().'/add-user-to-course?courseid=' . $_GET['courseid'] . '&existinguser\'">Bereits vorhandenen User hinzufügen</button></p>';
    echo '<p><button type="submit" onclick="document.location.href=\''.get_home_url().'/all-courses/\'">Zurück zu allen Kursen</button></p>';

    $query = $wpdb->prepare(
        'SELECT `user_id`,meta_value FROM ' . $wpdb->usermeta . ' WHERE `user_id` IN (SELECT userid FROM users_of_course WHERE courseid = %s) AND meta_key IN ("first_name","last_name", "nickname")',
        $course_id = $_GET['courseid']
    );
    $wpdb->query($query);
    if ( $wpdb->num_rows ) {
        $items = $wpdb->last_result;
        $names = array();
        $oldid = $items[0]->user_id;
        $name = array();
        foreach ($items as $item) {
            if($item->user_id==$oldid){
                array_push($name,$item->meta_value);
            }else{
                array_push($name,$oldid);
                array_push($names,$name);
                $name = array();
                array_push($name,$item->meta_value);
                $oldid = $item->user_id;
            }
        }
        array_push($name,$oldid);
        array_push($names,$name);
        $string = '<table class="ulcomponents">';
        foreach ($names as $item) {
            if(sizeof($item)>=4){
                $string .= '<tr id="trofulcomponents">';
                $string .= '<td class="lefttitle">' .$item[1] . ' ' . $item[2] . ' (' . $item[0] . ')' . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'' . get_home_url() . '/delete-userfromcourse?id=' . $item[3] . '\';"></div>
                    </td>';
                $string .= '</tr>';
            }
        }

        $string .= '</table>';
        $string .= '';
        $string .= '';
        echo $string;
    }else{
        echo '<p>Es wurden noch keine User hinzugefügt!</p>';
    }
}
?>