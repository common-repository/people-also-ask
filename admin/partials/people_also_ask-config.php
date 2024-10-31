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

global $wpdb;

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
$display_view_more = '';
$display_request_removal = '';

   
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
        else if ($item->chave == 'display-view-more')
            $display_view_more = $item->valor;
        else if ($item->chave == 'display-request-removal')
            $display_request_removal = $item->valor;

    }

}


?>



<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form id="item_form_config" class="form-table" method="post" autocomplete="off">



        <?php wp_nonce_field('salvar_form', 'meu_form_nonce'); ?>

        
    
            

        <div id="avisoPremium">

            <?php if ($config_serial <> '') { ?>
                
                <h1>
                    <a href="<?php esc_html_e('https://peoplealsoaskplugin.com', 'people-also-ask') ?>" target="_blank">
                        <strong>People Also Ask <i>Premium</i></strong>
                    </a>
                </h1>
                <p><?php esc_html_e('The Demo version of the plugin allows you to generate 25 posts', 'people-also-ask') ?></p>

            <?php } else { ?>
                
                <h1>                
                    <?php esc_html_e('Use the version', 'people-also-ask') ?> <a href="<?php esc_html_e('https://peoplealsoaskplugin.com', 'people-also-ask') ?>" target="_blank"><strong>People Also Ask <i>Premium</i></strong></a>
                </h1>

                <p><?php esc_html_e('The Demo version of the plugin allows you to generate 25 posts', 'people-also-ask') ?></p>
                <p><?php wp_kses_post(__('The <strong><i>Premium</i></strong> version allows you to generate unlimited posts!', 'people-also-ask')) ?></p>

                
            <?php } ?>

            <table style="width: 100%; margin-top: 2em;">
            <tbody>
            
                <tr>
                    <th scope="row"><label for="serial"><?php esc_html_e('Serial: People Also Ask Premium', 'people-also-ask') ?> </label></th>
                    <td>
                        

                        <?php if ($config_serial <> '') { ?>
                            <input name="serial" type="password" id="serial"  autocomplete="off" value="<?php echo esc_attr($config_serial); ?>" class="regular-text" autofocus  />
                        <?php } else { ?>
                            <input name="serial" type="text" id="serial"  autocomplete="off" value="<?php echo esc_attr($config_serial); ?>" class="regular-text" autofocus  />
                        <?php } ?>


                        <p><?php esc_html_e('(optional)', 'people-also-ask') ?></p>

                    </td>
                </tr>
                

                <?php if ($config_serial == '') { ?>
                    
                    <a href="<?php esc_html_e('https://peoplealsoaskplugin.com', 'people-also-ask') ?>" target="_blank" class="btn button-primary"><?php esc_html_e('Get the Premium version now!', 'people-also-ask') ?></a>    

                <?php } ?>
        
            
            </tbody>
        </table>


        </div>


        <table style="width: 100%; margin-top: 2em;">
            <tbody>
               
                <tr>
                    <th scope="row"><label for="api-scrapper"><?php esc_html_e('Scrapper API', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="api-scrapper"  id="api-scrapper" onchange="exibeCamposCredenciais(this);">
                            <option value="0"><?php esc_html_e('Select', 'people-also-ask') ?></option>
                            <option value="dataforseo" <?php echo (($config_api_scrapper == 'dataforseo') ? "selected" : "") ?>>DataForSEO</option>
                            <option value="serpapi" <?php echo (($config_api_scrapper == 'serpapi') ? "selected" : "") ?>>SerpAPI</option>
                        </select>


                        <p class="description">
                            <?php esc_html_e('Questions are scraped from Google through Scrapping services, which use Proxies to avoid blocking', 'people-also-ask') ?><br /><br />
                            <?php esc_html_e('We recommend that you test the 2 APIs before purchasing your plans. Both offer some free credits:', 'people-also-ask') ?><br /><br />

                            <?php wp_kses_post(__('<strong>SerpAPI:</strong> Faster, more expensive', 'people-also-ask')) ?><br />
                            <?php wp_kses_post(__('<strong>DataForSEO:</strong> Slower, cheaper', 'people-also-ask')) ?>
                            
                        </p>
                        
                    </td>
                </tr>

                <tr class="tr-serpapi">
                    <th scope="row"><label for="chave-serpapi"><?php esc_html_e('SERP API Key', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>
                        <input name="chave-serpapi" type="password" id="chave-serpapi" value="<?php echo esc_attr($config_chave_serp_api); ?>" class="regular-text" autofocus  />
                        <p class="description">
                            <a href="https://serpapi.com/manage-api-key" target="_blank">
                                <?php esc_html_e('Click here to get your API Key from SerpAPI', 'people-also-ask') ?>
                            </a> <br /> <?php esc_html_e('Get 100 free credits upon registration', 'people-also-ask') ?>
                        </p>
                    </td>
                </tr>

                <tr class="tr-dataforseo">
                    <th scope="row"><label for="usuario-dataforseo"><?php esc_html_e('DataForSEO - API login', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>
                        <input name="usuario-dataforseo" type="text" id="usuario-dataforseo" value="<?php echo esc_attr($config_usuario_data_for_seo); ?>" class="regular-text" autofocus />
                        <p class="description">
                            <a href="https://app.dataforseo.com/api-access" target="_blank">
                                <?php esc_html_e('Click here to get your username and password at DataForSEO', 'people-also-ask') ?>
                            </a> <br /> <?php esc_html_e('Get 400 free credits upon registration', 'people-also-ask') ?>                            
                        </p>

                    </td>
                </tr>

                <tr class="tr-dataforseo">

                    <th scope="row"><label for="senha-dataforseo"><?php esc_html_e('DataForSEO - API password', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>
                        <input name="senha-dataforseo" type="password" id="senha-dataforseo" value="<?php echo esc_attr($config_senha_data_for_seo); ?>" class="regular-text" autofocus />                        
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="google-country"><?php esc_html_e('Google Language', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>


                        <select name="google-country"  id="google-country" >
                            <?php

                                $dados_google_countries = $wpdb->get_results(" SELECT domain, location_name FROM ".$wpdb->prefix."people_also_askeds_config_countries ORDER BY location_name; ");

                                if (count($dados_google_countries) > 0) {
                                            
                                    foreach ( $dados_google_countries as $item ) {
                                        
                                        if ($config_google_country == $item->domain)
                                            echo '<option value="'. esc_attr($item->domain) .'" selected>'.esc_attr($item->domain).' ('.esc_attr($item->location_name).')</option>';
                                        else
                                            echo '<option value="'. esc_attr($item->domain) .'">'.esc_attr($item->domain).' ('.esc_attr($item->location_name).')</option>';

                                    }

                                }

                            ?>
                        </select>

                        <p class="description"><?php esc_html_e('Google language where questions will be scraped', 'people-also-ask') ?></p>                        
                        
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="status-posts"><?php esc_html_e('Post Status', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="status-posts" id="status-posts" >
                            <option value="automatico-publicado" <?php echo (($config_status_posts == 'automatico-publicado') ? "selected" : "") ?>><?php esc_html_e('Published', 'people-also-ask') ?></option>
                            <option value="automatico-rascunho" <?php echo (($config_status_posts == 'automatico-rascunho') ? "selected" : "") ?>><?php esc_html_e('Draft', 'people-also-ask') ?></option>
                        </select>
                        <p class="description"><?php esc_html_e('Default status of posts when they are created', 'people-also-ask') ?></p>
                        
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="categoria-post"><?php esc_html_e('Category', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>


                        <?php
                            $args = array(
                            'hierarchical' => 1,
                            'hide_empty' => 0,
                            'name' => 'categoria-post',
                            'id' => 'categoria-post',
                            'selected' => $config_categoria_post,
                            'show_option_none' => __('None', 'people-also-ask'),
                            );
                            wp_dropdown_categories( $args );
                        ?>
                      
                        <p class="description"><?php esc_html_e('Default category for posts when they are created', 'people-also-ask') ?></p>
                        
                    </td>
                </tr>
                

                <tr>
                    <th scope="row"><label for="niveis-busca"><?php esc_html_e('Search Depth', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>
                        
                        <select name="niveis-busca"  id="niveis-busca">
                            <option value="1" <?php echo (($config_niveis_busca == '1') ? "selected" : "") ?>>1 <?php esc_html_e('level search', 'people-also-ask') ?></option>
                            <option value="2" <?php echo (($config_niveis_busca == '2') ? "selected" : "") ?>>2 <?php esc_html_e('levels search', 'people-also-ask') ?></option>
                        </select>

                        <p class="description">
                            <?php wp_kses_post(__('<strong>1 Search level:</strong> Spends 1 credit per article. Content has 4 to 6 H2 headings on average', 'people-also-ask')) ?>
                            <br />
                            <?php wp_kses_post(__('<strong>2 Search levels:</strong> Spend 5 credits per article. Content has 20 to 30 H2/H3 headings on average', 'people-also-ask')) ?>                            
                        </p>

                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="hierarquia-post"><?php esc_html_e('Post Hierarchy', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="hierarquia-post"  id="hierarquia-post" >
                            <option value="h1-h2" <?php echo (($config_hierarquia_post == 'h1-h2') ? "selected" : "") ?>>H1 > H2</option>
                            <option value="h1-h2-h3" <?php echo (($config_hierarquia_post == 'h1-h2-h3') ? "selected" : "") ?>>H1 > H2 > H3</option>
                        </select>
                        <p class="description">
                            <?php wp_kses_post(__('<strong>H1 > H2:</strong> Create the heading H1 = Keyword, and the other questions as H2', 'people-also-ask')) ?>
                            <br />
                            <?php wp_kses_post(__('<strong>H1 > H2 > H3:</strong> Creates the header H1 = Keyword, the child questions as H2 and the grandchild questions as H3', 'people-also-ask')) ?>
                        </p>
                        
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="max-concurrent-requests"><?php esc_html_e('Maximum number of simultaneous requests', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="max-concurrent-requests"  id="max-concurrent-requests" >
                            <option value="1" <?php echo (($max_concurrent_requests == '1') ? "selected" : "") ?>>1</option>
                            <option value="2" <?php echo (($max_concurrent_requests == '2') ? "selected" : "") ?>>2</option>
                            <option value="3" <?php echo (($max_concurrent_requests == '3') ? "selected" : "") ?>>3</option>
                            <option value="4" <?php echo (($max_concurrent_requests == '4') ? "selected" : "") ?>>4</option>
                            <option value="5" <?php echo (($max_concurrent_requests == '5') ? "selected" : "") ?>>5</option>
                            <option value="6" <?php echo (($max_concurrent_requests == '6') ? "selected" : "") ?>>6</option>
                            <option value="7" <?php echo (($max_concurrent_requests == '7') ? "selected" : "") ?>>7</option>
                            <option value="8" <?php echo (($max_concurrent_requests == '8') ? "selected" : "") ?>>8</option>
                            <option value="9" <?php echo (($max_concurrent_requests == '9') ? "selected" : "") ?>>9</option>
                            <option value="10" <?php echo (($max_concurrent_requests == '10') ? "selected" : "") ?>>10</option>
                        </select>
                        
                        <p class="description"><?php wp_kses_post(__('<strong>Attention:</strong> Very high numbers can overload your server, or generate blockages in the Scrapping API\'s', 'people-also-ask')) ?></p>
                        
                    </td>
                </tr>
                

                <tr>
                    <th scope="row"><label for="incluir-video-post"><?php esc_html_e('Include YouTube video', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="incluir-video-post"  id="incluir-video-post" >
                            <option value="sim" <?php echo (($config_incluir_video_post == 'sim') ? "selected" : "") ?>><?php esc_html_e('Yes', 'people-also-ask') ?></option>
                            <option value="nao" <?php echo (($config_incluir_video_post == 'nao') ? "selected" : "") ?>><?php esc_html_e('No', 'people-also-ask') ?></option>
                        </select>

                        <p class="description"><?php esc_html_e('Whether videos will be included in posts', 'people-also-ask') ?></p>
                        
                    </td>
                </tr>

                <tr>
                    <th scope="row" nowrap="nowrap"><label for="display-view-more"><?php esc_html_e('Display link "See full answer at..."', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="display-view-more"  id="display-view-more" >
                            <option value="sim" <?php echo (($display_view_more == 'sim') ? "selected" : "") ?>><?php esc_html_e('Yes', 'people-also-ask') ?></option>
                            <option value="nao" <?php echo (($display_view_more == 'nao') ? "selected" : "") ?>><?php esc_html_e('No', 'people-also-ask') ?></option>
                        </select>

                        <p class="description"><?php esc_html_e('Whether to display \'See full answer at....\' links in Posts', 'people-also-ask') ?></p>
                        
                    </td>
                </tr>

                <tr>
                    <th scope="row" nowrap="nowrap"><label for="display-request-removal"><?php esc_html_e('Display link "Removal Request"', 'people-also-ask') ?> <code class="status-error" title="<?php esc_html_e('Require', 'people-also-ask') ?>">*</code></label></th>
                    <td>

                        <select name="display-request-removal"  id="display-request-removal" >
                            <option value="sim" <?php echo (($display_request_removal == 'sim') ? "selected" : "") ?>><?php esc_html_e('Yes', 'people-also-ask') ?></option>
                            <option value="nao" <?php echo (($display_request_removal == 'nao') ? "selected" : "") ?>><?php esc_html_e('No', 'people-also-ask') ?></option>
                        </select>

                        <p class="description"><?php esc_html_e('Whether \'Request to Remove\' links will be displayed on Posts', 'people-also-ask') ?></p>
                        
                    </td>
                </tr>

                <tr>

                    <th scope="row"><label for="pagina-soliticacao-remocao"><?php esc_html_e('Removal request page', 'people-also-ask') ?></label></th>
                    <td>
                        <input name="pagina-soliticacao-remocao" type="text"  id="pagina-soliticacao-remocao" value="<?php echo esc_attr(get_home_url()) ?>/<?php esc_html_e("removal-request", 'people-also-ask') ?>" class="regular-text" readonly />
                        <p class="description"><?php esc_html_e("Create a page", 'people-also-ask') ?> "<?php echo esc_attr(get_home_url()) ?>/<?php esc_html_e("removal-request", 'people-also-ask') ?>" <?php __("on the Blog, and include a contact form on it", 'people-also-ask') ?></p>
                    </td>
                </tr>
                
                
            </tbody>
        </table>

        <div>
            <span class="status-error" id="help_text"></span>
            <input type="submit" name="submit" id="submit" class="button button-primary button-large" value="<?php esc_html_e('Save', 'people-also-ask') ?>" />
        </div>
    </form>
</div>



