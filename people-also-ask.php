<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://peoplealsoaskplugin.com
 * @since             1.0.0
 * @package           People_Also_Ask
 *
 * @wordpress-plugin
 * Plugin Name:       People Also Ask
 * Plugin URI:        https://peoplealsoaskplugin.com
 * Description:       Wordpress Plugin that generates wordpress posts based on questions people ask on google
 * Version:           1.1.685
 * Author:            Caique Dourado
 * Author URI:        https://caiquedourado.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       people-also-ask
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PEOPLE_ALSO_ASK_VERSION', '1.1.685' );

/**
 * Define the Plugin basename
 */
define('PEOPLE_ALSO_ASK_BASE_NAME', 'people_also_asked');



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-people_also_ask-activator.php
 */
function people_also_ask_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-people_also_ask-activator.php';
	People_Also_Ask_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-people_also_ask-deactivator.php
 */
function people_also_ask_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-people_also_ask-deactivator.php';
	People_Also_Ask_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'people_also_ask_activate' );
register_deactivation_hook( __FILE__, 'people_also_ask_deactivate' );

/**
 * The core plugin class that is used to define internationalization and admin-specific hooks
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-people_also_ask.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-people_also_ask_shortcode.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function people_also_ask_run() {

	$plugin = new People_Also_Ask();
	$plugin->run();

}
people_also_ask_run();


