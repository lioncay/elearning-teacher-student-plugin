<?php
global $wpdb;

if(isset($_POST['submit'])){
    $chapterID = $_POST['chapter_id'];
    $query = $wpdb->prepare(
        'SELECT MAX(entry_order) AS `entry_order` FROM `chapterentries` WHERE chapter_id = %d ORDER BY entry_order',
        $chapter_id=$chapterID
    );
    $wpdb->query( $query );
    if($wpdb->num_rows){
        $highestOrderId=$wpdb->last_result[0]->entry_order;
        for($i=$_POST['entry_order']+1;$i<=$highestOrderId;$i++){
            $query = $wpdb->prepare(
                'UPDATE `chapterentries` SET `entry_order`=%d WHERE entry_order=%d AND chapter_id = %d',
                $newentry_order=$i+1,
                $oldentry_order=$i,
                $chapter_id=$chapterID
            );
            $wpdb->query( $query );
        }
        $query = $wpdb->prepare(
            'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
            $one=$_POST['etitle'],
            $two=$chapterID,
            $three='IN',
            $four=$_POST['input'],
            $five=$_POST['entry_order']+1
        );
        $wpdb->query( $query );
    }else{
        if($_POST['entry_order']==0){
            $query = $wpdb->prepare(
                'INSERT INTO `chapterentries` (`title`, `chapter_id`, `entry_type`, `input`, `entry_order`) VALUES (%s,%d,%s,%s,%d)',
                $one=$_POST['etitle'],
                $two=$chapterID,
                $three='IN',
                $four=$_POST['input'],
                $five=1
            );
            $wpdb->query( $query );
        }else{
            echo "Ups da hat etwas bei deiner Eingabe nicht funktioniert. Bitte wende dich an den Verantwortlichen Administrator.";
        }
    }
    echo '<script>window.location.replace("' . get_home_url() . '/chapter?chapter_id=' . $chapterID . '")</script>';
}
if(isset($_GET['chapter_id'])){

    $query = $wpdb->prepare(
        'SELECT title,entry_order FROM `chapterentries` WHERE `chapter_id` = %s ORDER BY entry_order',
        $chapter_id=$_GET['chapter_id']
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
        <?php
        $content = 'Hier wird der Info Eintrag hinzugefügt!';
        $editor_id = 'input';
        wp_editor( $content, $editor_id );
        ?>
        <input type="hidden" name="chapter_id" value="<?php echo $_GET['chapter_id'] ?>">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst ein Kapitel anclicken und von dort einen Eintrag hinzufügen!";
}
?>