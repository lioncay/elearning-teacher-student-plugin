<?php
if(isset($_POST['submit'])){

    global $wpdb;
    $query = $wpdb->prepare(
        'INSERT INTO `units` (`name`, `course_id`) VALUES (%s,%d)',
        $one=$_POST['unit_name'],
        $two=$_POST['courseid']
    );
    $wpdb->query( $query );

    $query = $wpdb->prepare(
        'SELECT `name` FROM `courses` WHERE `id` = %d',
        $one=$_POST['courseid']
    );
    $wpdb->query( $query );

    echo '<script>window.location.replace("' . get_home_url() . '/' . $wpdb->last_result[0]->name . '")</script>';
}
if(isset($_GET['courseid'])){
?>
<form action="" method="post">
    <input type="text" placeholder="Name" name="unit_name" required>
    <input type="hidden" name="courseid" value="<?php echo $_GET['courseid'] ?>">
    <button type="submit" name="submit">Hinzufügen</button>
</form>
<?php
} else{
    echo "Bitte zuerst einen Kurs anclicken und von dort eine Unit hinzufügen!";
}
?>
