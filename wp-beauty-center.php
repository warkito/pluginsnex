<?php
/*
  Plugin Name: WP Beauty Center
  Plugin URI: #
  Description:  WP Beauty Center.
  Version: 1.0
  Author: Elliot VARIN
  Text Domain: beauty_center
  License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('BEAUTY_CENTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BEAUTY_CENTER_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once (BEAUTY_CENTER_PLUGIN_PATH . 'inc/class-beauty-centers-post.php');
require_once (BEAUTY_CENTER_PLUGIN_PATH . 'inc/class-beauty-centers-page.php');
require_once (BEAUTY_CENTER_PLUGIN_PATH . 'inc/class-beauty-centers-backend.php');
require_once (BEAUTY_CENTER_PLUGIN_PATH . 'inc/class-beauty-centers-frontend.php');

//create tables
register_activation_hook(__FILE__, 'beauty_center_plugin_activation');
function beauty_center_plugin_activation() {
    require_once BEAUTY_CENTER_PLUGIN_PATH . 'inc/install.php';
}
// Multi Languages code here //
add_action('init','wpmsl_add_translation');
function wpmsl_add_translation() {
     load_plugin_textdomain('beauty_center', FALSE,  basename( dirname( __FILE__ ) ) . '/languages/');
}

//add_filter( 'template_include', 'beauty_center_single_id_template', 99 );
function beauty_center_single_id_template( $template ) {
    $post_id = get_the_ID();
    $post = get_post($post_id);

    if ( is_single() &&  $post->post_type == "beauty_center" ) {
        $template = BEAUTY_CENTER_PLUGIN_PATH . 'templates/single-beauty_center.php';
    }

    return $template;
}
