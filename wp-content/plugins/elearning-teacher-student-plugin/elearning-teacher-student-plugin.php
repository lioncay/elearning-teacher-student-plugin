<?php
/**
 * @package ElearningTeacherStudentPlugin
 */

/*
Plugin Name: elearning Teacher Student Plugin
Plugin URI: https://github.com/lioncay/elearning-teacher-student-plugin.git
Description: elearning plugin for creating courses and see attempt
Version: 1.0.0
Author: lioncay
Author URI: https://github.com/lioncay
License: GPLv2 or later
Text Domain: elearning-teacher-student-plugin
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2018 lioncay
*/

defined('ABSPATH') or die('Hey, try something else ;)');

if (!function_exists('add_action')){
    echo 'Hey, try something else ;)';
    exit;
}

if (file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
};

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLUGIN', plugin_basename(__FILE__));

use Inc\Base\Deactivate;
use Inc\Base\Activate;

function activate_elearning_plugin(){
    Activate::activate();
}

function deactivate_elearning_plugin(){
    Deactivate::deactivate();
}

register_activation_hook(__FILE__, 'activate_elearning_plugin');
register_deactivation_hook(__FILE__, 'deactivate_elearning_plugin');

if (class_exists('Inc\\Init')){
    Inc\Init::register_services();
}


