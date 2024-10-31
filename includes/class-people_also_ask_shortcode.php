<?php

/**
 * The file that defines the shortcode  plugin class
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
class People_Also_Ask_Shortcode {


	public function video_shortcode($atts = [])
	{		

		$a = shortcode_atts(array(
			'url' => '',
			'title' => '',
		), $atts);


		// Verifica se a URL é válida
		if (filter_var($a['url'], FILTER_VALIDATE_URL) === false) {
			return;
		}

		// Parseia a URL para extrair o parâmetro 'v' (ID do vídeo)
		$urlParts = wp_parse_url($a['url']);
		parse_str($urlParts['query'], $queryParams);

		if (!isset($queryParams['v'])) {
			return;
		}

		$videoId = $queryParams['v'];

		// Cria o iframe com o URL do vídeo
		$iframe = '<h3>' . $a['title'] . '</h3>';
		$iframe .= '<iframe src="https://www.youtube.com/embed/' . $videoId . '" width="560" height="315" frameborder="0" allowfullscreen></iframe>';

		// Retorna o iframe
		return $iframe;
	}

	public function barra_shortcode($atts = [])
	{		
		$a = shortcode_atts(array(
			'url' => '',
		), $atts);

		if (get_option( 'people_also_display-view-more' ) == 'nao' && get_option( 'people_also_display-request-removal' ) == 'nao')
			return '';

		// Verifica se a URL é válida
		if (filter_var($a['url'], FILTER_VALIDATE_URL) === false) {
			return;
		}

		
		$html = "<div class='article-actions'>";

			if (get_option( 'people_also_display-request-removal' ) == 'sim') {
			
				$html .= "<span class='report-it'>";
					$html .= "<span class='a' onclick=\"window.open('" . get_home_url() ."/". __("removal-request", 'people-also-ask') ."', '_blank')\">";
						$html .= __("Removal Request", 'people-also-ask');
					$html .= "</span>";
				$html .= "</span>";

			}
			
			if (get_option( 'people_also_display-view-more' ) == 'sim' && get_option( 'people_also_display-request-removal' ) == 'sim')
				$html .= "<span class='spacer'>|</span>";			

			if (get_option( 'people_also_display-view-more' ) == 'sim') {
				$html .= "<span class='view-url' onclick=\"window.open(reverse('" . people_also_reverte_string($a['url']) . "'), '_blank')\">" . __("See the full answer at", 'people-also-ask') . " " . people_also_extractDomain($a['url']) ."</span>";			
			}

		$html .= "</div>";

		
		
		// Retorna o iframe
		return $html;
	}

	public function add_custom_public_styles_scripts() {

		if (is_single()) { // Verifica se é um post

			wp_enqueue_style( 'plugin-style', plugin_dir_url( dirname(__FILE__) ) . 'public/css/people_also_ask-public.css' , array(), $this->version, false );
			wp_enqueue_script( 'plugin-script', plugin_dir_url( dirname(__FILE__) ) . 'public/js/people_also_ask-public.js' , array(), $this->version, false );
			
		}
	}

}

