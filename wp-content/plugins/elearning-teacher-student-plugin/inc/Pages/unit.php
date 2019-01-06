<?php
global $wpdb;

/*$pname = str_replace(" ","-",$_POST['unit_name']);
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
        \'SELECT ID from ' . $wpdb->posts . ' WHERE `post_title` = %s AND `post_name` = %s\',
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
do_insert($postid,'php_everywhere_code',$chapters);*/