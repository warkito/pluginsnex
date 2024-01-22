<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WP_Beauty_Centers_Post')){
	class WP_Beauty_Centers_Post{
		public function __construct(){
            //add_filter( 'post_type_link', array($this,'set_permalinks'), 1, 2 );
            add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );
			add_action('init', array($this,'beauty_center_init'));
			add_action('admin_menu', array($this,'disable_new_beauty_center_posts'));
			//create custom fields
			add_action('add_meta_boxes',array($this, 'add_meta_boxes'));
			//save custom fields
			add_action('save_post', array($this, 'save_meta_box_data'));
			//manage custom coulmns display for Beauty center
			add_filter('manage_edit-beauty_center_columns', array($this,'list_columns'));
			//manage custom coulmns content display for Beauty Center
			add_filter('manage_beauty_center_posts_custom_column', array($this,'manage_columns'), 10, 2);
			// add filters to the query


		}

        /**
         * Show the "Duplicate" link in admin products list.
         *
         * @param array   $actions Array of actions.
         * @param WP_Post $post Post object.
         * @return array
         */
        public function row_actions( $actions, $post ) {

            if ( 'beauty_center' !== $post->post_type ) {
                return $actions;
            }


            if ( 'publish' === $post->post_status) {
                $actions['trash'] = sprintf(
                    '<a href="%s" class="submitdelete trash-product" aria-label="%s">%s</a>',
                    get_delete_post_link( $post->ID, '', false ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash', 'beauty-center' ), $post->post_name ) ),
                    esc_html__( 'Trash', 'beauty-center' )
                );
            }

            $actions['view'] = '<a target="_blank" href="' .  get_permalink(  $post->ID  ) . '" aria-label="' . esc_attr__( 'View center', 'beauty-center' )
                . '" rel="permalink">' . esc_html__( 'View', 'beauty-center' ) . '</a>';

            return $actions;
        }
		public function beauty_center_init() {
		    //$beauty_center_single = get_option('beauty_center_single',true);
            //$single_options = get_option('beauty_center_single');
		    $beauty_center_slug = '';
		    if(isset( $beauty_center_single['beauty_center_slug'] )) {
		        $beauty_center_slug = $beauty_center_single['beauty_center_slug'];
		    }
		    if(empty($beauty_center_slug))
		        $beauty_center_slug = 'beauty-center';
            $beauty_center_slug = "";
		    $labels = array(
		        'name' => esc_html__('Beauty Center', 'beauty_center'),
		        'singular_name' => esc_html__('Beauty Center', 'beauty_center'),
		        'menu_name' => esc_html__('Beauty Center', 'beauty_center'),
		        'name_admin_bar' => esc_html__('Beauty Center', 'beauty_center'),
		        'add_new' => esc_html__('Add New Beauty Center', 'beauty_center'),
		        'add_new_item' => esc_html__('Add New Beauty Center', 'beauty_center'),
		        'new_item' => esc_html__('New Beauty Center', 'beauty_center'),
		        'edit_item' => esc_html__('Edit Beauty Center', 'beauty_center'),
		        'view_item' => esc_html__('View Beauty Center', 'beauty_center'),
		        'all_items' => esc_html__('List', 'beauty_center'),
		        'search_items' => esc_html__('Search Beauty Center', 'beauty_center'),
		        'parent_item_colon' => esc_html__('Beauty Center Partner:', 'beauty_center'),
		        'not_found' => esc_html__('No Beauty Center found.', 'beauty_center'),
		        'not_found_in_trash' => esc_html__('No Beauty Center found in Trash.', 'beauty_center')
		    );

            $rewrite = array(
                'slug'                  => 'center',
                'with_front'            => false,
                'pages'                 => false,
                'feeds'                 => false,
            );
		    $args = array(
		        'labels' => $labels,
		        'description' => esc_html__('Description.', 'beauty_center'),
		        'public' => false,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'query_var' => true,
                'rewrite' => $rewrite,
		        'capability_type' => 'post',
		        'has_archive' => true,
		        'hierarchical' => false,
		        'menu_position' => null,
                'taxonomies'  => array( 'beauty_center_service','beauty_center_city' ),
		        'menu_icon' => "dashicons-location-alt",
		       // 'supports' => array('thumbnail')
                'supports'              => array('title', 'editor', 'permalink','thumbnail')
		    );


		    register_post_type('beauty_center', $args);

		    // create custom city for Beauty center
            $city_rewrite = array(
                'slug'                  => 'ville',
                'with_front'            => false,
                'pages'                 => false,
                'feeds'                 => false,
            );
		    register_taxonomy( 'beauty_center_city', array('beauty_center','maps'), array(
		            'hierarchical' => true,
		            'label' => esc_html__('Cities', 'beauty_center'),
		            'singular_label' => esc_html__('City', 'beauty_center'),
		            'rewrite' => $city_rewrite,
                    'query_var'     => true,

		        )
		    );
		    register_taxonomy_for_object_type( 'beauty_center_city', 'beauty_center' );

		    // create custom tags for Beauty center
            $service_rewrite = array(
                'slug'                  => 'service',
                'with_front'            => false,
                'pages'                 => false,
                'feeds'                 => false,
            );
		    register_taxonomy(
		        'beauty_center_service',
		        'beauty_center',
		        array(
		            'hierarchical'  => true,
		            'label'         => esc_html__("Services", 'beauty_center'),
		            'singular_name' => esc_html__("Service", 'beauty_center'),
		            'rewrite'       => $service_rewrite,
		            'query_var'     => true
		        )
		    );
		}
		public function disable_new_beauty_center_posts() {
		    global $submenu;
		    unset($submenu['edit.php?post_type=beauty_center'][10]);
		}
		public function add_meta_boxes() {
		    add_meta_box('beauty-center-info',
		        esc_html__('Beauty Center Info', 'beauty_center'),
		        array($this,'meta_box_callback_beauty_center_info'),
		        'beauty_center');
		    add_meta_box('address-info',
		        esc_html__('Address Info', 'beauty_center'),
		        array($this,'beauty_center_meta_box_callback_address_info'),
		        'beauty_center');
         }

        public function meta_box_callback_beauty_center_info($post) {
		    // Add a nonce field so we can check for it later.
		    wp_nonce_field('beauty_center_save_meta_box_data', 'beauty_center_meta_box_nonce');
		    $post_id = $post->ID;
		    ?>
		    <table class="form-table" style="border: 0px;">
		        <tbody>
		        <tr>
		            <td><?php echo esc_html__("Code", 'beauty_center'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_code', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : $post_id; ?>" name="beauty_center_code" class="widefat" readonly />
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Name", 'beauty_center'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_name', true); ?>
		                <input type="text" class="widefat" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_name"/>
		            </td>
		        </tr>
                <?php  if($post) :?>
                <tr>
                    <td><?php echo esc_html__("Link ", 'beauty_center'); ?></td>
                    <td>
                        <a href="<?php echo get_permalink()?>"> <?php echo get_permalink()?></a>
                    </td>
                </tr>
                <?php endif;?>
		        <tr>
		            <td><?php echo esc_html__("Description", 'beauty_center'); ?></td>
		            <td>
		                <?php
		                $content = get_post_meta( $post_id, 'beauty_center_description', true );
		                wp_editor( $content, "beauty_center_description" );
		                ?>
		            </td>
		        </tr>
                <tr>
                    <td><?php echo esc_html__("Practical info", 'beauty_center'); ?></td>
                    <td>
                        <?php
                        $content = get_post_meta( $post_id, 'beauty_center_practical_info', true ); ?>
                        <textarea type="text" value="" name="beauty_center_practical_info" class="widefat"><?php echo !empty($content) ? nl2br($content) : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__("Service Price", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_service_price', true); ?>
                        <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_service_price" class="widefat"/>
                    </td>
                </tr>
                    <tr>
                        <td><?php echo esc_html__("Total Reviews", 'beauty_center'); ?></td>
                        <td><?php $meta= get_post_meta($post_id, 'beauty_center_total_reviews', true); ?>
                            <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_total_reviews" class="widefat"/>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__("Total Note", 'beauty_center'); ?></td>
                        <td><?php $meta= get_post_meta($post_id, 'beauty_center_total_note', true); ?>
                            <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_total_note" class="widefat"/>
                        </td>
                    </tr>

                <tr>
                    <td><?php echo esc_html__("Phone", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_phone', true); ?>
                        <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_phone" class="widefat"/>
                    </td>
                </tr>
		        <tr>
		            <td><?php echo esc_html__("Fax", 'beauty_center'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_fax', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_fax" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("E-Mail", 'beauty_center'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_email', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_email" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Website", 'beauty_center'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_website', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_website" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Working Hours", 'beauty_center'); ?></td>
		            <?php
		            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		            $days_meta = get_post_meta($post_id, 'beauty_center_days', true);
		            ?>
		            <td>
		                <table id="beauty_center_hours" style="background-color: rgb(241, 241, 241); border-radius: 5px;" class="widefat">
		                    <?php foreach ($days as $day): ?>
		                        <tr>
		                            <td style="border-bottom: 1px solid #dbdbdb;"><?php echo $day; ?></td>
		                            <td style="border-bottom: 1px solid #dbdbdb;">
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'checked':''; ?> type="radio" value="1" id="beauty_center_days_<?php echo $day; ?>_1" name="beauty_center_days[<?php echo $day; ?>][status]" > <label for="beauty_center_days_<?php echo $day; ?>_1"> Opened </label>
		                                <input <?php echo (!isset($days_meta[$day]) || $days_meta[$day]['status'] == '0')?'checked':''; ?> type="radio" value="0" id="beauty_center_days_<?php echo $day; ?>_0" name="beauty_center_days[<?php echo $day; ?>][status]" /> <label for="beauty_center_days_<?php echo $day; ?>_0"> Closed </label>
		                            </td>
		                            <td style="border-bottom: 1px solid #dbdbdb;">
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'':'style="display: none;"'; ?> size="9" placeholder="Open Time" type="text" value="<?php echo (isset($days_meta[$day]))?$days_meta[$day]['start']:''; ?>" name="beauty_center_days[<?php echo $day; ?>][start]" class="start_time"/>
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'':'style="display: none;"'; ?> size="9" placeholder="Close Time" type="text" value="<?php echo (isset($days_meta[$day]))?$days_meta[$day]['end']:''; ?>" name="beauty_center_days[<?php echo $day; ?>][end]" class="end_time" />
		                            </td>
		                        </tr>
		                    <?php endforeach; ?>
		                </table>
		            </td>
		        </tr>

                <tr>
                    <td><?php echo esc_html__("Opening Hours", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_opening_hours', true); ?>
                        <textarea type="text" value="" name="beauty_center_opening_hours" class="widefat"><?php echo !empty($meta) ? esc_attr($meta) : ''; ?></textarea>
                    </td>
                </tr>
                <tr>
                        <td><?php echo esc_html__("Transport", 'beauty_center'); ?></td>
                        <td><?php $meta= get_post_meta($post_id, 'beauty_center_transport', true); ?>
                            <textarea type="text" value="" name="beauty_center_transport" class="widefat"><?php echo !empty($meta) ? esc_attr($meta) : ''; ?></textarea>
                        </td>
                </tr>

                <tr>
                    <td><?php echo esc_html__("Doc Lib Link", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_doclib_link', true); ?>
                        <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_doclib_link" class="widefat"/>
                    </td>
                </tr>

                <tr>
                    <td><?php echo esc_html__("Google Map Link", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_googlemap_link', true); ?>
                        <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_googlemap_link" class="widefat"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__("Google Reviews Link", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_googlereviews_link', true); ?>
                        <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_googlereviews_link" class="widefat"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__("Nearby towns", 'beauty_center'); ?></td>
                    <td><?php $meta= get_post_meta($post_id, 'beauty_center_nearby_towns', true); ?>
                        <textarea type="text" value="" name="beauty_center_nearby_towns" class="widefat"><?php echo !empty($meta) ? esc_attr($meta) : ''; ?></textarea>
                    </td>
                </tr>

	             <?php foreach (WP_Beauty_Center_Frontend::get_others_fields() as $key => $label) : ?>

	               <tr>
	                   <td><?php echo $label; ?></td>
	                   <td><?php $meta= get_post_meta($post_id, 'beauty_center_'.$key, true); ?>
	                       <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_<?php echo $key ?>" class="widefat" />
	                       <small style="color: #dc3232;font-weight: bold;">  Shortcode [<?php echo $key ?>]</small>
	                   </td>
	               </tr>
           <?php endforeach;
           ?>

		        </tbody>
		    </table>
		    <script>
		        // initialize input widgets first
		        jQuery('.start_time, .end_time').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 60,
                    minTime: '10',
                    defaultTime: '09:00',
                    startTime: '09:00',
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true
                });;
		    </script>
		    <?php
		}
		public function beauty_center_meta_box_callback_address_info($post) {
		    $post_id = $post->ID; ?>
		    <table class="form-table">
		        <tbody>
		        <tr>
		            <th><?php echo esc_html__("Address", 'beauty_center'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_address', true); ?>
		                <input id="beauty_center_address" type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_address" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Ville", 'beauty_center'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_ville', true); ?>
		                <input id="beauty_center_address" type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="beauty_center_ville" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Longitude", 'beauty_center'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_lng', true); ?>
		                <input   type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" class="widefat"  name="beauty_center_lng" />
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("latitude", 'beauty_center'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'beauty_center_lat', true); ?>
		                <input  type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" class="widefat" name="beauty_center_lat"  />
		            </td>
		        </tr>

		        <tr>
		            <th><?php echo esc_html__("Postal Code", 'beauty_center'); ?></th>
		            <td>
		                <input id="beauty_center_zipcode" type="text" value="<?php echo get_post_meta($post_id, 'beauty_center_zipcode', true) ? get_post_meta($post_id, 'beauty_center_zipcode', true) : ''; ?>" name="beauty_center_zipcode" class="widefat"/>
		            </td>
		        </tr>
		        </tbody>
		    </table>

		    <?php
		}
		public function save_meta_box_data($post_id) {
		    if (isset($_POST['post_type']) && 'beauty_center' == $_POST['post_type']) {
		        // Check if our nonce is set.
		        if (!isset($_POST['beauty_center_meta_box_nonce'])) {
		            return;
		        }
		        // Verify that the nonce is valid.
		        if (!wp_verify_nonce($_POST['beauty_center_meta_box_nonce'], 'beauty_center_save_meta_box_data')) {
		            return;
		        }
		        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
		        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		            return;
		        }
                global $wpdb;
		        //update post title
                $unique_post_slug= wp_unique_post_slug(
                    $_POST['beauty_center_name'],
                    $post_id,
                    'publish',
                    'beauty_center',
                    $post_parent=null
                );

              	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_title = %s WHERE ID=%s",    $_POST['beauty_center_name'] ,$post_id) );
			    // Remap enclosure urls.
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_name = %s WHERE ID=%s",    $unique_post_slug ,$post_id) );
		        /*$my_post = array(
		            'ID' => $post_id,
		            'post_title' => $_POST['beauty_center_name'],
		            'post_name' => wp_unique_post_slug(
		                $_POST['beauty_center_name'],
		                $post_id,
		                'publish',
		                'beauty_center',
		                $post_parent=null
		            )
		        );
		        wp_update_post($my_post);
		        add_action('save_post', array($this,'beauty_center_save_meta_box_data'));*/

		        // update post meta
		        if (isset($_POST['beauty_center_name']))
		            update_post_meta($post_id, 'beauty_center_name', $_POST['beauty_center_name']);

		        if (isset($_POST['beauty_center_address']))
		            update_post_meta($post_id, 'beauty_center_address', $_POST['beauty_center_address']);

		        if (isset($_POST['beauty_center_lat']))
		            update_post_meta($post_id, 'beauty_center_lat', $_POST['beauty_center_lat']);

		        if (isset($_POST['beauty_center_lng']))
		            update_post_meta($post_id, 'beauty_center_lng', $_POST['beauty_center_lng']);

		        if (isset($_POST['beauty_center_transport']))
		            update_post_meta($post_id, 'beauty_center_transport', $_POST['beauty_center_transport']);

                 if (isset($_POST['beauty_center_opening_hours']))
                     update_post_meta($post_id, 'beauty_center_opening_hours', $_POST['beauty_center_opening_hours']);

		        if (isset($_POST['beauty_center_doclib_link']))
		            update_post_meta($post_id, 'beauty_center_doclib_link', $_POST['beauty_center_doclib_link']);

                if (isset($_POST['beauty_center_googlemap_link']))
                    update_post_meta($post_id, 'beauty_center_googlemap_link', $_POST['beauty_center_googlemap_link']);

		        if (isset($_POST['beauty_center_city']))
		            update_post_meta($post_id, 'beauty_center_city', $_POST['beauty_center_city']);

		        if (isset($_POST['beauty_center_phone']))
		            update_post_meta($post_id, 'beauty_center_phone', $_POST['beauty_center_phone']);

		        if (isset($_POST['beauty_center_fax']))
		            update_post_meta($post_id, 'beauty_center_fax', $_POST['beauty_center_fax']);

		         if (isset($_POST['beauty_center_email']))
		            update_post_meta($post_id, 'beauty_center_email', $_POST['beauty_center_email']);

		        if (isset($_POST['beauty_center_website']))
		            update_post_meta($post_id, 'beauty_center_website', $_POST['beauty_center_website']);

		        if (isset($_POST['beauty_center_zipcode']))
		            update_post_meta($post_id, 'beauty_center_zipcode', $_POST['beauty_center_zipcode']);

		        if (isset($_POST['beauty_center_code']))
		            update_post_meta($post_id, 'beauty_center_code', $_POST['beauty_center_code']);

		        if (isset($_POST['beauty_center_sales']))
		            update_post_meta($post_id, 'beauty_center_sales', $_POST['beauty_center_sales']);

		        if (isset($_POST['beauty_center_days']))
		            update_post_meta($post_id, 'beauty_center_days', $_POST['beauty_center_days']);

		        if (isset($_POST['beauty_center_description']))
		            update_post_meta($post_id, 'beauty_center_description', $_POST['beauty_center_description']);

               $fields= array('beauty_center_practical_info','beauty_center_total_reviews','beauty_center_service_price','beauty_center_total_note');
               foreach ($fields as $field){
                   update_post_meta($post_id, $field, $_POST[$field]);
               }

                foreach (WP_Beauty_Center_Frontend::get_others_fields() as $key => $label) {
                	 $field ='beauty_center_'.$key;
                	 update_post_meta($post_id, $field, $_POST[$field]);
                }

		    }

		}
		public function list_columns($columns) {
		    unset(
		        $columns['date']
		    );

		    $new_columns = array(
		        'title' => esc_html__('Name', 'beauty_center'),
		        'beauty_center_address' => esc_html__('Address', 'beauty_center'),
                'beauty_center_page' => esc_html__('Page', 'beauty_center'),
                'beauty_center_city' => esc_html__('City', 'beauty_center'),
                'beauty_center_service' => esc_html__('Service', 'beauty_center')
		    );
		    return array_merge($columns, $new_columns);
		}
		public function manage_columns($column, $post_id) {
		    global $post;
		    switch ($column) {
		        case 'beauty_center_address':
		            $meta = get_post_meta($post_id);
		            echo $meta['beauty_center_address'][0] . " " . $meta['beauty_center_city'][0] . " " . $meta['beauty_center_zipcode'][0];
		            break;
                case 'beauty_center_page':
                    $link =get_permalink($post_id);
                    echo  '<a target="_blank" href="'.$link.'">'.$link.'</a>';
                     break;
                case 'beauty_center_city':
                    $cities = wp_get_object_terms( $post_id, 'beauty_center_city' );
                    if( $cities ){
                        echo  $cities[0]->slug;
                    }
                    break;
                case 'beauty_center_service':
                    $services = wp_get_object_terms( $post_id, 'beauty_center_service' );
                    if( $services ){
                       echo  $services[0]->slug;
                    }
                    break;
		        default :
		            break;
		    }
		}

        public function set_permalinks( $post_link, $post ){
            $post_link= home_url();
            if ( is_object( $post ) && $post->post_type == 'beauty_center' ){
                $services = wp_get_object_terms( $post->ID, 'beauty_center_service' );

                if( $services ){
                    $post_link .= '/'. $services[0]->slug;//str_replace( '%beauty_center_service%' , $services[0]->slug , $post_link );
                }

                $cities = wp_get_object_terms( $post->ID, 'beauty_center_city' );
                if( $cities ){
                    $post_link .= '/'. $cities[0]->slug;//=  str_replace( '%beauty_center_city%' , $cities[0]->slug , $post_link );
                }
            }

            return $post_link;
        }
	}
	new WP_Beauty_Centers_Post();
}
