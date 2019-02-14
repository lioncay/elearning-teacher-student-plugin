<?php

if (isset($_GET['id'])&&isset($_GET['courseid'])){
    global $wpdb;
    $query = $wpdb->prepare(
        'DELETE FROM `users_of_course` WHERE userid=%d AND courseid=%d',
        $one = $_GET['id'],
        $two = $_GET['courseid']
    );
    $wpdb->query( $query );
    echo '<script>window.location.replace("' . get_home_url() . '/course-users?courseid=' . $_GET['courseid'] . '")</script>';

}
?>