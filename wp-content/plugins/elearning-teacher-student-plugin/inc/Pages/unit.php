<?php
global $wpdb;
if(isset($_GET['unitid'])){
    echo '<button type="submit" onclick="document.location.href=\'' . get_home_url() . '/add-chapter?unit_id=' . $_GET['unitid'] . '\'">Neues Kapitel erstellen</button>';

    $query = $wpdb->prepare(
        'SELECT `name` FROM `chapters` WHERE `unit_id` = %d',
        $unitId = $_GET['unitid']
    );
    $wpdb->query($query);

    if ($wpdb->num_rows) {
        $items = $wpdb->last_result;
        $string = '<table class="ulcomponents">';
        foreach ($items as $item) {
            $string .= '<tr id="trofulcomponents">';
            $string .= '<td class="lefttitle" onclick="location.href=\'' . get_home_url() . '/'.$item->id.'\'">' . $item->post_title . '</td><td class="rightaction">
                        <div class="paper_basket_icon" onclick="location.href=\'all-courses?id=' . $item->id . '\';"></div>
                        <div class="edit_icon" onclick="location.href=\'' . get_home_url() . '/edit-course?id=' . $item->id . '\';"></div>
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