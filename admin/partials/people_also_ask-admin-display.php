<?php
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://peoplealsoaskplugin.com
 * @since      1.0.0
 *
 * @package    People_Also_Ask
 * @subpackage People_Also_Ask/admin/partials
 */
?>
<?php
$min_datetime = substr(str_replace(' ', 'T', people_also_tz_strtodate('now')), 0, -3);
?>


<?php

global $wpdb;

/* --------------- Pega Config --------------- */

$config_status_posts = '';
$config_max_concurrent_requests = '';
$config_serial = '';

$config_api_scrapper = '';					
$config_chave_serp_api = '';
$config_usuario_data_for_seo = '';
$config_senha_data_for_seo = '';

$dados_config = $wpdb->get_results(" SELECT chave, valor FROM ".$wpdb->prefix."people_also_askeds_config ");

if (count($dados_config) > 0) {
            
    foreach ( $dados_config as $item ) {
        
        if ($item->chave == 'status-posts')
            $config_status_posts = $item->valor;
        else if ($item->chave == 'max-concurrent-requests')
            $config_max_concurrent_requests = $item->valor;
        else if ($item->chave == 'serial')
            $config_serial = $item->valor;        
        else if ($item->chave == 'api-scrapper')
                $config_api_scrapper = $item->valor;
        else if ($item->chave == 'chave-serpapi')
            $config_chave_serp_api = $item->valor;
        else if ($item->chave == 'usuario-dataforseo')
            $config_usuario_data_for_seo = $item->valor;
        else if ($item->chave == 'senha-dataforseo')
            $config_senha_data_for_seo = $item->valor;

    }

}


?>

<input id="config_status_posts" type="hidden" value="<?php echo esc_attr($config_status_posts); ?>" />
<input id="config_max_concurrent_requests" type="hidden" value="<?php echo esc_attr($config_max_concurrent_requests); ?>" />
<input id="config_api_scrapper" type="hidden" value="<?php echo esc_attr($config_api_scrapper); ?>" />
<input id="config_chave_serp_api" type="hidden" value="<?php echo esc_attr($config_chave_serp_api); ?>" />
<input id="config_usuario_data_for_seo" type="hidden" value="<?php echo esc_attr($config_usuario_data_for_seo); ?>" />
<input id="config_senha_data_for_seo" type="hidden" value="<?php echo esc_attr($config_senha_data_for_seo); ?>" />

<input id="translate_pendente" type="hidden" value="<?php esc_html_e("Pending", 'people-also-ask'); ?>" />
<input id="translate_post_status" type="hidden" value="<?php esc_html_e("Post Status", 'people-also-ask'); ?>" />
<input id="translate_finalizado" type="hidden" value="<?php esc_html_e("Finished", 'people-also-ask'); ?>" />
<input id="translate_erro" type="hidden" value="<?php esc_html_e("Error", 'people-also-ask'); ?>" />
<input id="translate_importante" type="hidden" value="<?php esc_html_e("Important:", 'people-also-ask'); ?>" />
<input id="translate_processando" type="hidden" value="<?php esc_html_e("Processing", 'people-also-ask'); ?>" />
<input id="translate_sem_conteudo" type="hidden" value="<?php esc_html_e("No content", 'people-also-ask'); ?>" />
<input id="translate_palavra_chave" type="hidden" value="<?php esc_html_e("Keyword", 'people-also-ask'); ?>" />
<input id="translate_acoes" type="hidden" value="<?php esc_html_e("Actions", 'people-also-ask'); ?>" />
<input id="translate_itens" type="hidden" value="<?php esc_html_e("Items", 'people-also-ask'); ?>" />
<input id="translate_editar_post" type="hidden" value="<?php esc_html_e("Edit Post", 'people-also-ask'); ?>" />
<input id="translate_visualizar" type="hidden" value="<?php esc_html_e("View", 'people-also-ask'); ?>" />
<input id="translate_rascunho" type="hidden" value="<?php esc_html_e("Draft", 'people-also-ask'); ?>" />
<input id="translate_publicado" type="hidden" value="<?php esc_html_e("Published", 'people-also-ask'); ?>" />
<input id="translate_selecione_uma_api" type="hidden" value="<?php esc_html_e("Select a Scrapper API in the Plugin settings!", 'people-also-ask'); ?>" />
<input id="translate_cadastre_usuario_api_dataforseo" type="hidden" value="<?php esc_html_e("Register the DataForSEO API user in the Plugin settings!", 'people-also-ask'); ?>" />
<input id="translate_cadastre_senha_api_dataforseo" type="hidden" value="<?php esc_html_e("Register the DataForSEO API password in the Plugin settings!", 'people-also-ask'); ?>" />
<input id="translate_cadastre_chave_api_serpapi" type="hidden" value="<?php esc_html_e("Register the SerpAPI API key in the Plugin settings!", 'people-also-ask'); ?>" />
<input id="translate_delete" type="hidden" value="<?php esc_html_e("Delete", 'people-also-ask'); ?>" />

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>


    <?php if ($config_serial == '') { ?>

  

    <div id="avisoPremium">

            <h1>                
                <?php esc_html_e('Use the version', 'people-also-ask') ?> <a href="<?php esc_html_e('https://peoplealsoaskplugin.com', 'people-also-ask') ?>" target="_blank"><strong>People Also Ask <i>Premium</i></strong></a>
            </h1>

            <p><?php esc_html_e('The Demo version of the plugin allows you to generate 25 posts', 'people-also-ask') ?></p>
            <p><?php echo wp_kses_post(__('The <strong><i>Premium</i></strong> version allows you to generate unlimited posts!', 'people-also-ask')); ?></p>


        <table style="width: 100%; margin-top: 2em;">
        <tbody>
                
                <a href="<?php esc_html_e('https://peoplealsoaskplugin.com', 'people-also-ask') ?>" target="_blank" class="btn button-primary"><?php esc_html_e('Get the Premium version now!', 'people-also-ask') ?></a>    

        </tbody>
        </table>
        

    </div>

    <?php } ?>


    <form id="item_form" class="form-table">
        
        <table style="width: 100%; margin-top: 2em;">
            <tbody>
                <tr>
                    <th scope="row">

                        <label for="palavra"><?php esc_html_e('Enter Keywords', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label>

                        <p class="description">
                            <?php esc_html_e('Each keyword will be a new article created', 'people-also-ask') ?>
                        </p>

                    </th>
                </tr>
                <tr>
                    <td>
                        <textarea name="palavra" id="palavra" rows="10" minlength="3" style="width: 100%;" autofocus required></textarea>
                        <p class="description"></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="text-align: right;">
            <span class="status-error" id="help_text"></span>
            &nbsp;&nbsp;&nbsp;&nbsp;    
            <input type="submit" name="submit" id="submit" class="button button-primary button-large" value="<?php esc_html_e('Save keywords', 'people-also-ask') ?>" />
        </div>
    </form>
</div>

<?php
require dirname(__FILE__) . '/people_also_ask-admin-table.php';
$keywords_list_table = new PeopleAlso_Keywords_Admin_Table();
$keywords_list_table->prepare_items();


?>
<div class="wrap">
    <form id="also-askd-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '' ) ) ); ?>" />
        <?php $keywords_list_table->display() ?>
    </form>
</div>