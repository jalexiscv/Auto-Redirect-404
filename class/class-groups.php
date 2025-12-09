<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect_Groups')){

class WP_404_Auto_Redirect_Groups{
    
    public $get_groups = array();
    
    /**
     * construct
     */
    function __construct(){
        
        // Groups: Register
        add_action('ar404/search/init', array($this, 'register_groups'), 1, 2);
        
	}
    
    
    /**
     * register_groups
     *
     * @return void
     */
    function register_groups(){
        
        $this->register_group(array(
            'name'      => 'Default',
            'slug'      => 'default',
            'custom'    => false,
            'engines'   => array(
                'default_fix_url',
                'default_direct',
                'default_post',
                'default_term',
                'default_post_fallback',
            )
        ));
        
    }
    
    
    /**
     * register_group
     *
     * @param $args
     *
     * @return void
     */
    function register_group($args){
        
        $args = wp_parse_args($args, array(
            'name'      => false,
            'slug'      => ar404_sanitize($args['name'], '_'),
            'custom'    => true,
            'engines'   => array()
        ));
        
        if(empty($args['name']) || empty($args['slug'])){
            return;
        }
        
        if($args['slug'] == 'default' && $args['custom']){
            return;
        }
        
        if(!$args['custom']){
            unset($args['custom']);
        }
        
        $group = apply_filters('ar404/define/group/' . $args['slug'], $args);
        
        if(!$group){
            return;
        }
        
        if(!empty($group['engines'])){
        
            $reset = false;
            
            foreach($group['engines'] as $e => $engine){
                
                if(ar404_engine_exists($engine)){
                    continue;
                }
                
                unset($group['engines'][$e]);
                $reset = true;
                
            }
            
            if($reset){
                $group['engines'] = array_values($group['engines']);
            }
            
        }
        
        $this->get_groups[] = $group;
        
    }
    
    
    /**
     * register_group_engines
     *
     * @param $args
     *
     * @return void
     */
    function register_group_engines($args){
        
        $args = wp_parse_args($args, array(
            'group'     => false,
            'engines'   => array()
        ));
        
        if(!$args['group'] || empty($this->get_groups)){
            return;
        }
        
        foreach($this->get_groups as &$group){
            
            if($group['slug'] != $args['group']){
                continue;
            }
            
            $group['engines'] = $args['engines'];
            
            if(empty($args['engines'])){
                break;
            }
            
            $reset = false;
            
            foreach($args['engines'] as $e => $engine){
                
                if(ar404_engine_exists($engine)){
                    continue;
                }
                
                unset($group['engines'][$e]);
                $reset = true;
                
            }
            
            if($reset){
                $group['engines'] = array_values($group['engines']);
            }
            
        }
        
    }
    
    
    /**
     * deregister_group
     *
     * @param $slug
     *
     * @return void
     */
    function deregister_group($slug){
        
        if(empty($this->get_groups) || $slug == 'default'){
            return;
        }
        
        $reset = false;
        
        // Engines
        foreach($this->get_groups as $g => $group){
        
            if($group['slug'] != $slug){
                continue;
            }
            
            // Engine
            unset($this->get_groups[$g]);
            $reset = true;
            break;
            
        }
        
        if($reset){
            $this->get_groups = array_values($this->get_groups);
        }
        
    }
    
    
    /**
     * deregister_group_engine
     *
     * Deregister a single engine from a group
     *
     * @param $args
     *
     * @return void
     */
    function deregister_group_engine($args){
        
        $args = wp_parse_args($args, array(
            'group'     => false,
            'engine'    => false
        ));
        
        if(!$args['group'] || !$args['engine']){
            return;
        }
        
        foreach($this->get_groups as $g => $group){
        
            if($group['slug'] != $args['group'] || empty($group['engines'])){
                continue;
            }
            
            $reset = false;
            
            foreach($group['engines'] as $ge => $group_engine){
            
                if($group_engine != $args['engine']){
                    continue;
                }
                
                unset($this->get_groups[$g]['engines'][$ge]);
                $reset = true;
                break;
                
            }
            
            if($reset){
                $this->get_groups[$g]['engines'] = array_values($this->get_groups[$g]['engines']);
            }
            
            break;
            
        }
        
    }
    
    
    /**
     * deregister_groups_engine
     *
     * Deregister a single engine from all groups
     *
     * @param $slug
     *
     * @return void
     */
    function deregister_groups_engine($slug){
        
        foreach($this->get_groups as $g => $group){
        
            if(empty($group['engines'])){
                continue;
            }
            
            $reset = false;
            
            foreach($group['engines'] as $ge => $group_engine){
            
                if($group_engine != $slug){
                    continue;
                }
                
                unset($this->get_groups[$g]['engines'][$ge]);
                $reset = true;
                break;
                
            }
            
            if($reset){
                $this->get_groups[$g]['engines'] = array_values($this->get_groups[$g]['engines']);
            }
            
        }
        
    }
    
    
    /**
     * reorder_group_engines
     *
     * @param $args
     *
     * @return void
     */
    function reorder_group_engines($args){
    
        $args = wp_parse_args($args, array(
            'group'     => false,
            'engine'    => false,
            'order'     => 0
        ));
        
        $get_engines = ar404()->engines->get_engines;
        
        if(!$args['group'] || empty($this->get_groups) || !$args['engine'] || empty($get_engines)){
            return;
        }

        $group_key = false;
        $engine_key = false;
        
        foreach($this->get_groups as $g => $group){
            
            if($group['slug'] != $args['group'] || empty($group['engines'])){
                continue;
            }
            
            foreach($group['engines'] as $e => $engine){
                
                if($engine == $args['engine']){
                    
                    $group_key = $g;
                    $engine_key = $e;
                    break;
                    
                }
                
            }
            
        }
        
        if($engine_key === false || $group_key === false){
            return;
        }
        
        ar404_array_move_by_key($this->get_groups[$group_key]['engines'], $engine_key, $args['order']);

    }
    
    
    /**
     * get_group_by_slug
     *
     * @param $slug
     *
     * @return false|mixed
     */
    function get_group_by_slug($slug){
        
        if(empty($this->get_groups)){
            return false;
        }
        
        foreach($this->get_groups as $group){
            
            if($group['slug'] != $slug){
                continue;
            }
            
            return $group;
            
        }
        
        return false;
        
    }
    
    
    /**
     * group_exists
     *
     * @param $slug
     *
     * @return false|mixed
     */
    function group_exists($slug){
        return $this->get_group_by_slug($slug);
    }
    
}

