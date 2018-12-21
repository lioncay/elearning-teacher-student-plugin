<?php

/**
 * @package ElearningTeacherStudentPlugin
 */

namespace Inc\Pages;

use function Composer\Autoload\includeFile;

class Admin
{

    public function register()
    {
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('admin_init', array($this, 'CreatePages'));
        add_action('admin_init', array($this, 'MyPluginPage'));
    }

    public function add_admin_pages()
    {
        add_menu_page(
            'Elearning Plugin',
            'Elearning',
            'manage_options',
            'elearning_plugin',
            array($this, 'admin_index'),
            'dashicons-welcome-learn-more',
            110
        );
    }

    public function admin_index()
    {
        require_once PLUGIN_PATH . 'templates/admin.php';

    }

    function CreatePages(){
        ob_start();
        include 'add_course.php';
        $string = ob_get_clean();
        $add_courses = "" . $string;
        $new_post = array(
            'post_title' => "All Courses",
            'post_status'=> 'publish',
            'post_type'  => 'page',
            'post_content' => $add_courses,
            'post_author' => '1',
            'post_category' => array(1,2),
            'page_template' => NULL
        );
        wp_insert_post($new_post,$wp_error=false);
    }

    function MyPluginPage()
    {
        global $wpdb;
        $query = $wpdb->prepare(
            'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s',
            $postTitle="All Courses"
        );
        $wpdb->query( $query );
        ob_start();
        include 'courses.php';
        $string = ob_get_clean();
        $courses = "" . $string;
        if ( $wpdb->num_rows ) {

        }else{
            $new_post = array(
                'post_title' => "All Courses",
                'post_status'=> 'publish',
                'post_type'  => 'page',
                'post_content' => $courses,
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
                'SELECT `count` FROM `wp_term_taxonomy` WHERE `term_taxonomy_id` = %d',
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
                'INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (%d,%d,%d)',
                $one=$id,
                $two=2,
                $three=0
            );
            $wpdb->query( $query );
            $query = $wpdb->prepare(
                'UPDATE `wp_term_taxonomy` SET `count` = `count` + 1 WHERE `term_taxonomy_id` = %d',
                $taxid = 2
            );
            $wpdb->query( $query );

            $query = $wpdb->prepare(
                'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s',
                $postTitle="All Courses"
            );
            $wpdb->query( $query );
            $postid = $wpdb->last_result[0]->ID;

            $this->do_insert($id,'_menu_item_type','post_type');
            $this->do_insert($id,'_menu_item_menu_item_parent','0');
            $this->do_insert($id,'_menu_item_object_id','' . $postid . '');
            $this->do_insert($id,'_menu_item_object','page');
            $this->do_insert($id,'_menu_item_target','');
            $this->do_insert($id,'_menu_item_classes','a:1:{i:0;s:0:"";}');
            $this->do_insert($id,'_menu_item_xfn','');
            $this->do_insert($id,'_menu_item_url','');
            $this->do_insert($id,'_menu_item_template','');
            $this->do_insert($id,'_menu_item_mega_template','0');
            $this->do_insert($id,'_menu_item_nolink','');
            $this->do_insert($id,'_menu_item_category_post','');
            $this->do_insert($id,'_menu_item_megamenu','');
            $this->do_insert($id,'_menu_item_megamenu_auto_width','');
            $this->do_insert($id,'_menu_item_megamenu_col','');
            $this->do_insert($id,'_menu_item_megamenu_heading','');
            $this->do_insert($id,'_menu_item_megamenu_widgetarea','0');
            $this->do_insert($id,'_menu_item_icon','');


        }
        $items = wp_get_nav_menu_items("main",array());
        foreach ($items as $item) {
            echo $item->title;
        }
    }

    function do_insert($postid, $second, $third){
        global $wpdb;
        $query = $wpdb->prepare(
            'INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (%d,%s,%s)',
            $one=$postid,
            $two=$second,
            $three=$third
        );
        $wpdb->query( $query );
    }

}