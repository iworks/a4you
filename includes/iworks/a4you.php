<?php
/*

Copyright 2018 Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_a4you' ) ) {
	return;
}

require_once( dirname( dirname( __FILE__ ) ) . '/iworks.php' );

class iworks_a4you extends iworks {

	private $capability;

	private $events = array();

	public function __construct() {
		parent::__construct();
		$this->version    = 'PLUGIN_VERSION';
		$this->capability = apply_filters( 'iworks_a4you_capability', 'manage_options' );
		/**
		 * hooks
		 */
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_head', array( $this, 'add_code' ), 0 );
		add_action( 'wp_footer', array( $this, 'maybe_print_events' ) );
		/**
		 * [GA4] Recommended events
		 */
		add_action( 'wp_login', array( $this, 'event_login' ) );

	}

	public function init() {
		iworks_a4you_options_init();
		global $iworks_a4you_options;
		$this->options = $iworks_a4you_options;
	}

	public function admin_init() {
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Plugin row data
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $this->dir . '/a4you.php' == $file ) {
			if ( ! is_multisite() && current_user_can( $this->capability ) ) {
				$links[] = '<a href="admin.php?page=' . $this->dir . '/admin/index.php">' . __( 'Settings' ) . '</a>';
			}
			/* start:free */
			$links[] = '<a href="http://iworks.pl/donate/a4you.php">' . __( 'Donate' ) . '</a>';
			/* end:free */
		}
		return $links;
	}

	public function add_code() {
		/**
		 * maybe not?
		 */
		if ( ! $this->should_it_be_used() ) {
			return;
		}
		$tag_id = $this->options->get_option( 'tag_id' );
		if ( empty( $tag_id ) ) {
			return;
		}
		echo PHP_EOL,'<!-- PLUGIN_NAME (PLUGIN_VERSION) -->',PHP_EOL;
		echo '<!-- Google tag (gtag.js) -->',PHP_EOL;
		printf(
			'<script async src="%s"></script>%s',
			esc_url(
				add_query_arg(
					'id',
					$tag_id,
					'https://www.googletagmanager.com/gtag/js'
				)
			),
			PHP_EOL
		);
		echo '<script>',PHP_EOL;
		echo 'window.dataLayer = window.dataLayer || [];',PHP_EOL;
		echo 'function gtag(){dataLayer.push(arguments);}',PHP_EOL;
		echo "gtag('js', new Date());",PHP_EOL;
		printf( "gtag('config', '%s');%s", $tag_id, PHP_EOL );
		echo '</script>',PHP_EOL;
	}

	public function should_it_be_used() {
		if ( is_user_logged_in() ) {
			return ! empty( $this->options->get_option( 'is_user_logged_in' ) );
		}
		return true;
	}

	private function add_event( $event, $params = array() ) {
		$this->events[ $event ] = $params;
	}

	public function maybe_print_events() {
		/**
		 * maybe not?
		 */
		if ( ! $this->should_it_be_used() ) {
			return;
		}
		/**
		 * is search?
		 */
		if ( is_search() ) {
			$this->add_event( 'search', array( 'search_term' => get_search_query() ) );
		}
		/**
		 * single content
		 */
		if ( is_singular() ) {
			$this->add_event(
				'select_content',
				array(
					'content_type' => get_post_type(),
					'content_id'   => get_the_ID(),
				)
			);
		}
		/**
		 * have I smth to show?
		 */
		if ( empty( $this->events ) ) {
			return;
		}
		echo '<script>';
		foreach ( $this->events as $event => $params ) {

			$p = array();
			foreach ( $params as $json_key => $json_value ) {
				$p[] = sprintf(
					'%s: "%s"',
					$json_key,
					esc_attr( $json_value )
				);
			}

			if ( empty( $p ) ) {
				printf(
					'gtag("event", "%s" );',
					$event
				);
			} else {
				printf(
					'gtag("event", "%s", {%s} );',
					$event,
					implode( ',', $p )
				);
			}
		}
		echo '</script>';
	}

	public function event_login() {
		$this->add_event( 'login', array( 'method' => 'WordPress' ) );
	}

}
