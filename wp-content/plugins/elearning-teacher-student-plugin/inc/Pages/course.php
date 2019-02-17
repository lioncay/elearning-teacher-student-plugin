<?php
global $wpdb;

$query = $wpdb->prepare(
    'SELECT * FROM courses WHERE pageid= %d',
    $course = $_GET['courseid']
);
$wpdb->query($query);
$courseid=$wpdb->last_result[0]->id;
$coursename=$wpdb->last_result[0]->name;
echo "<script>document.getElementsByTagName('h1')[0].innerHTML += ' - ".$wpdb->last_result[0]->name . "'</script>";

$query = $wpdb->prepare(
    'SELECT done_units FROM user_course_attempts WHERE userid = %d AND courseid= %d',
    $id = get_current_user_id(),
    $course = $courseid
);
$wpdb->query($query);

if ( $wpdb->num_rows ) {
    if (!isset($_POST['confirm'])) {
        ?>
        <form method="post">
            <h3>Willst du den Kurs "<?php echo $coursename; ?>" fortsetzen?</h3>
            <input type="submit" name="confirm" value="Ja">
            <input type="submit" name="confirm" value="Nein">
        </form>
        <?php
    }else if (isset($_POST['confirm'])){
        if ($_POST['confirm']=="Nein"){
            $doneunits = array();
            $query = $wpdb->prepare(
                'UPDATE `user_course_attempts` SET `done_units`="", `course_done`=0 WHERE userid=%d AND courseid=%d',
                $one=get_current_user_id(),
                $two=$courseid
            );
            $wpdb->query( $query );
            $doneunits = array();
        }else{
            $doneunits = explode(",", $wpdb->last_result[0]->done_units);
        }

        $query = $wpdb->prepare(
            'SELECT courseid FROM users_of_course WHERE userid = %d AND courseid= %d',
            $id = get_current_user_id(),
            $course = $_GET['courseid']
        );
        $wpdb->query($query);
        if ( $wpdb->num_rows ) {
            $query = $wpdb->prepare(
                'SELECT * FROM units WHERE course_id= %d',
                $course = $courseid
            );
            $wpdb->query($query);
            if ( $wpdb->num_rows ) {
                $items = $wpdb->last_result;
                $string = '<table class="ulcomponents">';
                foreach ($items as $item) {
                    if (!in_array($item->id,$doneunits)){
                        $string .= '<tr id="trofulcomponents">';
                        $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/user-unit?unitid=' . $item->id . '&unitname=' . $item->name . '\'">' . $item->name . '</td><td class="rightaction">
                        <div class="startcourse" onclick="location.href=\'user-unit?unitid=' . $item->id . '&unitname=' . $item->name . '\'"></div>
                    </td>';
                        $string .= '</tr>';
                    }else{
                        $string .= '<tr id="trofulcomponents">';
                        $string .= '<td class="lefttitle">' . $item->name . '</td><td class="rightaction">
                        <div class="unitdone"></div>
                    </td>';
                        $string .= '</tr>';
                    }
                }

                $string .= '</table>';
                $string .= '';
                $string .= '';
                echo $string;
            }
        }else{
            echo '<h1 style="text-align: center;">Du wurdest leider nicht zu diesem Kurs zugeteilt!</h1>';
        }
    }
}else{
    $query = $wpdb->prepare(
        'INSERT INTO `user_course_attempts` (`userid`, `courseid`, `done_units`, `course_done`) VALUES (%d,%d,%s,%d)',
        $one=get_current_user_id(),
        $two=$courseid,
        $three="",
        $four=0
    );
    $wpdb->query( $query );
    $doneunits = array();

    $query = $wpdb->prepare(
        'SELECT courseid FROM users_of_course WHERE userid = %d AND courseid= %d',
        $id = get_current_user_id(),
        $course = $_GET['courseid']
    );
    $wpdb->query($query);
    if ( $wpdb->num_rows ) {
        $query = $wpdb->prepare(
            'SELECT * FROM units WHERE course_id= %d',
            $course = $courseid
        );
        $wpdb->query($query);
        if ( $wpdb->num_rows ) {
            $items = $wpdb->last_result;
            $string = '<table class="ulcomponents">';
            foreach ($items as $item) {
                if (!in_array($item->id,$doneunits)){
                    $string .= '<tr id="trofulcomponents">';
                    $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/user-unit?unitid=' . $item->id . '&unitname=' . $item->name . '\'">' . $item->name . '</td><td class="rightaction">
                        <div class="startcourse" onclick="location.href=\'user-unit?unitid=' . $item->id . '&unitname=' . $item->name . '\'"></div>
                    </td>';
                    $string .= '</tr>';
                }else{
                    $string .= '<tr id="trofulcomponents">';
                    $string .= '<td class="lefttitle">' . $item->name . '</td><td class="rightaction">
                        <div class="unitdone"></div>
                    </td>';
                    $string .= '</tr>';
                }
            }

            $string .= '</table>';
            $string .= '';
            $string .= '';
            echo $string;
        }
    }else{
        echo '<h1 style="text-align: center;">Du wurdest leider nicht zu diesem Kurs zugeteilt!</h1>';
    }
}
?>