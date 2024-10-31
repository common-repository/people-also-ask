<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used the admin area.
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 * @author     Caique Dourado <ckdourado@gmail.com>
 */
class People_Also_Ask {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      People_Also_Ask_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PEOPLE_ALSO_ASK_VERSION' ) ) {
			$this->version = PEOPLE_ALSO_ASK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'people-also-ask';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - People_Also_Ask_Loader. Orchestrates the hooks of the plugin.
	 * - People_Also_Ask_i18n. Defines internationalization functionality.
	 * - People_Also_Ask_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helpers.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-people_also_ask-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-people_also_ask-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-people_also_ask-admin.php';

		$this->loader = new People_Also_Ask_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the People_Also_Ask_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new People_Also_Ask_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new People_Also_Ask_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('plugins_loaded', $plugin_admin, 'check_for_plugin_update');

		$this->loader->add_action('admin_menu', $plugin_admin, 'options_admin');
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action('wp_ajax_item_create', $plugin_admin, 'save_keywords');
		$this->loader->add_action('wp_ajax_processa_item', $plugin_admin, 'processa_palavras');
		
		$this->loader->add_action('admin_init', $plugin_admin, 'executa_acoes');

		$this->loader->add_action('wp_footer', $plugin_admin, 'add_custom_styles_scripts');
		
		//$this->loader->add_action('admin_init', $plugin_admin, 'acoes_de_debug');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_shortcode = new People_Also_Ask_Shortcode();

		$this->loader->add_shortcode('people-also-ask-video', $plugin_shortcode, 'video_shortcode');
		$this->loader->add_shortcode('people-also-ask-barra', $plugin_shortcode, 'barra_shortcode');
		$this->loader->add_action('wp_footer', $plugin_shortcode, 'add_custom_public_styles_scripts');
	}

	  

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    People_Also_Ask_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
