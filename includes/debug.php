<?php

if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WP_404_Auto_Redirect')){
    return;
}

trait WP_404_Auto_Redirect_Debug{
    
    /**
     * debug
     *
     * @param $query
     *
     * @return void
     */
    function debug($query){
        
        $title = 'Fallback Redirection disabled. Displaying 404.';
        
        if(isset($query['redirect']['url']) && !empty($query['redirect']['url'])){
            $title = 'Redirection: ' . "<a href='" . esc_url($query['redirect']['url']) . "'>" . esc_html($query['redirect']['url']) . "</a>" . ' (' . esc_html($query['settings']['method']) . ' Headers)';
        }
        
        ?>
        
        <style type="text/css">
            .ar404_debug_page{margin:0 auto; max-width:1150px; font-family: arial, sans-serif;}
            .ar404_debug_page .logo{text-align:center;}
            .ar404_debug_page .logo img{margin:0 auto;}
            .ar404_debug_page h2,
            .ar404_debug_page h4{text-align:center;}
            .ar404_debug_page pre{background:#f4f4f4; padding:15px; overflow:auto;}
            .ar404_debug_page a{color:blue;}
            .ar404_debug_page p{font-size:12px;}
        </style>
        <div class="ar404_debug_page">
        
            <?php if(!$query['preview']){ ?>
                <div class="logo">
                    <img src="<?php echo esc_url(plugins_url('../assets/logo.png', __FILE__)); ?>" class="logo" />
                </div>
                <h2>Auto Redirect 404 to Similar Post</h2>
                <p>This is the <strong>debug console</strong> of Auto Redirect 404 to Similar Post Plugin which is only visible to administrators. Head over your <a href="<?php echo esc_url(admin_url('options-general.php?page=auto-redirect-404')); ?>">settings page</a> if you would like to disable it.</p>
                <hr />
            <?php } ?>
            
            <h3>Summary:</h3>
            
            <pre>Requested URL: <a href="<?php echo esc_url(home_url() . $query['request']['url']); ?>"><?php echo esc_url(home_url()); ?><?php echo esc_html($query['request']['url']); ?></a><br />
<?php echo wp_kses_post($title); ?><br />
Engine: <?php echo esc_html($query['redirect']['engine']); ?><br />
Details: <?php echo esc_html($query['redirect']['why']); ?></pre>
            
            <h3>Advanced:</h3>
            <pre><?php print_r($query); ?></pre>
        </div>
        
        <?php 
        exit;
        
    }
}