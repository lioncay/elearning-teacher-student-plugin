<?php
global $wpdb;
if(isset($_GET['unitid'])){
    echo '<p><button type="submit" onclick="document.location.href=\'' . get_home_url() . '/add-chapter?unit_id=' . $_GET['unitid'] . '\'">Neues Kapitel erstellen</button></p>';

    $query = $wpdb->prepare(
        'SELECT `id`,`name` FROM `chapters` WHERE `unit_id` = %d',
        $unitId = $_GET['unitid']
    );
    $wpdb->query($query);

    if ($wpdb->num_rows) {
        $items = $wpdb->last_result;
        $string = '<table class="ulcomponents">';
        foreach ($items as $item) {
            $string .= '<tr id="trofulcomponents">';
            $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/chapter?chapter_id='.$item->id.'\'">' . $item->name . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'delete-courseunitchapterentries?id=' . $item->id . '&type=chapter\';"></div>
                        <div class="edit_icon" onclick="location.href=\'' . get_home_url() . '/edit-chapter?id=' . $item->id . '\';"></div>
                    </td>';
            $string .= '</tr>';
        }

        $string .= '</table>';
        $string .= '';
        $string .= '';
        echo $string;
    } else {
        echo '<p>Noch keine Kapitel erstellt!</p>';
    };
}else{
    echo '<p>Bitte zuerst einen Course anclicken und von dort eine Unit auswählen!</p>';
}

?>