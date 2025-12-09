<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect_Settings')):

class WP_404_Auto_Redirect_Settings{
    
    /**
     * get
     *
     * @return array
     */
    function get(){
        
        $option = get_option('ar404_settings');
        
        // 0.9.0.2 Deprecated compatibility
        if(!ar404_is_empty($option['rules']['redirection']['exclude'])){
            
            $option['rules']['exclude'] = $option['rules']['redirection']['exclude'];
            unset($option['rules']['redirection']['exclude']);
            
        }
        
        // 0.9.0.2 Deprecated compatibility
        if(!ar404_is_empty($option['rules']['redirection']['disable'])){
            
            $option['rules']['disable'] = $option['rules']['redirection']['disable'];
            unset($option['rules']['redirection']['disable']);
            
        }
        
        // 0.9.0.2 Deprecated compatibility
        if(isset($option['rules']['redirection'])){
            unset($option['rules']['redirection']);
        }
        
        // Defaults
        $settings = ar404_parse_args_recursive($option, array(
            'debug'     => null,
            'headers'   => null,
            'log'       => null,
            'method'    => 301,
            'priority'  => 999,
            'fallback'  => array(
                'type'      => 'home',
                'url'       => home_url(),
                'home_url'  => home_url(),
            ),
            'rules'     => array(
                'include'   => array(
                    'post_types'    => array(),
                    'taxonomies'    => array(),
                ),
                'exclude'   => array(
                    'post_meta'     => null,
                    'term_meta'     => null,
                    'post_types'    => array(),
                    'taxonomies'    => array(),
                ),
                'disable'   => array(
                    'taxonomies'    => null
                )
            )
        ));
        
        // Include
        $settings['rules']['include']['post_types'] = ar404_get_post_types($settings);
        $settings['rules']['include']['taxonomies'] = ar404_get_taxonomies($settings);
        
        // Falback
        if($settings['fallback']['type'] == 'home'){
            $settings['fallback']['url'] = home_url();
        }
        
        // Esc Fallback
        $settings['fallback']['url'] = esc_url($settings['fallback']['url']);
        
        // Esc Priority
        $settings['priority'] = (int) $settings['priority'];
        
        // Headers
        if(((int)$settings['method'] != 301) && ((int)$settings['method'] != 302)){
            $settings['method'] = 301;
        }
        
        // Return
        return $settings;
        
    }
    
}

ar404()->settings = new WP_404_Auto_Redirect_Settings();

endif;


/**
 * ar404_settings_get
 *
 * @return mixed
 */
function ar404_settings_get(){
    return ar404()->settings->get();
}