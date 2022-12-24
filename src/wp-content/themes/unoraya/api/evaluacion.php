<?php
    
    // API para guardar el resultado de la evaluacion
    function api_evaluacion_post($request) {
        $isLogin = isTmpLogginUser();
        $userId = $request['user'];
        $evaluacion = $request['evaluacion'];
        $response = array();
        if ($isLogin) {
            $query = new WP_Query(array(
                'post_type' => 'resultados',
                'numberposts' => 1,
                'pagename' => $userId
            ));
            $posts = $query->get_posts();
            $response = updateEvaluacion($posts[0], $evaluacion);
        } else {
            $response = new WP_Error('permisos', 'Usuario sin permisos', array('status' => 401));
        }
       return rest_ensure_response($response);
    }
    
    function updateEvaluacion($userId, $evaluacion) {
        update_field( 'evaluacion', $evaluacion, $userId->ID );
        return array('res' => $userId->ID);
    }
    
    function registrar_api_evaluacion_post() {
        register_rest_route('api', '/evaluacion', array(
            array(
                'methods' => 'POST',
                'callback' => 'api_evaluacion_post'
            ),
        ));
    }
    
    add_action('rest_api_init', 'registrar_api_evaluacion_post');

?>