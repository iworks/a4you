<?php
/*

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
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_a4you_integration_woocommerce' ) ) {
	return;
}

include_once dirname( dirname( __FILE__ ) ) . '/class-iworks-a4you-integration.php';

class iworks_a4you_integration_woocommerce extends iworks_a4you_integration {

	public function __construct() {
		parent::__construct();
		add_filter( 'iworks_a4you_options', array( $this, 'filter_add_options' ) );
		add_filter( 'iworks_a4you_array_set', array( $this, 'filter_add_set' ) );
        add_filter( 'iworks_a4you_event_search_params', array( $this, 'filter_add_event_search_params' ) );
        /**
         * WooCommerce
         */
        add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'filter_woocommerce_loop_add_to_cart_args' ), 10, 2 );
	}

	public function filter_add_event_search_params( $params ) {
		$params[] = 'post_type';
		return $params;
	}

	public function filter_add_set( $gtag_set ) {
		/**
		 * Set Country
		 */
		if ( $this->options->get_option( 'wc_country' ) ) {
			$country = WC()->customer->get_shipping_country();
			if ( ! empty( $country ) ) {
				$gtag_set['country'] = $country;
			}
		}
		/**
		 * Set currency
		 */
		if ( $this->options->get_option( 'wc_currency' ) ) {
			$currency = get_woocommerce_currency();
			if ( ! empty( $currency ) ) {
				$gtag_set['currency'] = $currency;
			}
		}

		return $gtag_set;
	}

	public function filter_add_options( $options ) {
		$options['index']['options'][] = array(
			'type'  => 'heading',
			'label' => __( 'WooCommerce', 'a4you' ),
		);
		$options['index']['options'][] = array(
			'name'              => 'wc_country',
			'type'              => 'checkbox',
			'th'                => __( 'Set Country', 'a4you' ),
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button' ),
			'since'             => '1.0.0',
		);
		$options['index']['options'][] = array(
			'name'              => 'wc_currency',
			'type'              => 'checkbox',
			'th'                => __( 'Set Currency', 'a4you' ),
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button' ),
			'since'             => '1.0.0',
		);
		return $options;
	}

    public function filter_woocommerce_loop_add_to_cart_args( $args, $product ) {
        if ( $product->is_purchasable() && $product->is_in_stock() ) {
            $data = array(
                'currency' => get_woocommerce_currency_symbol(),
                'value' => $product->get_price(),
                'items' => array(
                    array(
                        'item_id' => $product->get_sku(),
                        'item_name' => $product->get_name(),
                        'price' => $product->get_price(),
                        'quantity' => 1,
                    )
                )
            );
            $args['attributes']['data-a4you'] = json_encode( $data );
        }
        return $args;
    }
}

