<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect')){
    return;
}

trait WP_404_Auto_Redirect_Ajax{
    
    /**
     * preview
     *
     * @return void
     */
    function preview(){
        
        // check nonce
        if(!wp_verify_nonce($_POST['nonce'], 'preview_nonce')){
            wp_die();
        }
        
        // check permission
        if(!current_user_can('administrator')){
            wp_die();
        }
        
        // get request
        $request = esc_url($_POST['request']);
        
        // check request
        if(empty($request)){
            wp_die();
        }
        
        // do request
        $this->request($request, true);
        wp_die();
        
    }
    
}