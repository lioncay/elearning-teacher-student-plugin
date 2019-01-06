<?php

if (isset($_GET['type'])){
    if ($_GET['type']=="entry"){
        $chapterID = deleteEntry($_GET['id']);
        echo '<script>window.location.replace("' . get_home_url() . '/chapter?chapter_id=' . $chapterID . '")</script>';
    }elseif ($_GET['type']=="chapter"){
        $unitID = deleteChapter($_GET['id']);
        echo '<script>window.location.replace("' . get_home_url() . '/unit?unitid=' . $unitID . '")</script>';
    }
    elseif ($_GET['type']=="unit"){
        $course = deleteUnit($_GET['id']);
        echo '<script>window.location.replace("' . get_home_url() . '/' . $course . '")</script>';
    }
    elseif ($_GET['type']=="course"){
        deleteCourse($_GET['id']);
        echo '<script>window.location.replace("' . get_home_url() . '/all-courses")</script>';
    }
}

function clean($string) {
    $string = str_replace(' ', '-', $string);
    $string =  preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return strtolower($string);
}

function deleteCourse($coursePageId){
    global $wpdb;
    $query = $wpdb->prepare(
        'SELECT `id` FROM `courses` WHERE  pageid=%d',
        $id = $coursePageId
    );
    $wpdb->query( $query );
    $courseId = $wpdb->last_result[0]->id;
    $query = $wpdb->prepare(
        'SELECT id FROM `units` WHERE  course_id=%d',
        $id = $courseId
    );
    $wpdb->query( $query );
    $items = $wpdb->last_result;

    foreach ($items as $item){
        deleteUnit($item->id);
    }
    wp_delete_post($coursePageId, true);
    $query = $wpdb->prepare(
        'DELETE FROM `courses` WHERE  pageid=%d',
        $id = $coursePageId
    );
    $wpdb->query( $query );
}

function deleteUnit($unitId){
    global $wpdb;
    $query = $wpdb->prepare(
        'SELECT * FROM `units` WHERE id = %d',
        $id=$unitId
    );
    $wpdb->query( $query );
    $courseID=$wpdb->last_result[0]->course_id;

    $query = $wpdb->prepare(
        'SELECT `id` FROM `chapters` WHERE unit_id = %d',
        $chapter_id=$unitId
    );
    $wpdb->query( $query );
    $items = $wpdb->last_result;

    foreach ($items as $item){
        deleteChapter($item->id);
    }

    $query = $wpdb->prepare(
        'DELETE FROM `units` WHERE  id=%d',
        $id = $unitId
    );
    $wpdb->query( $query );

    $query = $wpdb->prepare(
        'SELECT `name` FROM `courses` WHERE id = %d',
        $id=$courseID
    );
    $wpdb->query( $query );
    $course = clean($wpdb->last_result[0]->name);

    return $course;
}

function deleteChapter($chapterid){
    global $wpdb;
    $query = $wpdb->prepare(
        'SELECT `unit_id` FROM `chapters` WHERE id = %d',
        $id=$chapterid
    );
    $wpdb->query( $query );
    $unitID=$wpdb->last_result[0]->unit_id;

    $query = $wpdb->prepare(
        'SELECT `id` FROM `chapterentries` WHERE chapter_id = %d',
        $chapter_id=$chapterid
    );
    $wpdb->query( $query );
    $items = $wpdb->last_result;

    foreach ($items as $item){
        deleteEntry($item->id);
    }

    $query = $wpdb->prepare(
        'DELETE FROM `chapters` WHERE  id=%d',
        $id = $chapterid
    );
    $wpdb->query( $query );
    return $unitID;
}

function deleteEntry($chapterentryid){
    global $wpdb;
    $query = $wpdb->prepare(
        'SELECT `chapter_id`,`entry_order` FROM `chapterentries` WHERE id = %d',
        $id=$chapterentryid
    );
    $wpdb->query( $query );
    $chapterID=$wpdb->last_result[0]->chapter_id;
    $entry_order=$wpdb->last_result[0]->entry_order;

    $query = $wpdb->prepare(
        'SELECT MAX(entry_order) AS `entry_order` FROM `chapterentries` WHERE chapter_id = %d ORDER BY entry_order',
        $chapter_id=$chapterID
    );
    $wpdb->query( $query );
    $highestOrderId=$wpdb->last_result[0]->entry_order;
    for($i=$entry_order;$i<=$highestOrderId;$i++){
        $query = $wpdb->prepare(
            'UPDATE `chapterentries` SET `entry_order`=`entry_order`-1 WHERE entry_order=%d AND chapter_id = %d',
            $one=$i,
            $chapter_id=$chapterID
        );
        $wpdb->query( $query );
    }
    $query = $wpdb->prepare(
        'DELETE FROM `chapterentries` WHERE  id=%d',
        $id = $chapterentryid
    );
    $wpdb->query( $query );
    return $chapterID;
}
?>