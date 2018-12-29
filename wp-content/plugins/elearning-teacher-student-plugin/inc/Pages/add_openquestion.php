<?php
global $wpdb;
$pname = str_replace(" ", "-", $_GET['chapter_name']);
$pname = strtolower($pname);
$query = $wpdb->prepare(
    'SELECT ID FROM ' . $wpdb->posts . ' WHERE `post_title` = %s AND `post_name` = %s',
    $postTitle = $_GET['chapter_name'],
    $postTitlesec = $pname
);
$wpdb->query($query);
$chapterID = $wpdb->last_result[0]->ID;
if(isset($_POST['submit'])){
    $openquestionstring = "";
    $openquestionstring .= $_POST['question'] . "&";
    $openquestionstring .= $_POST['answer_1'] . "&";
    $openquestionstring .= $_POST['answer_2'] . "&";
    $openquestionstring .= $_POST['answer_3'];
    $query = $wpdb->prepare(
        'SELECT MAX(entry_order) AS `entry_order` FROM `chapterentries` WHERE chapter_id = %d ORDER BY entry_order',
        $chapter_id=$chapterID
    );
    $wpdb->query( $query );
    if ($wpdb->num_rows) {
        $highestOrderId=$wpdb->last_result[0]->entry_order;
        for ($i = $_POST['entry_order'] + 1; $i <= $highestOrderId; $i++) {
            $query = $wpdb->prepare(
                'UPDATE `chapterentries` SET `entry_order`=%d WHERE entry_order=%d AND chapter_id = %d',
                $newentry_order = $i + 1,
                $oldentry_order = $i,
                $chapter_id = $chapterID
            );
            $wpdb->query($query);
        }
        $query = $wpdb->prepare(
            'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
            $one = $_POST['etitle'],
            $two = $chapterID,
            $three = 'OQ',
            $four = $openquestionstring,
            $five = $_POST['entry_order'] + 1
        );
        $wpdb->query($query);
    } else {
        if ($_POST['entry_order'] == 0) {
            $query = $wpdb->prepare(
                'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
                $one = $_POST['etitle'],
                $two = $chapterID,
                $three = 'OQ',
                $four = $openquestionstring,
                $five = 1
            );
            $wpdb->query($query);
        } else {
            echo "Ups da hat etwas bei deiner Eingabe nicht funktioniert. Bitte wende dich an den Verantwortlichen Administrator.";
        }
    }
    echo '<script>window.location.replace("' . get_home_url() . '/' . $_POST['unit_name'] . '")</script>';
}
if(isset($_GET['chapter_name'])){
    $query = $wpdb->prepare(
        'SELECT title,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$chapterID
    );
    $wpdb->query( $query );
    $items=$wpdb->last_result;
    $itemsleng=$wpdb->num_rows;
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Bezeichnung" name="etitle" required/>
        <select id="entry_order" name="entry_order" required>
            <option value="" disabled selected>Wo soll dieser Eintrag eingeordnet werden</option>
            <option value="0">am Anfang</option>
            <?php
            if($itemsleng){
                foreach ($items as $item) {
                    echo '<option value="'. $item->entry_order .'">nach '. $item->title .'</option>';
                }
            }
            ?>
        </select>
        <input type="text" placeholder="Frage" name="question" required/>
        <input type="text" placeholder="Antwortmöglichkeit 1 (mögliche Antwort oder nur Keywords die vorkommen sollten.)" name="answer_1" required/>
        <input type="text" placeholder="Antwortmöglichkeit 2 (optional)" name="answer_2"/>
        <input type="text" placeholder="Antwortmöglichkeit 3 (optional)" name="answer_3"/>
        <input type="hidden" name="unit_name" value="<?php echo $_GET['chapter_name'] ?>">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst ein Kapitel anclicken und von dort einen Eintrag hinzufügen!";
}
?>