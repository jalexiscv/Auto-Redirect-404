<?php

if(!defined('ABSPATH')){
    exit;
}


/**
 * ar404_set_result
 *
 * @param $result
 * @param $engine
 *
 * @return false
 */
function ar404_set_result($result, $engine){
    
    $engine = wp_parse_args($engine, array(
        'name'      => false,
        'slug'      => false,
        'weight'    => 100,
        'primary'   => false,
    ));
    
    if(empty($engine['name']) || empty($engine['slug'])){
        return false;
    }
    
    if(is_array($result)){
        
        $result = wp_parse_args($result, array(
            'url'   => false,
            'score' => 0,
            'why'   => __("No reason.", 'auto-redirect-404'),
        ));
        
    }else{
        
        $result = array(
            'url'   => false,
            'score' => 0,
            'why'   => $result,
        );
        
    }

    $result['score'] = (int) $result['score'] * $engine['weight'];
    
    return array_merge(array('engine' => $engine['name'], 'primary' => $engine['primary']), $result);
    
}


/**
 * ar404_is_empty
 *
 * @param $var
 *
 * @return bool
 */
function ar404_is_empty(&$var){
    return (isset($var) && !empty($var)) ? false : true;
}


/**
 * ar404_sanitize
 *
 * @param $input
 * @param $delimiter
 *
 * @return array|string|string[]
 */
function ar404_sanitize($input, $delimiter = '-'){
    return str_replace(array('-', '_'), $delimiter, sanitize_title(str_replace(array('_', '/', '?', '#', '=', '&amp;', '&'), '-', $input)));
}


/**
 * ar404_parse_args_recursive
 *
 * @param $a
 * @param $b
 *
 * @return array
 */
function ar404_parse_args_recursive(&$a, $b){
    
    $a = (array) $a;
    $b = (array) $b;
    $result = $b;
    
    foreach($a as $k => &$v){
        if(is_array($v) && isset($result[$k])){
            $result[$k] = ar404_parse_args_recursive($v, $result[$k]);
        }else{
            $result[$k] = $v;
        }
    }
    
    return $result;
    
}


/**
 * ar404_array_swap
 *
 * @param $key1
 * @param $key2
 * @param $array
 *
 * @return array
 */
function ar404_array_swap($key1, $key2, $array){
        
    $newArray = array();
    
    foreach($array as $key => $value){
        
        if($key == $key1){
            $newArray[$key2] = $array[$key2];
            
        }elseif ($key == $key2){
            $newArray[$key1] = $array[$key1];
            
        }else{
            $newArray[$key] = $value;
            
        }
        
    }
    
    return $newArray;
    
}


/**
 * ar404_array_move_by_key
 *
 * @param $a
 * @param $oldpos
 * @param $newpos
 *
 * @return void
 */
function ar404_array_move_by_key(&$a, $oldpos, $newpos){
    
    if($oldpos == $newpos){
        return;
    }
    
    array_splice($a, max($newpos,0), 0, array_splice($a, max($oldpos,0), 1));
    
}


/**
 * ar404_get_post_types
 *
 * @param $settings
 *
 * @return array|string[]|WP_Post_Type[]
 */
function ar404_get_post_types($settings){
    
    $get_post_types = get_post_types(array('public' => true), 'names');
    
    // No exclude
    if(!isset($settings['rules']['exclude']['post_types']) || !is_array($settings['rules']['exclude']['post_types']) || empty($settings['rules']['exclude']['post_types'])){
        return $get_post_types;
    }
    
    // Exclude
    $return = array();
    foreach($get_post_types as $post_type){
        
        if(in_array($post_type, $settings['rules']['exclude']['post_types'])){
            continue;
        }
        
        $return[] = $post_type;
        
    }
    
    return $return;
    
}


/**
 * ar404_get_taxonomies
 *
 * @param $settings
 *
 * @return array|string[]|WP_Taxonomy[]
 */
function ar404_get_taxonomies($settings){
    
    $get_taxonomies = get_taxonomies(array('public' => true), 'names');
    
    // No exclude
    if(!isset($settings['rules']['exclude']['taxonomies']) || !is_array($settings['rules']['exclude']['taxonomies']) || empty($settings['rules']['exclude']['taxonomies'])){
        return $get_taxonomies;
    }
    
    // Exclude
    $return = array();
    foreach($get_taxonomies as $taxonomy){
        
        if(in_array($taxonomy, $settings['rules']['exclude']['taxonomies'])){
            continue;
        }
        
        $return[] = $taxonomy;
        
    }
    
    return $return;
    
}