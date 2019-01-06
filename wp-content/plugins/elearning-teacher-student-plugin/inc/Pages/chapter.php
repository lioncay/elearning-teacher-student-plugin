<?php

if (isset($_GET['chapter_id'])) {
    global $wpdb;

    echo '<p><button type="submit" onclick="document.location.href=\'' . get_home_url() . '/add-info?chapter_id=' . $_GET['chapter_id'] . '\'">Neuen Info Eintrag erstellen</button></p>';
    echo '<p><button type="submit" onclick="document.location.href=\'' . get_home_url() . '/add-multiple-choice-question?chapter_id=' . $_GET['chapter_id'] . '\'">Neue Multiple Choice Frage erstellen</button></p>';
    echo '<p><button type="submit" onclick="document.location.href=\'' . get_home_url() . '/add-open-question?chapter_id=' . $_GET['chapter_id'] . '\'">Neue offene Frage erstellen</button></p>';


    $query = $wpdb->prepare(
        'SELECT `id`,`entry_type`,`title` FROM `chapterentries` WHERE `chapter_id` = %d ORDER BY `entry_order`',
        $courseId = $_GET['chapter_id']
    );
    $wpdb->query($query);
    if ($wpdb->num_rows) {
        $items = $wpdb->last_result;
        $string = '<table class="ulcomponents">';
        foreach ($items as $item) {
            $string .= '<tr id="trofulcomponents">';
            $string .= '<td class="lefttitle">' . $item->title . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'delete-courseunitchapterentries?id=' . $item->id . '&type=entry\';"></div>
                        <div class="edit_icon" onclick="location.href=\'' . get_home_url() . '/edit-chapterentry?id=' . $item->id . '&type=' . $item->entry_type . '\';"></div>
                    </td>';
            $string .= '</tr>';
        }

        $string .= '</table>';
        $string .= '';
        $string .= '';
        echo $string;
    } else {
        echo '<p>Noch keine Einträge erstellt!</p>';
    };
} else {
    echo '<p>Bitte zuerst eine Unit anclicken und von dort ein Kapitel auswählen!</p>';
}

?>