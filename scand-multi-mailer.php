<?php
/**
 * @package Scand_Multi_Mailer
 * @version 1.0.0
 */

/*
Plugin Name: Multi Mailer
Plugin URI: https://wordpress.org/plugins/scand-multi-mailer/
Description: This plugin allows you to intercept an email that is sent by wp_mail() function and duplicates it as many times as you need.
Text Domain: scand-multi-mailer
Domain Path: /languages
Version: 1.0.0
Author: SCAND Ltd.
Author email: wordpress@scand.com
Author URI: http://scand.com/
License: GPLv2 or later
*/

/* Copyright SCAND Ltd. http://www.scand.com
Plugin is free software: you can redistribute it and/or modify  it under the terms of the GNU General Public License
as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
Plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this plugin.
If not, see http://www.gnu.org/licenses/gpl.html .
*/

// Prohibit direct script loading
defined('ABSPATH') || die('No direct script access allowed!');

// Plugin constant
define('SCAND_MULTI_MAILER_FOLDER_NAME', 'scand-multi-mailer');
define('SCAND_MULTI_MAILER_TEXTDOMAIN', 'scand-multi-mailer');
define('SCAND_MULTI_MAILER_FILE', __FILE__);
define( 'SCAND_MULTI_MAILER_DIR', plugin_dir_path( __FILE__ ) );

require_once(SCAND_MULTI_MAILER_DIR . "providers/class-multi-mailer.php");

register_activation_hook( SCAND_MULTI_MAILER_FILE, array( 'Scand_Multi_Mailer', 'plugin_activation' ) );
register_uninstall_hook( SCAND_MULTI_MAILER_FILE, array( 'Scand_Multi_Mailer', 'plugin_uninstall' ) );

if(is_admin()) {
    add_action('init', array('Scand_Multi_Mailer', 'init_for_admin'));
} else {
    add_action('init', array('Scand_Multi_Mailer', 'init_for_user'));
}
