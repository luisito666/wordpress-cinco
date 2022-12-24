<?php

    // API para obtener las preguntas del cuestionario
    function api_cuestionario() {
        $isLogin = isTmpLogginUser();
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'evaluaciones',
                'numberposts' => 1
            ));
            $posts = $query->get_posts();
            foreach ($posts as $key => $value) {
                $post_id = $value->ID;
    			$contenido = get_field('preguntas', $post_id);
    			$response[] = schemaCuestionario($contenido);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
        return rest_ensure_response($response);
    }

    function schemaCuestionario($contenido) {
		return array(
		    'contenido' => $contenido
		);
    }

    function registrar_api_cuestionario() {
        register_rest_route('api', '/cuestionario', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_cuestionario'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_cuestionario');

?>