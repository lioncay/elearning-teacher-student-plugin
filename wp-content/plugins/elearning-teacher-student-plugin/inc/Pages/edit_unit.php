<?php
global $wpdb;
if(isset($_POST['submit'])){
    $query = $wpdb->prepare(
        'UPDATE `units` SET `name`=%s WHERE `id` = %d',
        $name=$_POST['unit_name'],
        $id=$_POST['unit_id']
    );
    $wpdb->query( $query );

    $query = $wpdb->prepare(
        'SELECT `name` FROM `courses` WHERE `id` = %d',
        $one=$_POST['courseid']
    );
    $wpdb->query( $query );

    echo '<script>window.location.replace("' . get_home_url() . '/' . $wpdb->last_result[0]->name . '")</script>';
}
if(isset($_GET['id'])){
    $query = $wpdb->prepare(
        'SELECT * FROM `units` WHERE `id` = %d',
        $id=$_GET['id']
    );
    $wpdb->query( $query );
    $unit = $wpdb->last_result[0];
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Name" name="unit_name" value="<?php echo $unit->name; ?>" required>
        <input type="hidden" name="courseid" value="<?php echo $unit->course_id; ?>">
        <input type="hidden" name="unit_id" value="<?php echo $_GET['id']; ?>">
        <button type="submit" name="submit">Bearbeiten</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst einen Kurs anclicken und von dort eine Unit bearbeiten!";
}
?>
