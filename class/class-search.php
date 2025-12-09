<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect_Search')):

class WP_404_Auto_Redirect_Search{
    
    /**
     * sql
     *
     * @param $args
     * @param $query
     *
     * @return array
     */
    function sql($args, $query){
        
        global $wpdb;
        
        $args = wp_parse_args($args, array(
            'keywords' 	=> false,
            'mode'      => 'post',
            'post_type' => false,
            'taxonomy'  => false
        ));
        
        if(!$args['keywords']){
            return array('result' => array('score' => 0), 'dump' => false);
        }
        
        // Prepare SQL Args
        $query_params = array();
        
        // Mode: Post
        if($args['mode'] == 'post'){
            
            // Early Stop checks...
            if(!$args['post_type'] && empty($query['settings']['rules']['include']['post_types'])){
                 return array('result' => array('score' => 0), 'dump' => false);
            }

            $select_score = array();
            
            if(!is_array($args['keywords'])){
                $args['keywords'] = array($args['keywords']);
            }

            foreach($args['keywords'] as $k){
                
                $strlen = strlen($k);
                
                if($strlen > 1){
                    // Left: post_name LIKE 'keyword-%'
                    $select_score[] = "IF(p.post_name LIKE %s, 2, 0)";
                    $query_params[] = $k . '-%';
                    
                    // Right: post_name LIKE '%-keyword'
                    $select_score[] = "IF(p.post_name LIKE %s, 2, 0)";
                    $query_params[] = '%-' . $k;
                    
                    // Inside: post_name LIKE '%-keyword-%'
                    $select_score[] = "IF(p.post_name LIKE %s, 2, 0)";
                    $query_params[] = '%-' . $k . '-%';
                    
                    // Direct: post_name = 'keyword'
                    $select_score[] = "IF(p.post_name = %s, 2, 0)";
                    $query_params[] = $k;
                }
                
                // Wildcard: post_name LIKE '%keyword%'
                $select_score[] = "IF(p.post_name LIKE %s, 1, 0)";
                $query_params[] = '%' . $k . '%';
                
            }

            $sql = "SELECT p.ID, (" . implode(' + ', $select_score) . ") AS score FROM " . $wpdb->posts . " AS p";
                
            if($query['settings']['rules']['exclude']['post_meta']){
                $sql .= " INNER JOIN " . $wpdb->postmeta . " AS pm ON(p.ID = pm.post_id) WHERE p.post_status = 'publish' AND (pm.meta_key = 'ar404_no_redirect' AND pm.meta_value != '1') ";
            } else {
                $sql .= " WHERE p.post_status = 'publish' ";
            }
                
            if($args['post_type'] != 'any' && $args['post_type'] != array('any')){
                
                $get_post_types = array();
                
                if(!$args['post_type']){
                    $get_post_types = $query['settings']['rules']['include']['post_types'];
                } elseif(is_array($args['post_type']) && !empty($args['post_type'])){
                    $get_post_types = $args['post_type'];
                } elseif(is_string($args['post_type'])){
                    $get_post_types[] = $args['post_type'];
                }
                
                if(!empty($get_post_types)){
                    $placeholders = implode(',', array_fill(0, count($get_post_types), '%s'));
                    $sql .= " AND p.post_type IN ($placeholders)";
                    foreach($get_post_types as $pt) {
                        $query_params[] = $pt;
                    }
                }
            }
                
            $sql .= " ORDER BY score DESC, post_modified DESC LIMIT 1";
        
        }
        
        // Mode: Term
        elseif($args['mode'] == 'term'){
        
            if(!$args['taxonomy'] && (empty($query['settings']['rules']['include']['taxonomies']) || $query['settings']['rules']['disable']['taxonomies'])){
                return array('result' => array('score' => 0), 'dump' => false);
            }
            
            $select_score = array();

            if(!is_array($args['keywords'])){
                $args['keywords'] = array($args['keywords']);
            }

            foreach($args['keywords'] as $k){
                $strlen = strlen($k);
                if($strlen > 1){
                    $select_score[] = "IF(t.slug LIKE %s, 2, 0)";
                    $query_params[] = $k . '-%';
                    
                    $select_score[] = "IF(t.slug LIKE %s, 2, 0)";
                    $query_params[] = '%-' . $k;
                    
                    $select_score[] = "IF(t.slug LIKE %s, 2, 0)";
                    $query_params[] = '%-' . $k . '-%';
                    
                    $select_score[] = "IF(t.slug = %s, 2, 0)";
                    $query_params[] = $k;
                }
                $select_score[] = "IF(t.slug LIKE %s, 1, 0)";
                $query_params[] = '%' . $k . '%';
            }

            $sql = "SELECT t.term_id, (" . implode(' + ', $select_score) . ") AS score FROM " . $wpdb->terms . " AS t";
            $sql .= " INNER JOIN " . $wpdb->term_taxonomy . " AS tt ON(t.term_id = tt.term_id)";
            
            if($query['settings']['rules']['exclude']['term_meta']){
                $sql .= " INNER JOIN " . $wpdb->termmeta . " AS tm ON(t.term_id = tm.term_id)";
            }
            
            $sql .= " WHERE 1=1 ";

            if($args['taxonomy'] != 'any' && $args['taxonomy'] != array('any')){
                
                $get_taxonomies = array();
                if(!$args['taxonomy']){
                    $get_taxonomies = $query['settings']['rules']['include']['taxonomies'];
                } elseif(is_array($args['taxonomy']) && !empty($args['taxonomy'])){
                    $get_taxonomies = $args['taxonomy'];
                } elseif(is_string($args['taxonomy'])){
                    $get_taxonomies[] = $args['taxonomy'];
                }
                
                if(!empty($get_taxonomies)){
                    $placeholders = implode(',', array_fill(0, count($get_taxonomies), '%s'));
                    $sql .= " AND tt.taxonomy IN ($placeholders)";
                    foreach($get_taxonomies as $tax) {
                        $query_params[] = $tax;
                    }
                }
            }
            
            if($query['settings']['rules']['exclude']['term_meta']){
                $sql .= " AND (tm.meta_key = 'ar404_no_redirect' AND tm.meta_value != '1')";
            }
            
            $sql .= " ORDER BY score DESC LIMIT 1";
            
        }
        
        // Execute Prepared Query
        if(!empty($query_params)){
            $search = $wpdb->get_row($wpdb->prepare($sql, $query_params), 'ARRAY_A');
        } else {
            $search = $wpdb->get_row($sql, 'ARRAY_A');
        }
        
        // init Result
        $result = array();
        
        // SQL Dump
        $result['sql'] = $sql;
        
        // Post ID
        if(isset($search['ID']) && !empty($search['ID'])){
            $result['post_id'] = (int) $search['ID'];
        }
        
        // Term ID
        if(isset($search['term_id']) && !empty($search['term_id'])){
            $result['term_id'] = (int) $search['term_id'];
        }
        
        // Score
        $result['score'] = 0;
        if(isset($search['score']) && !empty($search['score'])){
            $result['score'] = (int) $search['score'];
        }
        
        // Return Result
        return $result;
    }
    
}

ar404()->search = new WP_404_Auto_Redirect_Search();

endif;


/**
 * ar404_search
 *
 * @param $args
 * @param $query
 *
 * @return mixed
 */
function ar404_search($args, $query){
	return ar404()->search->sql($args, $query);
}