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
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'filter_woocommerce_cart_item_remove_link' ), 10, 2 );
		/**
		 * own
		 */
		add_filter( 'a4you/function/get_config_javascript', array( $this, 'filter_get_config_javascript_add_product_data' ) );
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
			$data = apply_filters(
				'a4you/gtag/parameters/add_to_cart',
				array(
					'currency' => get_woocommerce_currency_symbol(),
					'value'    => $product->get_price(),
					'items'    => array(
						$this->get_product_data( $product ),
					),
				)
			);
			$args['attributes']['data-a4you_add_to_cart_loop'] = 'a4you';
			$args['attributes']['data-a4you_event']            = 'event';
			$args['attributes']['data-a4you_event_name']       = 'add_to_cart';
			$args['attributes']['data-a4you_event_parameters'] = json_encode( $data );
		}
		return apply_filters( 'a4you/woocommerce_loop_add_to_cart_args', $args );
	}

	private function get_item_data_for_gtag( $product ) {
		$data = array();
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		if ( ! is_product( $product ) ) {
			return $data;
		}
		return $this->get_product_data( $product );
	}

	private function get_product_data( $product, $quantity = 1 ) {
		$data      = array(
			'item_id'   => $product->get_sku(),
			'item_name' => $product->get_name(),
			'price'     => $product->get_price(),
			'quantity'  => $quantity,
		);
		$term_list = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
		if ( is_array( $term_list ) ) {
			$i = 1;
			foreach ( $term_list as $term_name ) {
				if ( $i < 6 ) {
					$key = 'item_category';
					if ( $i > 1 ) {
						$key .= $i;
					}
					$data[ $key ] = $term_name;
					$i++;
				}
			}
		}
		return $data;
	}

	public function filter_get_config_javascript_add_product_data( $config ) {
		if ( is_product() ) {
			$config['product'] = $this->get_item_data_for_gtag( get_the_ID() );
		}
		return $config;
	}

	public function filter_woocommerce_cart_item_remove_link( $link, $cart_item_key ) {
		/**
		 * cart item
		 */
		$cart_item = WC()->cart->get_cart()[ $cart_item_key ];
		$product   = $cart_item['data'];
		/**
		 * data
		 */
		$data = apply_filters(
			'a4you/gtag/parameters/remove_from_cart',
			array(
				'currency' => get_woocommerce_currency_symbol(),
				'value'    => $cart_item['line_total'],
				'items'    => array(
					$this->get_product_data( $product, $cart_item['quantity'] ),
				),
			)
		);
		/**
		 * attributes
		 */
		$attributes = array(
			'data-a4you_event_remove_from_cart' => 'a4you',
			'data-a4you_event'                  => 'event',
			'data-a4you_event_name'             => 'remove_from_cart',
			'data-a4you_event_parameters'       => json_encode( $data ),
		);
		foreach ( $attributes as $key => $value ) {
			$link = preg_replace(
				'/<a /',
				sprintf( '<a %s="%s"', $key, esc_attr( $value ) ),
				$link
			);
		}
		return $link;
	}
}

