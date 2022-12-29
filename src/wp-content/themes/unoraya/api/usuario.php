<?php
    // API para hacer el registro de usuario
    function api_usuario($request) {
        $user_name = $request['username'];
        $user_email = $request['user_email'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $regional = $request['regional'];
        $user_id = username_exists( $user_name );
        if ( ! $user_id && false == email_exists( $user_email ) ) {
            $userdata = array(
                'user_login' => $user_name,
                'user_pass' => $user_name,
                'role' => 'subscriber',
                'user_email' => $user_email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'meta_input' => array(
                    'regional' => $regional
                )
            );
            wp_insert_user( $userdata );
            $response = array(
                'msj' => 'Registro completo'
            );
        } else {
            $response = new WP_Error('found', 'Usuario ya existe', array('status' => 401));
        }
        return rest_ensure_response($response);
    }
    
    function registrar_api_usuario() {
        register_rest_route('api', '/register', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_usuario'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_usuario');
    
    
    
    
    // API PARA OBTENER USUARIOS
    function api_get_usuario() {
        $usersData = get_users( array( 'role__in' => array( 'subscriber' ) ) );
        $response = array();
        foreach ($usersData as $key => $value) {
            $user_meta = get_user_meta( $value->ID );
            $id = $value->user_login;
            $name = $value->display_name;
            $response[] = schemaUsers($user_meta, $id, $name);
        }
        return rest_ensure_response($response);
    }
    
    function schemaUsers($user_meta, $id, $name) {
        $resultByUser = getResultsPosts($id);
        $nameAgency = getAgencyPosts($user_meta['regional'][0]);
        $result = array(
            'cedula' => $id,
            'name' => $name,
            'agency' => $nameAgency,
            'results' => $resultByUser
        );
        return $result;
    }
    
    function getAgencyPosts($idAgency) {
        $query = new WP_Query(array(
            'post_type' => 'agencias',
            'numberposts' => 1,
            'pagename' => 'Agencias'
        ));
        $posts = $query->get_posts();
        $nameAgency = '';
        foreach ($posts as $key => $value) {
            $post_id = $value->ID;
			$agencias = get_field('agencias', $post_id);
			foreach ($agencias as $keyA => $valueA) {
			    if ($valueA['id'] == $idAgency) {
			        $nameAgency = $valueA['nombre'];
			        return $nameAgency;
			    }
			}
        }
        return $idAgency;
    }
    
    function getResultsPosts($userId) {
        $query = new WP_Query(array(
            'post_type' => 'resultados',
            'numberposts' => 1,
            'pagename' => $userId
        ));
        $posts = $query->get_posts();
        foreach ($posts as $key => $value) {
            $post_id = $value->ID;
			$datos = array(
			    'area' => get_field('nivel', $post_id),
			    'subarea' => get_field('area', $post_id),
			    'caso' => get_field('resultado', $post_id),
			    'evaluacion' => get_field('evaluacion', $post_id),
			    'votos' => get_field('votos', $post_id)
			);
        }
        return $datos;
    }
    
    
    function get_api_usuario() {
        register_rest_route('api', '/users', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_get_usuario'
            ),
        ));
    }

    add_action('rest_api_init', 'get_api_usuario');
    
    
    

?>