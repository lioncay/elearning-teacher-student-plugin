<?php
if(isset($_POST['submit'])){
    global $wpdb;
    $query = $wpdb->prepare(
        'INSERT INTO `chapters` (`name`, `unit_id`, `summary`) VALUES (%s,%d,%s)',
        $one=$_POST['chapter_name'],
        $two=$_POST['unit_id'],
        $three=$_POST['summary']
    );
    $wpdb->query( $query );
    echo '<script>window.location.replace("' . get_home_url() . '/unit?unitid=' . $_POST['unit_id'] . '")</script>';
}
if(isset($_GET['unit_id'])){
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Name" name="chapter_name" required>
        <?php
        $content = 'Hier wird das Summary für das Kapitel hinzugefügt!';
        $editor_id = 'summary';
        wp_editor( $content, $editor_id );
        ?>
        <input type="hidden" name="unit_id" value="<?php echo $_GET['unit_id'] ?>">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst eine Unit anclicken und von dort ein Kapitel hinzufügen!";
}
?>