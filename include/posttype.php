<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action( 'init', 'scoreboard_ui_post_type' );
function scoreboard_ui_post_type() {
	$labels = array(
		'name'               => _x( 'Scoreboard UI', 'post type general scoreboard-ui', 'scoreboard-ui' ),
		'singular_name'      => _x( 'Scoreboard UI', 'post type singular name', 'scoreboard-ui' ),
		'menu_name'          => _x( 'Scoreboard UI', 'admin menu', 'scoreboard-ui' ),
		'name_admin_bar'     => _x( 'Scoreboard UI', 'add new on admin bar', 'scoreboard-ui' ),
		'add_new'            => _x( 'Add New', 'Scoreboard UI', 'scoreboard-ui' ),
		'add_new_item'       => __( 'Add New Scoreboard', 'scoreboard-ui' ),
		'new_item'           => __( 'New Scoreboard', 'scoreboard-ui' ),
		'edit_item'          => __( 'Edit Scoreboard', 'scoreboard-ui' ),
		'view_item'          => __( 'View Scoreboard', 'scoreboard-ui' ),
		'all_items'          => __( 'Gallery', 'scoreboard-ui' ),
		'search_items'       => __( 'Search Scoreboard', 'scoreboard-ui' ),
		'parent_item_colon'  => __( 'Parent Scoreboard:', 'scoreboard-ui' ),
		'not_found'          => __( 'No Scoreboard found.', 'scoreboard-ui' ),
		'not_found_in_trash' => __( 'No Scoreboard found in Trash.', 'scoreboard-ui' )
	);
	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'scoreboard-ui' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'scoreboard_ui' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'menu_icon'           => 'dashicons-schedule',
		'supports'           => array( 'title', 'editor', 'thumbnail','sticky' )
	);
	register_post_type( 'scoreboard_ui', $args );
}
// hook into the init action and call scoreboard_ui_create_book_taxonomies when it fires
add_action( 'init', 'scoreboard_ui_create_book_taxonomies', 0 );
// create two taxonomies, genres and writers for the post type "book"
function scoreboard_ui_create_book_taxonomies() {
 // Add new taxonomy, make it hierarchical (like categories)
 $labels = array(
  'name'              => _x( 'Scoreboard Teams', 'taxonomy general name', 'scoreboard-ui' ),
  'singular_name'     => _x( 'Scoreboard Teams', 'taxonomy singular name', 'scoreboard-ui' ),
  'search_items'      => __( 'Search Scoreboard Teams', 'scoreboard-ui' ),
  'all_items'         => __( 'All Scoreboard Teams', 'scoreboard-ui' ),
  'parent_item'       => __( 'Parent Scoreboard Teams', 'scoreboard-ui' ),
  'parent_item_colon' => __( 'Parent Scoreboard Teams:', 'scoreboard-ui' ),
  'edit_item'         => __( 'Edit Scoreboard Teams', 'scoreboard-ui' ),
  'update_item'       => __( 'Update Scoreboard Teams', 'scoreboard-ui' ),
  'add_new_item'      => __( 'Add New Scoreboard Teams', 'scoreboard-ui' ),
  'new_item_name'     => __( 'New Scoreboard Teams Name', 'scoreboard-ui' ),
  'menu_name'         => __( 'Scoreboard Teams', 'scoreboard-ui' ),
 );
 $args = array(
  'hierarchical'      => true,
  'labels'            => $labels,
  'show_ui'           => true,
  'show_in_menu'       => false,
  'show_admin_column' => false,
  'query_var'         => true,
  'rewrite'           => array( 'slug' => 'scoreboard_teams' ),  
  'show_in_menu'               => 'edit.php?post_type=scoreboard_ui',
  'show_in_nav_menus'          => true, // Set this to true to display in navigation menus
  'show_in_rest'               => true,
 );

 register_taxonomy( 'scoreboard_teams', array( 'scoreboard_ui' ), $args );
}
// Step 1: Add the extra input field to the term edit form
function scoreboard_ui_teams_taxonomy_teams_profile($term) {
    $term_id = is_object($term) && property_exists($term, 'term_id') ? $term->term_id : null;
    $extra_field_value = $term_id !== null ? get_term_meta($term_id, 'teams_profile', true) : '';
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="teams_profile">Teams Profile URL</label>
        </th>
        <td>
            <?php
            // Generate nonce
            $nonce = wp_create_nonce('scoreboard_teams_profile_nonce');
            // Output the form with the nonce field
            ?>
            <?php wp_nonce_field('scoreboard_teams_profile_action', 'scoreboard_teams_profile_nonce'); ?>
            <!-- Other form fields go here -->
            <input type="text" name="teams_profile" value="<?php echo esc_attr($extra_field_value); ?>">
            <p class="description">Enter Teams Profile URL here.</p>
        </td>
    </tr>
    <?php
    $term_id = is_object($term) && property_exists($term, 'term_id') ? $term->term_id : null;
    $teams_score_value = $term_id !== null ? get_term_meta($term_id, 'teams_score', true) : '';
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="teams_score">Teams Score</label>
        </th>
        <td>
        <?php
            // Generate nonce
            $nonce = wp_create_nonce('scoreboard_teams_score_nonce');
            // Output the form with the nonce field
            ?>
            <?php wp_nonce_field('scoreboard_teams_score_action', 'scoreboard_teams_score_nonce'); ?>
            <!-- Other form fields go here -->
            <input type="text" name="teams_score" value="<?php echo esc_attr($teams_score_value); ?>"> 
            <p class="description">Enter Teams Score here.</p>
        </td>
    </tr>
    <?php
}
add_action( 'scoreboard_teams_add_form_fields', 'scoreboard_ui_teams_taxonomy_teams_profile', 10, 2);
add_action('scoreboard_teams_edit_form_fields', 'scoreboard_ui_teams_taxonomy_teams_profile', 10, 2);
// Step 2: Save the extra input field value when the term is updated
function scoreboard_taxonomy_teams_profile($term_id) {
    // Verify nonce
    if ( ! isset( $_POST['scoreboard_teams_profile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['scoreboard_teams_profile_nonce'] ) ) , 'scoreboard_teams_profile_nonce' ) )
     {
        // Nonce verification succeeded, process form data
        if (isset($_POST['teams_profile'])) {
            update_term_meta($term_id, 'teams_profile', sanitize_text_field($_POST['teams_profile']));
        }
    } else {
        // Nonce verification failed, handle accordingly (e.g., show an error message)
        // You might want to redirect or display an error message to the user
        wp_die('Security check failed. Please try again.');
    }
    // Verify nonce
    if ( ! isset( $_POST['scoreboard_teams_score_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['scoreboard_teams_score_nonce'] ) ) , 'scoreboard_teams_score_nonce' ) )
    {
        // Nonce verification succeeded, process form data
        if (isset($_POST['teams_score'])) {
            update_term_meta($term_id, 'teams_score', sanitize_text_field($_POST['teams_score']));
        }
    } else {
        // Nonce verification failed, handle accordingly (e.g., show an error message)
        // You might want to redirect or display an error message to the user
        wp_die('Security check failed. Please try again.');
    } 
}
add_action('created_scoreboard_teams', 'scoreboard_taxonomy_teams_profile', 10, 2);
add_action('edited_scoreboard_teams', 'scoreboard_taxonomy_teams_profile', 10, 2);
//ad meta box-----------------------
function scoreboard_ui_matabox(){
    add_meta_box( 
    'scoreboard_ui_meta',//id
    'Scoreboard Options',//title
    'scoreboard_ui_meta_callback',//callback
    'scoreboard_ui',//screen
    'normal',//context
    'high'//priority
    );
   }   
   add_action('add_meta_boxes','scoreboard_ui_matabox');     
   function scoreboard_ui_meta_callback($post){   
     $sc_date = get_post_meta($post->ID,'sc_date',true); 
     $sc_status = get_post_meta($post->ID,'sc_status',true); 
     $sc_status_color = get_post_meta($post->ID,'sc_status_color',true); 
     $sc_watchLink = get_post_meta($post->ID,'sc_watchLink',true); 
     $sc_ranking = get_post_meta($post->ID,'sc_ranking',true);  
     $sc_duration = get_post_meta($post->ID,'sc_duration',true); 
     $sc_other_text = get_post_meta($post->ID,'sc_other_text',true); 
     $sc_other_link = get_post_meta($post->ID,'sc_other_link',true); 
     wp_nonce_field('scoreboard_ui_save_meta','name_nonce');   
    ?> 
    <p><label>Data and Time </label><input type="text" name="sc_date" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_date));?>" placeholder="Data and Time" /></p>  
    <p><label>Status </label><input type="text" name="sc_status" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_status));?>" placeholder="Status" /></p> 
    <p><label>Status Color </label><input type="color" name="sc_status_color" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_status_color));?>" placeholder="Status Color" /></p> 
    <p><label>Watch Link </label><input type="text" name="sc_watchLink" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_watchLink));?>" placeholder="Watch Link" /></p> 
    <p><label>Ranking </label><input type="text" name="sc_ranking" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_ranking));?>" placeholder="Ranking" /></p> 
    <p><label>Durations </label><input type="text" name="sc_duration" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_duration));?>" placeholder="Durations" /></p> 
    <p><label>Other Text </label><input type="text" name="sc_other_text" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_other_text));?>" placeholder="Other Text" /></p> 
    <p><label>Other Link </label><input type="text" name="sc_other_link" value="<?php printf(esc_html__( '%s', 'scoreboard-ui' ),esc_html($sc_other_link));?>" placeholder="Other Link" /></p> 
   <?php   
   }     
   function scoreboard_ui_save_meta($post_id){ 
    //Check if our nonce is set   
    if(! isset($_POST['name_nonce'])){   
     return;   
    }   
    //Check if our nonce is valid   
    if ( ! isset( $_POST['name_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['name_nonce'] ) ) , 'scoreboard_ui_save_meta' ) )   { 
     return;   
    }   
    // Make sure that it(input) is set.   
    if(! isset($_POST['sc_status'])){   
     return;   
    }
    if(! isset($_POST['sc_status_color'])){   
        return;   
    }
    if(! isset($_POST['sc_watchLink'])){   
        return;   
    }
    if(! isset($_POST['sc_ranking'])){   
        return;   
    }
    if(! isset($_POST['sc_duration'])){   
        return;   
    }
    if(! isset($_POST['sc_other_text'])){   
        return;   
    }
    if(! isset($_POST['sc_other_link'])){   
        return;   
    }
    if(! isset($_POST['sc_date'])){   
        return;   
    }      
    $my_sc_date = sanitize_text_field($_POST['sc_date']);  
    update_post_meta($post_id,'sc_date',$my_sc_date); 
    //sanitize text fields   
    $my_sc_status = sanitize_text_field($_POST['sc_status']);   
    //Update post meta   
    update_post_meta($post_id,'sc_status',$my_sc_status);       
    $my_sc_status_color = sanitize_text_field($_POST['sc_status_color']);  
    update_post_meta($post_id,'sc_status_color',$my_sc_status_color);  
    $my_sc_watchLink = sanitize_text_field($_POST['sc_watchLink']);  
    update_post_meta($post_id,'sc_watchLink',$my_sc_watchLink);  
    $my_sc_ranking = sanitize_text_field($_POST['sc_ranking']);  
    update_post_meta($post_id,'sc_ranking',$my_sc_ranking);  
    $my_sc_duration = sanitize_text_field($_POST['sc_duration']);  
    update_post_meta($post_id,'sc_duration',$my_sc_duration); 
    $my_sc_other_text = sanitize_text_field($_POST['sc_other_text']);  
    update_post_meta($post_id,'sc_other_text',$my_sc_other_text); 
    $my_sc_other_link = sanitize_text_field($_POST['sc_other_link']);  
    update_post_meta($post_id,'sc_other_link',$my_sc_other_link); 
   } 
   add_action('save_post','scoreboard_ui_save_meta');
    function scoreboard_ui_show_sticky_option__post_edit($post) {
        if (current_user_can('edit_others_posts') && post_type_supports($post->post_type, 'sticky')) {
            $is_sticky = is_sticky($post->ID);
            $sticky_checkbox_checked = $is_sticky ? 'checked="checked"' : '';
            $sticky_span = '<span id="sticky-span" style="margin-left:0"><input id="sticky" name="sticky" type="checkbox" value="sticky" ' . $sticky_checkbox_checked . ' /> <label for="sticky" class="selectit">' . esc_html__('Stick this post to the front page') . '</label><br /></span>';
            echo $sticky_span;
        }
    }
    add_action('post_submitbox_start', 'scoreboard_ui_show_sticky_option__post_edit');

