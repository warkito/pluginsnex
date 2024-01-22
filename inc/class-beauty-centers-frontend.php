<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WP_Beauty_Center_Frontend')){
    /**
     * Beauty Center Frontend Class
     */
    class WP_Beauty_Center_Frontend{
        public function __construct(){
            //add scripts to frontend
            add_action('wp_enqueue_scripts', array($this,'beauty_center_frontend_script'),200);
            add_action( 'wp_ajax_set_schedules_content', [ $this, 'ajax_schedules_content' ] );
            add_action( 'wp_ajax_nopriv_set_schedules_content', [ $this, 'ajax_schedules_content' ] );
            add_shortcode('km',  array($this,'geo_distance'));
            //$last_part=  basename($_SERVER['REQUEST_URI']);
            $acf_shortcodes =array(
                'openhour',
                'heures_douvertures',
                "heuresdouvertureslanding",
                "contactlanding",
                'numero',
                'adresselanding',
                'heuresdopeningslanding',
                'codepostal',
                'villesproches',
                'nomducentrelanding',// "Centre Nom_Service Nom_ville
                'nomducentrelanding2',
                'nomducentrelanding3',
                'numerolanding',
                'numerolandinglink',
                'emaillanding',
                'emaillandinglink',
                'heures_$openings',
                'jourlanding',
                'google_maps',
                'googlemaplink',
                'lien_des_avis',
                'emaillandinglink',
                'liendoctolib',
                'infopratiques',
                'image_promotionnels',
                'note_total',
                'nombre_total_avis',
                'totalclients',
                'description_du_centre_copy',
                'description_du_centre',
                'prixhydrafacial',
                'prendrerdv',
                'nous_contacter',
                'lien_google_du_centre',
                'transport_en_communn');

            foreach ($acf_shortcodes as $field){
                add_shortcode( $field, function($atts ) use ( $field) {

                    global $post;
                    $output = "";
                    if($post->post_type=="page"){

                        $city_name = is_array($atts) && !empty($atts['city_name']) ?  sanitize_title($atts['city_name']) :"";
                        if(!empty($city_name)) {
                            $city_name = sanitize_title($city_name);
                            $center_id = $this->get_center_by_city($city_name);
                        }else{
                            $center_id = get_post_meta($post->ID, '_beauty_center_id', true);
                        }
                        // get value
                        if(!empty($center_id)){
                            ob_start();
                            $field_value= $this->get_center_meta_value($center_id,$field);
                            echo $field_value ? $field_value :"";//'__EMPTY__ '.$field;
                            $output = ob_get_contents();
                            ob_end_clean();

                        }
                    }

                    return $output;
                } );
            }
            /**
             * Last Added Fields
             */
            $others_fields = self::get_others_fields();
            foreach ($others_fields as $key => $label){
                add_shortcode( $key, function($atts ) use ( $key) {

                    global $post;
                    $output = "";
                    if($post->post_type=="page"){
                        $city_name = is_array($atts) && !empty($atts['city_name']) ?  sanitize_title($atts['city_name']) :"";
                        if(!empty($city_name)) {
                            $city_name = sanitize_title($city_name);
                            $center_id = $this->get_center_by_city($city_name);
                        }else{
                            $center_id = get_post_meta($post->ID, '_beauty_center_id', true);
                        }
                        // get value
                        if(!empty($center_id)){
                            $field = 'beauty_center_'.$key;
                            $field_value= get_post_meta($center_id,$field,true);
                            return $field_value ? $field_value :"";//'__EMPTY__ '.$field;

                        }
                    }

                    return $output;
                } );
            }
            /**
             * Override ACF Values
             */
            if(!is_admin()){
                add_filter("acf/load_value",function ($value,$post_id,$field) use ($acf_shortcodes){
                    if(in_array($field['name'],$acf_shortcodes)){
                        global $post;
                        $override="";
                        if($post && $post->post_type=="page"){
                            $center_id = get_post_meta($post->ID, '_beauty_center_id', true);
                            if(!empty($center_id)){
                                $override= $this->get_center_meta_value($center_id,$field['name']);
                            }
                        }
                        return  !empty($override) ? $override : $value;
                    }
                    return $value;
                },3,10);
            }
        }

        /**
         * @return array
         */
        public static function get_others_fields(){
            $other_fields= array();
            $other_fields["center_name"]=__('Center Name','beauty_center');
            $other_fields["center_name_1"]=__('Center Name 1','beauty_center');
            $other_fields["center_name_2"]=__('Center Name 2','beauty_center');
            $other_fields["center_name_3"]=__('Center Name 3','beauty_center');
            $other_fields["care_list"]=__('Care list','beauty_center');
            $other_fields["first_opening_date"]=__('First Opening Date','beauty_center');
            $other_fields["manager"]=__('Manager','beauty_center');
            $other_fields["short_codes"]=__('Shortcodes','beauty_center');
            $other_fields["popup"]=__('Popup','beauty_center');
            $other_fields["offer_link"]=__('Offer link','beauty_center');

            return $other_fields;
        }

        /**
         * @return array
         */
        public static function  shortcode_mapping(){
            $mapping['nearby_towns']='villesproches';
            $mapping['googlereviews_link']='googlereviews_link';
            $mapping['googlereviews_link']='googlemap_link';

            return $mapping;

        }

        /**
         * @return string
         */

        /**
         * @param $attributes
         * @return int
         */
        public  function get_center_by_city($attributes){
            $city_attr = sanitize_title($attributes['city']);
            if(!empty($city_attr)){
                $beauty_centers = get_posts(
                    array(
                        'posts_per_page' => -1,
                        'post_type' => 'beauty_center',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'beauty_center_city',
                                'field' => 'slug',
                                'terms' => $city_attr,
                            )
                        )
                    )
                );
                return is_array($beauty_centers) && isset($beauty_centers[0]->ID) ? $beauty_centers[0]->ID : 0;
            }

            return 0;
        }

        /**
         * @param $center_id
         * @param $shordcode
         * @return mixed|string
         */
        public  function get_center_meta_value($center_id,$shordcode){
            $override = "";
            switch ($shordcode){
                
                case "google_maps" :
                case "googlemaplink":
                    $override=  get_post_meta($center_id,'beauty_center_googlemap_link',true);
                    break;
                case "infopratiques" :
                    $override=  get_post_meta($center_id,'beauty_center_practical_info',true);
                    break;
                case "prixhydrafacial" :
                    $override=  get_post_meta($center_id,'beauty_center_service_price',true);
                    break;
                case "nombre_total_avis" :
                    $override=  get_post_meta($center_id,'beauty_center_total_reviews',true);
                    break;
                case "note_total" :
                    $override=  get_post_meta($center_id,'beauty_center_total_note',true);
                    break;
                case "heuresdopeningslanding" :
                case "heures_douvertures" :
                case "heuresdouvertureslanding" :
                    $opening_hours= get_post_meta($center_id,'beauty_center_opening_hours',true);
                    $override=  $opening_hours ?? nl2br($opening_hours);break;
                //$opening_stauts= $this->opening_stauts_content($center_id);
                //$override=  $opening_stauts ?? $opening_stauts;break;
                case "openhour" :
                    $opening_stauts= $this->opening_stauts_content($center_id);
                    $override=  $opening_stauts ?? $opening_stauts;break;

                case "liendoctolib" :
                    $override=  get_post_meta($center_id,'beauty_center_doclib_link',true); break;
                case "adresselanding" :
                    $override= get_post_meta($center_id,'beauty_center_address',true);break;
                case "numerolanding" :
                    $override= get_post_meta($center_id,'beauty_center_phone',true);break;
                case "numero" :
                    $override =  $this->get_contact_content($center_id);break;
                case "numerolandinglink" :
                    $override= "tel:".get_post_meta($center_id,'beauty_center_phone',true);break;
                case "codepostal" :
                    $override= get_post_meta($center_id,'beauty_center_zipcode',true);break;
                case "villesproches" :
                    $override= get_post_meta($center_id,'beauty_center_nearby_towns',true);break;
                case "emaillanding" :
                    $override= get_post_meta($center_id,'beauty_center_email',true);break;
                case "emaillandinglink" :
                    $override= 'mailto:'.get_post_meta($center_id,'beauty_center_email',true);break;
                case "transport_en_communn" :
                    $transport= get_post_meta($center_id,'beauty_center_transport',true);
                    $override=  $transport ?? nl2br($transport);break;
            }

            return $override;
        }

        /**
         * @param $center_id
         * @return string
         */
        public function get_contact_content($center_id){

            $phone= get_post_meta($center_id,'beauty_center_phone',true);
            $email= get_post_meta($center_id,'beauty_center_email',true);
            // Get the text of the number and email from ACF fields
            $output = '';
            // Create the clickable number link
            if ( !empty($phone)) {
                $output .= '<b><a href="tel:' . ($phone) . '">' . ($phone) . '</a></b> ';

            } // Insert line break
            if (!empty($email) && !empty($phone)) {
                $output .= '<br>';
            }

            // Create the clickable email link
            if (!empty($email)) {
                $output .= '<a href="email:' . ($email) . '">' . ($email) . '</a>';
            }

            return  $output ;
        }

        /**
         * @return void
         */
        public function beauty_center_frontend_script(){
            ?>
            <script>
                var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
            </script>
            <?php

            global $post;

            if ( is_a( $post, 'WP_Post' ) ) {
                wp_enqueue_script('beauty_center_js', BEAUTY_CENTER_PLUGIN_URL . '/assets/js/beauty-center.js', array('jquery'),time());

                wp_localize_script('beauty_center_js','beautyCenter',array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'security' => esc_html( wp_create_nonce( 'schedules-content-nonce' ) )
                ) );

            }
        }

        /**
         * @param $center_id
         * @return string
         */
        public function opening_stauts_content($center_id){

            //setlocale(LC_TIME, 'fr_FR.UTF8', 'fr_FR');
            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
            $output = "";
            $days_meta = get_post_meta($center_id, 'beauty_center_days', true);
            if(empty($days_meta) or !is_array($days_meta)){
                return "";
            }
            $working_days= $not_working_days= array();
            $schedules = array();
            foreach ($days as $day){
                if($days_meta[$day]){
                    if( $days_meta[$day]['status'] == '1'){
                        $key = $days_meta[$day]['start']." ".$days_meta[$day]['end'];
                        $working_days[$key][]=$day;
                        $schedules[$day] =$days_meta[$day]['start']." - ".$days_meta[$day]['end'];
                    }else{
                        $not_working_days[]=$day;
                        $schedules[$day] ="closed - closed";
                    }
                }
            }

            

            foreach ($working_days as $time => $days){
                $working_day_label = implode(', ',$this->days_translations($days));
                // $output .= '<span class="status-ouvert">'.   $working_day_label.' : '.$time.'.</span><br>';
            }

            if(!empty($not_working_days)){
                $time_label =implode(', ',$this->days_translations($not_working_days)) .': Fermé';
                //$output .= '<span class="status-ferme">'. $time_label.'.</span><br>';
            }


            $output .= $this->get_schedules_content($schedules,$center_id);

            return $output;
        }

        /**
         * @param $schedules
         * @param $center_id
         * @return string
         */
