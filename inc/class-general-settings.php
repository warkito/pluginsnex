<div class="beauty_center_settings_div">
    <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="updated below-h2"><p><?php esc_html_e("Settings updated.", 'beauty_center'); ?></p></div>
    <?php endif; ?>

</div>
<?php
if(!class_exists('WP_Beauty_Center_Settings')){
class WP_Beauty_Center_Settings {
    public function __construct(){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $tabs = array(
        'basic-settings'=> esc_html__( 'Initialize', 'beauty_center' ),
        'map-settings'  => esc_html__( 'Map Settings', 'beauty_center' ),
        'dynamic-text'  => esc_html__('Placeholder Settings','beauty_center'),
        'grid-settings' => esc_html__('Grid Settings','beauty_center'),
        'single-page-settings' => esc_html__('Single Page Settings','beauty_center'),
        );
        $this->init_tabs(apply_filters('wpml_setting_tabs',$tabs));
        $this->current_tab(apply_filters('wpml_current_tab',$current));
    }
    public function init_tabs($tabs=array()){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="edit.php?post_type=beauty_center&page=beauty_center_settings_page&tab=' . esc_attr($tab).'">'.esc_html($name).'</a>';
        }
        $html .= '</h2>';
        echo $html;

    }
    public function current_tab($current='basic-settings'){
        switch ($current) {
        case 'basic-settings':
            $this->initialize_settings();
            break;
        case 'map-settings':
            $this->map_settings();
            break;
        case 'dynamic-text':
            $this->dynamic_text_settings();
            break;
        case 'grid-settings':
            $this->grid_settings();
            break;
        case 'single-page-settings':
            $this->single_page_settings();
            break;
        default:
            $this->initialize_settings();
            break;
        }
    }
    public function initialize_settings(){
        $_POST = array_map( 'stripslashes_deep', $_POST );
        if (isset($_POST['api-settings'])) {
            update_option('beauty_center_API_KEY', $_POST['beauty_center_API_KEY']);
          //  update_option('beauty_center_street_API_KEY', $_POST['beauty_center_street_API_KEY']);
            if(isset($_POST['map_landing_address']))
                update_option('map_landing_address', $_POST['map_landing_address']);
        }
        $beauty_center_API_KEY  = get_option('beauty_center_API_KEY');
        $map_landing_address  = get_option('map_landing_address');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Google Maps Api", 'beauty_center'); ?></span></h3>
                        <div class="inside beauty_center_singel_page_settings">
                        <table class="widefat" style="border: 0px;">
                        <tr>
                            <th><label  for="beauty_center_API_KEY"><?php esc_html_e("Google Maps API KEY", 'beauty_center'); ?>:</label></th>
                            <td><input value="<?php print_r($beauty_center_API_KEY); ?>" type="text" id="beauty_center_API_KEY" name="beauty_center_API_KEY"  class="regular-text"></td>
                        </tr>
                        <?php if(!empty($beauty_center_API_KEY)): ?>
                        <tbody>
                        <tr>
                            <td colspan="2"><h3><?php esc_html_e("Map Landing Address", 'beauty_center'); ?></h3></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Address", 'beauty_center'); ?></td>
                            <td>
                                <input id="beauty_center_address" class="regular-text" type="text" value="<?php echo isset($map_landing_address['address']) ? $map_landing_address['address'] : ''; ?>" name="map_landing_address[address] "/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Country", 'beauty_center'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[country]" id="beauty_center_country">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allCountries = $wpdb->get_results("SELECT * FROM beauty_center_country");
                                   $selectedCountry =  isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
                                    foreach ($allCountries as $country) {
                                        ?>
                                        <option value="<?php echo $country->name; ?>" <?php  echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
                            <td><?php esc_html_e("State", 'beauty_center'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[state]" id="beauty_center_state">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allStates = $wpdb->get_results("SELECT * FROM beauty_center_state");
                                    $selectedState = isset($map_landing_address['state']) ? $map_landing_address['state'] : '';
                                    foreach ($allStates as $state) {
                                        ?>
                                        <option value="<?php echo $state->name; ?>" <?php echo ($selectedState == $state->name) ? "selected" : ""; ?>><?php echo $state->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("City", 'beauty_center'); ?></td>
                            <td>
                                <input id="beauty_center_city" type="text" class="regular-text" value="<?php echo isset($map_landing_address['city']) ? $map_landing_address['city'] : ''; ?>" name="map_landing_address[city]"/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Postal Code", 'beauty_center'); ?></td>
                            <td>
                                <input id="beauty_center_zipcode" class="regular-text" type="text" value="<?php echo isset($map_landing_address['zipcode']) ? $map_landing_address['zipcode'] : ''; ?>" name="map_landing_address[zipcode]"/>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="2">
                            <p><?php esc_html_e('Select default location for marker from bottom','beauty_center'); ?></p>
                            <input type="hidden" value="<?php echo isset($map_landing_address['lat']) ? $map_landing_address['lat'] : ''; ?>" name="map_landing_address[lat]" id="beauty_center_lat"/>
                            <input type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>" name="map_landing_address[lng]" id="beauty_center_lng"/>
                            <div id="map-container" style="position: relative;">
                                <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;">
                                    <div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div>
                                </div>
                                <div id="map-canvas" style="height: 200px;width: 100%;"></div>
                            </div>
                            <script>
                                jQuery(document).ready(function (jQuery) {
                                      initializeMapBackend();
                                });
                            </script>
                            </td></tr></tbody>
                              <?php else: ?>
                              <tr><td colspan="2"><?php esc_html_e('To set map landing address please add API key first.','beauty_center'); ?></td></tr>
                            <?php endif; ?>

                            <tr><td colspan="2">
                                <input type="submit" class="button-primary" name="api-settings" value="<?php esc_html_e("Save Changes", 'beauty_center'); ?>">
                            </td></tr>
                            </table>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
        <?php
    }
    public function map_settings(){
        if (isset($_POST['map-settings'])) {
            $_POST['beauty_center_map']['custom_style']=isset($_POST['beauty_center_map']['custom_style']) ? stripslashes($_POST['beauty_center_map']['custom_style']) : '';
            update_option('beauty_center_map', $_POST['beauty_center_map']);
        }
        $map_options  = get_option('beauty_center_map'); ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Map settings -->
                <form method="POST">
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Map Settings", 'beauty_center'); ?></span></h3>
                        <div class="inside beauty_center_map_settings">
                            <table>
                                <tbody>
                            <tr>
                                <td><label title="Enable the display of map on the frontend" for="beauty_center_map_enable"><?php esc_html_e("Show map on frontend", 'beauty_center'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="beauty_center_map[enable]" >
                                <input <?php echo ($map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_map_enable" name="beauty_center_map[enable]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Width pixels" for="beauty_center_map_width"><?php esc_html_e('Map Width', 'beauty_center'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['width']; ?>" type="text" id="beauty_center_map_width" name="beauty_center_map[width]" size="4">
                                <select name="beauty_center_map[widthunit]" id="beauty_center_map_widthunit" >
                                    <option <?php  ($map_options['widthunit'] == 'px') ?"selected": ""; ?> selected value="px">PX</option>
                                    <option <?php  echo ($map_options['widthunit'] == '%') ?"selected": ""; ?>  value="%">%</option>
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Height pixels. Min height 500px" for="beauty_center_map_height"><?php esc_html_e("Map Height ", 'beauty_center'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['height']; ?>" type="text" id="beauty_center_map_height"  min="550" max="800" name="beauty_center_map[height]" size="4" >
                                <select name="beauty_center_map[heightunit]" id="beauty_center_map_heightunit" >
                                    <option <?php  echo ($map_options['heightunit'] == 'px') ?"selected": ""; ?>  value="px">PX</option>
                                    <?php /* <option <?php  ($map_options['heightunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Select Map Type','beauty_center'); ?>" for="beauty_center_map_type"><?php esc_html_e("Map Type", 'beauty_center'); ?>:</label></td>
                                <td><select name="beauty_center_map[type]" id="beauty_center_map_type">
                                    <option <?php echo ($map_options['type'] == 'roadmap') ?"selected": ""; ?> value="roadmap"><?php esc_html_e('Roadmap','beauty_center');?></option>
                                    <option <?php echo ($map_options['type'] == 'hybrid') ?"selected": ""; ?> value="hybrid"><?php esc_html_e('Hybrid','beauty_center');?></option>
                                    <option <?php echo ($map_options['type'] == 'satellite') ?"selected": ""; ?> value="satellite"><?php esc_html_e('Satellite','beauty_center');?></option>
                                    <option <?php echo ($map_options['type'] == 'terrain') ?"selected": ""; ?> value="terrain"><?php esc_html_e('Terrain','beauty_center');?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Choose the unit of search km/mile','beauty_center'); ?>" for="beauty_center_map_unit"><?php esc_html_e("Search Unit", 'beauty_center'); ?>:</label></td>
                                <td><select name="beauty_center_map[unit]" id="beauty_center_map_unit">
                                    <option <?php echo ($map_options['unit'] == 'km') ? "selected": ""; ?> value="km">Km</option>
                                    <option <?php echo ($map_options['unit'] == 'mile') ? "selected": ""; ?> value="mile">Mile</option>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Choose search options here. the default one will be between square brakets','beauty_center'); ?>" for="beauty_center_map_radius"><?php esc_html_e("Search radius options", 'beauty_center'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['radius']; ?>" type="text" id="beauty_center_map_radius" name="beauty_center_map[radius]" >
                                <div class="beauty_center_tip">e.g: 5,10,[25],50,100,200,500</div></td>
                            </tr>
                            <tr>
                                <td><label title="Show street control on the map in frontend" for="beauty_center_map_streetViewControl"><?php esc_html_e("Show street view control", 'beauty_center'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="beauty_center_map[streetViewControl]" >
                                <input <?php echo ($map_options['streetViewControl'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_map_streetViewControl" name="beauty_center_map[streetViewControl]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Scroll Map to screen after search" for="beauty_center_map_scroll_to_top"><?php esc_html_e("Scroll to map top after search", 'beauty_center'); ?>?</label></td>
                                <td><input <?php echo (isset($map_options['mapscrollsearch']) && ($map_options['mapscrollsearch'])==1)?'checked':''; ?> value="1" type="checkbox" id="beauty_center_map_scroll_to_top" name="beauty_center_map[mapscrollsearch]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable the user to change the map type from the frontend" for="beauty_center_map_mapTypeControl"><?php esc_html_e("Show map type control", 'beauty_center'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="beauty_center_map[mapTypeControl]" >
                                <input <?php echo ($map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_map_mapTypeControl" name="beauty_center_map[mapTypeControl]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable/Disable zoom by scroll on map" for="beauty_center_map_scroll"><?php esc_html_e('Zoom by scroll on map', 'beauty_center'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="beauty_center_map[scroll]" >
                                <input <?php echo ($map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_map_scroll" name="beauty_center_map[scroll]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Display default Map Search" for="beauty_center_default_search"><?php esc_html_e('Show Map Search options', 'beauty_center'); ?></label></td>
                                <td><input value="0" type="hidden" name="beauty_center_map[default_search]" >
                                <input <?php echo ($map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_default_search" name="beauty_center_map[default_search]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Hide Field Options" for="beauty_center_default_search"><?php esc_html_e('Hide Fields for Search', 'beauty_center'); ?></label></td>
                                <td><ul class="hide_fields">
                                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="beauty_center_map[search_field_get_my_location]" ><?php esc_html_e('Get My Location','beauty_center');?></li>
                                    <li>
            <input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="beauty_center_map[search_field_location]" ><?php esc_html_e('Location Field','beauty_center');?>
                                    </li>
                                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="beauty_center_map[search_field_radius]" ><?php esc_html_e('Radius Field','wpmsl');?></li>
                                    <li><input <?php echo (isset($map_options['city']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="beauty_center_map[city]" ><?php esc_html_e('City Field','beauty_center');?></li>
                                    <li><input <?php echo (isset($map_options['tag']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="beauty_center_map[tag]" ><?php esc_html_e('Tags Field','beauty_center');?></li>
                                </ul></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Map Search Open as Default','beauty_center'); ?>" for="map_window_open"><?php esc_html_e("Map Search Open as Default", 'beauty_center'); ?></label></td>
                                <td><input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="beauty_center_map[map_window_open]" ></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Switch To RTL','beauty_center'); ?>" for="rtl_enabled"><?php esc_html_e("Switch To RTL", 'beauty_center'); ?></label></td>
                                <td><input <?php echo (isset($map_options['rtl_enabled']))?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="beauty_center_map[rtl_enabled]" ></td>
                            </tr>
                            <tr><td colspan="2"><b><?php esc_html_e('Map Styles','beauty_center');?></b></td></tr>
                            <tr><td colspan="2">
                            <div class="map_Styles_div">
                            <p>
                                <label title="<?php esc_html_e('Standard Map','beauty_center'); ?>" for="beauty_center_map_style1">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/staticmap.png'); ?>" />
                                    <?php esc_html_e("Standard Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 1) ? 'checked':''; ?> value="1" type="radio" id="beauty_center_map_style1" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Silver Map','beauty_center'); ?>" for="beauty_center_map_style2">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/silver.png'); ?>" />
                                    <?php esc_html_e("Silver Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 2)?'checked':''; ?> value="2" type="radio" id="beauty_center_map_style2" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Retro Map','beauty_center'); ?>" for="beauty_center_map_style3">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/retro.png'); ?>" />
                                    <?php esc_html_e("Retro Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 3)?'checked':''; ?> value="3" type="radio" id="beauty_center_map_style3" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Dark Map','beauty_center'); ?>" for="beauty_center_map_style4">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/dark.png'); ?>" />
                                    <?php esc_html_e("Dark Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 4)?'checked':''; ?> value="4" type="radio" id="beauty_center_map_style4" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Night Map','beauty_center'); ?>" for="beauty_center_map_style5">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/night.png'); ?>" />
                                    <?php esc_html_e("Night Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 5)?'checked':''; ?> value="5" type="radio" id="beauty_center_map_style5" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Aubergine Map','beauty_center'); ?>" for="beauty_center_map_style6">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/aubergine.png'); ?>" />
                                    <?php esc_html_e("Aubergine Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 6)?'checked':''; ?> value="6" type="radio" id="beauty_center_map_style6" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Basic Map','beauty_center'); ?>" for="beauty_center_map_style7">
                                    <img src="<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/basic.png'); ?>" />
                                    <?php esc_html_e("Basic Map", 'beauty_center'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 7)?'checked':''; ?> value="7" type="radio" id="beauty_center_map_style7" name="beauty_center_map[map_style]" >
                                </label>
                            </p>
                        </div></td>
                        </tr>
                            <tr>
                            <td>
                                <div style="clear: both;"></div>
                                <label title="Choose the color of user marker" for="beauty_center_map_type"><?php esc_html_e("User Marker", 'beauty_center'); ?>:</label>
                            </td>
                            <td><ul class="default_markers">
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/blue.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="beauty_center_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/red.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="beauty_center_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/green.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="beauty_center_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/orange.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="beauty_center_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/purple.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="beauty_center_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL . 'assets/img/yellow.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="beauty_center_map[marker1]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                              <td><?php esc_html_e('or add custom marker url','beauty_center');?></td>
                              <?php
                                    if(isset($map_options['marker1_custom']) && !empty($map_options['marker1_custom'])){
                                        $marker1=$map_options['marker1_custom'];
                                        $class='wpmsl_custom_marker';
                                        $uploadRemove=__('Remove','beauty_center');
                                    }
                                    else{
                                        $marker1=BEAUTY_CENTER_PLUGIN_URL . 'assets/img/upload.png';
                                        $class='wpmsl_custom_marker_upload';
                                        $uploadRemove =__('Upload','beauty_center');
                                    }

                                ?>
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="beauty_center_map[marker1_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div></td>
                            </tr>
                            <tr>
                            <td>
                                <label title="Choose the color of Beauty Center marker" for="beauty_center_map_type"><?php esc_html_e("Beauty Center Marker", 'beauty_center'); ?>:</label>
                            </td>
                            <td>
                            <ul class="default_markers">
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/blue.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'blue.png')? 'checked':''; ?> type="radio" value="blue.png" name="beauty_center_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/red.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="beauty_center_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/green.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="beauty_center_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/orange.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="beauty_center_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/purple.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="beauty_center_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(BEAUTY_CENTER_PLUGIN_URL.'assets/img/yellow.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="beauty_center_map[marker2]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                               <td><?php esc_html_e('or add custom marker url','beauty_center');?></td>
                                <?php
                                    if(isset($map_options['marker2_custom']) && !empty($map_options['marker2_custom'])){
                                        $marker2=$map_options['marker2_custom'];
                                        $class='wpmsl_custom_marker';
                                        $uploadRemove=esc_html__('Remove','beauty_center');
                                    }
                                    else{
                                        $marker2=BEAUTY_CENTER_PLUGIN_URL . 'assets/img/upload.png';
                                        $class='wpmsl_custom_marker_upload';
                                        $uploadRemove =esc_html__('Upload','beauty_center');
                                    }

                                ?>
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker2; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker2 : ''; ?>" name="beauty_center_map[marker2_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div>
                              </td>
                            </tr>
                            <?php echo do_action('wpmsl_private_marker_settings'); ?>
                            <tr>
                                <td colspan="2"><label title="<?php esc_html_e('You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content','beauty_center'); ?>" for="beauty_center_map_infowindow"><b><?php esc_html_e("Info Window Content", 'beauty_center'); ?></b>: </label><p class="beauty_center_tip">placeholders: {image} {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</p>
                                <textarea name="beauty_center_map[infowindow]" rows="10" cols="70" id="beauty_center_map_infowindow" class="widefat"><?php echo $map_options['infowindow']; ?></textarea></td>
                            </tr>
                             <tr>
                                <td colspan="2"><label title="<?php esc_html_e('You can customize the look of the map by adding styles here','beauty_center'); ?>" for="beauty_center_map_style"><b><?php esc_html_e("Customised Map Style", 'beauty_center'); ?></b>: <p class="beauty_center_tip"><?php esc_html_e('You can get some styles from','beauty_center');?><a target="_blanck" href="https://snazzymaps.com"> <?php _e('Snazzy Maps','beauty_center');?></a></p> </label>
                                <textarea name="beauty_center_map[custom_style]"  rows="10" cols="70" id="beauty_center_map_style" class="widefat"><?php echo stripslashes($map_options['custom_style']); ?></textarea></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="map-settings" value="<?php esc_html_e("Save Changes", 'beauty_center'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
    public function dynamic_text_settings(){
         //handle save single page settings
        if (isset($_POST['placeholder-setting'])) {
            $placeholders = array();
            $placeholders['get_location_btn_txt'] = $_POST['get_location_btn_txt'];
            $placeholders['search_btn_txt'] = $_POST['search_btn_txt'];
            $placeholders['getdirection_btn_txt'] = $_POST['getdirection_btn_txt'];
            $placeholders['enter_location_txt'] = $_POST['enter_location_txt'];
            $placeholders['select_city_txt'] = $_POST['select_city_txt'];
            $placeholders['select_services_txt'] = $_POST['select_services_txt'];
            $placeholders['search_options_btn']=$_POST['search_options_btn'];
            $placeholders['location_not_found'] = $_POST['location_not_found'];
            $placeholders['beauty_center_list'] = $_POST['beauty_center_list'];
            $placeholders['visit_website'] = $_POST['visit_website'];
            update_option('placeholder_settings',$placeholders);
        }
        $placeholder_setting = get_option('placeholder_settings');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
        <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Placeholder Text", 'beauty_center'); ?></span></h3>
                        <div class="inside beauty_center_grid_settings">
                        <table>
                            <tbody>
                            <tr><th><?php esc_html_e('Get Location Text Button','beauty_center');?></th>
                            <td><input type="text" name="get_location_btn_txt" value="<?php echo !empty($placeholder_setting['get_location_btn_txt']) ? esc_attr($placeholder_setting['get_location_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
							<tr><th><?php esc_html_e('Search Button Text','beauty_center');?></th>
                            <td><input type="text" name="search_btn_txt" value="<?php echo !empty($placeholder_setting['search_btn_txt']) ? esc_attr($placeholder_setting['search_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
							<tr><th><?php esc_html_e('Get Direction Text','beauty_center');?></th>
                            <td><input type="text" name="getdirection_btn_txt" value="<?php echo !empty($placeholder_setting['getdirection_btn_txt']) ? esc_attr($placeholder_setting['getdirection_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Enter Location Text','beauty_center');?></th>
                            <td><input type="text" name="enter_location_txt" value="<?php echo !empty($placeholder_setting['enter_location_txt']) ? esc_attr($placeholder_setting['enter_location_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Select City','beauty_center');?></th>
                            <td><input type="text" name="select_city_txt" value="<?php echo !empty($placeholder_setting['select_city_txt']) ? esc_attr($placeholder_setting['select_city_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Select Tags','beauty_center');?></th>
                            <td><input type="text" name="select_services_txt" value="<?php echo !empty($placeholder_setting['select_services_txt']) ? esc_attr($placeholder_setting['select_services_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Search Options Button Text','beauty_center');?></th>
                            <td><input type="text" name="search_options_btn" value="<?php echo !empty($placeholder_setting['search_options_btn']) ? esc_attr($placeholder_setting['search_options_btn']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                             <tr><th><?php esc_html_e('Location not found text','beauty_center');?></th>
                            <td><input type="text" name="location_not_found" value="<?php echo !empty($placeholder_setting['location_not_found']) ? esc_attr($placeholder_setting['location_not_found']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Beauty Center list text','beauty_center');?></th>
                            <td><input type="text" name="beauty_center_list" value="<?php echo !empty($placeholder_setting['beauty_center_list']) ? esc_attr($placeholder_setting['beauty_center_list']) : '' ;?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Visit Website text','beauty_center');?></th>
                            <td><input type="text" name="visit_website" value="<?php echo !empty($placeholder_setting['visit_website']) ? esc_attr($placeholder_setting['visit_website']) : '' ;?>" class="regular-text" /></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="placeholder-setting" value="<?php esc_html_e("Save Changes", 'beauty_center'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
              </div>
        </div>
    </div>
        <?php
    }
    public function grid_settings(){
        //handle save grid settings
        if (isset($_POST['grid-settings'])) {
            //$_POST['beauty_center_grid']['columns'] = explode(",", $_POST['beauty_center_grid']['columns']);
            update_option('beauty_center_grid', $_POST['beauty_center_grid']);
        }
        $grid_options = get_option('beauty_center_grid');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Grid settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle">
                            <span><?php esc_html_e("Grid Settings", 'beauty_center'); ?></span></h3>
                        <div class="inside beauty_center_grid_settings">
                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php esc_html_e("Show the results in grid in the frontend","beauty_center"); ?>" for="beauty_center_grid_enable"><?php esc_html_e("Show grid on frontend", 'beauty_center'); ?>?</label>
                            </th><td>
                                <input value="0" type="hidden" name="beauty_center_grid[enable]" >
                                <input <?php echo ($grid_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_grid_enable" name="beauty_center_grid[enable]" >
                           </td> </tr>

                            <tr><th>
                                <label title="<?php esc_html_e("Maximum number of markers to be displayed","beauty_center") ?>" for="beauty_center_grid_number"><?php esc_html_e("Maximum number of markers to be displayed", 'beauty_center'); ?>:</label>
                                </th><td>
                                <input value="<?php echo isset($grid_options['total_markers']) ? trim($grid_options['total_markers']) : '-1'; ?>" type="text" id="beauty_center_grid_number" name="beauty_center_grid[total_markers]" >
                            </td> </tr>
                            <?php /*
                            <tr><th>
                                <label title="<?php esc_html_e("Enable/Disable autoload results when scroll down","beauty_center") ?>" for="beauty_center_grid_scroll"><?php esc_html_e("Autoload results on scroll", 'beauty_center'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="beauty_center_grid[scroll]" >
                                <input <?php echo ($grid_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_grid_scroll" name="beauty_center_grid[scroll]" >
                            </td> </tr>

                            <!-- <tr><th>
                                <label title="<?php esc_html_e("Select the displayed column in the grid in the frontend by order","beauty_center") ?>" for="beauty_center_grid_columns"><?php esc_html_e("Displayed Columns", 'beauty_center'); ?>:<span class="beauty_center_tip"><?php esc_html_e("Select columns with order to be displayed on frontend", 'beauty_center'); ?></span></label>
                                </th><td>
                                <select  id="beauty_center_grid_columns" multiple="multiple" class="regular-text">
                                    <?php
                                    if(isset($grid_options['columns']) && $grid_options['columns']){
                                        $selectedColumns  = $grid_options['columns'];
                                    }else{
                                        $selectedColumns  = array();
                                    }
                                    ?>
                                    <?php
                                    $columns = array("name", "address", "city", "state", "country", "zipcode", "website", "full_address", "managers", "phone", "working_hours", "fax");
                                    if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                        $columns[] = 'gravity_form';
                                    }
                                    $columns = array_diff($columns, $selectedColumns);
                                    $columns = array_merge($selectedColumns, $columns);
                                    ?>
                                    <?php foreach ($columns as $column): ?>
                                        <?php if ($column): ?>
                                            <option value="<?php echo $column; ?>" <?php echo (in_array($column, $selectedColumns)) ? "selected" : ""; ?>><?php echo $column; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input name="beauty_center_grid[columns]" type="hidden" value="<?php echo implode(",", $selectedColumns); ?>">
                            </td> </tr> --> */ ?>
                            <tr><th>
                                <label title="Map Result Show on" for="beauty_center_map_type"><?php esc_html_e("Map Result Show on", 'beauty_center'); ?>:</label>
                                </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Left Side','beauty_center');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="beauty_center_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Right Side','beauty_center');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'right')?'checked':''; ?> type="radio" value="right" name="beauty_center_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('Below Map','beauty_center');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'below_map')?'checked':''; ?> type="radio" value="below_map" name="beauty_center_grid[listing_position]" /></label>
                                    </li>
                                </ul>
                                </td> </tr>

                            <tr><th>
                                <label title="Map Search Options Window Show on" for="beauty_center_map_type"><?php esc_html_e("Map Search Options Window Show on", 'beauty_center'); ?>:</label>
                            </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Left Side','beauty_center');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="beauty_center_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Right Side','beauty_center');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_search_right')?'checked':''; ?> type="radio" value="wpml_search_right" name="beauty_center_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('Above Map','beauty_center');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_above_map')?'checked':''; ?> type="radio" value="wpml_above_map" name="beauty_center_grid[search_window_position]" /></label>
                                    </li>
                                </ul>
                           </td> </tr>
                           <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="grid-settings" value="<?php esc_html_e('Save Changes', 'beauty_center'); ?>">
                            </p>
                            </td> </tr>
                        </table>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
    <?php
    }
    public function single_page_settings(){
        //handle save single page settings
        if (isset($_POST['single-settings'])) {
            $_POST['beauty_center_single']['items'] = explode(",", $_POST['beauty_center_single']['items']);
            update_option('beauty_center_single', $_POST['beauty_center_single']);
        }
        $single_options = get_option('beauty_center_single');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Single Page Settings", 'beauty_center'); ?></span></h3>
                        <div class="inside beauty_center_singel_page_settings">

                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable when click on Beauty Center goto single page for more details','beauty_center') ?>" for="beauty_center_single_page"><?php esc_html_e("Link Beauty Center to a single page", 'beauty_center'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="beauty_center_single[page]" >
                                <input <?php echo (isset($single_options['page']) && $single_options['page'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_single_page" name="beauty_center_single[page]" >
                             </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Enter Unique Slug Name','beauty_center') ?>" for="beauty_center_slug"><?php esc_html_e("Enter Unique Slug Name", 'beauty_center'); ?></label>   </th><td>
                                <input <?php echo (isset($single_options['beauty_center_slug']) && $single_options['beauty_center_slug'] != '')? $single_options['beauty_center_slug']:''; ?> placeholder="beauty-center" value="<?php echo (isset($single_options['beauty_center_slug']) && !empty($single_options['beauty_center_slug']) ? $single_options['beauty_center_slug'] : '')?>" type="text" id="beauty_center_slug" name="beauty_center_single[beauty_center_slug]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable the display of feature image of the beauty center in the inner page','beauty_center') ?>" for="beauty_center_single_image"><?php esc_html_e("Show feature image of the Beauty Center?", 'beauty_center'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="beauty_center_single[image]" >
                                <input <?php echo (isset($single_options['image']) && $single_options['image'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_single_image" name="beauty_center_single[image]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable showing map in the inner page of the beauty center','beauty_center') ?>" for="beauty_center_single_map"><?php esc_html_e("Show map on page?", 'beauty_center'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="beauty_center_single[map]" >
                                <input <?php echo (isset($single_options['map']) && $single_options['map'])?'checked':''; ?> value="1" type="checkbox" id="beauty_center_single_map" name="beauty_center_single[map]" >
                           </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Select the displayed column in the page in the frontend by order','beauty_center') ?>" for="beauty_center_single_items"><?php esc_html_e("Displayed Columns", 'beauty_center'); ?>:<span class="beauty_center_tip"><?php esc_html_e("Select details you want to display on the page", 'beauty_center'); ?></span></label>
                                </th><td>
                                <select  id="beauty_center_single_items" multiple="multiple" class="regular-text">
                                    <?php
                                    if(isset($single_options['items']) && $single_options['items']){
                                        $selectedItems  = $single_options['items'];
                                    }else{
                                        $selectedItems  = array();
                                    }
                                    ?>
                                    <?php
                                    $items = array("name", "website", "full_address", "managers", "phone", "working_hours", "fax", "description");
                                    $items = array_diff($items, $selectedItems);
                                    $items = array_merge($selectedItems, $items);
                                    ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php if ($item): ?>
                                            <option value="<?php echo $item; ?>" <?php echo (in_array($item, $selectedItems)) ? "selected" : ""; ?>><?php echo $item; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>

                                <input name="beauty_center_single[items]" type="hidden" value="<?php echo implode(",", $selectedItems); ?>">
                            </td></tr>
                            <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="single-settings" value="<?php esc_html_e("Save Changes", 'beauty_center'); ?>">
                            </p>
                            </td></tr>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
}
    new WP_Beauty_Center_Settings();
}
