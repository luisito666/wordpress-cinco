<?php
    $template_directorio = get_template_directory();
    require_once($template_directorio . '/api/contenido.php');
    require_once($template_directorio . '/api/configuracion.php');
    require_once($template_directorio . '/api/usuario.php');
    require_once($template_directorio . '/api/resultados.php');
    require_once($template_directorio . '/api/evaluacion.php');
    require_once($template_directorio . '/api/cuestionario.php');
    require_once($template_directorio . '/api/videos.php');
    require_once($template_directorio . '/api/agencias.php');
    
    // Tiempo de session
    function expire_token() {
        return time() + (60 * 60 * 24);
    }
    add_action('jwt_auth_expire', 'expire_token');

    // Campo extras para el usuario
    add_filter('user_contactmethods','add_field_user');
    function add_field_user( $arr ) {
        $arr['regional'] = __('Regional');
        return $arr;
    }

	// Se agregan cabeceras de seguridad
	add_action( 'send_headers', 'add_header_seguridad' );
	function add_header_seguridad() {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-XSS-Protection: 1;mode=block' );
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
		header( "Content-Security-Policy: default-src 'self' 'unsafe-eval' 'unsafe-hashes' 'unsafe-inline' data: blob: https://unoraya.com https://backend.cincobmm.com https://www.cincobmm.com");
		header( 'Referrer-Policy: strict-origin' );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS' );
		header( "Feature-Policy: microphone 'none'; geolocation 'none'" );
	}

	// Remover X-Powered-By
	add_action('wp', 'jltwp_adminify_remove_powered');
	function jltwp_adminify_remove_powered()
	{
		if (function_exists('header_remove')) {
			header_remove('x-powered-by');
		}
	}
    
    function mod_jwt_auth_token_before_dispatch( $data, $user ) {
        $user_info = get_user_by( 'email',  $user->data->user_email );
        $regional = get_user_meta($user_info->id, 'regional');
        $profile = array (
            'user_email' => $user->data->user_email,
            'user_display_name' => $user->data->display_name,
            'regional' => $regional[0],
        );
        $response = array(
            'token' => $data['token'],
            'profile' => $profile
        );
        return $response;
    }
    add_filter( 'jwt_auth_token_before_dispatch', 'mod_jwt_auth_token_before_dispatch', 10, 2 );
    
    
    add_action('init', 'isTmpLogginUser');
    function isTmpLogginUser(){
        $user_ID= get_current_user_id();
        return true;
        //return $user_ID;
    }
    
    
/**
     * Cabeceras para servicios Rest
     * 
    */
    add_action( 'rest_api_init', function() {
    	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    	add_filter( 'rest_pre_serve_request', function( $value ) {
    	    header( 'Access-Control-Allow-Origin: https://www.cincobmm.com');
    	    header( 'Access-Control-Allow-Methods: POST, GET' );
    		header( 'X-Content-Type-Options: nosniff' );
    		header( 'X-Frame-Options: SAMEORIGIN' );
    		header( 'X-XSS-Protection: 1;mode=block' );
    		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
    		header( 'Referrer-Policy: strict-origin' );
    		header( 'Content-Security-Policy: frame-ancestors "none"' );
    		header( "Feature-Policy: microphone 'none'; geolocation 'none'" );
    		return $value;
    	});
    }, 15 );
