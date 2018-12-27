<?php
if(isset($_POST['submit'])){
    function do_insert($postid, $second, $third){
        global $wpdb;
        $query = $wpdb->prepare(
            'INSERT INTO ' . $wpdb->postmeta . ' (`post_id`, `meta_key`, `meta_value`) VALUES (%d,%s,%s)',
            $one=$postid,
            $two=$second,
            $three=$third
        );
        $wpdb->query( $query );
    }

    global $wpdb;
    $new_post = array(
        'post_title' => $_POST['unit_name'],
        'post_status'=> 'publish',
        'post_type'  => 'page',
        'post_content' => '[php_everywhere]',
        'post_author' => '1',
        'post_category' => array(1,2),
        'page_template' => NULL
    );
    wp_insert_post($new_post,$wp_error=false);

    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s',
        $postTitle=$_POST['coursename']
    );
    $wpdb->query( $query );
    $parentID = $wpdb->last_result[0]->ID;

    //TODO add to units table (parent id and name)

    $query = $wpdb->prepare(
        'INSERT INTO `units` (`name`, `course_id`) VALUES (%s,%d)',
        $one=$_POST['unit_name'],
        $two=$parentID
    );
    $wpdb->query( $query );

    $pname = str_replace(" ","-",$_POST['unit_name']);
    $pname = strtolower($pname);
    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND `post_name` = %s',
        $postTitle=$_POST['unit_name'],
        $postTitlesec=$pname
    );
    $wpdb->query( $query );
    $postid = $wpdb->last_result[0]->ID;

    $string = "";
    $string .= '<?php echo \'<form><button type="button" onclick="document.location.href=\\\''.get_home_url().'/add-chapter?unit_name=' . $_POST['unit_name'] . '\\\'">Neues Kapitel erstellen</button></form>\';';
    $string .= '
        global $wpdb;
        $query = $wpdb->prepare(
        \'SELECT ID from `wp_posts` WHERE `post_title` = %s AND `post_name` = %s\',
        $postTitle = \''.$_POST['unit_name'].'\',
        $postTitlesec = \''.$pname.'\'
    );';
    $string .= '$wpdb->query($query);
    $id = $wpdb->last_result[0]->ID;

    $query = $wpdb->prepare(
        \'SELECT `name` FROM `chapters` WHERE `unit_id` = %d\',
        $courseId = $id
    );
    $wpdb->query($query);
    if ( $wpdb->num_rows ) {
        $items = $wpdb->last_result;
        $string = \'<ul>\';
        foreach ($items as $item) {
            $string .= \'<a href="' . get_home_url() . '/\'.$item->name.\'"><li class=\\\'\\\'>\';
            $string .= $item->name;
            $string .= \'</li></a>\';
        }

        $string .= \'</ul>\';
        $string .= \'\';
        $string .= \'\';
        echo $string;
    }else{
        echo \'Noch keine Kapitel erstellt!\';
    };?>';
    $chapters = "" . $string;
    do_insert($postid,'php_everywhere_code',$chapters);
    echo '<script>window.location.replace("' . get_home_url() . '/' . $_POST['coursename'] . '")</script>';
}
if(isset($_GET['coursename'])){
?>
<form action="" method="post">
    <input type="text" placeholder="Name" name="unit_name" required>
    <input type="hidden" name="coursename" value="<?php echo $_GET['coursename'] ?>">
    <button type="submit" name="submit">Hinzufügen</button>
</form>
<?php
} else{
    echo "Bitte zuerst einen Kurs anclicken und von dort eine Unit hinzufügen!";
}
?>
