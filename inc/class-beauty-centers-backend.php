<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WP_Beauty_Center_Backend')){
	class WP_Beauty_Center_Backend{
		public function __construct(){
           // add submenu page
			add_action('admin_menu', array($this,'register_beauty_center_submenu_page'));
			//add scripts to backend
			add_action('admin_enqueue_scripts', array($this,'beauty_center_backend_script'));
		}
		public function register_beauty_center_submenu_page() {
		    add_submenu_page('edit.php?post_type=beauty_center', esc_html__('Settings','beauty_center'), esc_html__('Settings','beauty_center'), 'manage_options', 'beauty_center_settings_page', array($this,'beauty_center_settings_page_callback'));
		}
		public function beauty_center_settings_page_callback() {
		    $beauty_center_API_KEY  = get_option('beauty_center_API_KEY');
		    $beauty_center_street_API_KEY  = get_option('beauty_center_street_API_KEY');
		    $map_options  = get_option('beauty_center_map');
		    $grid_options = get_option('beauty_center_grid');
		    $single_options = get_option('beauty_center_single');
		    $placeholder_setting = get_option('placeholder_settings');
		    include BEAUTY_CENTER_PLUGIN_PATH . 'inc/class-general-settings.php';
		}
		public function beauty_center_backend_script() {
			global $pagenow; ?>
		    <script>
		        var beauty_centers_json_encoded;
		        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		        var wpmsl_url = '<?php echo BEAUTY_CENTER_PLUGIN_URL; ?>';
		    </script>
		    <?php
            $beauty_center_api_key = get_option('BEAUTY_CENTER_API_KEY');
		    $post_type = get_post_type( get_the_ID() );

		    if( $post_type  == 'beauty_center'
		        || @$_GET['page'] == 'beauty_center_settings_page'
		        || @$_GET['page'] == 'import-export-submenu-page-partner'
		        || $post_type  == 'maps'
		        || $pagenow=='edit-tags.php'
		        || $pagenow=='term.php'
		        ) {
		        wp_enqueue_media();
		        wp_enqueue_script('beauty_center_backend_map', "https://maps.googleapis.com/maps/api/js?key=".$beauty_center_api_key."&libraries=places");
		        wp_enqueue_script('beauty_center_backend_script',  BEAUTY_CENTER_PLUGIN_URL . '/assets/js/backend_script.js', array('jquery'));
		        wp_enqueue_script('beauty_center_backend_select2', BEAUTY_CENTER_PLUGIN_URL . '/assets/js/select2.js');
		        wp_enqueue_style('beauty_center_backend_select2_style', BEAUTY_CENTER_PLUGIN_URL . '/assets/css/select2.css');
		        wp_enqueue_script('ldm_script_time_js', BEAUTY_CENTER_PLUGIN_URL . 'assets/js/jquery.timepicker.js');
		        wp_enqueue_style('ldm_script_time_css', BEAUTY_CENTER_PLUGIN_URL . 'assets/css/jquery.timepicker.css');
                wp_enqueue_style('ldm_script_jqueryui_css', BEAUTY_CENTER_PLUGIN_URL . 'assets/css/jquery-ui.css');
		    }
		    wp_enqueue_style('wpmsl_backend', BEAUTY_CENTER_PLUGIN_URL . 'assets/css/backend_styles.css');
		}


	}
	new WP_Beauty_Center_Backend();
}
