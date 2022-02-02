<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://maatio.fi
 * @since      1.0.0
 *
 * @package    Woo_Inventaario
 * @subpackage Woo_Inventaario/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Inventaario
 * @subpackage Woo_Inventaario/includes
 * @author     Nader Gam / Maatio Oy <dev@myyntimaatio.fi>
 */
class Woo_Inventaario_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-inventaario',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
