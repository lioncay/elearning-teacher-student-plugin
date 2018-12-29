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
        'post_title' => ''.$_POST['chapter_name'].'',
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
        $postTitle=$_POST['unit_name']
    );
    $wpdb->query( $query );
    $parentID = $wpdb->last_result[0]->ID;

    //TODO add to units table (parent id and name)

    $query = $wpdb->prepare(
        'INSERT INTO `chapters` (`name`, `unit_id`, `summary`) VALUES (%s,%d,%s)',
        $one=$_POST['chapter_name'],
        $two=$parentID,
        $three=$_POST['summary']
    );
    $wpdb->query( $query );

    $pname = str_replace(" ","-",$_POST['chapter_name']);
    $pname = strtolower($pname);
    $query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . ' WHERE `post_title` = %s AND `post_name` = %s',
        $postTitle=$_POST['chapter_name'],
        $postTitlesec=$pname
    );
    $wpdb->query( $query );
    $postid = $wpdb->last_result[0]->ID;

    $string = "";
    $string .= '<?php echo \'<form><button type="button" onclick="document.location.href=\\\''.get_home_url().'/add-info?chapter_name=' . $_POST['chapter_name'] . '\\\'">Neuen Info Eintrag erstellen</button>';
    $string .= '<button type="button" onclick="document.location.href=\\\''.get_home_url().'/add-multiple-choice-question?chapter_name=' . $_POST['chapter_name'] . '\\\'">Neue Multiple Choice Frage erstellen</button>';
    $string .= '<button type="button" onclick="document.location.href=\\\''.get_home_url().'/add-open-question?chapter_name=' . $_POST['chapter_name'] . '\\\'">Neue offene Frage erstellen</button></form>\';';
    $string .= '
        global $wpdb;
        $query = $wpdb->prepare(
        \'SELECT ID from `wp_posts` WHERE `post_title` = %s AND `post_name` = %s\',
        $postTitle = \''.$_POST['chapter_name'].'\',
        $postTitlesec = \''.$pname.'\'
    );';
    $string .= '$wpdb->query($query);
    $id = $wpdb->last_result[0]->ID;

    $query = $wpdb->prepare(
        \'SELECT `title` FROM `chapterentries` WHERE `chapter_id` = %d ORDER BY `entry_order`\',
        $courseId = $id
    );
    $wpdb->query($query);
    if ( $wpdb->num_rows ) {
        $items = $wpdb->last_result;
        $string = \'<ul>\';
        foreach ($items as $item) {
            $string .= \'<li class=\\\'\\\'>\';
            $string .= $item->title;
            $string .= \'</li>\';
        }

        $string .= \'</ul>\';
        $string .= \'\';
        $string .= \'\';
        echo $string;
    }else{
        echo \'Noch keine Einträge erstellt!\';
    };?>';
    $chapters = "" . $string;
    do_insert($postid,'php_everywhere_code',$chapters);
    echo '<script>window.location.replace("' . get_home_url() . '/' . $_POST['unit_name'] . '")</script>';
}
if(isset($_GET['unit_name'])){
    ?>
    <form action="" method="post">
        <input type="text" placeholder="Name" name="chapter_name" required>
        <?php
        $content = 'Hier wird das Summary für das Kapitel hinzugefügt!';
        $editor_id = 'summary';
        wp_editor( $content, $editor_id );
        ?>
        <input type="hidden" name="unit_name" value="<?php echo $_GET['unit_name'] ?>">
        <button type="submit" name="submit">Hinzufügen</button>
    </form>
    <?php
} else{
    echo "Bitte zuerst eine Unit anclicken und von dort ein Kapitel hinzufügen!";
}
?>