<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect')){
    return;
}

trait WP_404_Auto_Redirect_Admin{
    
    /**
     * admin_menu
     *
     * @return void
     */
    function admin_menu(){
        add_submenu_page('options-general.php', 'Auto Redirect 404', 'Auto Redirect 404', 'manage_options', 'auto-redirect-404', array($this, 'admin_page'));
    }
    
    
    /**
     * admin_link
     *
     * @param $links
     * @param $plugin_file
     *
     * @return mixed
     */
    function admin_link($links, $plugin_file){
        
        $plugin = plugin_basename(AR404_FILE);
        
        if($plugin !== $plugin_file){
            return $links;
        }
        
        return array_merge(
            $links, 
            array('<a href="' . admin_url('options-general.php?page=auto-redirect-404') . '">' . __('Settings', 'auto-redirect-404') . '</a>')
        );
        
    }
    
    
    /**
     * admin_settings
     *
     * @return void
     */
    function admin_settings(){
        register_setting('ar404_settings', 'ar404_settings', array('sanitize_callback' => array($this, 'sanitize_setting')));
    }
    
    
    /**
     * sanitize_setting
     *
     * @param $settings
     *
     * @return array
     */
    function sanitize_setting($settings){
        
        $settings['fallback']['url'] = sanitize_url($settings['fallback']['url']);
        $settings['priority'] = (int) $settings['priority'];
        
        return $settings;
        
    }
    
    
    /**
     * admin_scripts
     *
     * @param $page
     *
     * @return void
     */
    function admin_scripts($page){
        
        if($page !== 'settings_page_auto-redirect-404'){
            return;
        }
        
        wp_enqueue_script('ar404_admin_js', plugins_url('assets/admin.js', AR404_FILE), array('jquery'));
        wp_enqueue_style('ar404_admin_css', plugins_url('assets/admin.css', AR404_FILE));
        
    }
    
    
    /**
     * admin_page
     *
     * @return void
     */
    function admin_page(){
    ?>
    <div class="wrap" id="ar404_settings">
        <h1 class="wp-heading-inline">Auto Redirect 404 to Similar Post</h1>
        <hr class="wp-header-end" />
        
        <h2 class="nav-tab-wrapper">
            <a href="#settings" class="nav-tab nav-tab-active"><?php _e('Settings', 'auto-redirect-404'); ?></a>
            <a href="#post-types" class="nav-tab"><?php _e('Post Types', 'auto-redirect-404'); ?></a>
            <a href="#taxonomies" class="nav-tab"><?php _e('Taxonomies', 'auto-redirect-404'); ?></a>
            <a href="#hooks" class="nav-tab"><?php _e('Engines', 'auto-redirect-404'); ?></a>
        </h2>
        
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                
                    <form method="post" action="options.php">
                    <?php 
                    settings_fields('ar404_settings');
                    do_settings_sections('ar404_settings');
                    $settings = ar404_settings_get();
                    ?>

                        <div class="meta-box-sortables ui-sortable">
                        
                            <!-- Tab: Settings -->
                            <div class="nav-tab-panel" id="settings">
                            
                                <div class="postbox">
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                                            
                                                <tr>
                                                    <th scope="row"><?php _e('Debug Mode', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Debug Mode', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_debug">
                                                                <input 
                                                                    name="ar404_settings[debug]" 
                                                                    id="ar404_settings_debug" 
                                                                    value="1" 
                                                                    type="checkbox" 
                                                                    <?php checked(1, $settings['debug'], true); ?> 
                                                                    />
                                                                <?php _e('Enable', 'auto-redirect-404'); ?>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Display the Debug Console instead of being redirected. <code>Administrators</code> only.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Expose Headers', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Expose Headers', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_headers">
                                                                <input 
                                                                    name="ar404_settings[headers]" 
                                                                    id="ar404_settings_headers" 
                                                                    value="1" 
                                                                    type="checkbox" 
                                                                    <?php checked(1, $settings['headers'], true); ?> 
                                                                    />
                                                                <?php _e('Enable', 'auto-redirect-404'); ?>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Expose \'Auto-Redirect-404\' headers on 404 pages. <code>Administrators</code> only.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Log Redirections', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        
                                                        <?php if(!WP_DEBUG || !WP_DEBUG_LOG){ ?>
                                                            <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Log Redirections', 'auto-redirect-404'); ?></span></legend>
                                                                <p class="description"><?php _e('To enable this feature, please set <code>WP_DEBUG</code> and <code>WP_DEBUG_LOG</code> to <code>true</code>. Read the <a href="https://codex.wordpress.org/Editing_wp-config.php#Debug" target="_blank">WP Config documentation</a>.', 'auto-redirect-404'); ?></p>
                                                            </fieldset>
                                                        <?php }else{ ?>
                                                            <fieldset>
                                                                <legend class="screen-reader-text"><span><?php _e('Log Redirections', 'auto-redirect-404'); ?></span></legend>
                                                                <label for="ar404_settings_log">
                                                                    <input 
                                                                        name="ar404_settings[log]" 
                                                                        id="ar404_settings_log" 
                                                                        value="1" 
                                                                        type="checkbox" 
                                                                        <?php checked(1, $settings['log'], true); ?> 
                                                                        />
                                                                    <?php _e('Enable', 'auto-redirect-404'); ?>
                                                                </label>
                                                            </fieldset>
                                                            <p class="description"><?php _e('Log redirections in the <code>/wp-content/debug.log</code> file.', 'auto-redirect-404'); ?></p>
                                                        <?php } ?>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Fallback Behavior', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Fallback Behavior', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_fallback_type">
                                                                <select name="ar404_settings[fallback][type]" id="ar404_settings_fallback_type">
                                                                    <option value="home" <?php if($settings['fallback']['type'] == 'home') echo "selected"; ?>><?php _e('Redirect to Homepage', 'auto-redirect-404'); ?></option>
                                                                    <option value="custom" <?php if($settings['fallback']['type'] == 'custom') echo "selected"; ?>><?php _e('Custom Redirection', 'auto-redirect-404'); ?></option>
                                                                    <option value="disabled" <?php if($settings['fallback']['type'] == 'disabled') echo "selected"; ?>><?php _e('Default 404', 'auto-redirect-404'); ?></option>
                                                                </select>
                                                            </label>
                                                            
                                                            <?php 
                                                            $fallback = array(
                                                                'value' => home_url(),
                                                                'class' => 'disabled',
                                                                'attr'  => 'readonly="readonly"',
                                                            );
                                                            
                                                            if($settings['fallback']['type'] == 'custom'){
                                                                $fallback['value']  = $settings['fallback']['url'];
                                                                $fallback['attr']   = '';
                                                                $fallback['class']  = '';
                                                            }
                                                            
                                                            if($settings['fallback']['type'] == 'disabled'){
                                                                $fallback['value']  = '';
                                                                $fallback['attr']   = '';
                                                                $fallback['class']  = 'hidden';
                                                            }
                                                            ?>
                                                            
                                                            <input name="ar404_settings[fallback][home_url]" id="ar404_settings_fallback_home_url" type="hidden" value="<?php echo home_url(); ?>" />
                                                            <input name="ar404_settings[fallback][url]" id="ar404_settings_fallback_url" type="text" value="<?php echo $fallback['value']; ?>" class="<?php echo $fallback['class']; ?>" <?php echo $fallback['attr']; ?> />
                                                            
                                                        </fieldset>
                                                        <p class="description"><?php _e('If nothing similar is found, this behavior will be applied.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Redirections Headers', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Redirections Headers', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_method">
                                                                <select name="ar404_settings[method]" id="ar404_settings_method">
                                                                    <option value="301" <?php if($settings['method'] == 301) echo "selected"; ?>>301 Status</option>
                                                                    <option value="302" <?php if($settings['method'] == 302) echo "selected"; ?>>302 Status</option>
                                                                </select>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Learn more about <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">HTTP headers & redirections</a>.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Plugin Priority', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Plugin Priority', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_priority">
                                                                <input 
                                                                    type="number" 
                                                                    name="ar404_settings[priority]" 
                                                                    id="ar404_settings_priority" 
                                                                    value="<?php echo isset($settings['priority']) ? $settings['priority'] : '999'; ?>" 
                                                                    required 
                                                                    />
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Advanced users only. Default: <code>999</code>', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                                
                                <div id="ar404_settings_redirection_preview">
                                    <div class="postbox">
                                        <div class="inside">
                                            <table class="form-table">
                                                <tbody>
                                                
                                                    <tr>
                                                        <th scope="row"><?php echo home_url(); ?></th>
                                                        <td>
                                                            <input class="request" type="text" value="/example-url" />
                                                            <p class="description"><?php _e('Enter the URL you would like to test, starting with <code>/</code>.', 'auto-redirect-404'); ?></p>
                                                            
                                                            <p class="submit">
                                                                <input class="nonce" type="hidden" name="nonce" value="<?php echo wp_create_nonce('preview_nonce'); ?>" />
                                                                <?php submit_button(__('Preview', 'auto-redirect-404'), 'secondary', '', false); ?>
                                                                <span class="loading spinner"></span>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="results"></div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Tab: Post Types -->
                            <div class="nav-tab-panel" id="post-types">
                            
                                <div class="postbox">
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                                            
                                                <tr>
                                                    <th scope="row"><?php _e('Exclude Post Meta', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Exclude Post Meta', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_rules_redirection_exclude_post_meta">
                                                                <input 
                                                                    name="ar404_settings[rules][exclude][post_meta]" 
                                                                    id="ar404_settings_rules_redirection_exclude_post_meta" 
                                                                    type="checkbox" 
                                                                    value="1" 
                                                                    <?php checked(1, $settings['rules']['exclude']['post_meta'], true); ?>
                                                                    />
                                                                <?php _e('Enable', 'auto-redirect-404'); ?>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Exclude posts with the post meta: <code>ar404_no_redirect = 1</code> from possible redirections.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <th scope="row"><?php _e('Exclude Post Type(s)', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Exclude Post Type(s)', 'auto-redirect-404'); ?></span></legend>
                                                            <div id="ar404_settings_rules_redirection_exclude_post_types">
                                                                <?php foreach(get_post_types(array('public' => true), 'objects') as $post_type) { ?>
                                                                    <?php 
                                                                    $checked = '';
                                                                    if( 
                                                                        isset($settings['rules']['exclude']['post_types']) && 
                                                                        is_array($settings['rules']['exclude']['post_types']) && 
                                                                        in_array($post_type->name, $settings['rules']['exclude']['post_types'])
                                                                    )
                                                                        $checked = 'checked="checked"'; ?>
                                                                    <div><input type="checkbox" name="ar404_settings[rules][exclude][post_types][]" id="ar404_settings_rules_redirection_exclude_post_types_<?php echo $post_type->name; ?>" value="<?php echo $post_type->name; ?>" <?php echo $checked; ?> />
                                                                    <label for="ar404_settings_rules_redirection_exclude_post_types_<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></label></div>
                                                                <?php } ?>
                                                            </div>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Exclude one or multiple post types from possible redirections.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Tab: Taxonomies -->
                            <div class="nav-tab-panel" id="taxonomies">
                            
                                <div class="postbox">
                                    <div class="inside">
                                        <table class="form-table">
                                            <tbody>
                                            
                                                <tr>
                                                    <th scope="row"><?php _e('Disable Taxonomy Redirection', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Disable Taxonomy Redirection', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_rules_redirection_disable_taxonomies">
                                                                <input 
                                                                    name="ar404_settings[rules][disable][taxonomies]" 
                                                                    id="ar404_settings_rules_redirection_disable_taxonomies" 
                                                                    type="checkbox" 
                                                                    value="1" 
                                                                    <?php checked(1, $settings['rules']['disable']['taxonomies'], true); ?>
                                                                    />
                                                                <?php _e('Disable', 'auto-redirect-404'); ?>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Never redirect to terms archives.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                            
                                                <tr class="ar404_settings_taxonomies">
                                                    <th scope="row"><?php _e('Exclude Term Meta', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Exclude Term Meta', 'auto-redirect-404'); ?></span></legend>
                                                            <label for="ar404_settings_rules_redirection_exclude_term_meta">
                                                                <input 
                                                                    name="ar404_settings[rules][exclude][term_meta]" 
                                                                    id="ar404_settings_rules_redirection_exclude_term_meta" 
                                                                    type="checkbox" 
                                                                    value="1" 
                                                                    <?php checked(1, $settings['rules']['exclude']['term_meta'], true); ?>
                                                                    />
                                                                <?php _e('Enable', 'auto-redirect-404'); ?>
                                                            </label>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Exclude terms with the term meta: <code>ar404_no_redirect = 1</code> from possible redirections.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                                <tr class="ar404_settings_taxonomies">
                                                    <th scope="row"><?php _e('Exclude Taxonomie(s)', 'auto-redirect-404'); ?></th>
                                                    <td>
                                                        <fieldset>
                                                            <legend class="screen-reader-text"><span><?php _e('Exclude Taxonomie(s)', 'auto-redirect-404'); ?></span></legend>
                                                            <div id="ar404_settings_rules_redirection_exclude_taxonomies">
                                                                <?php foreach(get_taxonomies(array('public' => true), 'objects') as $taxonomy) { ?>
                                                                    <?php 
                                                                    $checked = '';
                                                                    if( 
                                                                        isset($settings['rules']['exclude']['taxonomies']) && 
                                                                        is_array($settings['rules']['exclude']['taxonomies']) && 
                                                                        in_array($taxonomy->name, $settings['rules']['exclude']['taxonomies'])
                                                                    )
                                                                        $checked = 'checked="checked"'; ?>
                                                                    <div><input type="checkbox" name="ar404_settings[rules][exclude][taxonomies][]" id="ar404_settings_rules_redirection_exclude_taxonomies_<?php echo $taxonomy->name; ?>" value="<?php echo $taxonomy->name; ?>" <?php echo $checked; ?> />
                                                                    <label for="ar404_settings_rules_redirection_exclude_taxonomies_<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></label></div>
                                                                <?php } ?>
                                                            </div>
                                                        </fieldset>
                                                        <p class="description"><?php _e('Exclude one or multiple taxonomies from possible redirections.', 'auto-redirect-404'); ?></p>
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Tab: Hooks -->
                            <div class="nav-tab-panel" id="hooks">
                            
                                <?php 
                                do_action('ar404/search/init', false);
                                
                                // init Groups
                                $groups = array();
                                $groups = ar404()->groups->get_groups;
                                $groups_count = count($groups);
                                
                                // init Engines
                                $engines = array();
                                $engines = ar404()->engines->get_engines;
                                $engines_count = count($engines);
                                ?>
                                
                                <div style="float:left; width:49%; margin-right:2%;">
                                    <table class="widefat" style="margin-bottom:20px;">
                                    
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="row-title"><h3 style="margin:7px 0;">Groups <span style="color:#555d66;">(<?php echo $groups_count; ?>)</span></h3></th>
                                            </tr>
                                        </thead>
                                    
                                        <thead>
                                            <tr>
                                                <th class="row-title">Name</th>
                                                <th>Engines</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <?php if(!empty($groups)){ ?>
                                            
                                                <?php $i=0; foreach($groups as $group){ $i++; ?>
                                                    <tr <?php echo !($i % 2) ? 'class="alternate"': ''; ?>>
                                                        <td class="row-title">
                                                            <span style="color: #0073aa;"><?php echo $group['name']; ?></span><br />
                                                            <small style="font-weight:normal;"><?php echo $group['slug']; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php foreach($group['engines'] as $engine){ ?>
                                                                
                                                                <?php if($engine = ar404_get_engine_by_slug($engine)){ ?>
                                                                    <div style="margin-bottom:5px;">
                                                                        <?php echo $engine['name']; ?>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                            
                                                        </td>
                                                    </tr>
                                                    
                                                <?php } ?>
                                            
                                            <?php }else{ ?>
                                            
                                                <tr>
                                                    <td class="row-title" colspan="2" style="text-align:center;">
                                                        <em>No Groups found.</em>
                                                    </td>
                                                </tr>
                                                
                                            <?php } ?>
                                        </tbody>
                                        
                                    </table>
                                </div>
                                
                                <div style="float:left; width:49%;">
                                    <table class="widefat" style="margin-bottom:20px;">
                                        <thead>
                                            <tr>
                                                <th colspan="4" class="row-title"><h3 style="margin:7px 0;">Engines <span style="color:#555d66;">(<?php echo $engines_count; ?>)</span></h3></th>
                                            </tr>
                                        </thead>
                                    
                                        <thead>
                                            <tr>
                                                <th class="row-title">Name</th>
                                                <th>Weight</th>
                                                <th>Primary</th>
                                                <th>Defined</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            <?php if(!empty($engines)){ ?>
                                            
                                                <?php $i=0; foreach($engines as $engine){ $i++; ?>
                                                    
                                                    <?php $has_filter = has_filter('ar404/search/engine/' . $engine['slug']); ?>
                                                    
                                                    <tr <?php echo !($i % 2) ? 'class="alternate"': ''; ?> <?php if(!$has_filter) echo 'style="background:#f7e5e5;"'; ?>>
                                                        <td class="row-title">
                                                            <span style="color: #0073aa;"><?php echo $engine['name']; ?></span><br />
                                                            <small style="font-weight:normal;"><?php echo $engine['slug']; ?></small>
                                                        </td>
                                                        <td><?php echo $engine['weight']; ?></td>
                                                        <td>
                                                            <?php if($engine['primary']){ ?>
                                                                <span class="dashicons dashicons-yes"></span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php if($has_filter){ ?>
                                                            
                                                                <span class="dashicons dashicons-yes"></span>
                                                                
                                                            <?php }else{ ?>
                                                                
                                                                <span class="dashicons dashicons-no" style="color:#cc0000;"></span>
                                                                
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                
                                            <?php }else{ ?>
                                                
                                                <tr>
                                                    <td class="row-title" colspan="3" style="text-align:center;">
                                                        <em>No Engines found.</em>
                                                    </td>
                                                </tr>
                                                
                                            <?php } ?>
                                        </tbody>
                                        
                                    </table>

                                </div>
                                <div style="clear:both;"></div>
                                
                            </div>
                            
                        </div>
                        
                        <div class="postbox">
                            <div class="inside">
                                <p class="submit">
                                    <?php submit_button(__('Save Settings', 'auto-redirect-404'), 'primary', '', false); ?>
                                </p>
                            </div>
                        </div>
                        
                    </form>
                    
                </div>
                
                <?php $plugin_data = get_plugin_data(AR404_FILE, false, false); ?>
                <div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">

                            <div class="inside">
                                <img src="<?php echo plugins_url('assets/logo.png', AR404_FILE); ?>" class="logo" />

                                <p><?php _e('Automatically redirect 404 pages to similar posts based on Title, Post Types & Taxonomies.', 'auto-redirect-404'); ?></p>
                                
                                <h3><?php _e('Rate us', 'auto-redirect-404'); ?></h3>
                                
                                <p><?php _e('Enjoying this plugin? Please rate us. It\'s always much appreciated!', 'auto-redirect-404'); ?></p>
                                <p><a href="https://wordpress.org/support/plugin/auto-redirect-404-to-similar-post/reviews/#new-post" target="_blank" class="button"><?php _e('Rate this plugin', 'auto-redirect-404'); ?></a></p>
                                
                                <?php if(!ar404_is_empty($plugin_data['Version'])){ ?>
                                
                                    <h3><?php _e('Changelog', 'auto-redirect-404'); ?></h3>
                                    <p><?php _e('See what\'s new in', 'auto-redirect-404'); ?> <a href="https://wordpress.org/plugins/auto-redirect-404-to-similar-post/#developers" target="_blank" style="text-decoration:none;">version <?php echo $plugin_data['Version']; ?></a>.</p>
                                    
                                <?php } ?>
                                
                                <h3><?php _e('Resources', 'auto-redirect-404'); ?></h3>
                                
                                <ul>
                                    <li><a href="https://wordpress.org/plugins/auto-redirect-404-to-similar-post/" target="_blank" style="text-decoration:none;"><i class="dashicons dashicons-admin-home"></i> <?php _e('Website', 'auto-redirect-404'); ?></a></li>
                                    <li><a href="https://wordpress.org/plugins/auto-redirect-404-to-similar-post/" target="_blank" style="text-decoration:none;"><i class="dashicons dashicons-sos"></i> <?php _e('Documentation', 'auto-redirect-404'); ?></a></li>
                                    <li><a href="https://wordpress.org/support/plugin/auto-redirect-404-to-similar-post" target="_blank" style="text-decoration:none;"><i class="dashicons dashicons-editor-help"></i> <?php _e('Support', 'auto-redirect-404'); ?></a></li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                </div>

            </div>
            <br class="clear">
            
        </div>
    </div>
    <?php

    }
    
}