ar404()->groups = new WP_404_Auto_Redirect_Groups();

}


/**
 * ar404_register_group
 *
 * @param $args
 *
 * @return mixed
 */
function ar404_register_group($args){
	return ar404()->groups->register_group($args);
}


/**
 * ar404_register_group_engines
 *
 * @param $args
 *
 * @return mixed
 */
function ar404_register_group_engines($args){
	return ar404()->groups->register_group_engines($args);
}


/**
 * ar404_deregister_group_engine
 *
 * @param $args
 *
 * @return mixed
 */
function ar404_deregister_group_engine($args){
	return ar404()->groups->deregister_group_engine($args);
}


/**
 * ar404_deregister_groups_engine
 *
 * @param $slug
 *
 * @return mixed
 */
function ar404_deregister_groups_engine($slug){
	return ar404()->groups->deregister_groups_engine($slug);
}


/**
 * ar404_reorder_group_engines
 *
 * @param $args
 *
 * @return mixed
 */
function ar404_reorder_group_engines($args){
	return ar404()->groups->reorder_group_engines($args);
}


/**
 * ar404_get_group_by_slug
 *
 * @param $slug
 *
 * @return mixed
 */
function ar404_get_group_by_slug($slug){
	return ar404()->groups->get_group_by_slug($slug);
}


/**
 * ar404_group_exists
 *
 * @param $slug
 *
 * @return mixed
 */
function ar404_group_exists($slug){
	return ar404()->groups->group_exists($slug);
}