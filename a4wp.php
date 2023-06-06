<?php
/*
Plugin Name: a4wp
Text Domain: a4wp
Plugin URI: http://iworks.pl/a4wp/
Description:
Version: PLUGIN_VERSION
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright 2023-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * static options
 */
define( 'IWORKS_A4WP_VERSION', 'PLUGIN_VERSION' );
define( 'IWORKS_A4WP_PREFIX',  'iworks_a4wp_' );
$base = dirname( __FILE__ );
$vendor = $base.'/includes';

/**
 * require: Iworksa4wp Class
 */
if ( ! class_exists( 'iworks_a4wp' ) ) {
	require_once $vendor.'/iworks/a4wp.php';
}
/**
 * configuration
 */
require_once $base.'/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $vendor.'/iworks/options/options.php';
}

/**
 * i18n
 */
load_plugin_textdomain( 'a4wp', false, plugin_basename( dirname( __FILE__ ) ).'/languages' );

/**
 * load options
 */
$iworks_a4wp_options = new iworks_options();
$iworks_a4wp_options->set_option_function_name( 'iworks_a4wp_options' );
$iworks_a4wp_options->set_option_prefix( IWORKS_A4WP_PREFIX );

function iworks_a4wp_get_options() {
	global $iworks_a4wp_options;
	return $iworks_a4wp_options;
}

function iworks_a4wp_options_init() {
	global $iworks_a4wp_options;
	$iworks_a4wp_options->options_init();
}

function iworks_a4wp_activate() {
	$iworks_a4wp_options = new iworks_options();
	$iworks_a4wp_options->set_option_function_name( 'iworks_a4wp_options' );
	$iworks_a4wp_options->set_option_prefix( IWORKS_A4WP_PREFIX );
	$iworks_a4wp_options->activate();
}

function iworks_a4wp_deactivate() {
	global $iworks_a4wp_options;
	$iworks_a4wp_options->deactivate();
}

$iworks_a4wp = new iworks_a4wp();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__,   'iworks_a4wp_activate' );
register_deactivation_hook( __FILE__, 'iworks_a4wp_deactivate' );
