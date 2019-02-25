<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package ElearningTeacherStudentPlugin
 */

if(!defined('WP_UNINSTALL_PLUGIN')){
    die;
}


$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'course'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)");

function deletePost($title, $name){
    global $wpdb;
    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND `post_name` = %s',
        $postTitle = $title,
        $postName = $name
    );
    $wpdb->query($query);
    if ($wpdb->num_rows){
        wp_delete_post($wpdb->last_result[0]->ID);
    }
}

deletePost("Alle Kurse", "all-courses");
deletePost("Kurs Hinzufügen", "add-course");
deletePost("Kapitel Hinzufügen", "add-chapter");
deletePost("Unit Hinzufügen", "add-unit");
deletePost("Info Hinzufügen", "add-info");
deletePost("Multiple Choice Frage Hinzufügen", "add-multiple-choice-question");
deletePost("Offene Frage Hinzufügen", "add-open-question");
deletePost("Kurs-Daten Bearbeiten", "edit-course");
deletePost("Kapitel-Daten Bearbeiten", "edit-chapter");
deletePost("Unit-Daten Bearbeiten", "edit-unit");
deletePost("Unit", "unit");
deletePost("Kapitel", "chapter");
deletePost("Kapitel Eintrag Bearbeiten", "edit-chapterentry");
deletePost("Kurs Unit Kapitel oder Eintrag Löschen", "delete-courseunitchapterentries");
deletePost("User zu Kurs hinzufügen", "add-user-to-course");
deletePost("Kurs Users", "course-users");
deletePost("User von Kurs löschen", "delete-userfromcourse");
deletePost("Kurs", "course");
deletePost("Unit User", "user-unit");
