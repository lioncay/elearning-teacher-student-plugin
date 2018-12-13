<?php

/**
 * @package ElearningTeacherStudentPlugin
 */

namespace Inc\Base;

class Enqueue{

    public function register(){
        add_action('add_enqueue_scripts', array($this, 'enqueue'));
    }

    function enqueue() {
        wp_enqueue_style('pluginstyle', PLUGIN_URL . '/assets/style.css');
        wp_enqueue_script('pluginscript', PLUGIN_URL . '/assets/script.js');
    }

}