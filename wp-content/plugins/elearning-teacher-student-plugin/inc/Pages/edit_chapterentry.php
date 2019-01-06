<?php
if (isset($_GET['id'])) {
    global $wpdb;

    $query = $wpdb->prepare(
        'SELECT * FROM `chapterentries` WHERE `id` = %d',
        $id=$_GET['id']
    );
    $wpdb->query( $query );
    $entry = $wpdb->last_result[0];

    $query = $wpdb->prepare(
        'SELECT title,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$entry->chapter_id
    );
    $wpdb->query( $query );
    $items=$wpdb->last_result;
    $itemsleng=$wpdb->num_rows;
    if ($_GET['type'] == "IN") {
        ?>
        <form action="" method="post">
            <input type="text" placeholder="Bezeichnung" name="etitle" value="<?php echo $entry->title; ?>" required/>
            <select id="entry_order" name="entry_order" required>
                <option value="" disabled>Wo soll dieser Eintrag eingeordnet werden</option>
                <?php
                if ($entry->entry_order-1==0){
                    echo '<option value="0" selected>am Anfang</option>';
                }else{
                    echo '<option value="0">am Anfang</option>';
                }
                if ($itemsleng) {
                    foreach ($items as $item) {
                        if ($item->entry_order==$entry->entry_order-1){
                            echo '<option value="' . $item->entry_order . '" selected>nach ' . $item->title . '</option>';
                        }else if($item->entry_order!=$entry->entry_order){
                            echo '<option value="' . $item->entry_order . '">nach ' . $item->title . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <?php
            $content = $entry->input;
            $editor_id = 'input';
            wp_editor($content, $editor_id);
            ?>
            <input type="hidden" name="chapter_id" value="<?php echo $chapter_id ?>">
            <button type="submit" name="submit">Hinzufügen</button>
        </form>
        <?php
    } elseif ($_GET['type'] == "OQ") {
        echo "dass ist ein oq eintrag";
    } elseif ($_GET['type'] == "MC") {
        echo "dass ist ein mc eintrag";
    }
} else {
    echo "<p>Hoppala! Da hat etwas nicht funktioniert. Bitte versuche es noch einmal..." .
        "Suche dir ein Kapitel aus und klicke noch einmal auf den Bearbeiten-Stift neben einem Eintrag deiner Wahl</p>";
}

?>