<?php

function iworks_a4y_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Analitics 4 You', 'a4y' ),
		'menu'       => 'options',
		'options'    => array(
			array(
				'type'  => 'heading',
				'label' => __( 'General', 'a4y' ),
			),
			array(
				'name'              => 'tag_id',
				'type'              => 'text',
				'th'                => __( 'Google Tag ID', 'a4y' ),
				'placeholder'       => 'G-xxxxxxxxxx',
				'sanitize_callback' => 'esc_html',
				'since'             => '1.0.0',
			),
			array(
				'name'              => 'is_user_logged_in',
				'type'              => 'checkbox',
				'th'                => __( 'Logged Users', 'a4y' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since'             => '1.0.0',
				'description'       => esc_html__( 'Use analitics for logged users too.', 'a4y' ),
			),
		),
		'metaboxes'  => array(),
		'pages'      => array(),
	);
	return $options;
}

