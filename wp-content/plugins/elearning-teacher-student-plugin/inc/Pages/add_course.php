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
        'post_title' => $_POST['coursename'],
        'post_status'=> 'publish',
        'post_type'  => 'page',
        'post_content' => '[php_everywhere]',
        'post_author' => '1',
        'post_category' => array(1,2),
        'page_template' => NULL
    );
    wp_insert_post($new_post,$wp_error=false);
    $query = $wpdb->prepare(
        'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME = %s',
        $table = $wpdb->posts
    );
    $wpdb->query( $query );
    $id=$wpdb->last_result[0]->AUTO_INCREMENT;
    $wpdb->query( $query );
    $query = $wpdb->prepare(
        'SELECT `count` FROM ' . $wpdb->term_taxonomy . ' WHERE `term_taxonomy_id` = %d',
        $taxid = 2
    );
    $wpdb->query( $query );
    $menuorder=$wpdb->last_result[0]->count+1;
    $menu = array(
        'post_status'=> 'publish',
        'post_type'  => 'nav_menu_item',
        'post_author' => '1',
        'guid' => 'http://localhost/elearning_plugin/?p='.$id,
        'menu_order' => $menuorder,
        'post_name' => $id,
        'comment_status' => "closed",
        'ping_status' => "closed"
    );
    wp_insert_post($menu,$wp_error=false);
    $query = $wpdb->prepare(
        'INSERT INTO ' . $wpdb->term_relationships . ' (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (%d,%d,%d)',
        $one=$id,
        $two=2,
        $three=0
    );
    $wpdb->query( $query );
    $query = $wpdb->prepare(
        'UPDATE ' . $wpdb->term_taxonomy . ' SET `count` = `count` + 1 WHERE `term_taxonomy_id` = %d',
        $taxid = 2
    );
    $wpdb->query( $query );

    $pname = str_replace(" ","-",$_POST['coursename']);
    $pname = strtolower($pname);
    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND `post_name` = %s',
        $postTitle=$_POST['coursename'],
        $postTitle=$pname
    );
    $wpdb->query( $query );
    $postid = $wpdb->last_result[0]->ID;

    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s',
        $postTitle='All Courses'
    );
    $wpdb->query( $query );
    $parentID = $wpdb->last_result[0]->ID;

    $query = $wpdb->prepare(
        'SELECT `post_id` FROM ' . $wpdb->postmeta . ' WHERE `meta_key`=\'_menu_item_object_id\' AND `meta_value`= %s',
        $objId=$parentID
    );
    $wpdb->query( $query );
    $parentID = $wpdb->last_result[0]->post_id;

    do_insert($id,'_menu_item_type','post_type');
    do_insert($id,'_menu_item_menu_item_parent',$parentID);
    do_insert($id,'_menu_item_object_id','' . $postid . '');
    do_insert($id,'_menu_item_object','page');
    do_insert($id,'_menu_item_target','');
    do_insert($id,'_menu_item_classes','a:1:{i:0;s:0:"";}');
    do_insert($id,'_menu_item_xfn','');
    do_insert($id,'_menu_item_url','');
    do_insert($id,'_menu_item_template','');
    do_insert($id,'_menu_item_mega_template','0');
    do_insert($id,'_menu_item_nolink','');
    do_insert($id,'_menu_item_category_post','');
    do_insert($id,'_menu_item_megamenu','');
    do_insert($id,'_menu_item_megamenu_auto_width','');
    do_insert($id,'_menu_item_megamenu_col','');
    do_insert($id,'_menu_item_megamenu_heading','');
    do_insert($id,'_menu_item_megamenu_widgetarea','0');
    do_insert($id,'_menu_item_icon','');

    $string = "";
    $string .= '<?php echo \'<form><button type="button" onclick="document.location.href=\\\''.get_home_url().'/add-unit?coursename=' . $_POST['coursename'] . '\\\'">Neue Unit erstellen</button></form>\';';
    $string .= '
        global $wpdb;
        $query = $wpdb->prepare(
        \'SELECT ID from `wp_posts` WHERE `post_title` = %s AND `post_name` = %s\',
        $postTitle = \''.$_POST['coursename'].'\',
        $postTitlesec = \''.$pname.'\'
    );';
    $string .= '$wpdb->query($query);
    $id = $wpdb->last_result[0]->ID;

    $query = $wpdb->prepare(
        \'SELECT `name` FROM `units` WHERE `course_id` = %d\',
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
        echo \'Noch keine Units erstellt!\';
    };?>';
    $units = "" . $string;
    do_insert($postid,'php_everywhere_code',$units);
    echo '<script>window.location.replace("' . get_home_url() . '/all-courses")</script>';
}
?>

<form action="" method="post">
    <input type="text" placeholder="Name" id="coursename" name="coursename" required>
    <select id="age" name="age" required>
        <option value="" disabled selected>Alter</option>
        <option value="0">unter 13 Jahre</option>
        <option value="1">13 und älter</option>
    </select>
    <button type="submit" name="submit">Hinzufügen</button>
</form>
