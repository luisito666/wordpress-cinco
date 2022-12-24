<?php

    // API para obtener las agencias
    function api_agencias() {
        $response = array();
        $query = new WP_Query(array(
            'post_type' => 'agencias',
            'numberposts' => 1,
            'pagename' => 'Agencias'
        ));
        $posts = $query->get_posts();
        foreach ($posts as $key => $value) {
            $post_id = $value->ID;
			$agencias = get_field('agencias', $post_id);
			$response[] = schemaAgencia($agencias);
        }
        return rest_ensure_response($response);
    }

    function schemaAgencia($agencias) {
		return array(
		    'agencia' => $agencias
		);
    }

    function registrar_api_agencias() {
        register_rest_route('api', '/agencias', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_agencias'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_agencias');
    
?>