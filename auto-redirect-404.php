<?php
/**
 * Plugin Name: Auto Redirect 404-301
 * Plugin URI:  https://github.com/jalexiscv
 * Description: Automatically Redirect any 404 page to a Similar Post based on the Title, Post Type & Taxonomy using 301 Redirects!
 * Version: 	1.1.1
 * Author: 		Jose Alexis Correa Valencia
 * Author URI: 	https://alterplex.net
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: auto-redirect-404-301
 */
 
if(!defined('ABSPATH')){
    exit;
}

if(!defined('AR404_PATH')){
    define('AR404_PATH', plugin_dir_path(__FILE__));
}

if(!defined('AR404_FILE')){
    define('AR404_FILE', __FILE__);
}

if(!class_exists('WP_404_Auto_Redirect')):

// includes
include_once(AR404_PATH . 'includes/admin.php');
include_once(AR404_PATH . 'includes/ajax.php');
include_once(AR404_PATH . 'includes/debug.php');

class WP_404_Auto_Redirect{
    
    /**
     * traits
     */
    Use WP_404_Auto_Redirect_Admin;
    Use WP_404_Auto_Redirect_Ajax;
    Use WP_404_Auto_Redirect_Debug;
    
    public $engines;
    public $groups;
    public $search;
    public $settings;
    
    
    /**
     * construct
     */
    function __construct(){
        // ...
    }
    
    
    /**
     * init
     *
     *****************************************************************************
     *  Filters & Actions Fire Sequence:
     *****************************************************************************
     *                                                                           *
     *  filter('ar404/init',                    true,       $request      )  *
     *                                                                           *
     *  action('ar404/search/init',             $query                    )  *
     *  filter('ar404/search/group',            $group,     $query        )  *
     *  filter('ar404/search/query',            $query                    )  *
     *  filter('ar404/search/engine/{engine}',  $result,    $query, $group)  *
     *  filter('ar404/search/results',          $query                    )  *
     *  filter('ar404/search/redirect',         $redirect,  $query        )  *
     *                                                                           *
     *  filter('ar404/redirect',                $query                    )  *
     *  action('ar404/after_redirect',          $query                    )  *
     *                                                                           *
     *****************************************************************************
     *
     * @return void
     */
    function init(){
        
        // Helpers
        include_once(AR404_PATH . 'includes/helpers.php');
        
        // Classes
        include_once(AR404_PATH . 'class/class-engines.php');
        include_once(AR404_PATH . 'class/class-groups.php');
        include_once(AR404_PATH . 'class/class-search.php');
        include_once(AR404_PATH . 'class/class-settings.php');
        
        // WP: Admin
        add_action('admin_menu',                     array($this, 'admin_menu'),     10, 1);
        add_filter('plugin_action_links',            array($this, 'admin_link'),     10, 2);
        add_action('admin_init',                     array($this, 'admin_settings'), 10, 1);
        add_action('admin_enqueue_scripts',          array($this, 'admin_scripts'),  10, 1);
        
        // WP: Run
        add_action('template_redirect',              array($this, 'run'),            $this->priority());
        
        // Preview
        add_action('wp_ajax_ar404_ajax_preview', array($this, 'preview'),        1, 1);
        
        // Log
        add_action('ar404/after_redirect',       array($this, 'log'),            1, 1);
        
    }
    
    
    /**
     * priority
     *
     * @return int
     */
    function priority(){
        
        $priority = 999;
        $ar404_settings = get_option('ar404_settings');
        
        if(isset($ar404_settings['priority'])){
            $priority = (int) $ar404_settings['priority'];
        }
        
        return $priority;
        
    }
    
    
    /**
     * run
     *
     * @return void
     */
    function run(){
        
        // is 404
        if(!is_404() || wp_doing_ajax() || is_admin() || !isset($_SERVER['REQUEST_URI']) || ar404_is_empty($_SERVER['REQUEST_URI'])){
            return;
        }
        
        // admin ajax
        if(isset($_SERVER['SCRIPT_URI']) && !ar404_is_empty($_SERVER['SCRIPT_URI']) && sanitize_url(wp_unslash($_SERVER['SCRIPT_URI'])) == admin_url('admin-ajax.php')){
            return;
        }
        
        // xml request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !ar404_is_empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REQUESTED_WITH']))) == 'xmlhttprequest'){
            return;
        }
        
