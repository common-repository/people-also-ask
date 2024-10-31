<?php


function people_also_tz_strtodate($str, $to_timestamp = false)
{
    // This function behaves a bit like PHP's StrToTime() function, but taking into account the Wordpress site's timezone
    // CAUTION: It will throw an exception when it receives invalid input - please catch it accordingly
    // From https://mediarealm.com.au/

    $tz_string = get_option('timezone_string');
    $tz_offset = get_option('gmt_offset', 0);

    if (!empty($tz_string)) {
        // If site timezone option string exists, use it
        $timezone = $tz_string;
    } elseif ($tz_offset == 0) {
        // get UTC offset, if it isn’t set then return UTC
        $timezone = 'UTC';
    } else {
        $timezone = $tz_offset;

        if (substr($tz_offset, 0, 1) != '-' && substr($tz_offset, 0, 1) != '+' && substr($tz_offset, 0, 1) != 'U') {
            $timezone = '+' . $tz_offset;
        }
    }

    $datetime = new DateTime($str, new DateTimeZone($timezone));
    return $datetime->format($to_timestamp ? 'U' : 'Y-m-d H:i:s');
}

function people_also_startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function people_also_endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}



// function get_request_parameter($key, $default = '') {
//     // Primeiro, verifica se a chave existe e remove barras invertidas
//     if (isset($_REQUEST[$key])) {
//         $value = wp_unslash($_REQUEST[$key]);

//         // Caso seja um array, sanitiza cada valor do array
//         if (is_array($value)) {
//             return array_map(function ($v) {
//                 // Sanitiza o valor utilizando a função apropriada
//                 return sanitize_text_field((string) $v);
//             }, $value);
//         }

//         // Sanitiza o valor único utilizando a função apropriada
//         return sanitize_text_field((string) $value);
//     }

//     // Retorna o valor padrão se a chave não existir
//     return sanitize_text_field((string) $default);
// }

function people_also_extractDomain($url) {
    // Parse the URL and extract its components
    $parsedUrl = wp_parse_url($url);

    // Return the host component of the parsed URL
    return $parsedUrl['host'];
}

function people_also_reverte_string($data) {    
    return strrev($data);
}

function people_also_user_ip() {

    // Inicializar a variável $ip
    $ip = '';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        // Verificar o IP compartilhado pela internet
        $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
    
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        // Verificar o IP passado pelo proxy (pode haver múltiplos IPs separados por vírgula)
        $ip_list = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        $ip_array = explode( ',', $ip_list );

        // O primeiro IP da lista geralmente é o IP original do cliente
        $ip = trim( sanitize_text_field( $ip_array[0] ) );
    
    } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
        // Usar o IP remoto, caso esteja definido
        $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }

    // Validar se o IP é válido e retornar. Se não for válido, retornar uma string vazia
    return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}
