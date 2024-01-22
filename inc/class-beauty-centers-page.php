<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WP_Beauty_Centers_Page')){
	class WP_Beauty_Centers_Page{
		public function __construct(){
            add_action('add_meta_boxes',array($this, 'add_meta_boxes'));
			//save custom fields
			add_action('save_post', array($this, 'save_meta_box_data'));
			//manage custom coulmns display for Beauty center
			add_filter('manage_edit-page_columns', array($this,'list_columns'));
			//manage custom coulmns content display for Beauty Center
			add_filter('manage_page_posts_custom_column', array($this,'manage_columns'), 10, 2);
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
		public function add_meta_boxes() {
		   add_meta_box( 'postimagediv',
                esc_html( "Center Infos",'beauty_center' ),
                array($this,'select_taxonomies'), "page", 'side', 'low', array( '__back_compat_meta_box' => true ) );
        }
        public  function select_taxonomies($post){

            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'date',
                'order'            => 'DESC',
                'post_type'        => 'beauty_center',
                'post_status'      => 'publish',
                'fields'           => '',
            );
            $query_params = [
                'taxonomy' =>'beauty_center_service',
                'hide_empty' => false,
            ];
            $terms  = get_terms( $query_params );
            $centers = get_posts( $args );
            wp_nonce_field('beauty_center_save_meta_box_data', 'beauty_center_meta_box_nonce');
            $post_id = $post->ID;
            $center_id= get_post_meta($post_id,"_beauty_center_id",true);
            $service_id= get_post_meta($post_id,"_beauty_center_service_id",true); ?>
            <p class="post-attributes-label-wrapper parent-id-label-wrapper">
                <label class="post-attributes-label" for="beauty_center_id" ><?php echo esc_html__("Associated Center", 'beauty_center'); ?></label>
            </p>
            <select style="max-width:253px"  name="beauty_center_id" id="beauty_center_id">
                <option value="0"><?php echo esc_html__("Select associated center", 'beauty_center'); ?></option>
                <?php  foreach ($centers as $center)  :?>
                    <option  <?php  echo $center_id ==  $center->ID ? "selected='selected'" : "" ?> value="<?php echo $center->ID ?>"><?php echo $center->post_title ?></option>
                <?php endforeach;?>
            </select>
            <p class="post-attributes-label-wrapper parent-id-label-wrapper">
                <label class="post-attributes-label"  for="beauty_center_service_id"><?php echo esc_html__("Associated Service", 'beauty_center'); ?></label>
            </p>
            <select style="max-width:253px" name="beauty_center_service_id" id="beauty_center_service_id">
                <option value="0"><?php echo esc_html__("Select associated service", 'beauty_center'); ?></option>
                <?php  foreach ($terms as $term)  :?>
                    <option <?php  echo $service_id ==  $term->term_id ? "selected='selected'" : "" ?>   value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
                <?php endforeach;?>
            </select>

        <?php
        }
		public function save_meta_box_data($post_id) {
		     if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
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
                update_post_meta($post_id, '_beauty_center_id', $_POST['beauty_center_id']);
                update_post_meta($post_id, '_beauty_center_service_id', $_POST['beauty_center_service_id']);
                if(!empty( $_POST['beauty_center_id'])){
                    $wp_center= get_post($_POST['beauty_center_id']);
                    update_post_meta($post_id, '_beauty_center_name', $wp_center->post_title);
                }

                 if(!empty( $_POST['beauty_center_service_id'])){
                     $wp_term= get_term($_POST['beauty_center_service_id']);
                     update_post_meta($post_id, '_beauty_center_service_name', $wp_term->name);
                 }
            }
		}
		public function list_columns($columns) {

		    $new_columns = array(
		        'beauty_center_id' => esc_html__('Center', 'beauty_center'),
                'beauty_center_service_id' => esc_html__('Service', 'beauty_center')
		    );
		    return array_merge($columns, $new_columns);
		}
		public function manage_columns($column, $post_id) {
		    global $post;

		    switch ($column) {

		        case 'beauty_center_id':

		            $center_name= get_post_meta($post_id,'_beauty_center_name',true);
                    if(!empty($center_name)){
                        $center_id= get_post_meta($post_id,'_beauty_center_id',true);
                        $edit_url = get_edit_post_link( $center_id, 'raw' );
                        echo '<a target="_blanc" href="'. esc_url( $edit_url ).'">'.$center_name.'</a>';
                    }
		            break;
                case 'beauty_center_service_id':

                    $service_name= get_post_meta($post_id,'_beauty_center_service_name',true);
                    if(!empty($service_name)){
                        echo $service_name;
                    }

		        default :
		            break;
		    }
		}
	}
	new WP_Beauty_Centers_Page();
}
