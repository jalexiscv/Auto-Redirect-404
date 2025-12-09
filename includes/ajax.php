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
        if(!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'preview_nonce')){
            wp_die();
        }
        
        // check permission
        if(!current_user_can('administrator')){
            wp_die();
        }
        
        // get request
        $request = isset($_POST['request']) ? esc_url_raw(wp_unslash($_POST['request'])) : '';
        
        // check request
        if(empty($request)){
            wp_die();
        }
        
        // do request
        $this->request($request, true);
        wp_die();
        
    }
    
}