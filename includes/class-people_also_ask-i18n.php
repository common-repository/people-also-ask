<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 * @author     Caique Dourado <ckdourado@gmail.com>
 */
class People_Also_Ask_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'people-also-ask',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
