<?php
global $wpdb;
function clean($string) {
    $string = str_replace(' ', '-', $string);
    $string =  preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return strtolower($string);
}
if(isset($_POST['submit'])){

    $new_post = array(
        'ID' => $_POST['postid'],
        'post_title' => $_POST['coursename'],
        'post_name' => clean($_POST['coursename'])
    );
    wp_update_post($new_post);
    $query = $wpdb->prepare(
        'UPDATE `courses` SET `name`=%s,`age`=%d WHERE `pageid` = %d',
        $name=$_POST['coursename'],
        $age=$_POST['age'],
        $id=$_POST['postid']
    );
    $wpdb->query( $query );
    echo '<script>window.location.replace("' . get_home_url() . '/all-courses")</script>';
}else if(isset($_GET['id'])){
    $query = $wpdb->prepare(
        'SELECT * FROM `courses` WHERE `pageid` = %d',
        $id=$_GET['id']
    );
    $wpdb->query( $query );
    $course = $wpdb->last_result[0];
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Name" id="coursename" name="coursename" required value="<?php echo $course->name; ?>">
        <select id="age" name="age" required>
            <?php
            if ($course->age==0){
                echo '<option value="0" selected>unter 13 Jahre</option>';
            }else{
                echo '<option value="1" selected>13 und älter</option>';
            }
            ?>
            <option value="" disabled>Alter</option>
            <option value="0">unter 13 Jahre</option>
            <option value="1">13 und älter</option>
        </select>
        <input type="hidden" value="<?php echo $_GET['id'];?>" name="postid">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
<?php
}else{
    echo "Hoppala! Da hat etwas nicht funltioniert. Bitte versuche es noch einmal!";
}
?>

