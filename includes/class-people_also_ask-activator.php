<?php

/**
 * Fired during plugin activation
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/includes
 * @author     Caique Dourado <ckdourado@gmail.com>
 */
class People_Also_Ask_Activator
{

	/**
	 * The $_REQUEST during plugin activation.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $request    The $_REQUEST array during plugin activation.
	 */
	private static $request = [];

	/**
	 * The $_REQUEST['plugin'] during plugin activation.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin    The $_REQUEST['plugin'] value during plugin activation.
	 */
	private static $plugin = PEOPLE_ALSO_ASK_BASE_NAME;
	private static $version = PEOPLE_ALSO_ASK_VERSION;

	/**
	 * Activate the plugin.
	 *
	 * Checks if the plugin was (safely) activated.
	 * Place to add any custom action during plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		

		if (
			false === self::get_request()
			|| false === self::validate_request(self::$plugin)
			|| false === self::check_caps()
		) {
			if (isset($_REQUEST['plugin'])) {
				if (!check_admin_referer('activate-plugin_' . self::$request['plugin'])) {
					exit;
				}
			} elseif (isset($_REQUEST['checked'])) {
				if (!check_admin_referer('bulk-plugins')) {
					exit;
				}
			}
		}

		/**
		 * The plugin is now safely activated.
		 * Perform your activation actions here.
		 */
		global $wpdb;
		$table_name = $wpdb->prefix . self::$plugin . 's';
		$charset_collate = $wpdb->get_charset_collate();
		

		$sql = "";

