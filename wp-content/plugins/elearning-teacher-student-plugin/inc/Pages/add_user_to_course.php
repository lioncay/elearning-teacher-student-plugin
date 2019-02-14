<?php
function clean($string) {
    $string = str_replace(' ', '', $string);
    $string =  preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return strtolower($string);
}
global $wpdb;
if(isset($_POST['submit'])){
    if (!isset($_POST['user_to_add'])){
        $username = clean($_POST['lastname']) . date("y");
        $user_email = $_POST['mail'];
        $user_id = username_exists( $username );
        if ($user_id){
            $username = clean($_POST['lastname']) . substr(clean($_POST['firstname']),0,2) . date("y");
            $user_id = username_exists( $username );
        }
        if ( !$user_id and email_exists($user_email) == false ) {
            $_GET["userexists"]=false;
            $random_password = wp_generate_password( 12, true );
            $user_id = wp_create_user( $username, $random_password, $user_email );
            wp_update_user(
                array(
                        'ID'    => $user_id,
                    'last_name' => $_POST['lastname'],
                    'first_name'=> $_POST['firstname'],
                    'role'      => "subscriber"
                )
            );
            wp_mail( $user_email, 'Welcome!', 'Your username for https://elearning.e-co-foot.eu is:'.$username.'\nYour Password is: ' . $random_password );
            $query = $wpdb->prepare(
                'INSERT INTO `users_of_course` (`userid`, `courseid`) VALUES (%d,%d)',
                $one=$user_id,
                $two=$_POST['courseid']
            );
            $wpdb->query( $query );
        } else {
            echo '<script>window.location.replace("' . get_home_url() . '/add-user-to-course?courseid=' . $_POST['courseid'] . '&userexists=true")</script>';
        }
    }else{
        $query = $wpdb->prepare(
            'SELECT * FROM `users_of_course` WHERE `userid` = %d AND `courseid` = %d',
            $one=$_POST['user_to_add'],
            $two=$_POST['courseid']
        );
        $wpdb->query($query);
        if ( !$wpdb->num_rows ) {
            $query = $wpdb->prepare(
                'INSERT INTO `users_of_course` (`userid`, `courseid`) VALUES (%d,%d)',
                $one=$_POST['user_to_add'],
                $two=$_POST['courseid']
            );
            $wpdb->query( $query );
        }
    }

    echo '<script>window.location.replace("' . get_home_url() . '/course-users?courseid=' . $_POST['courseid'] . '")</script>';
}

if(isset($_GET['courseid'])&&!isset($_GET['existinguser'])){
?>

<form action="" method="post" id="addnewuser">
    <?php if (isset($_GET["userexists"])&& $_GET["userexists"]){ ?>
    <label>User existiert bereits!</label>
    <?php }; ?>
    <input type="text" placeholder="Nachname (Username wird aus Nachname automatisch generiert)" id="lastname" name="lastname" required>
    <input type="text" placeholder="Vorname" id="firstname" name="firstname" required>
    <input type="email" placeholder="Mail" id="mail" name="mail" required>
    <input type="hidden" name="courseid" value="<?php echo $_GET['courseid']; ?>">
    <button type="submit" name="submit">Hinzufügen</button>
</form>
<?php }else if (isset($_GET['courseid'])&&isset($_GET['existinguser'])){
    echo '<form action="" method="post" id="addexistinguser">';
        $wpdb->query('SELECT ID,user_login FROM ' . $wpdb->users);
        if ( $wpdb->num_rows ) {
            $items = $wpdb->last_result;
            echo '<select id="user_to_add" name="user_to_add" required><option value="" disabled selected>User auswählen</option>';
            foreach ($items as $item) {
                echo '<option value="'. $item->ID .'">'. $item->user_login .'</option>';
            }
            echo '</select>';
            };
        echo '<input type="hidden" name="courseid" value="'. $_GET['courseid'] . '"><button type="submit" name="submit">Hinzufügen</button>';
    echo '</form>';
} else{
    echo "Bitte zuerst einen Kurs anclicken und von dort einen User hinzufügen!";
};?>