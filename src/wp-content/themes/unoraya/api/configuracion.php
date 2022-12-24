<?php

    // API para obtener la configuración del home
    function api_configuracion($request) {
        $isLogin = isTmpLogginUser();
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'configuraciones',
                'numberposts' => 1
            ));
            $posts = $query->get_posts();
            foreach ($posts as $key => $value) {
                $post_id = $value->ID;
    			$contenido = get_field('areas', $post_id);
    			$banner = get_field('banner', $post_id);
    			$bannerSmall = get_field('banner_small', $post_id);
    			$instructivo = get_field('instructivo', $post_id);
    			$maintenance = get_field('mantenimiento', $post_id);
    			$response[] = schemaConfiguracion($post_id, $banner, $bannerSmall, $instructivo, $contenido, $maintenance);
            }
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
        return rest_ensure_response($response);
    }

    function schemaConfiguracion($id, $banner, $bannerSmall, $instructivo, $contenido, $maintenance) {
		return array(
		    'id' => $id,
		    'banner' => $banner,
		    'banner_small' => $bannerSmall,
		    'contenido' => $contenido,
		    'instructivo' => $instructivo,
		    'maintenance' => $maintenance
		);
    }

    function registrar_api_configuracion() {
        register_rest_route('api', '/configuracion', array(
            array(
                'methods' => 'GET',
                'callback' => 'api_configuracion'
            ),
        ));
    }

    add_action('rest_api_init', 'registrar_api_configuracion');

?>