        // Sanitize Request
        $request = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])); // sanitize url
        $request = urldecode($request);                              // decode to remove %20 etc...
        $request = esc_html($request);                               // escape output for display
        
        if(empty($request)){
            return;
        }
        
        // do request
        $this->request($request);
        
    }
    
    
    /**
     * request
     *
     * @param $request
     * @param $preview
     *
     * @return void
     */
    function request($request, $preview = false){
        
        // Pathinfo
        $path = pathinfo(strtok($request, '?'));
        $path['dirname'] = str_replace('\\', '/', $path['dirname']);
        
        // Params
        $params = array();
        $request_parts = parse_url($request);
        
        if(!ar404_is_empty($request_parts['query'])){
            wp_parse_str($request_parts['query'], $params);
        }
        
        // Query
        $query = array(
            'preview'   => $preview,
            'request'   => array(
                'url'       => $request,
                'referrer'  => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_REFERER'])) : false,
                'dirname'   => $path['dirname'],
                'filename'  => $path['filename'],
                'extension' => (!ar404_is_empty($path['extension']) ? $path['extension'] : ''),
                'params'    => $params,
                'keywords'  => array(
                    'all'   => '',
                    'array' => array()
                ),
            )
        );
        
        // Remove Params in URL
        $url = strtok($query['request']['url'], '?');
        
        // Keywords: Sanitize
        $query['request']['keywords']['all'] = str_replace('.' . $query['request']['extension'], '', $url);
        $query['request']['keywords']['all'] = ar404_sanitize($query['request']['keywords']['all']);
        
        // Keywords: Explode Array
        $keywords = explode('/', trim($url, '/'));
        
        foreach($keywords as $keyword){
            
            if(!ar404_is_empty($query['request']['extension'])){
                $keyword = str_replace('.' . $query['request']['extension'], '', $keyword);
            }
            
            $query['request']['keywords']['array'][] = ar404_sanitize($keyword);
            
        }
        
        // Keywords: Reverse for priority (last part is probably the most important)
        $query['request']['keywords']['array'] = array_reverse($query['request']['keywords']['array']);
        
        // WP Query
        global $wp_query;
        
        if(isset($wp_query->query_vars)){
            
            $query_vars = $wp_query->query_vars;
            
            // WP Query: Post Type found
            if(!ar404_is_empty($query_vars['post_type']) && !ar404_is_empty($query_vars['name'])){
                
                $query['request']['wp_query']['post_type'] = $query_vars['post_type'];
                $query['request']['wp_query']['name'] = $query_vars['name'];
                
            }
            
            // WP Query: Taxonomy found
            if(!ar404_is_empty($query_vars['taxonomy']) && !ar404_is_empty($query_vars['term'])){
                
                $query['request']['wp_query']['taxonomy'] = $query_vars['taxonomy'];
                $query['request']['wp_query']['term'] = $query_vars['term'];
                
            }
            
        }
        
        // Settings
        $query['settings'] = ar404_settings_get($query);
        
        // Filter init
        if(!apply_filters('ar404/init', true, $query)){
            return;
        }
        
        // Search
        $this->search($query);
        
    }
    
    
    /**
     * search
     *
     * @param $query
     *
     * @return void
     */
    function search($query){
        
        // init Engines & Groups
        do_action('ar404/search/init', $query);
        
        // add Engines & Groups
        $query['engines'] = ar404()->engines->get_engines;
        $query['groups'] = ar404()->groups->get_groups;
        
        // init Search
        $query['search'] = array();
        
        // init Search Group
        $query['search']['group'] = apply_filters('ar404/search/group', 'default', $query);
        
        // Filter Query
        $query = apply_filters('ar404/search/query', $query);
        
        // Run Search
        if(!empty($query['groups']) && !empty($query['search']['group'])){
            
            foreach($query['groups'] as $g => $group){
                
                if($group['slug'] != $query['search']['group']){
                    continue;
                }
            
                if(empty($query['engines']) || empty($query['groups'][$g]['engines'])){
                    break;
                }
                
                foreach($query['groups'][$g]['engines'] as $e_slug){
                    
                    if(!$engine = ar404_get_engine_by_slug($e_slug)){
                        continue;
                    }
                    
                    if(!$result = apply_filters('ar404/search/engine/' . $engine['slug'], false, $query, $group)){
                        continue;
                    }
                    
                    $result = ar404_set_result($result, $engine);
                    if(!$result){
                        continue;
                    }
                    
                    $query['search']['results'][] = $result;
                
                    // Stop Search if Engine's Primary = true AND Score > 0
                    if($result['score'] > 0 && $result['primary']){
                        break;
                    }
                    
                }
                
                break;
            
            }
            
        }
        
        // Filter Search Results
        $query['search'] = apply_filters('ar404/search/results', $query['search'], $query);
        
        // init Redirection
        $query['redirect'] = false;
        
        // Redirection by highest score
        if(!empty($query['search']['results'])){
            
            $s=0; foreach($query['search']['results'] as $r){
                
                if($r['score'] > $s){
                    $query['redirect'] = $r;
                }
                
                // Stop if engine = primary
                if($r['score'] > 0 && $r['primary'] === true){
                    break;
                }
                
                $s = $r['score'];
                
            }
            
        }
        
        // Redirection fallback
        if(!$query['redirect']){
            
            $fallback = $query['settings']['fallback']['url'];
            
            if($query['settings']['fallback']['type'] == 'disabled'){
                $fallback = false;
            }
            
            $engine = array(
                'name' => 'None',
                'slug' => 'none'
            );
            
            $query['redirect'] = ar404_set_result(array(
                'url'   => $fallback,
                'score' => 0,
                'why'   => "Nothing found. Applying fallback behavior."
            ), $engine);
            
        }
        
        // Filter Search Redirect
        $query['redirect'] = apply_filters('ar404/search/redirect', $query['redirect'], $query);
        
        // Redirect
        $this->redirect($query);
        
    }
    
    
    /**
     * redirect
     *
     * @param $query
     *
     * @return void|null
     */
    function redirect($query){
        
        // Filter: ar404/redirect
        $query = apply_filters('ar404/redirect', $query);
        
        // Debug
        if(is_user_logged_in() && current_user_can('administrator') && ($query['settings']['debug'] || $query['preview'])){
            return $this->debug($query);
        }
        
        // Fallback: 404
        if(!$query['redirect']['url']){
            return;
        }
        
        // Redirect
        $this->redirect_to($query);
        
        return;
        
    }
    
    
    /**
     * redirect_to
     *
     * @param $query
     *
     * @return false|void
     */
    function redirect_to($query){
        
        // Copy/paste from legacy WP_Redirect function()
        // File: wp-includes/pluggable.php
        
        // Added: 'Auto-Redirect-404: true' header
        // Added: 'ar404/after_redirect' action
        // Added: PHP exit;
        
        global $is_IIS;
        
        $status = $query['settings']['method'];
        $location = apply_filters('wp_redirect', $query['redirect']['url'], $status);
        $status = apply_filters('wp_redirect_status', $status, $location);
     
        if(!$location){
            return false;
        }
     
        $location = wp_sanitize_redirect($location);
     
        if(!$is_IIS && PHP_SAPI != 'cgi-fcgi'){
            status_header($status);
        }
     
        header("Location: $location", true, $status);
        
        // Expose Headers
        if(current_user_can('administrator') && $query['settings']['headers']){
            
            header('Auto-Redirect-404: true');
            
            if(isset($query['search']['group'])){
                header('Auto-Redirect-404-Group: ' . $query['search']['group']);
            }
            
            if(isset($query['redirect']['engine'])){
                header('Auto-Redirect-404-Engine: ' . $query['redirect']['engine']);
            }
            
            if(isset($query['redirect']['primary'])){
                header('Auto-Redirect-404-Primary: ' . $query['redirect']['primary']);
            }
            
            if(isset($query['redirect']['engine'])){
                header('Auto-Redirect-404-Score: ' . $query['redirect']['engine']);
            }
            
            if(isset($query['redirect']['why'])){
                header('Auto-Redirect-404-Why: ' . wp_strip_all_tags($query['redirect']['why']));
            }
            
        }
        
        // Action: ar404/after_redirect
        do_action('ar404/after_redirect', $query);
        
        exit;
        
    }
    
    
    /**
     * log
     *
     * @param $query
     *
     * @return void
     */
    function log($query){
        
        if(empty($query['settings']['log']) || !WP_DEBUG || !WP_DEBUG_LOG){
            return;
        }
        
        $request_url = home_url() . $query['request']['url'];
        $redirect = $query['redirect']['url'];
        $group = $query['search']['group'];
        $engine = $query['redirect']['engine'];
        $score = $query['redirect']['score'];
        $why = wp_strip_all_tags($query['redirect']['why']);
        
        // Cloudflare Fix
        if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $_SERVER['REMOTE_ADDR'] = sanitize_text_field(wp_unslash($_SERVER["HTTP_CF_CONNECTING_IP"]));
        }
        
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
        
        error_log('Auto Redirect 404: ' . $request_url . ' => ' . $redirect . ' (Group: ' . $group . ' | Engine: ' . $engine . ' | Score: ' . $score . ' | Why: ' . $why . ' | IP: ' . $ip . ')');
        
    }
    
}


/**
 * ar404
 *
 * @return WP_404_Auto_Redirect
 */
function ar404(){
 
	global $ar404;

	if(isset($ar404)){
        return $ar404;
    }
    
    $ar404 = new WP_404_Auto_Redirect();
    $ar404->init();

	return $ar404;
	
}

// init
ar404();

endif;