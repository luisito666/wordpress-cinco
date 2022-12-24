<?php

    // API para obtener los videos
    function api_videos($request) {
        $isLogin = isTmpLogginUser();
        $userId = $request['id'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'videos',
            ));
            $posts = $query->get_posts();
            foreach ($posts as $key => $value) {
                $post_id = $value->ID;
    			$imagen = get_field('imagen', $post_id);
			    $agencia = get_field('agencia', $post_id);
    			$response[] = schemaVideos($imagen, $agencia);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }
    
    function schemaVideos($imagen, $agencia) {
		return array(
		    'imagen' => $imagen,
		    'agencia' => $agencia
		);
    }
    
    // API para alamacenar la votacion de los videos
    function api_save_votacion($request) {
        $isLogin = isTmpLogginUser();
        $userId = $request['user'];
        $votos = $request['votos'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'resultados',
                'numberposts' => 1,
                'pagename' => $userId
            ));
            $posts = $query->get_posts();
            $response = updateVideoResultado($posts[0], $votos);
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }
    
    function updateVideoResultado($userId, $votos) {
        update_field( 'votos', $votos, $userId->ID );
        return array('id' => $userId->ID, 'vots' => $votos);
    }


    function registrar_api_videos() {
        register_rest_route('api', '/videos', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_videos'
            ),
        ));
    }
    
    add_action('rest_api_init', 'registrar_api_videos');
    
    function registrar_api_save_votacion() {
        register_rest_route('api', '/videos', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_save_votacion'
            ),
        ));
    }
    
    add_action('rest_api_init', 'registrar_api_save_votacion');