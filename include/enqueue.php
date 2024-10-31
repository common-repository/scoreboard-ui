<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//-------------------------- Optional - If Add Shortcode use any single page with design ------------- //
function scoreboard_ui_add_css_js(){    
    //front-end unityg
    wp_enqueue_style( 'scoreui-front-dashboard', plugin_dir_url(__FILE__) . '../css/front-dashboard.css', array(), '1.0.0', 'all' );
    wp_enqueue_style( 'scoreui-front-normalize', plugin_dir_url(__FILE__) . '../css/normalize.min.css', array(), '1.0.0', 'all' );
    wp_enqueue_script('vue-front-scoreui', plugin_dir_url(__FILE__) . '../js/vue.min.js' , array('jquery'),'1.0.0',true); 
    wp_enqueue_script('front-dashboard-scoreui', plugin_dir_url(__FILE__) . '../js/front-dashboard.js' , array('jquery'),'1.0.0',true);     
}add_action('wp_enqueue_scripts','scoreboard_ui_add_css_js'); 
?>