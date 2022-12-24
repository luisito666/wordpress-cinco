<?php
    
    // API para guardar informaci贸n de resultados
    function api_resultados_post($request) {
        $isLogin = isTmpLogginUser();
        $userId = $request['user'];
        $nivel = $request['nivel'];
        $resultado = $request['resultado'];
        $area = $request['area'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'resultados',
                'numberposts' => 1,
                'pagename' => $userId
            ));
            $posts = $query->get_posts();
            if (!$posts)  {
                $response = saveResultados($userId, $nivel, $resultado, $area);
            } else {
                $response = updateResultados($posts[0], $nivel, $resultado, $area);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }
    
    function saveResultados($userId, $nivel, $resultado, $area) {
        $my_post = array(
            'post_type' => 'resultados',
            'post_title' => $userId,
            'post_status' => 'publish',
            'meta_input' => array(
                'nivel' => $nivel,
                'resultado' => $resultado,
                'area' => $area
            )
        );
        $res = wp_insert_post( $my_post );
        return array('res' => $res);
    }
    
    function updateResultados($userId, $nivel, $resultado, $area) {
        update_field( 'nivel', $nivel, $userId->ID );
        update_field( 'resultado', $resultado, $userId->ID );
        update_field( 'area', $area, $userId->ID );
        return array('res' => $userId->ID);
    }
    
    // API para obtener informaci贸n de resultados
    function api_resultados($request) {
        $isLogin = isTmpLogginUser();
        $userId = $request['id'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'resultados',
                'numberposts' => 1,
                'pagename' => $userId
            ));
            $posts = $query->get_posts();
            foreach ($posts as $key => $value) {
                $post_id = $value->ID;
    			$datos = array(
    			    'nivel' => get_field('nivel', $post_id),
    			    'area' => get_field('area', $post_id),
    			    'resultado' => get_field('resultado', $post_id),
    			    'evaluacion' => get_field('evaluacion', $post_id),
    			    'votos' => get_field('votos', $post_id)
    			);
    			$response[] = schemaResultados($datos);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }

    function schemaResultados($datos) {
		return array(
		    'nivel' => $datos['nivel'],
		    'area' => $datos['area'],
		    'resultado' => $datos['resultado'],
		    'evaluacion' => $datos['evaluacion'],
		    'votos' => $datos['votos']
		);
    }
    
    function registrar_api_resultados() {
        register_rest_route('api', '/resultados/(?P<id>[-\w]+)', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_resultados'
            ),
        ));
    }
    
    add_action('rest_api_init', 'registrar_api_resultados');
    
    function registrar_api_resultados_post() {
        register_rest_route('api', '/resultados', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_resultados_post'
            ),
        ));
    }
    
    add_action('rest_api_init', 'registrar_api_resultados_post');