<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.riksiot.com
 * @since      1.0.0
 *
 * @package    Riks_woo_connect
 * @subpackage Riks_woo_connect/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Riks_woo_connect
 * @subpackage Riks_woo_connect/includes
 * @author     Ocean Vu <hadolanh@yahoo.com>
 */
class Riks_woo_connect_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'riks_woo_connect',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