		if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
			$sql .= "CREATE TABLE {$table_name} (
				`ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`palavra` VARCHAR(300) NULL,
				`status` VARCHAR(50) NULL,
				`itens` INT NULL,                
				`wp_post_id` BIGINT UNSIGNED NULL,
				`created_by` BIGINT UNSIGNED NOT NULL,
				`saved_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`ID`),
				UNIQUE INDEX `palavra` (`palavra`),
				INDEX `created_by` (`created_by`),
				INDEX `wp_post_id` (`wp_post_id`)
			) $charset_collate;";
		}
		

		if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", "{$table_name}_config")) !== "{$table_name}_config") {
			
			$sql .= "CREATE TABLE {$table_name}_config (
				`ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`chave` VARCHAR(200) NULL,
				`valor` VARCHAR(200) NULL,
				PRIMARY KEY (`ID`),
				INDEX `chave` (`chave`)
			) $charset_collate;";
		}



		$wpdb->query( $wpdb->prepare( "DROP TABLE %s_config_countries;", $table_name ) );

		if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", "{$table_name}_config_countries")) !== "{$table_name}_config_countries") {

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
			
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ad','2020','ca','ad','ca','Andorra');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ae','2784','ar','ae','ar','United Arab Emirates');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.af','2004','ps','af','ps','Afghanistan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ag','2028','en','ag','en','Antigua and Barbuda');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ai','2660','en','ai','en','Anguilla');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.al','2008','sq','al','sq','Albania');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.am','2051','hy','am','hy','Armenia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ao','2024','pt','ao','pt','Angola');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ar','2032','es','ar','es','Argentina');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.as','2016','en','as','en','American Samoa');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.at','2040','de','at','de','Austria');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.au','2036','en','au','en','Australia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.az','2031','az','az','az','Azerbaijan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ba','2070','bs','ba','bs','Bosnia and Herzegovina');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bd','2050','bn','bd','bn','Bangladesh');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.be','2056','nl','be','nl','Belgium');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bf','2854','fr','bf','fr','Burkina Faso');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bg','2100','bg','bg','bg','Bulgaria');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bh','2048','ar','bh','ar','Bahrain');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bi','2108','fr','bi','fr','Burundi');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bj','2204','fr','bj','fr','Benin');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bn','2096','ms','bn','ms','Brunei');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bo','2068','es','bo','es','Bolivia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.br','2076','pt-BR','br','pt','Brazil');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bs','2044','en','bs','en','The Bahamas');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.bt','2064','bt','bt','bt','Bhutan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.bw','2072','en','bw','en','Botswana');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.bz','2084','en','bz','en','Belize');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ca','2124','en','ca','en','Canada');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cf','2140','fr','cf','fr','Central African Republic');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cd','2178','fr','cg','fr','Republic of the Congo');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cg','2178','fr','cg','fr','Republic of the Congo');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ch','2756','de','ch','de','Switzerland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ci','2384','fr','ci','fr','Cote d'Ivoire');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ck','2184','en','ck','en','Cook Islands');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cl','2152','es','cl','es','Chile');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cm','2120','en','cm','en','Cameroon');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.co','2170','es','co','es','Colombia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.cr','2188','es','cr','es','Costa Rica');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cv','2132','pt','cv','pt','Cabo Verde');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.cy','2196','el','cy','el','Cyprus');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.cz','2203','cs','cz','cs','Czechia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.de','2276','de','de','de','Germany');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dj','2262','ar','dj','ar','Djibouti');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dk','2208','da','dk','da','Denmark');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dm','2212','en','dm','en','Dominica');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.do','2214','es','do','es','Dominican Republic');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.dz','2012','ar','dz','ar','Algeria');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ec','2218','es','ec','es','Ecuador');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ee','2233','et','ee','et','Estonia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.eg','2818','ar','eg','ar','Egypt');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.es','2724','es','es','es','Spain');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.et','2231','am','et','am','Ethiopia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fi','2246','fi','fi','fi','Finland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.fj','2242','en','fj','en','Fiji');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fm','2583','en','fm','en','Micronesia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.fr','2250','fr','fr','fr','France');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ga','2266','fr','ga','fr','Gabon');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ge','2268','ka','ge','ka','Georgia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gh','2288','en','gh','en','Ghana');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gi','2292','en','gi','en','Gibraltar');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gl','2304','kl','gl','kl','Greenland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gm','2270','en','gm','en','The Gambia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gp','2312','fr','gp','fr','Guadeloupe');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gr','2300','el','gr','el','Greece');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.gt','2320','es','gt','es','Guatemala');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.gy','2328','en','gy','en','Guyana');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.hk','2344','en','hk','en','Hong Kong');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hn','2340','es','hn','es','Honduras');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hr','2191','hr','hr','hr','Croatia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ht','2332','fr','ht','fr','Haiti');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.hu','2348','hu','hu','hu','Hungary');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.id','2360','id','id','id','Indonesia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ie','2372','en','ie','en','Ireland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.il','2376','iw','il','iw','Israel');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.in','2356','hi','in','hi','India');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.iq','2368','ar','iq','ar','Iraq');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.is','2352','is','is','is','Iceland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.it','2380','it','it','it','Italy');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.jm','2388','en','jm','en','Jamaica');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.jo','2400','ar','jo','ar','Jordan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.jp','2392','ja','jp','ja','Japan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ke','2404','en','ke','en','Kenya');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.kg','2417','ky','kg','ky','Kyrgyzstan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.kh','2116','km','kh','km','Cambodia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ki','2296','en','ki','en','Kiribati');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.kr','2410','ko','kr','ko','South Korea');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.kw','2414','ar','kw','ar','Kuwait');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.kz','2398','kk','kz','kk','Kazakhstan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.la','2418','lo','la','lo','Laos');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.lb','2422','ar','lb','ar','Lebanon');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.li','2438','de','li','de','Liechtenstein');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lk','2144','si','lk','si','Sri Lanka');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ls','2426','en','ls','en','Lesotho');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lt','2440','lt','lt','lt','Lithuania');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lu','2442','fr','lu','fr','Luxembourg');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.lv','2428','lv','lv','lv','Latvia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ly','2434','ar','ly','ar','Libya');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ma','2504','ar','ma','ar','Morocco');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.md','2498','ro','md','ro','Moldova');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mg','2450','fr','mg','fr','Madagascar');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mk','2807','mk','mk','mk','North Macedonia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ml','2466','fr','ml','fr','Mali');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mm','2104','my','mm','my','Myanmar (Burma)');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mn','2496','mn','mn','mn','Mongolia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ms','2500','en','ms','en','Montserrat');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mt','2470','mt','mt','mt','Malta');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mu','2480','en','mu','en','Mauritius');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mv','2462','mv','mv','mv','Maldives');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.mw','2454','en','mw','en','Malawi');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.mx','2484','es','mx','es','Mexico');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.my','2458','ms','my','ms','Malaysia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.mz','2508','pt','mz','pt','Mozambique');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.na','2516','en','na','en','Namibia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ne','2562','fr','ne','fr','Niger');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ng','2566','en','ng','en','Nigeria');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ni','2558','es','ni','es','Nicaragua');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nl','2528','nl','nl','nl','Netherlands');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.no','2578','no','no','no','Norway');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.np','2524','ne','np','ne','Nepal');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nr','2520','en','nr','en','Nauru');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.nu','2570','en','nu','en','Niue');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.nz','2554','en','nz','en','New Zealand');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.om','2512','ar','om','ar','Oman');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pa','2591','es','pa','es','Panama');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pe','2604','es','pe','es','Peru');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pg','2598','en','pg','en','Papua New Guinea');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ph','2608','tl','ph','tl','Philippines');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pk','2586','en','pk','en','Pakistan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.pl','2616','pl','pl','pl','Poland');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.pr','2630','es','pr','es','Puerto Rico');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ps','2275','ar','ps','ar','Palestine');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.pt','2620','pt-PT','pt','pt','Portugal');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.py','2600','es','py','es','Paraguay');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.qa','2634','ar','qa','ar','Qatar');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ro','2642','ro','ro','ro','Romania');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.rs','2688','sr','rs','sr','Serbia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.rw','2646','rw','rw','rw','Rwanda');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sa','2682','ar','sa','ar','Saudi Arabia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sb','2090','en','sb','en','Solomon Islands');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sc','2690','fr','sc','fr','Seychelles');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.se','2752','sv','se','sv','Sweden');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sg','2702','en','sg','en','Singapore');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sh','2654','en','sh','en','Saint Helena, Ascension and Tristan da Cunha');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.si','2705','sl','si','sl','Slovenia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sk','2703','sk','sk','sk','Slovakia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sl','2694','en','sl','en','Sierra Leone');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sm','2674','it','sm','it','San Marino');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sn','2686','fr','sn','fr','Senegal');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.so','2706','so','so','so','Somalia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.sr','2740','nl','sr','nl','Suriname');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.sv','2222','es','sv','es','El Salvador');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.td','2148','ar','td','ar','Chad');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tg','2768','fr','tg','fr','Togo');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.th','2764','th','th','th','Thailand');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tj','2762','tg','tj','tg','Tajikistan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tk','2772','en','tk','en','Tokelau');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tl','2626','pt','tl','pt','Timor-Leste');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tm','2795','tk','tm','tk','Turkmenistan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tn','2788','ar','tn','ar','Tunisia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.to','2776','en','to','en','Tonga');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tr','2792','tr','tr','tr','Turkiye');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tr','2792','tr','tr','tr','Turkiye');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.tt','2780','en','tt','en','Trinidad and Tobago');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.tw','2158','zh-tw-TW','tw','zh-tw','Taiwan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.tz','2834','sw','tz','sw','Tanzania');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.ua','2804','uk','ua','uk','Ukraine');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ug','2800','en','ug','en','Uganda');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com','2840','en','us','en','United States');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.uy','2858','es','uy','es','Uruguay');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.uz','2860','uz','uz','uz','Uzbekistan');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.vc','2670','en','vc','en','Saint Vincent and the Grenadines');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.ve','2862','es','ve','es','Venezuela');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.vi','2850','en','vi','en','U.S. Virgin Islands');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.com.vn','2704','vi','vn','vi','Vietnam');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.vu','2548','vu','vu','vu','Vanuatu');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.ws','2882','ws','ws','ws','Samoa');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.za','2710','af','za','af','South Africa');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.zm','2894','en','zm','en','Zambia');
			INSERT INTO {$table_name}_config_countries (domain, location_code, language_country, country_iso_code, language_code, location_name) VALUES ('google.co.zw','2716','en','zw','en','Zimbabwe');
						
			";
		}

		if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", "{$table_name}_related")) !== "{$table_name}_related") {
					
			$sql .= "CREATE TABLE {$table_name}_related (
				`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`asked_id` bigint(20) NOT NULL,
				`parent_id` bigint(20) DEFAULT NULL,
				`palavra` varchar(300) DEFAULT NULL,
				`buscou_serp_api` bit(1) DEFAULT b'0',
				`nivel` INT NULL,
				PRIMARY KEY (`ID`),
				KEY `asked_id` (`asked_id`)
			) $charset_collate;";

		}

		if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", "{$table_name}_other_people_searched")) !== "{$table_name}_other_people_searched") {
					
			$sql .= "CREATE TABLE {$table_name}_other_people_searched (
				`ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`asked_id` BIGINT NOT NULL,
				`parent_id` BIGINT  NULL,
				`keyword` VARCHAR(200) NULL,
				PRIMARY KEY (`ID`),
				INDEX `asked_id` (`asked_id`)
			) $charset_collate;";

		}

		if (!empty($sql)) {


			//echo $sql;

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		

		add_option(self::$plugin . '_version', self::$version);
		
	}

	/**
	 * Get the request.
	 *
	 * Gets the $_REQUEST array and checks if necessary keys are set.
	 * Populates self::request with necessary and sanitized values.
	 *
	 * @since    1.0.0
	 * @return bool|array false or self::$request array.
	 */
	private static function get_request()
	{
		if (
			!empty($_REQUEST)
			&& isset($_REQUEST['_wpnonce'])
			&& isset($_REQUEST['action'])
		) {
			if (isset($_REQUEST['plugin'])) {
				if (false !== wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'activate-plugin_' . sanitize_text_field(wp_unslash($_REQUEST['plugin'])))) {

					self::$request['plugin'] = sanitize_text_field(wp_unslash($_REQUEST['plugin']));
					self::$request['action'] = sanitize_text_field(wp_unslash($_REQUEST['action']));

					return self::$request;
				}
			} elseif (isset($_REQUEST['checked'])) {
				if (false !== wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'bulk-plugins')) {

					self::$request['action'] = sanitize_text_field(wp_unslash($_REQUEST['action']));
					self::$request['plugins'] = array_map('sanitize_text_field', wp_unslash($_REQUEST['checked']));

					return self::$request;
				}
			}
		} else {

			return false;
		}
	}

	/**
	 * Validate the Request data.
	 *
	 * Validates the $_REQUESTed data is matching this plugin and action.
	 *
	 * @since    1.0.0
	 * @param string $plugin The Plugin folder/name.php.
	 * @return bool false if either plugin or action does not match, else true.
	 */
	private static function validate_request($plugin)
	{
		if (
			isset(self::$request['plugin'])
			&& $plugin === self::$request['plugin']
			&& 'activate' === self::$request['action']
		) {

			return true;
		} elseif (
			isset(self::$request['plugins'])
			&& 'activate-selected' === self::$request['action']
			&& in_array($plugin, self::$request['plugins'])
		) {
			return true;
		}

		return false;
	}

	/**
	 * Check Capabilities.
	 *
	 * We want no one else but users with activate_plugins or above to be able to active this plugin.
	 *
	 * @since    1.0.0
	 * @return bool false if no caps, else true.
	 */
	private static function check_caps()
	{
		if (current_user_can('activate_plugins')) {
			return true;
		}

		return false;
	}
}
