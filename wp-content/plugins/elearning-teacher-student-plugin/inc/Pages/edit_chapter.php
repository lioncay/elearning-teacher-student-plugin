<?php
global $wpdb;
if(isset($_POST['submit'])){
    $query = $wpdb->prepare(
        'UPDATE `chapters` SET `name`=%s, `summary`=%s WHERE `id` = %d',
        $name=$_POST['chapter_name'],
        $summary=$_POST['summary'],
        $id=$_POST['chapter_id']
    );
    $wpdb->query( $query );

    echo '<script>window.location.replace("' . get_home_url() . '/unit?unitid=' . $_POST['unit_id'] . '")</script>';
}
if(isset($_GET['id'])){
    $query = $wpdb->prepare(
        'SELECT * FROM `chapters` WHERE `id` = %d',
        $id=$_GET['id']
    );
    $wpdb->query( $query );
    $unit = $wpdb->last_result[0];
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Name" name="chapter_name" value="<?php echo $unit->name; ?>" required>
        <?php
        $content = $unit->summary;
        $editor_id = 'summary';
        wp_editor( $content, $editor_id );
        ?>
        <input type="hidden" name="unit_id" value="<?php echo $unit->unit_id ?>">
        <input type="hidden" name="chapter_id" value="<?php echo $_GET['id']; ?>">
        <button type="submit" name="submit">Bearbeiten</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst eine Unit anclicken und von dort ein Kapitel bearbeiten!";
}
?>

