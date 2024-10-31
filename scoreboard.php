<?php
/*
 * Plugin Name: Scoreboard UI
 * Plugin URI: https://wordpress.org/plugins/scoreboard-ui
 * Description: Scoreboard UI Take full control over your WordPress site, build any shortcode paste you can imagine – no programming knowledge required.
 * Author: Md. Shahinur Islam
 * Author URI: https://profiles.wordpress.org/shahinurislam
 * Version: 1.02
 * Text Domain: scoreboard-ui   
 * Domain Path: /lang
 * Network: True
 * License: GPLv2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
define( 'SCOREBOARDUI_PLUGIN', __FILE__ );
define( 'SCOREBOARDUI_PLUGIN_DIR', untrailingslashit( dirname( SCOREBOARDUI_PLUGIN ) ) );
require_once SCOREBOARDUI_PLUGIN_DIR . '/include/posttype.php';
require_once SCOREBOARDUI_PLUGIN_DIR . '/include/enqueue.php'; 
//-------------All post show------------//
function scoreboardui_shortcode($atts) {
ob_start();
//set attributies
$atts = shortcode_atts(
	array(
		'post_type' => 'scoreboard_ui' 
	), $atts, 'scoreboard_ui_shahin'); 
?>  
<!-- partial:index.partial.html --> 
<div class="layout" id="app">  
  <main class="dashboard">
    <div class="col-left">    
    <?php
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $scoreboard_ui_main_blog = new WP_Query(array(
      'post_type'=>'scoreboard_ui', 
      'paged' => $paged
    ));
    if($scoreboard_ui_main_blog->have_posts())	:	 
    while($scoreboard_ui_main_blog->have_posts())	: $scoreboard_ui_main_blog->the_post();   
    if(is_sticky( $scoreboard_ui_main_blog->post_id )):
    ?>
      <section class="featured-live">
        <h2 class="sr-only">Featured live match</h2>
        <div class="card">
          <div class="card__body">
            <div class="featured-live__title">
              <div class="featured-live__stage">
                <h3><?php the_title();?></h3>
                <small><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_other_text', true)));?></small>
              </div>
              <span class="tag tag--red tag--icon" style="color: <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_status_color', true)));?>"><svg width="24" height="24" viewBox="0 0 24 24"> <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z" /> </svg><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_status', true)));?></span>
            </div>
            <?php
              global $post;
              $terms = wp_get_post_terms( $post->ID, 'scoreboard_teams' );                           
              if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {   ?>
                <div class="featured-live__score score">
                  <p class="score__team score__team--home">
                    <img src="<?php echo esc_url(get_term_meta($terms[0]->term_id, 'teams_profile', true))?>" /><span><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms[0]->name));?></span>
                  </p>
                  <div class="score__info">
                    <p class="score__result">
                      <span class="score__winner"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_term_meta($terms[0]->term_id, 'teams_score', true)));?></span><span class="score__separator">:</span><span class="score__loser"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_term_meta($terms[1]->term_id, 'teams_score', true)));?></span>
                    </p>
                    <p class="score__time">
                      <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_duration', true)));?> 
                    </p>
                  </div>
                  <p class="score__team score__team--away">
                    <img src="<?php echo esc_url(get_term_meta($terms[1]->term_id, 'teams_profile', true))?>" /><span><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms[1]->name));?></span>
                  </p>
                </div>                
                <?php                  
              }
            ?> 
            <div class="featured-live__actions">              
            <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'sc_watchLink', true));?>"><button class="btn btn--primary">Watch</button></a>
            </div>
          </div>
          <div class="card__footer card__footer--link">
            <a href="<?php the_permalink();?>">Match details</a>
          </div>
        </div>
      </section>
    <?php endif;?>
    <?php endwhile; ?>		
    <?php endif;?>
      <section class="leagues">
        <h2>
          Leagues<a href="<?php echo esc_url(get_option('scoreboardui_matches_link')); ?>"><button class="btn btn--no-bg btn--icon btn--round-lg" type="button"><svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M16,12A2,2 0 0,1 18,10A2,2 0 0,1 20,12A2,2 0 0,1 18,14A2,2 0 0,1 16,12M10,12A2,2 0 0,1 12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12M4,12A2,2 0 0,1 6,10A2,2 0 0,1 8,12A2,2 0 0,1 6,14A2,2 0 0,1 4,12Z" /></svg></button></a>
        </h2>
        <div class="card">
        <?php
        global $post;
        $terms = wp_get_post_terms( $post->ID, 'scoreboard_teams' );                           
        if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach($terms as $terms){
          ?>
          <div class="card__item league">
            <img alt="" class="league__logo" src="<?php echo esc_url(get_term_meta($terms->term_id, 'teams_profile', true))?>" />
            <div class="league__title">
              <h3>
              <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms->name));?> 
              </h3>
              <small><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms->description));?></small>
            </div>
            <span class="league__number"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_term_meta($terms->term_id, 'teams_score', true)));?></span>
          </div>
          <?php                  
            }
          }
          ?> 
        </div>
      </section>
    </div>
    <div class="col-right">
    <?php if(get_option('scoreboardui_title')){ ?>
      <section class="hero">
        <div class="hero__img">
          <img alt="" src="<?php echo esc_url(get_option('scoreboardui_url')); ?>" />
        </div>
        <h2 class="hero__title">
          <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_option('scoreboardui_title')));?> 
        </h2>
        <p class="hero__text">
        <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_option('scoreboardui_des')));?> 
        </p>
        <a href="<?php echo esc_url(get_option('scoreboardui_link')); ?>" class="hero__cta btn btn--secondary">More details</a>
      </section>
    <?php } ?>
      <section class="matches">
        <h2>
          Matches<a href="<?php echo esc_url(get_option('scoreboardui_teams_link')); ?>"><button class="btn btn--no-bg btn--icon btn--round-lg" type="button"><svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M16,12A2,2 0 0,1 18,10A2,2 0 0,1 20,12A2,2 0 0,1 18,14A2,2 0 0,1 16,12M10,12A2,2 0 0,1 12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12M4,12A2,2 0 0,1 6,10A2,2 0 0,1 8,12A2,2 0 0,1 6,14A2,2 0 0,1 4,12Z" /></svg></button></a>
        </h2>
        <div class="card">
          <div class="card__header matches__nav">
            <ul class="nav">
              <li class="nav-item">
                <a class="nav-link active">All matches</a>              
            </ul>            
          </div>
          <div class="card__body matches__data">
            <table>
              <thead>
                <tr>
                  <th>
                    Date
                  </th>
                  <th style="text-align: center;">
                    Match
                  </th>
                  <th>
                    Duration
                  </th>
                  <th>
                    Ranking
                  </th>                  
                  <th>
                    <span class="sr-only">Stats</span>
                  </th>
                </tr>
              </thead>
              <tbody>
              <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $scoreboard_ui_main_blog = new WP_Query(array(
                    'post_type'=>'scoreboard_ui', 
                    'paged' => $paged
                ));
                if($scoreboard_ui_main_blog->have_posts())	:	
                $count = 1;		
                while($scoreboard_ui_main_blog->have_posts())	: $scoreboard_ui_main_blog->the_post(); ?>
                <tr>
                  <td>
                    <span class="matches__time matches__time--live"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_date', true)));?>                   
                  </span><span class="tag tag--red tag--icon" style="color:<?php echo esc_attr(get_post_meta(get_the_ID(), 'sc_status_color', true));?>"><svg width="6" height="6" viewBox="0 0 8 8"><circle fill="currentColor" cx="4" cy="4" r="4"/></svg><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_status', true)));?></span>
                  </td>
                  <td>
                    <?php
                        global $post;
                        $terms = wp_get_post_terms( $post->ID, 'scoreboard_teams' );                           
                        if( ! empty( $terms ) && ! is_wp_error( $terms ) ) { ?>
                          <div class="score score--vertical">
                            <div class="score__team score__team--vertical">
                              <span><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms[0]->name));?></span><img alt="" src="<?php echo esc_url(get_term_meta($terms[0]->term_id, 'teams_profile', true))?>" />
                            </div>
                            <p class="score__result score__result--vertical">
                              <span class="score__goals"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_term_meta($terms[0]->term_id, 'teams_score', true)));?></span><span class="score__separator">:</span><span class="score__goals"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_term_meta($terms[1]->term_id, 'teams_score', true)));?></span>
                            </p>
                            <div class="score__team score__team--vertical">
                              <img alt="" src="<?php echo esc_url( get_term_meta($terms[1]->term_id, 'teams_profile', true))?>" /><span>
                              <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($terms[1]->name));?></span>
                            </div>
                          </div>
                          <?php                            
                        }
                      ?>                    
                  </td>
                  <td>
                    <span class="tag tag--green rating rating--up">
                    <?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_duration', true)));?></span>
                  </td>
                  <td>
                    <span class="tag rating"><?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html(get_post_meta(get_the_ID(), 'sc_ranking', true)));?></span>
                  </td> 
                  <td>                  
                    <a href="<?php the_permalink();?>" class="matches__stats btn btn--icon"><span class="sr-only">Stats</span><svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M16,12A2,2 0 0,1 18,10A2,2 0 0,1 20,12A2,2 0 0,1 18,14A2,2 0 0,1 16,12M10,12A2,2 0 0,1 12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12M4,12A2,2 0 0,1 6,10A2,2 0 0,1 8,12A2,2 0 0,1 6,14A2,2 0 0,1 4,12Z" /></svg></a>
                  </td>
                </tr>
                <?php endwhile; ?>		
	              <?php endif;?> 
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </main>
</div>
<!-- partial -->	
<?php
    return ob_get_clean();
}
add_shortcode('scoreboardui','scoreboardui_shortcode');
// Dashboard Front Show settings page
register_activation_hook(__FILE__, 'scoreboardui_plugin_activate');
add_action('admin_init', 'scoreboardui_plugin_redirect');
function scoreboardui_plugin_activate() {
    add_option('scoreboardui_plugin_do_activation_redirect', true);
}
function scoreboardui_plugin_redirect() {
    if (get_option('scoreboardui_plugin_do_activation_redirect', false)) {
        delete_option('scoreboardui_plugin_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("edit.php?post_type=post&page=scoreboardui_settings");
        }
    }
}
//side setting link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'scoreboardui_plugin_action_links' );
function scoreboardui_plugin_action_links( $actions ) {
   $actions[] = '<a href="'. esc_url( get_admin_url(null, 'edit.php?post_type=post&page=scoreboardui_settings') ) .'">Settings</a>';
   $actions[] = '<a href="https://m.me/md.shahinur.islam.96" target="_blank">Support for contact</a>';
   return $actions;
}
add_action('admin_menu', 'scoreboardui_register_my_custom_submenu_page'); 
function scoreboardui_register_my_custom_submenu_page() {
    //add_menu_page
  add_menu_page(  
    'Settings',
    'Scoreboard UI',
    'read',
    'scoreboardui_settings',
    'scoreboardui_submenu_page_callback',
    'dashicons-sos'    
    );     
    add_submenu_page(
        'scoreboardui_settings',
        'Set Score', 
        'Set Score',
        'manage_options',
        'edit.php?post_type=scoreboard_ui'   
        ); 
            
    add_submenu_page(
      'scoreboardui_settings',
      'Scoreboard Teams', 
      'Scoreboard Teams',
      'manage_options',
      'edit-tags.php?taxonomy=scoreboard_teams'   
      ); 
} 
function scoreboardui_submenu_page_callback() {
    ?>
<h1>
<?php esc_html_e( 'Welcome to Scoreboard UI.', 'scoreboard-ui' ); ?>
</h1>
<h3><?php esc_html_e( 'Copy and paste this shortcode here:', 'scoreboard-ui' );?></h3>
<p><?php esc_html_e( '[scoreboardui]', 'scoreboard-ui' );?></p> 
<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>	
	<p><strong><?php esc_html_e( 'Title:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_title" size="45" value="<?php echo esc_html(get_option('scoreboardui_title')); ?>" />
	<a><?php esc_html_e( 'Win $100 000 with Free Prediction', 'scoreboard-ui' );?></a>
	</p>
  <p><strong><?php esc_html_e( 'Short Description:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_des" size="45" value="<?php echo esc_html(get_option('scoreboardui_des')); ?>" />
	<a><?php esc_html_e( 'Predict the first goal of two different teams in two selected matches and win up to $100 000.', 'scoreboard-ui' );?></a>
	</p>
  <p><strong><?php esc_html_e( 'Photo URL:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_url" size="45" value="<?php echo esc_html(get_option('scoreboardui_url')); ?>" />
	<a><?php esc_html_e( 'https://link.png', 'scoreboard-ui' );?></a>
	</p>
  <p><strong><?php esc_html_e( 'Link:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_link" size="45" value="<?php echo esc_html(get_option('scoreboardui_link')); ?>" />
	<a><?php esc_html_e( 'https://link.com', 'scoreboard-ui' );?></a>
	</p>  
  <p><strong><?php esc_html_e( 'All Matches Link:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_matches_link" size="45" value="<?php echo esc_html(get_option('scoreboardui_matches_link')); ?>" />
	<a><?php esc_html_e( 'https://link.com', 'scoreboard-ui' );?></a>
	</p>  
  <p><strong><?php esc_html_e( 'All Teams Link:', 'scoreboard-ui' );?></strong><br />
	<input type="text" name="scoreboardui_teams_link" size="45" value="<?php echo esc_html(get_option('scoreboardui_teams_link')); ?>" />
	<a><?php esc_html_e( 'https://link.com', 'scoreboard-ui' );?></a>
	</p>
	<p><input type="submit" name="Submit" value="Store Options" /></p>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="scoreboardui_title,scoreboardui_des,scoreboardui_url,scoreboardui_link,scoreboardui_matches_link,scoreboardui_teams_link" />
</form>
<?php
}