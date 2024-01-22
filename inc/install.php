<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//create tables
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $wpdb;
    if (!empty($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if (!empty($wpdb->collate))
        $charset_collate .= " COLLATE $wpdb->collate";

     $placeholders = get_option('placeholder_settings');
    if(empty($placeholders)) {
         $placeholders['location_not_found'] = __('No details available for input:','beauty_center');
         $placeholders['select_services_txt'] = __('Select services','beauty_center');
         $placeholders['select_city_txt'] = __('Select city','beauty_center');
         update_option('placeholder_settings',$placeholders);
    } else{
		$placeholders['location_not_found'] = __('No details available for input:','beauty_center');
		$placeholders['search_options_btn'] = __('Search Options','beauty_center');
		update_option('placeholder_settings',$placeholders);
	}