public function get_schedules_content($schedules, $center_id) {
    $output = '';
    $current_day = date('l'); 
    $current_time = date('H:i');
    $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

    if (!empty($schedules)) {
        foreach ($days as $day) {
            $day = trim($day);
            if (!isset($schedules[$day])) {
                continue;
            }
            $opening_time = trim($schedules[$day]);
            list($opening, $closing) = explode(' - ', $opening_time);

            $status = '';
            $class = '';

            if ($opening == '00:00' && $closing == '00:00') {
                $status = __('Fermé exceptionnellement', "beauty_center");
                $class = 'status-ferme';
            } elseif ($opening == '01:00' && $closing == '01:00') {
                $status = __('Nouveau centre à venir.', "beauty_center");
                $class = 'status-opening';
            } elseif (strtolower($opening) == 'na' && strtolower($closing) == 'na') {
                $status = __('Votre centre est fermé', "beauty_center");
                $class = 'status-ferme';
            } elseif (strtolower($opening) == 'closed' && strtolower($closing) == 'closed') {
                $status = __('Fermé', "beauty_center");
                $class = 'status-ferme';
            } else {
                $current_time_obj = DateTime::createFromFormat('H:i', $current_time);
                $opening_obj = DateTime::createFromFormat('H:i', $opening);
                $closing_obj = DateTime::createFromFormat('H:i', $closing);

                if ($current_time_obj >= $opening_obj && $current_time_obj <= $closing_obj) {
                    $interval = $current_time_obj->diff($closing_obj);
                    $minutes_to_close = ($interval->h * 60) + $interval->i;

                    if ($minutes_to_close <= 60) {
                        $status = 'Ferme dans ' . $minutes_to_close . ' minutes';
                        $class = 'status-bientot';
                    } else {
                        $status = 'Ouvert jusqu\'à ' . $closing_obj->format('H:i');
                        $class = 'status-ouvert';
                    }
                } else {
                    $status = 'Fermé';
                    $class = 'status-ferme';
                }
            }

            if (strtolower($current_day) == strtolower($day)) {
                if ($class == 'status-ferme') {
    $status = $this->next_opening_time($schedules, $current_day, $current_time);
}
                $output .= '<span data-center_id="' . $center_id . '" class="schedules-status ' . $class . '">' . $status . '</span>';
                break;
            }
        }
    } else {
        $output .= '<span data-center_id="' . $center_id . '" class="schedules-status status-indisponible">Pas d\'horaires disponibles.</span>';
    }

    return $output;
}
private function next_opening_time($schedules, $current_day, $current_time) {
    $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $current_day_index = array_search($current_day, $days);
    $current_time_obj = DateTime::createFromFormat('H:i', $current_time);
    $translated_current_day = $this->days_translations(array($current_day))[0];

    // Check today's schedule first
    if (isset($schedules[$current_day])) {
        list($opening, $closing) = explode(' - ', $schedules[$current_day]);
        $opening_obj = DateTime::createFromFormat('H:i', $opening);

        if ($current_time_obj < $opening_obj) {
            return "Fermé, réouvre <b>" . $translated_current_day . "</b> à <b>" . $opening . "</b>";
        }
    }

    // If the store isn't opening today, check the next days
    for ($i = 1; $i <= 7; $i++) {
        $next_day_index = ($current_day_index + $i) % 7;
        $next_day = $days[$next_day_index];

        if (isset($schedules[$next_day])) {
            list($opening, $closing) = explode(' - ', $schedules[$next_day]);
            
            if (strtolower($opening) != 'na' && strtolower($closing) != 'na' && strtolower($opening) != 'closed' && strtolower($closing) != 'closed') {
                $translated_next_day = $this->days_translations(array($next_day))[0];
                return "Fermé, réouvre <b>" . $translated_next_day . "</b> à <b>" . $opening . "</b>";
            }
        }
    }
    return "Fermé, réouverture inconnue";
}


