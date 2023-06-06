<?php

function iworks_a4wp_options() {
	$options = array();
	/**
	 * main settings
	 */
    $parent = 'settings.php';

	$options['index'] = array(
		'version'  => '0.0',
		'page_title' => __( 'Configuration', 'a4wp' ),
		'menu' => 'submenu',
		'parent' => $parent,
		'options'  => array(),
		'metaboxes' => array(),
		'pages' => array(),
	);
	return $options;
}

