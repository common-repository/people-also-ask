<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/admin
 * @author     Caique Dourado <ckdourado@gmail.com>
 */
class People_Also_Ask_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles($hook_suffix) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in People_Also_Ask_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The People_Also_Ask_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/people_also_ask-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts($hook_suffix) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in People_Also_Ask_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The People_Also_Ask_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if (people_also_endsWith('/admin/partials/people_also_ask-admin-display.php', $hook_suffix)) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/people_also_ask-admin.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce($this->plugin_name . '_create_nonce');
		wp_localize_script(
			$this->plugin_name,
			'people_also_ask_admin_ajax',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => $title_nonce,
			]
		);

	}

	public function options_admin()
	{
		add_menu_page(
			esc_html__('People Also Ask', 'people-also-ask'),
			esc_html__('People Also Ask', 'people-also-ask'),
			'manage_options',
			plugin_dir_path(__FILE__) . 'partials/people_also_ask-admin-display.php',
			null,
			'dashicons-tickets-alt',
			99
		);

		add_submenu_page(
			plugin_dir_path(__FILE__) . 'partials/people_also_ask-admin-display.php',
			esc_html__('Settings', 'people-also-ask'),
			esc_html__('Settings', 'people-also-ask'),
			'manage_options',
			plugin_dir_path(__FILE__) . 'partials/people_also_ask-config.php',
			null
		);
	}


	public function add_custom_styles_scripts() {

		

	}

	public function check_for_plugin_update() {

		//Versão atual do Plugin
		$current_version = PEOPLE_ALSO_ASK_VERSION;

		// Obter a versão salva no banco de dados
		$saved_version = get_option( 'people_also_asked_version' );

		//  echo 'current_version: ' . $current_version  . '<br />';
		//  echo 'saved_version: ' . $saved_version  . '<br />';

		//  if ( version_compare( $saved_version, '1.1.675', '<' ) ) {
		// 	echo 'Sim, menor versão<br />';
		//  }

		/* ----------------- Se a versão foi alterada ----------------- */
		if ( version_compare( $saved_version, $current_version, '<' ) ) {
			

			// O plugin foi atualizado manualmente
			update_option( 'people_also_asked_version', $current_version );

			global $wpdb;
			$table_name = $wpdb->prefix . 'people_also_askeds';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "";


			

			
			/* ----------------- Se a versão for anterior a '1.0.28' ----------------- */

			//Se a versão for anterior a '1.0.31', cadastra países para busca de Also ask do google em outros países
			if ( version_compare( $saved_version, '1.0.69', '<' ) ) {

				$sql .= " 
				
				INSERT INTO ".$table_name."_config (chave, valor) 
					SELECT * FROM (SELECT 'google-country','') AS tmp
					WHERE NOT EXISTS (
						SELECT chave FROM ".$table_name."_config WHERE chave = 'google-country'
					) LIMIT 1;
				
				
				";

			}


			/* ----------------- Se a versão for anterior a '1.0.73' ----------------- */

			//Cadastra config de serial
			if ( version_compare( $saved_version, '1.0.73', '<' ) ) {

				$sql .= " 
				
				INSERT INTO ".$table_name."_config (chave, valor) 
					SELECT * FROM (SELECT 'serial','') AS tmp
					WHERE NOT EXISTS (
						SELECT chave FROM ".$table_name."_config WHERE chave = 'serial'
					) LIMIT 1;
				
				";

			}

			/* ----------------- Se a versão for anterior a '1.1.3' ----------------- */

			//Cadastra config de serial
			if ( version_compare( $saved_version, '1.1.3', '<' ) ) {

				if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name . '_related')) !== $table_name . '_related') {
    
					$sql .= $wpdb->prepare(
						"CREATE TABLE {$table_name}_related (
							`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
							`asked_id` bigint(20) NOT NULL,
							`parent_id` bigint(20) DEFAULT NULL,
							`palavra` varchar(300) DEFAULT NULL,
							`buscou_serp_api` bit(1) DEFAULT b'0',
							`nivel` INT NULL,
							PRIMARY KEY (`ID`),
							KEY `asked_id` (`asked_id`)
						) $charset_collate;"
					);
				}

				$wpdb->query( $wpdb->prepare( "DROP TABLE %s_config_countries;", $table_name ) );
				
				//Insere dados			
				if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name . '_config_countries')) !== $table_name . '_config_countries') {
					
					$sql .= "CREATE TABLE {$table_name}_config_countries (
						`ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						`domain` varchar(50) DEFAULT NULL,
						`location_code` varchar(50) DEFAULT NULL,
						`language_country` varchar(10) DEFAULT NULL,
						`country_iso_code` varchar(10) DEFAULT NULL,
						`language_code` varchar(10) DEFAULT NULL,
						`location_name` varchar(150) DEFAULT NULL,
						PRIMARY KEY (`ID`)
					) $charset_collate;

					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ad','2020','ca','ad','ca','Andorra');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ae','2784','ar','ae','ar','United Arab Emirates');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.af','2004','ps','af','ps','Afghanistan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ag','2028','en','ag','en','Antigua and Barbuda');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ai','2660','en','ai','en','Anguilla');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.al','2008','sq','al','sq','Albania');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.am','2051','hy','am','hy','Armenia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ao','2024','pt','ao','pt','Angola');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ar','2032','es','ar','es','Argentina');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.as','2016','en','as','en','American Samoa');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.at','2040','de','at','de','Austria');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.au','2036','en','au','en','Australia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.az','2031','az','az','az','Azerbaijan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ba','2070','bs','ba','bs','Bosnia and Herzegovina');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bd','2050','bn','bd','bn','Bangladesh');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.be','2056','nl','be','nl','Belgium');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bf','2854','fr','bf','fr','Burkina Faso');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bg','2100','bg','bg','bg','Bulgaria');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bh','2048','ar','bh','ar','Bahrain');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bi','2108','fr','bi','fr','Burundi');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bj','2204','fr','bj','fr','Benin');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bn','2096','ms','bn','ms','Brunei');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bo','2068','es','bo','es','Bolivia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.br','2076','pt-BR','br','pt','Brazil');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bs','2044','en','bs','en','The Bahamas');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bt','2064','bt','bt','bt','Bhutan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.bw','2072','en','bw','en','Botswana');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bz','2084','en','bz','en','Belize');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ca','2124','en','ca','en','Canada');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cf','2140','fr','cf','fr','Central African Republic');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cd','2178','fr','cg','fr','Republic of the Congo');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cg','2178','fr','cg','fr','Republic of the Congo');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ch','2756','de','ch','de','Switzerland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ci','2384','fr','ci','fr','Cote d'Ivoire');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ck','2184','en','ck','en','Cook Islands');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cl','2152','es','cl','es','Chile');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cm','2120','en','cm','en','Cameroon');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.co','2170','es','co','es','Colombia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.cr','2188','es','cr','es','Costa Rica');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cv','2132','pt','cv','pt','Cabo Verde');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.cy','2196','el','cy','el','Cyprus');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cz','2203','cs','cz','cs','Czechia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.de','2276','de','de','de','Germany');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dj','2262','ar','dj','ar','Djibouti');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dk','2208','da','dk','da','Denmark');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dm','2212','en','dm','en','Dominica');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.do','2214','es','do','es','Dominican Republic');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dz','2012','ar','dz','ar','Algeria');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ec','2218','es','ec','es','Ecuador');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ee','2233','et','ee','et','Estonia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.eg','2818','ar','eg','ar','Egypt');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.es','2724','es','es','es','Spain');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.et','2231','am','et','am','Ethiopia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fi','2246','fi','fi','fi','Finland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.fj','2242','en','fj','en','Fiji');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fm','2583','en','fm','en','Micronesia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fr','2250','fr','fr','fr','France');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ga','2266','fr','ga','fr','Gabon');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ge','2268','ka','ge','ka','Georgia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gh','2288','en','gh','en','Ghana');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gi','2292','en','gi','en','Gibraltar');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gl','2304','kl','gl','kl','Greenland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gm','2270','en','gm','en','The Gambia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gp','2312','fr','gp','fr','Guadeloupe');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gr','2300','el','gr','el','Greece');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gt','2320','es','gt','es','Guatemala');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gy','2328','en','gy','en','Guyana');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.hk','2344','en','hk','en','Hong Kong');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hn','2340','es','hn','es','Honduras');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hr','2191','hr','hr','hr','Croatia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ht','2332','fr','ht','fr','Haiti');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hu','2348','hu','hu','hu','Hungary');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.id','2360','id','id','id','Indonesia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ie','2372','en','ie','en','Ireland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.il','2376','iw','il','iw','Israel');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.in','2356','hi','in','hi','India');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.iq','2368','ar','iq','ar','Iraq');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.is','2352','is','is','is','Iceland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.it','2380','it','it','it','Italy');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.jm','2388','en','jm','en','Jamaica');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.jo','2400','ar','jo','ar','Jordan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.jp','2392','ja','jp','ja','Japan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ke','2404','en','ke','en','Kenya');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.kg','2417','ky','kg','ky','Kyrgyzstan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.kh','2116','km','kh','km','Cambodia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ki','2296','en','ki','en','Kiribati');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.kr','2410','ko','kr','ko','South Korea');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.kw','2414','ar','kw','ar','Kuwait');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.kz','2398','kk','kz','kk','Kazakhstan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.la','2418','lo','la','lo','Laos');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.lb','2422','ar','lb','ar','Lebanon');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.li','2438','de','li','de','Liechtenstein');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lk','2144','si','lk','si','Sri Lanka');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ls','2426','en','ls','en','Lesotho');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lt','2440','lt','lt','lt','Lithuania');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lu','2442','fr','lu','fr','Luxembourg');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lv','2428','lv','lv','lv','Latvia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ly','2434','ar','ly','ar','Libya');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ma','2504','ar','ma','ar','Morocco');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.md','2498','ro','md','ro','Moldova');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mg','2450','fr','mg','fr','Madagascar');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mk','2807','mk','mk','mk','North Macedonia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ml','2466','fr','ml','fr','Mali');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mm','2104','my','mm','my','Myanmar (Burma)');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mn','2496','mn','mn','mn','Mongolia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ms','2500','en','ms','en','Montserrat');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mt','2470','mt','mt','mt','Malta');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mu','2480','en','mu','en','Mauritius');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mv','2462','mv','mv','mv','Maldives');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mw','2454','en','mw','en','Malawi');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mx','2484','es','mx','es','Mexico');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.my','2458','ms','my','ms','Malaysia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.mz','2508','pt','mz','pt','Mozambique');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.na','2516','en','na','en','Namibia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ne','2562','fr','ne','fr','Niger');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ng','2566','en','ng','en','Nigeria');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ni','2558','es','ni','es','Nicaragua');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nl','2528','nl','nl','nl','Netherlands');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.no','2578','no','no','no','Norway');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.np','2524','ne','np','ne','Nepal');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nr','2520','en','nr','en','Nauru');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nu','2570','en','nu','en','Niue');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.nz','2554','en','nz','en','New Zealand');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.om','2512','ar','om','ar','Oman');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pa','2591','es','pa','es','Panama');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pe','2604','es','pe','es','Peru');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pg','2598','en','pg','en','Papua New Guinea');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ph','2608','tl','ph','tl','Philippines');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pk','2586','en','pk','en','Pakistan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.pl','2616','pl','pl','pl','Poland');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pr','2630','es','pr','es','Puerto Rico');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ps','2275','ar','ps','ar','Palestine');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.pt','2620','pt-PT','pt','pt','Portugal');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.py','2600','es','py','es','Paraguay');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.qa','2634','ar','qa','ar','Qatar');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ro','2642','ro','ro','ro','Romania');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.rs','2688','sr','rs','sr','Serbia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.rw','2646','rw','rw','rw','Rwanda');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sa','2682','ar','sa','ar','Saudi Arabia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sb','2090','en','sb','en','Solomon Islands');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sc','2690','fr','sc','fr','Seychelles');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.se','2752','sv','se','sv','Sweden');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sg','2702','en','sg','en','Singapore');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sh','2654','en','sh','en','Saint Helena, Ascension and Tristan da Cunha');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.si','2705','sl','si','sl','Slovenia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sk','2703','sk','sk','sk','Slovakia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sl','2694','en','sl','en','Sierra Leone');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sm','2674','it','sm','it','San Marino');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sn','2686','fr','sn','fr','Senegal');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.so','2706','so','so','so','Somalia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sr','2740','nl','sr','nl','Suriname');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sv','2222','es','sv','es','El Salvador');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.td','2148','ar','td','ar','Chad');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tg','2768','fr','tg','fr','Togo');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.th','2764','th','th','th','Thailand');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tj','2762','tg','tj','tg','Tajikistan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tk','2772','en','tk','en','Tokelau');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tl','2626','pt','tl','pt','Timor-Leste');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tm','2795','tk','tm','tk','Turkmenistan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tn','2788','ar','tn','ar','Tunisia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.to','2776','en','to','en','Tonga');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tr','2792','tr','tr','tr','Turkiye');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tr','2792','tr','tr','tr','Turkiye');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tt','2780','en','tt','en','Trinidad and Tobago');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tw','2158','zh-tw-TW','tw','zh-tw','Taiwan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.tz','2834','sw','tz','sw','Tanzania');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ua','2804','uk','ua','uk','Ukraine');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ug','2800','en','ug','en','Uganda');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com','2840','en','us','en','United States');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.uy','2858','es','uy','es','Uruguay');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.uz','2860','uz','uz','uz','Uzbekistan');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.vc','2670','en','vc','en','Saint Vincent and the Grenadines');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ve','2862','es','ve','es','Venezuela');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.vi','2850','en','vi','en','U.S. Virgin Islands');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.vn','2704','vi','vn','vi','Vietnam');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.vu','2548','vu','vu','vu','Vanuatu');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ws','2882','ws','ws','ws','Samoa');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.za','2710','af','za','af','South Africa');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.zm','2894','en','zm','en','Zambia');
					INSERT INTO ".$table_name."_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.zw','2716','en','zw','en','Zimbabwe');
								
					";
					
				}
				

			}

			/* ----------------- Se a versão for anterior a '1.1.9' ----------------- */

			
			if ( version_compare( $saved_version, '1.1.308', '<' ) ) {

				$sql .= " 
				
					INSERT INTO ".$table_name."_config (chave, valor) 
					SELECT * FROM (SELECT 'max-concurrent-requests','1') AS tmp
					WHERE NOT EXISTS (
						SELECT chave FROM ".$table_name."_config WHERE chave = 'max-concurrent-requests'
					) LIMIT 1;

					INSERT INTO ".$table_name."_config (chave, valor) 
					SELECT * FROM (SELECT 'display-view-more','1') AS tmp
					WHERE NOT EXISTS (
						SELECT chave FROM ".$table_name."_config WHERE chave = 'display-view-more'
					) LIMIT 1;

					INSERT INTO ".$table_name."_config (chave, valor) 
					SELECT * FROM (SELECT 'display-request-removal','1') AS tmp
					WHERE NOT EXISTS (
						SELECT chave FROM ".$table_name."_config WHERE chave = 'display-request-removal'
					) LIMIT 1;
				
				";

			}


			/* ----------------- Se a versão for anterior a '1.1.47' ----------------- */

			
			if ( version_compare( $saved_version, '1.1.47', '<' ) ) {

				if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name . '_other_people_searched')) !== $table_name . '_other_people_searched') {
    
					$sql .= $wpdb->prepare(
						"CREATE TABLE {$table_name}_other_people_searched (
							`ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
							`asked_id` BIGINT NOT NULL,
							`parent_id` BIGINT  NULL,
							`keyword` VARCHAR(200) NULL,
							PRIMARY KEY (`ID`),
							INDEX `asked_id` (`asked_id`)
						) $charset_collate;"
					);
				}

			}




			/* ----------------- Executa atualizações ----------------- */

			if (!empty($sql)) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}



			

			
				
		}	
		/* ----------------- Se a versão foi alterada ----------------- */


	}	
	
	// public function acoes_de_debug() {

	// 	if (isset($_GET['debugar']) != '') {
			
	// 		$lines = file('/home2/caiqu152//estilizador.com.br/blog/wp-content/plugins/people-also-ask-plugin/tradução.txt');
	// 		$count = 0;
	// 		foreach($lines as $line) {
	// 			$count += 1;
				
	// 			$partes = explode("|", $line);

	// 			if (count($partes) > 1) {

	// 				$texto =  rtrim(ltrim($partes[0]));
	// 				$texto_traduzido =  __($texto, 'people-also-ask');

	// 				if ($texto != $texto_traduzido)
	// 					echo  '<span style="color: blue;">' .str_pad($count, 2, 0, STR_PAD_LEFT) .'	:' . $texto .  '</span> / <span style="color: green;">' . $texto_traduzido .'</span><br />';
	// 				else
	// 				echo '<span style="color: red;">' .  str_pad($count, 2, 0, STR_PAD_LEFT) .'	:' . $texto .'</span><br />';
	// 			}
				
	// 		}

	// 	}
	// 	die();
	// }

	public function executa_acoes() {
		

		if (isset($_POST['submit']) && $_POST['submit'] === __('Save', 'people-also-ask')) {

			if (!isset($_POST['meu_form_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['meu_form_nonce'])), 'salvar_form')) {
				die('Falha na verificação de segurança!');
			}
			
			$config_chave_serp_api = (isset($_POST['chave-serpapi'])) ? sanitize_text_field( wp_unslash( $_POST['chave-serpapi'] ) ) : '';
			$config_usuario_data_for_seo = (isset($_POST['usuario-dataforseo'])) ? sanitize_text_field( wp_unslash( $_POST['usuario-dataforseo'] ) ) : '';
			$config_senha_data_for_seo = (isset($_POST['senha-dataforseo'])) ? sanitize_text_field( wp_unslash( $_POST['senha-dataforseo'] ) ) : '';
			$config_status_posts = (isset($_POST['status-posts'])) ? sanitize_text_field( wp_unslash( $_POST['status-posts'] ) ) : '';
			$config_categoria_post = (isset($_POST['categoria-post'])) ? sanitize_text_field( wp_unslash( $_POST['categoria-post'] ) ) : '';
			$config_hierarquia_post = (isset($_POST['hierarquia-post'])) ? sanitize_text_field( wp_unslash( $_POST['hierarquia-post'] ) ) : '';
			$config_incluir_video_post = (isset($_POST['incluir-video-post'])) ? sanitize_text_field( wp_unslash( $_POST['incluir-video-post'] ) ) : '';
			$config_api_scrapper = (isset($_POST['api-scrapper'])) ? sanitize_text_field( wp_unslash( $_POST['api-scrapper'] ) ) : '';
			$config_niveis_busca = (isset($_POST['niveis-busca'])) ? sanitize_text_field( wp_unslash( $_POST['niveis-busca'] ) ) : '';
			$config_google_country = (isset($_POST['google-country'])) ? sanitize_text_field( wp_unslash( $_POST['google-country'] ) ) : '';
			$config_serial = (isset($_POST['serial'])) ? sanitize_text_field( wp_unslash( $_POST['serial'] ) ) : '';
			$max_concurrent_requests = (isset($_POST['max-concurrent-requests'])) ? sanitize_text_field( wp_unslash( $_POST['max-concurrent-requests'] ) ) : '';
			$display_view_more = (isset($_POST['display-view-more'])) ? sanitize_text_field( wp_unslash( $_POST['display-view-more'] ) ) : '';
			$display_request_removal = (isset($_POST['display-request-removal'])) ? sanitize_text_field( wp_unslash( $_POST['display-request-removal'] ) ) : '';

			

			$this->salva_configuracoes($config_chave_serp_api, $config_status_posts, $config_categoria_post, $config_hierarquia_post, $config_incluir_video_post,$config_usuario_data_for_seo,$config_senha_data_for_seo,$config_api_scrapper,$config_niveis_busca,$config_google_country,$config_serial, $max_concurrent_requests, $display_view_more, $display_request_removal);
		}

	}

	
	protected function salva_configuracoes($config_chave_serp_api, $config_status_posts, $config_categoria_post, $config_hierarquia_post, $config_incluir_video_post,$config_usuario_data_for_seo,$config_senha_data_for_seo,$config_api_scrapper,$config_niveis_busca,$config_google_country,$config_serial, $max_concurrent_requests, $display_view_more, $display_request_removal) {

		global $wpdb;


		$wpdb->query($wpdb->prepare("
			
			INSERT INTO {$wpdb->prefix}people_also_askeds_config (chave, valor)
			SELECT tmp.chave, tmp.valor
			FROM (
				SELECT 'status-posts' AS chave, 'automatico-publicado' AS valor
				UNION ALL
				SELECT 'hierarquia-post', 'h1-h2'
				UNION ALL
				SELECT 'incluir-video-post', 'sim'
				UNION ALL
				SELECT 'chave-serpapi', ''
				UNION ALL
				SELECT 'usuario-dataforseo', ''
				UNION ALL
				SELECT 'senha-dataforseo', ''
				UNION ALL
				SELECT 'categoria-post', '0'
				UNION ALL
				SELECT 'api-scrapper', ''
				UNION ALL
				SELECT 'niveis-busca', '2'
				UNION ALL
				SELECT 'google-country', ''
				UNION ALL
				SELECT 'serial', ''
				UNION ALL
				SELECT 'max-concurrent-requests', '1'
				UNION ALL
				SELECT 'display-view-more', '1'
				UNION ALL
				SELECT 'display-request-removal', '1'
			) AS tmp
			WHERE NOT EXISTS (
				SELECT 1 FROM {$wpdb->prefix}people_also_askeds_config WHERE {$wpdb->prefix}people_also_askeds_config.chave = tmp.chave
			);

		"));
	

		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'chave-serpapi'", $config_chave_serp_api));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'usuario-dataforseo'", $config_usuario_data_for_seo));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'senha-dataforseo'", $config_senha_data_for_seo));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'status-posts'", $config_status_posts));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'categoria-post'", $config_categoria_post));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'hierarquia-post'", $config_hierarquia_post));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'incluir-video-post'", $config_incluir_video_post));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'api-scrapper'", $config_api_scrapper));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'niveis-busca'", $config_niveis_busca));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'google-country'", $config_google_country));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'serial'", $config_serial));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'max-concurrent-requests'", $max_concurrent_requests));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'display-view-more'", $display_view_more));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_config SET valor = %s WHERE chave = 'display-request-removal'", $display_request_removal));


		update_option( 'people_also_display-view-more', $display_view_more );
		update_option( 'people_also_display-request-removal', $display_request_removal );
		
	}

	protected function cria_post($keyword, $post_content, $keyword_id, $config_status_posts, $config_categoria_post, $config_hierarquia_post, $config_incluir_video_post) {

		$wp_post_id_gravado = 0;

		global $wpdb;

		$post_status = "draft";

		$post_title = ""; //Título (H1)

		

		//Pega H1 do Post
		$pega_h1 = $wpdb->get_results($wpdb->prepare("SELECT palavra FROM {$wpdb->prefix}people_also_askeds WHERE id = %d", $keyword_id));

		if (count($pega_h1) > 0) {
			$post_title = $pega_h1[0]->palavra;
		} 


		 /* ---------------------------- Cadastra Post ----------------------------*/
        
		 $post_data = array(
			'post_author'           => get_current_user_id(),
			'post_date'             => current_time('mysql'),
			'post_date_gmt'         => current_time('mysql', 1),
			'post_content'          => wp_kses_post($post_content),
			'post_title'            => sanitize_text_field($post_title),
			'post_status'           => $post_status,
			'comment_status'        => 'open',
			'ping_status'           => 'closed',
			'post_name'             => sanitize_title($post_title),
			'post_modified'         => current_time('mysql'),
			'post_modified_gmt'     => current_time('mysql', 1),
			'guid'                  => wp_generate_uuid4(),
			'post_type'             => 'post'
		);
		
		// Insere o post no banco de dados
		$wp_post_id_gravado = wp_insert_post($post_data);
				 

		 //Seta categoria do Post
		 if ($config_categoria_post > 0)
			 wp_set_post_categories( $wp_post_id_gravado, array( $config_categoria_post ) ); 

		 /* ---------------------------- Marca como Feito ----------------------------*/                    

		 $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET wp_post_id = %d WHERE id = %d", $wp_post_id_gravado, $keyword_id));
		 


		return $wp_post_id_gravado;
	}



	protected function busca_serp_api($keyword, $config_chave_serp_api, $dados_google_country_domain, $dados_google_country_language_country, $dados_google_country_country_iso_code) 
	{
		$url = add_query_arg(
			array(
				'engine' => 'google',
				'gl' => 'br',
				'q' => $keyword,
				'google_domain' => $dados_google_country_domain,
				'gl' => $dados_google_country_country_iso_code,
				'hl' => strtolower($dados_google_country_language_country),
				'api_key' => $config_chave_serp_api
			),
			'https://serpapi.com/search.json'
		);

		$args = array(
			'timeout'     => 600,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => true,
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
		);

		$response = wp_remote_get($url, $args);

		if (is_wp_error($response)) {
			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Error in API request', 'people-also-ask')
			], 400);
			exit();
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if ($data === null) {
			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Error: Check if your API key is correct https://serpapi.com/manage-api-key', 'people-also-ask')
			], 400);
			exit();
		}

		return $body;
	}

	function busca_data_for_seo($keyword, $config_usuario_data_for_seo, $config_senha_data_for_seo, $dados_google_country_location_code, $dados_google_country_language_country) {
		// Combine login and password with a colon
		$credentials = $config_usuario_data_for_seo . ':' . $config_senha_data_for_seo;
		
		// Encode the credentials using base64
		$base64Credentials = base64_encode($credentials);
	
		// Prepara o array de dados
		$data = [
			[
				"keyword" => $keyword,
				"location_code" => $dados_google_country_location_code,
				"language_code" => $dados_google_country_language_country,
				"device" => "desktop",
				"os" => "windows",
				"depth" => 100,
				"people_also_ask_click_depth" => 1
			]
		];
	
		// Codifica o array para JSON
		$jsonData = wp_json_encode($data);
	
		// Configuração da requisição
		$args = array(
			'method'      => 'POST',
			'timeout'     => 600,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => array(
				'Authorization' => 'Basic ' . $base64Credentials,
				'Content-Type'  => 'application/json'
			),
			'body'        => $jsonData,
		);
	
		// Faz a requisição usando a API HTTP do WordPress
		$response = wp_remote_post('https://api.dataforseo.com/v3/serp/google/organic/live/advanced', $args);
	
		// Verifica se houve erro na requisição
		if (is_wp_error($response)) {
			wp_send_json([
				'status'  => 'error',
				'message' => esc_html__('Error in API request', 'people-also-ask')
			], 400);
			exit();
		}
	
		// Obtém o corpo da resposta
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);
	
		// Verifica se a decodificação foi bem-sucedida
		if ($data === null) {
			wp_send_json([
				'status'  => 'error',
				'message' => esc_html__('Error decoding JSON (1)', 'people-also-ask')
			], 400);
			exit();
		}
	
		if (isset($data['tasks'])) {
			foreach ($data['tasks'] as $task) {
				if (isset($task['status_code'])) {
					if ($task['status_code'] == '40200' || $task['status_code'] == '40210') {
						wp_send_json([
							'status'  => 'error',
							'message' => esc_html__('Important: Insufficient balance! Buy credits from the DataForSEO API', 'people-also-ask')
						], 400);
						exit();
					}
	
					if ($task['status_code'] == '40201') {
						wp_send_json([
							'status'  => 'error',
							'message' => esc_html__('Important: Your DataForSEO API has been blocked. Contact DataForSEO', 'people-also-ask')
						], 400);
						exit();
					}
	
					if ($task['status_code'] != '20000' && $task['status_code'] != '20100') {
						wp_send_json([
							'status'  => 'error',
							'message' => esc_html__('Error: code', 'people-also-ask') . ' ' . $task['status_code']
						], 400);
						exit();
					}
				}
			}
		} else {
			if (isset($data['status_message'])) {
				wp_send_json([
					'status'  => 'error',
					'message' => 'Important: ' . $data['status_message']
				], 400);
				exit();
			}
		}
	
		return $body;
	}


	protected function pega_dados_api_also_ask($palavra, $config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_google_country) {

		global $wpdb;

		

		/* -------------------------------------- Pega dados do país do Google selecionado -------------------------------------- */

			$dados_google_country_location_code = '';
			$dados_google_country_language_country = '';
			$dados_google_country_country_iso_code = '';
			$dados_google_country_language_code = '';

			
			if ($config_google_country != '') {
	
				// Preparar a consulta SQL
				$pega_dados_google_country = $wpdb->get_results($wpdb->prepare("SELECT location_code, language_country, country_iso_code, language_code FROM {$wpdb->prefix}people_also_askeds_config_countries WHERE domain = %s", $config_google_country));

				if (count($pega_dados_google_country) > 0) {
            
					$dados_google_country_location_code = $pega_dados_google_country[0]->location_code;
					$dados_google_country_language_country = $pega_dados_google_country[0]->language_country;
					$dados_google_country_country_iso_code = $pega_dados_google_country[0]->country_iso_code;
					$dados_google_country_language_code = $pega_dados_google_country[0]->language_code;
					
				}

			}


		/* -------------------------------------- Pega conteúdo -------------------------------------- */


		if ($config_api_scrapper == 'serpapi')
			return $this->busca_serp_api($palavra, $config_chave_serp_api,$config_google_country, $dados_google_country_language_country, $dados_google_country_country_iso_code);
		else if ($config_api_scrapper == 'dataforseo')
			return $this->busca_data_for_seo($palavra,$config_usuario_data_for_seo, $config_senha_data_for_seo, $dados_google_country_location_code, $dados_google_country_language_country);
			
	}

	protected function grava_perguntas_localmente($idPalavra, $dados_api_also_ask, $nivel, $idRelatedPai, $config_api_scrapper) {

		if ($config_api_scrapper == 'serpapi')
			return $this->grava_perguntas_localmente_serp_api($idPalavra, $dados_api_also_ask, $nivel, $idRelatedPai);
		else if ($config_api_scrapper == 'dataforseo')
			return $this->grava_perguntas_localmente_data_for_seo($idPalavra, $dados_api_also_ask, $nivel, $idRelatedPai);
			
	}

	function grava_perguntas_localmente_serp_api($idPalavra, $html_postado, $nivel, $idRelatedPai) 
	{
		global $wpdb;
  
  	    $data = json_decode($html_postado, true);

		
		//Perguntas
		if (isset($data['related_questions']) && is_array($data['related_questions'])) {
		
			$marcouComoFeito = true;

			foreach ($data['related_questions'] as $video) {
		
			$keyword_filha = $video['question'];
			$snippet_filha = $video['snippet'];
			$link_filha = $video['link'];
			$lista = $video['list'];
			
			$checa_se_existe = $wpdb->get_results($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}people_also_askeds_related WHERE palavra = %s AND asked_id = %d", $keyword_filha, $idPalavra));


			//Se não foi gravada ainda
			if (count($checa_se_existe) == 0) {

				/* -------------------------------------- Se não existe a palavra -------------------------------------- */
		
				// Insere
				$wpdb->query($wpdb->prepare(
					"INSERT INTO " . $wpdb->prefix . "people_also_askeds_related (
						asked_id,
						parent_id,
						palavra,
						nivel,
						buscou_serp_api
					) VALUES (
						%d, %d, %s, %d, %d
					)",
					$idPalavra,
					$idRelatedPai,
					$keyword_filha,
					$nivel,
					0
				));

				
				/* -------------------------------------- Se não existe a palavra -------------------------------------- */
									
			}
		
			}

		}


		//Buscas relacionadas
		if (isset($data['related_searches']) && is_array($data['related_searches'])) {

			foreach ($data['related_searches'] as $video) {
		  
				// Insere
				$wpdb->query($wpdb->prepare(
					"INSERT INTO " . $wpdb->prefix . "people_also_askeds_other_people_searched (
						asked_id,
						parent_id,
						keyword
					) VALUES (
						%d, %d, %s
					)",
					$idPalavra,
					$idRelatedPai,
					$video['query']
				));

				
			}
	
		}
      
	}

	function grava_perguntas_localmente_data_for_seo($idPalavra, $html_postado, $nivel, $idRelatedPai) {
    
		global $wpdb;

		$data = json_decode($html_postado, true);
		
		foreach ($data['tasks'] as $task) {
		
		  foreach ($task['result'] as $result) {

			foreach ($result['items'] as $item) {
		
			  
		
			  if (isset($item['type'])) { 

				/* -------------------------------------- Item: people_also_ask -------------------------------------- */
		
				if ($item['type'] == 'people_also_ask') {
		
		
				  if (isset($item['items'])) {
	
	
					foreach ($item['items'] as $subItem) {
		
					  $keyword_filha = (isset($subItem['title'])) ? $subItem['title'] : '';
					  $link_filha = '';
					  $lista = [];
					  $htmlTable = '';
					  
						$checa_ja_processada = $wpdb->get_results($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}people_also_askeds_related WHERE palavra = %s AND asked_id = %d", $keyword_filha, $idPalavra));
				  
						//Se não foi gravada ainda
						if (count($checa_ja_processada) == 0) {
		
						  /* -------------------------------------- Se não existe a palavra -------------------------------------- */
		
						  
						  //Quebra a descrição em vários itens
						  if (isset($subItem['expanded_element'])) {
							
							foreach ($subItem['expanded_element'] as $expandedElement) {
							
							  if (isset($expandedElement['description'])) {
								$lista = explode(PHP_EOL, $expandedElement['description']);
							  }
		
							  if (isset($expandedElement['url'])) {
								$link_filha = $expandedElement['url'];
							  }
		
							  if (isset($expandedElement['table'])) {
		
								//Monta tabela HTML
								if (isset($expandedElement['table']['table_header'])) {
								  $htmlTable .= '<p>' . implode(" | ", $expandedElement['table']['table_header']) . '</p>';
								}
						  
								if (isset($expandedElement['table']['table_content'])) {
								  
								  $htmlTable .= '<table border="1">';
								  foreach ($expandedElement['table']['table_content'] as $col) {
						  
									$htmlTable .= '<tr>';
									foreach ($col as $rol) {
						  
									  if (count($col) == 1) {
										$htmlTable .= '</tr></table>';
										$htmlTable .= '<p>' . $rol . '</p>';
										$htmlTable .= '<table border="1">';
									  }
									  else {
										$htmlTable .= '<td>' . $rol . "</td>";
									  }
					
									}
									$htmlTable .= '</tr>';
							  
								  }   
								  $htmlTable .= '</table>';
								}
							  }
							
							}
							
						  }

						  
							// Insere
							$wpdb->query($wpdb->prepare(
								"INSERT INTO " . $wpdb->prefix . "people_also_askeds_related (
									asked_id,
									parent_id,
									palavra,
									nivel,
									buscou_serp_api
								) VALUES (
									%d, %d, %s, %d, %d
								)",
								$idPalavra,
								$idRelatedPai,
								$keyword_filha,
								$nivel,
								0
							));

						  
					  
							  
						/* -------------------------------------- Se não existe a palavra -------------------------------------- */
											  
					  }
	
					}
	
				  }
		
		
				}

				/* -------------------------------------- Item: people_also_ask -------------------------------------- */

				/* -------------------------------------- Item: related_searches -------------------------------------- */
					
				if ($item['type'] == 'related_searches') {
						
					if (isset($item['items'])) {
						foreach ($item['items'] as $subItem) {

							// Insere
							$wpdb->query($wpdb->prepare(
								"INSERT INTO " . $wpdb->prefix . "people_also_askeds_other_people_searched (
									asked_id,
									parent_id,
									keyword
								) VALUES (
									%d, %d, %s
								)",
								$idPalavra,
								$idRelatedPai,
								$subItem
							));

						}
					}

				}

				/* -------------------------------------- Item: related_searches -------------------------------------- */
		
			  }
		
			  

			  
		
		
			}
			
		  }
		
		}


	}

	protected function monta_html_post($keyword, $keyword_filha, $dados_api_also_ask, $config_hierarquia_post, $config_incluir_video_post, $config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_niveis_busca, $status_atual, $config_google_country, $config_serial, $max_concurrent_requests) {
		global $wpdb;
	
		$wp_language = get_locale();
		$conteudo_html = '';
		$status_processamento = '';
		$total_itens_gravados = 0;
	
		// Combine login and password with a colon
		$credentials = 'usuario:senha';
	
		// Encode the credentials using base64
		$base64Credentials = base64_encode($credentials);
	
		$HTTP_USER_AGENT = (isset($_SERVER['HTTP_USER_AGENT'])) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
		$SERVER_NAME = (isset($_SERVER['SERVER_NAME'])) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME'])) : '';
		$ip = people_also_user_ip();
		$usuario = get_current_user_id();
	
		$url = 'https://peoplealsoaskplugin.com/api_1.1.67/worker.php?' . http_build_query([
			'palavra' => $keyword,
			'palavra_filha' => $keyword_filha,
			'status_atual' => $status_atual,
			'api_scrapper' => $config_api_scrapper,
			'chave_serp_api' => $config_chave_serp_api,
			'usuario_data_for_seo' => $config_usuario_data_for_seo,
			'senha_data_for_seo' => $config_senha_data_for_seo,
			'hierarquia_post' => $config_hierarquia_post,
			'incluir_video_post' => $config_incluir_video_post,
			'niveis_busca' => $config_niveis_busca,
			'navegador' => $HTTP_USER_AGENT,
			'ip' => $ip,
			'u' => $usuario,
			'dominio' => $SERVER_NAME,
			'v' => $this->version,
			'gc' => $config_google_country,
			'sn' => $config_serial,
			'cr' => $max_concurrent_requests,
			'l' => $wp_language
		]);
	
		// Prepare the request headers and body
		$args = [
			'method' => 'POST',
			'body' => $dados_api_also_ask,
			'headers' => [
				'Authorization' => 'Basic ' . $base64Credentials,
				'Content-Type' => 'application/json',
			],
			'timeout' => 600
		];
	
		// Execute the HTTP API request
		$response = wp_remote_post($url, $args);
	
		// Check for errors in the response
		if (is_wp_error($response)) {
			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Error in the HTTP request', 'people-also-ask')
			], 400);
			exit;
		}
	
		// Parse the response body
		$data = json_decode(wp_remote_retrieve_body($response), true);
	
		// Check if decoding was successful
		if ($data === null) {
			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Error decoding JSON', 'people-also-ask')
			], 400);
			exit;
		}
	
		// Handle the successful response
		if (isset($data['conteudo'])) {
			$conteudo_html = $data['conteudo'];
			$total_itens_gravados = $data['total'];
		}
	
		// Handle status processing
		if (isset($data['status'])) {
			$status_processamento = $data['status'];
		}
	
		// Handle errors
		if (isset($data['erro'])) {

			$erro = $data['erro'];

			if ($erro == 'Error decoding JSON (1)') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Error decoding JSON (1)', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Error decoding JSON (2)') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Error decoding JSON (2)', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Insufficient balance! Buy credits from the DataForSEO API') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Insufficient balance! Buy credits from the DataForSEO API', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Your DataForSEO API has been blocked. Contact DataForSEO') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Your DataForSEO API has been blocked. Contact DataForSEO', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Error: Enter the word') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Error: Enter the word', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Choose the type of Scrapper API you will use in the Plugin settings') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Choose the type of Scrapper API you will use in the Plugin settings', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Unauthorized website') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Unauthorized website', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Enter the Scrapper API credentials you will use in the Plugin settings') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Enter the Scrapper API credentials you will use in the Plugin settings', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Domain blocked. Please contact our support') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Domain blocked. Please contact our support', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Serial inactive. Please contact our support') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Serial inactive. Please contact our support', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Serial expired. Please contact our support, or renew your subscription') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Serial expired. Please contact our support, or renew your subscription', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Serial already in use. Please contact our support') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Serial already in use. Please contact our support', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Invalid serial. Please contact our support') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Invalid serial. Please contact our support', 'people-also-ask')
				], 400);
			}
			
			if ($erro == 'Important: Problem connecting to the database') {
				wp_send_json([
					'status' => 'error',
					'message' => esc_html__('Important: Problem connecting to the database', 'people-also-ask')
				], 400);
			}
	
			if ($erro == 'Important: You used the XPTO Free credits available in the Demo version. Please UPGRADE to the Premium (unlimited) version at https://peoplealsoaskplugin.com') {
				$max_requests_gratuitos = isset($data['max_requests_gratuitos']) ? $data['max_requests_gratuitos'] : '';
	
				$message = __('Important: You used the XPTO Free credits available in the Demo version. Please UPGRADE to the Premium (unlimited) version at https://peoplealsoaskplugin.com', 'people-also-ask');
	
				wp_send_json([
					'status' => 'error',
					'message' => str_replace('XPTO', $max_requests_gratuitos, $message)
				], 400);
			}
	
			wp_send_json([
				'status' => 'error',
				'message' => $erro
			], 400);
			return;
		}
	
		return [$conteudo_html, $total_itens_gravados, $status_processamento];
	}
	


	
	
	public function processa_palavras()
	{
		check_ajax_referer($this->plugin_name . '_create_nonce');

		$idPalavra = isset($_REQUEST['idPalavra']) ? sanitize_text_field( wp_unslash( $_REQUEST['idPalavra'] ) ) : '';


		global $wpdb;
		$keyword = '';
		$wp_post_id_gravado = 0;
		$total_itens_gravados = 0;
		$status_processamento = '';
		$post_content = '';


		/* --------------- Pega Config --------------- */

		$config_api_scrapper = '';					
		$config_chave_serp_api = '';
		$config_usuario_data_for_seo = '';
		$config_senha_data_for_seo = '';
		$config_status_posts = '';
		$config_categoria_post = 0;
		$config_hierarquia_post = '';
		$config_incluir_video_post = '';
		$config_niveis_busca = 0;
		$config_google_country = '';
		$config_serial = '';
		$max_concurrent_requests = '';
			
		$dados_config = $wpdb->get_results(" SELECT chave, valor FROM ".$wpdb->prefix."people_also_askeds_config; ");

		if (count($dados_config) > 0) {
					
			foreach ( $dados_config as $item ) {
				
				if ($item->chave == 'status-posts')
					$config_status_posts = $item->valor;
				else if ($item->chave == 'hierarquia-post')
					$config_hierarquia_post = $item->valor;
				else if ($item->chave == 'incluir-video-post')
					$config_incluir_video_post = $item->valor;
				else if ($item->chave == 'chave-serpapi')
					$config_chave_serp_api = $item->valor;
				else if ($item->chave == 'usuario-dataforseo')
					$config_usuario_data_for_seo = $item->valor;
				else if ($item->chave == 'senha-dataforseo')
					$config_senha_data_for_seo = $item->valor;
				else if ($item->chave == 'categoria-post')
					$config_categoria_post = $item->valor;
				else if ($item->chave == 'api-scrapper')
					$config_api_scrapper = $item->valor;
				else if ($item->chave == 'niveis-busca')
					$config_niveis_busca = $item->valor;
				else if ($item->chave == 'google-country')
					$config_google_country = $item->valor;
				else if ($item->chave == 'serial')
					$config_serial = $item->valor;
				else if ($item->chave == 'max-concurrent-requests')
					$max_concurrent_requests = $item->valor;
				

			}

		}

		/* --------------- Pega Config --------------- */



		/* -------------------------------------- Validação de dados postados -------------------------------------- */
				
		if (trim($config_google_country) == '') {

			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Error: Select the Google country where you want to search in the Plugin settings', 'people-also-ask')
			], 400);
			return;						
		}
		
		if (trim($config_api_scrapper) == '') {

			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Important: Choose the type of Scrapper API you will use in the Plugin settings', 'people-also-ask')
			], 400);
			return;
			
		}
			
		if (trim($config_chave_serp_api) == '' && trim($config_usuario_data_for_seo) == '' && trim($config_senha_data_for_seo) == '') {
		
			wp_send_json([
				'status' => 'error',
				'message' => esc_html__('Important: Enter the Scrapper API credentials you will use in the Plugin settings', 'people-also-ask')
			], 400);
			return;
			
		}
		

		/* --------------- Pega palavra passada como parâmetro --------------- */

		$pega_keyword_pendente = $wpdb->get_results($wpdb->prepare("SELECT  palavra, status, wp_post_id FROM {$wpdb->prefix}people_also_askeds WHERE id = %d", $idPalavra));

		if (count($pega_keyword_pendente) > 0) {

			$keyword = $pega_keyword_pendente[0]->palavra;
			$status = $pega_keyword_pendente[0]->status;
			
			
			

			if ($status == __('Pending', 'people-also-ask')) {

				

				/* -------------------------- Se não foi processada ainda -------------------------- */

					/* ------------------------------ 1º Execução ------------------------------ */
			
						//Se só há 1 nível para execução
						// if ($config_niveis_busca == '1') {
						// 	$status_pergunta_pai_para_setar = __("Questions processed", 'people-also-ask'); //Ao fazer o scrapper do nível 1, seta status como 'As perguntas foram processadas', a próxima execução do Ajax será para montar e devolver o HMTL
						// }
						// else {
						// 	$status_pergunta_pai_para_setar = __('Topic Parent Processed', 'people-also-ask'); //Ao fazer o scrapper do nível 1, seta status como 'Tópico Pai Processado', a próxima execução do Ajax será para processar as perguntas filhas
						// }
						
						/* --------------- Pega o conteúdo  --------------- */

						//Busca Perguntas de 1º nível
						$dados_api_also_ask = $this->pega_dados_api_also_ask($keyword, $config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_google_country);
						
						/* --------------- Grava perguuntas localmente  --------------- */

						$this->grava_perguntas_localmente($idPalavra, $dados_api_also_ask, 0, 0, $config_api_scrapper);

						/* --------------- Grava conteúdo na API  --------------- */

						$pega_dados = $this->monta_html_post($keyword, '', $dados_api_also_ask, $config_hierarquia_post, $config_incluir_video_post,$config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_niveis_busca, $status, $config_google_country,$config_serial, $max_concurrent_requests);
						
						if ($pega_dados != null) {
							$post_content = $pega_dados[0];
							$total_itens_gravados = $pega_dados[1];
							$status_processamento = $pega_dados[2];
						}
						
						//$wpdb->query("INSERT INTO temp_response (log) VALUES ('A: ".$status_processamento."');");

						/* ------------------------------ Se ocorreu algum problema no sincronismo ------------------------------ */

						if ($status_processamento == __('Restarting', 'people-also-ask')) {

							//Retorna para pendente
							$status_processamento = __('Pending', 'people-also-ask');

							//Apaga dados
							$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_other_people_searched WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
							$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_related WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
						}

						
						/* ------------------------------ Atualiza status ------------------------------ */

						//Atualiza status							
						$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET status = %s WHERE id = %d", $status_processamento, $idPalavra));
						

						/* ------------------------------ Envia resposta ------------------------------ */

						//Envia resposta
						wp_send_json([
							'status' => $status_processamento,
							'total_itens' => 0,
							'wp_post_id' => 0
						], 201);
						exit();


					/* ------------------------------ 1º Execução ------------------------------ */

				/* -------------------------- Se não foi processada ainda -------------------------- */
	  
			}
			else {

			/* -------------------------- Se já foi processada -------------------------- */

					if (
						$status == __('Topic Parent Processed', 'people-also-ask') || (str_contains($status, __('Question processed', 'people-also-ask') . ' ') && str_contains($status, '/')) //Pergunta processada 1/4
					) {
			
						/* ------------------------------ 2º, 3º, 4º ... 6º Execuções ------------------------------ */
			
			
							$total_perguntas = 0;
							$contador_perguntas = 0;
			
							//Varre perguntas filhas
							$pega_filhos = $wpdb->get_results($wpdb->prepare("SELECT ID, palavra, buscou_serp_api FROM {$wpdb->prefix}people_also_askeds_related WHERE asked_id = %d AND nivel = 0 ORDER BY ID ASC;", $idPalavra));

							if (count($pega_filhos) > 0) {
			
								/* ------------------------- Se retornou dados -------------------------*/
			
									//Conta total de perguntas
									foreach ( $pega_filhos as $item ) {
										$total_perguntas++;
									}
			
									//Pega primeira pergunta pendente
									foreach ( $pega_filhos as $item ) {
			
										//Incrementa total de perguntas varridas
										$contador_perguntas++;
				
										if ($item->buscou_serp_api == '0') {
				
											// //Status para setar
											// if ($total_perguntas == $contador_perguntas) {
				
											// 	//Se é a última pergunta (seta como tudo finalizado)
											// 	$status_pergunta_pai_para_setar = __("Questions processed", 'people-also-ask');
					
											// }
											// else {
				
											// 	//Seta como andamento
											// 	$status_pergunta_pai_para_setar = __('Question processed', 'people-also-ask') . ' '.$contador_perguntas.'/'.$total_perguntas;  
					
											// }
				
							

											/* --------------- Pega o conteúdo  --------------- */

											//Busca Perguntas de 2º nível
											$dados_api_also_ask = $this->pega_dados_api_also_ask($item->palavra, $config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_google_country);
											

											
											// echo 'Debug: ';
											// var_dump($dados_api_also_ask);
											// exit();
															
											/* --------------- Grava perguuntas localmente  --------------- */

											//$this->grava_perguntas_localmente($idPalavra, $dados_api_also_ask, 1, $item->ID, $config_api_scrapper);

							
											/* --------------- Grava conteúdo na API  --------------- */

											$pega_dados = $this->monta_html_post($keyword, $item->palavra, $dados_api_also_ask, $config_hierarquia_post, $config_incluir_video_post,$config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_niveis_busca, $status, $config_google_country,$config_serial, $max_concurrent_requests);

											if ($pega_dados != null) {
												$post_content = $pega_dados[0];
												$total_itens_gravados = $pega_dados[1];
												$status_processamento = $pega_dados[2];
											}

											//$wpdb->query("INSERT INTO temp_response (log) VALUES ('B: ".$status_processamento."');");

											/* ------------------------------ Se ocorreu algum problema no sincronismo ------------------------------ */

											if ($status_processamento == __('Restarting', 'people-also-ask')) {

												//Retorna para pendente
												$status_processamento = __('Pending', 'people-also-ask');

												//Apaga dados
												$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_other_people_searched WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
												$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_related WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
											}
									
											/* ------------------------------ Atualiza status ------------------------------ */

											//Atualiza status							
											$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET status = %s WHERE id = %d", $status_processamento, $idPalavra));
											$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds_related SET buscou_serp_api = 1 WHERE id = %d", $item->ID));

											/* ------------------------------ Envia resposta ------------------------------ */

											//Envia resposta
											wp_send_json([
												'status' => $status_processamento,
												'total_itens' => 0,
												'wp_post_id' => 0
											], 201);

											//Interrompe a execução
											break;

										}
									}
			
								/* ------------------------- Se retornou dados -------------------------*/
			
							}
							else {
								
								/* ------------------------- Se não retornou dados -------------------------*/
			
									$status_pergunta_pai_para_setar = __('No content', 'people-also-ask'); //Status final
			
									//Dá UPDATE
									$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET status = %s WHERE id = %d", $status_pergunta_pai_para_setar, $idPalavra)); 
									
									//Envia resposta
									wp_send_json([
										'conteudo' => $pc,
										'total' => $total_itens_gravados,
										'status' => $status_pergunta_pai_para_setar,
										'total_itens' => 0,
										'wp_post_id' => 0
									], 201);
									exit();
			
								/* ------------------------- Se não retornou dados -------------------------*/
			
							}
			
			
						/* ------------------------------ 2º, 3º, 4º ... 6º Execuções ------------------------------ */
			
					}
					else if ($status == __("Questions processed", 'people-also-ask') || $status == __("Finished", 'people-also-ask')) {
						
						/* ------------------------------ Monta HTML do Post ------------------------------ */
				
							//$status_pergunta_pai_para_setar = __("Finished", 'people-also-ask'); //Status final
				
				
							//Pega o conteúdo do Post
							$pega_dados = $this->monta_html_post($keyword, $item->palavra, '', $config_hierarquia_post, $config_incluir_video_post,$config_api_scrapper, $config_chave_serp_api, $config_usuario_data_for_seo, $config_senha_data_for_seo, $config_niveis_busca, $status, $config_google_country,$config_serial, $max_concurrent_requests);

							$post_content = '';
							$total_itens_gravados = 0;
							$status_processamento = '';

							if ($pega_dados != null) {
								$post_content = $pega_dados[0];
								$total_itens_gravados = $pega_dados[1];
								$status_processamento = $pega_dados[2];
							}

							//$wpdb->query("INSERT INTO temp_response (log) VALUES ('C: ".$status_processamento."');");

							/* ------------------------------ Se ocorreu algum problema no sincronismo ------------------------------ */

							if ($status_processamento == __('Restarting', 'people-also-ask')) {

								//Retorna para pendente
								$status_processamento = __('Pending', 'people-also-ask');

								//Apaga dados
								$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_other_people_searched WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
								$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}people_also_askeds_related WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID = %d ); ", $idPalavra));
							}

							/* --------------- Cria post --------------- */

							$wp_post_id_gravado = $this->cria_post($keyword, $post_content, $idPalavra, $config_status_posts, $config_categoria_post, $config_hierarquia_post, $config_incluir_video_post);

							/* --------------- Atualiza dados --------------- */
							
							$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET wp_post_id = %d, itens = %d, status = %s WHERE id = %d", $wp_post_id_gravado, $total_itens_gravados, $status_processamento, $idPalavra));

							/* --------------- Publica post --------------- */

							//Publica se a configuração de publicação automática estiver ativada
							//Publica se o post foi gravado no WP
							//Publica se o post possui conteúdo em HTML
							if ($config_status_posts == 'automatico-publicado' && $wp_post_id_gravado > 0 && $post_content <> '') {

								//Publica o post
								wp_publish_post($wp_post_id_gravado);
								
							}
				
				
							//Envia resposta
							wp_send_json([
								'conteudo' => $post_content,
								'total_itens' => $total_itens_gravados,
								'status' => $status_processamento,
								'wp_post_id' => $wp_post_id_gravado
							], 201);
							exit();
			
						/* ------------------------------ Monta HTML do Post ------------------------------ */
			
					} else if ($status == __('No content', 'people-also-ask')) {
				
						/* ------------------------------ Monta HTML do Post ------------------------------ */
			
						//Envia resposta
						wp_send_json([
							'conteudo' => '',
							'total' => 0,
							'status' => $status,
							'total_itens' => 0,
							'wp_post_id' => 0
						], 201);
						exit();
			
						
						/* ------------------------------ Monta HTML do Post ------------------------------ */
			
					}
					else {

						$status_processamento = __('Pending', 'people-also-ask');

						/* ------------------------------ Atualiza status ------------------------------ */

						//Atualiza status							
						$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}people_also_askeds SET status = %s WHERE id = %d", $status_processamento, $idPalavra));

						/* ------------------------------ Envia resposta ------------------------------ */

						//Envia resposta
						wp_send_json([
							'status' => $status_processamento,
							'total_itens' => 0,
							'wp_post_id' => 0
						], 201);
						exit();

					}

			/* -------------------------- Se já foi processada -------------------------- */

			}

			  


				

			}
			

	}


	

	public function save_keywords()
	{
		global $wpdb;

		check_ajax_referer($this->plugin_name . '_create_nonce');

		$action = isset($_REQUEST['action']) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';

		if ($action !== 'item_create' || !current_user_can('administrator')) {
			header('Status: 403 Forbidden', true, 403);
			wp_die();
		}


		$user_id  = get_current_user_id();

		// $palavra = '';
		// if (isset($_REQUEST['palavra'])) {
		// 	$palavra = wp_strip_all_tags((string) wp_unslash($_REQUEST['palavra']));
		// }

		// $palavra = str_replace('	', ' ', $palavra);
		// $palavras = explode("\r\n", $palavra);

		$palavra = '';
		if (isset($_REQUEST['palavra'])) {
			// Sanitiza o dado de entrada, tratando como texto de uma textarea
			$palavra = sanitize_textarea_field(wp_unslash($_REQUEST['palavra']));
			
			// Valida se $palavra é uma string após a sanitização
			if (!is_string($palavra)) {
				$palavra = '';  // Se não for string, redefine para uma string vazia
			}
		}
		
		// Remove tabs e quebra a string em múltiplas linhas
		$palavra = str_replace("\t", ' ', $palavra);
		$palavras = explode("\r\n", $palavra);




		//Palaveas gravadas
		$palavrasGravadas = array();

		foreach ($palavras as $palavra)
		{
			//Salta palavras vazias
			if (trim($palavra) == '')
				continue;

			/* -------------------------------------- Varre cada palavra -------------------------------------- */

			//Checa se a palavra já foi gravada
			$findOne = $wpdb->get_row($wpdb->prepare(
				"SELECT 1 FROM {$wpdb->prefix}people_also_askeds WHERE palavra = %s ",
				ucfirst(trim($palavra)),
			), OBJECT);
	
			
			//Se não existe ainda
			if (is_null($findOne)) {

				$insert_data = [
					'palavra' => ucfirst(trim($palavra)),
					'status' => __('Pending', 'people-also-ask'),
					'created_by' => $user_id,
				];

				$wpdb->insert(
					$wpdb->prefix . 'people_also_askeds',
					$insert_data,
					['%s', '%s', '%d']
				);

				//Lista final de palavras
				$palavrasGravadas[] = array (
				'id' => $wpdb->insert_id,
				'palavra' => ucfirst($palavra)
				);
				
			}
	
	
			/* -------------------------------------- Varre cada palavra -------------------------------------- */
		}


		wp_send_json($palavrasGravadas, 201);


	}


	
	

	

}
