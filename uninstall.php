<?php

// Verifica se o WordPress está realmente desinstalando o plugin
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 */

/**
 * Perform Uninstall Actions.
 *
 * If uninstall not called from WordPress,
 * If no uninstall action,
 * If not this plugin,
 * If no caps,
 * then exit.
 *
 * @since 1.0.0
 */
function people_also_asked_uninstall()
{
	if (
		!defined('WP_UNINSTALL_PLUGIN')
		|| empty($_REQUEST)
		|| !isset($_REQUEST['plugin'])
		|| !isset($_REQUEST['action'])
		|| dirname(plugin_basename(__FILE__)) . '/people_also_ask.php' !== $_REQUEST['plugin']
		|| 'delete-plugin' !== $_REQUEST['action']
		|| !check_ajax_referer('updates', '_ajax_nonce')
		|| !current_user_can('activate_plugins')
	) {
		exit;
	}

	/**
	 * It is now safe to perform your uninstall actions here.
	 *
	 * @see https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/#method-2-uninstall-php
	 */
	global $wpdb;
	$plugin_name = 'people_also_askeds';
	$table_name = $wpdb->prefix . $plugin_name. 's';
	$wpdb->query("DROP TABLE IF EXISTS {$table_name},{$table_name}_related,{$table_name}_other_people_searched_related,{$table_name}_config,{$table_name}_config_countries;");
	
	delete_option($plugin_name . '_version');
}
people_also_asked_uninstall();
