<?php
/**
 * a4you — simpel analitycs for you
 *
 * @package           PLUGIN_NAME
 * @author            AUTHOR_NAME
 * @copyright         2023-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       a4you
 * Plugin URI:        PLUGIN_URI
 * Description:       PLUGIN_DESCRIPTION
 * Version:           PLUGIN_VERSION
 * Requires at least: PLUGIN_REQUIRES_WORDPRESS
 * Requires PHP:      PLUGIN_REQUIRES_PHP
 * Author:            AUTHOR_NAME
 * Author URI:        AUTHOR_URI
 * Text Domain:       mutatio
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt

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
define( 'IWORKS_A4WP_PREFIX', 'iworks_a4you_' );
$base   = dirname( __FILE__ );
$vendor = $base . '/includes';

/**
 * require: Iworksa4you Class
 */
if ( ! class_exists( 'iworks_a4you' ) ) {
	require_once $vendor . '/iworks/class-iworks-a4you.php';
}
/**
 * configuration
 */
require_once $base . '/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $vendor . '/iworks/options/options.php';
}

/**
 * i18n
 */
load_plugin_textdomain( 'a4you', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * load options
 */
global $iworks_a4you_options;
$iworks_a4you_options = new iworks_options();
$iworks_a4you_options->set_option_function_name( 'iworks_a4you_options' );
$iworks_a4you_options->set_option_prefix( IWORKS_A4WP_PREFIX );

function iworks_a4you_get_options() {
	global $iworks_a4you_options;
	return $iworks_a4you_options;
}

function iworks_a4you_options_init() {
	global $iworks_a4you_options;
	$iworks_a4you_options->options_init();
}

function iworks_a4you_activate() {
	$iworks_a4you_options = new iworks_options();
	$iworks_a4you_options->set_option_function_name( 'iworks_a4you_options' );
	$iworks_a4you_options->set_option_prefix( IWORKS_A4WP_PREFIX );
	$iworks_a4you_options->activate();
}

function iworks_a4you_deactivate() {
	global $iworks_a4you_options;
	$iworks_a4you_options->deactivate();
}

$iworks_a4you = new iworks_a4you();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_a4you_activate' );
register_deactivation_hook( __FILE__, 'iworks_a4you_deactivate' );

/**
 * Ask for vote
 *
 * @since 1.0.0
 */

include_once $vendor . '/iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'PLUGIN_NAME', 'a4you' ),
	'a4you'
);

