<?php
    // API para obtener el contenido de los casos
    function api_contenido($request) {
        $isLogin = isTmpLogginUser();
        $id = $request['id'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'cinco',
                'page_id' => $id,
                'numberposts' => 1
            ));
            $posts = $query->get_posts();
            foreach ($posts as $key => $value) {
                $post_id = $value->ID;
                $title = $value->post_title;
    			$contenido = get_field('contenido', $post_id);
    			$response[] = schemaContenido($post_id, $title, $contenido);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }

    function schemaContenido($id, $title, $contenido) {
		return array(
		    'id' => $id,
		    'title' => $title,
		    'contenido' => $contenido
		);
    }

    function registrar_api_contenido() {
        register_rest_route('api', '/cinco/(?P<id>[-\w]+)', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_contenido'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_contenido');

?>