public function find_next_opening_day($schedules) {
    $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $current_day = date('l');
    $current_day_index = array_search($current_day, $days);

    for ($i = 1; $i <= 7; $i++) {
        $next_day_index = ($current_day_index + $i) % 7;
        $next_day = $days[$next_day_index];

        if (isset($schedules[$next_day]) && $schedules[$next_day] != 'Closed') {
            list($opening, $closing) = explode(' - ', $schedules[$next_day]);
            return "Fermé, réouvre " . $next_day . " à " . $opening;
        }
    }
    return "Fermé, pas d'horaires disponibles.";
}

        /**
         * @return void
         */
        public function ajax_schedules_content(){
            check_ajax_referer( 'schedules-content-nonce', 'nonce' );
            $center_id= $_POST['center_id'];
            $post = get_post($center_id);

            if(is_a( $post, 'WP_Post' )  && $post->post_type=="beauty_center"){
                $html = $this->opening_stauts_content($center_id);
            }

            echo $html;
            wp_die();
        }

        /**
         * @param $days
         * @return array
         */
        public function days_translations($days){
            $translations = array();
            $english["Monday"] ="Lundi" ;
            $english["Tuesday"] ="Mardi" ;
            $english["Wednesday"] ="Mercredi" ;
            $english["Thursday"] ="Jeudi" ;
            $english["Friday"] ="Vendredi" ;
            $english["Saturday"] ="Samedi" ;
            $english["Sunday"] ="Dimanche" ;
            foreach ($days as $day){
                $translations[]=  !empty($english[$day]) ? $english[$day] : "";
            }

            return $translations;

        }
    }

}
add_action('init',function (){
    new WP_Beauty_Center_Frontend();
});