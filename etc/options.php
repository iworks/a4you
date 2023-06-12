<?php

function iworks_a4you_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Analitics 4 You', 'a4you' ),
		'menu'       => 'options',
		'options'    => array(
			array(
				'type'  => 'heading',
				'label' => __( 'General', 'a4you' ),
			),
			array(
				'name'              => 'tag_id',
				'type'              => 'text',
				'th'                => __( 'Google Tag ID', 'a4you' ),
				'placeholder'       => 'G-xxxxxxxxxx',
				'sanitize_callback' => 'esc_html',
				'since'             => '1.0.0',
			),
			array(
				'type'  => 'heading',
				'label' => __( 'User', 'a4you' ),
			),
			array(
				'name'              => 'is_user_logged_in',
				'type'              => 'checkbox',
				'th'                => __( 'Logged Users', 'a4you' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since'             => '1.0.0',
				'description'       => esc_html__( 'Use analitics for logged users too.', 'a4you' ),
			),
			array(
				'name'              => 'user_role',
				'type'              => 'checkbox',
				'th'                => __( 'Roles', 'a4you' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since'             => '1.0.0',
				'description'       => esc_html__( 'Log logged user roles.', 'a4you' ),
			),
		),
		'metaboxes'  => array(),
		'pages'      => array(),
	);
	return $options;
}

