<?php

function iworks_a4wp_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Analitics 4 WP', 'a4wp' ),
		'menu'       => 'options',
		'options'    => array(
			array(
				'type'  => 'heading',
				'label' => __( 'General', 'a4wp' ),
			),
			array(
				'name'              => 'tag_id',
				'type'              => 'text',
				'th'                => __( 'Google Tag ID', 'a4wp' ),
				'placeholder'       => 'G-xxxxxxxxxx',
				'sanitize_callback' => 'esc_html',
			),
			array(
				'type'  => 'heading',
				'label' => __( 'Content', 'a4wp' ),
			),
		),
		'metaboxes'  => array(),
		'pages'      => array(),
	);
	return $options;
}

