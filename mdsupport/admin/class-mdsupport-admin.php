<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/admin
 * @author     multidots <info@multidots.com>
 */
class Mdsupport_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mdsupport_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mdsupport_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mdsupport-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mdsupport_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mdsupport_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mdsupport-admin.js', array( 'jquery' ), $this->version, false );
        $ajaxurlset = array(
            'mdsupportajaxurl' => admin_url('admin-ajax.php'),
        );

        wp_localize_script('mdsupport-js', 'pn_vars', $ajaxurlset);

	}

    public function theme_options_panel(){
        add_submenu_page(
            'edit.php?post_type=support',
            'Support Settings', /*page title*/
            'Settings', /*menu title*/
            'manage_options', /*roles and capabiliyt needed*/
            'spport_page',
            array($this, 'mdsupport_setting_page')
        );
    }

    function mdsupport_setting_page(){
        ?>

        <h2>Md Support Plugin Settings Page</h2>
        <div class="support-setting">

            <?php
            if(isset($_POST['save_setting'])){
                $support_enable = !empty($_POST['support-on'])?'1':'0';
                update_option('mdsupport_myaccount_page_enble',$support_enable);
            }
            $mdsupport_enable = get_option('mdsupport_myaccount_page_enble');
            ?>
            <form method="post" action="">
                <input type="checkbox" name="support-on" value="on"  <?php if($mdsupport_enable) echo "checked"; ?>>Enable page in myaccount page<br>
                <p></p><input type="submit" name="save_setting"  class="button"  value="Save Settings"></p>
            </form>

        </div>
        <?php
    }


    /**
     * Register meta box(es).
     */
    function mdsupport_register_meta_boxes() {
        add_meta_box( 'meta-box-id', __( 'Support Fileds', 'textdomain' ), array($this, 'mdsupport_my_display_callback'), 'support' );
    }

    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    function mdsupport_my_display_callback( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'mdsupport_inner_custom_box', 'mdsupport_inner_custom_box_nonce' );
        // Display code/markup goes here. Don't forget to include nonces!
        $attachement = get_post_meta( $post->ID, 'mdsupport_attachement_file_url', true );
        ?>
        <label> <?php  echo __('Attachement :','mdsupport')  ?></label>
        <input type="file"  name="upload">
        <?php if(!empty($attachement)) { ?>
            <a href="<?php echo $attachement ?>" download>Download</a>
            <?php
        }
        ?>
        <br><br><br>
        <div class="Product">
        <label><?php echo __('Select Product :','mdsuppot'); ?></label>
        <?php
        $productid = get_post_meta( $post->ID, 'mdsupport_procuct_id', true );
        $products  = get_posts(array('post_type'=> 'product', 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
        echo '<select name="product_id" id="product-id">';
            echo '<option value = "" >Select Product </option>';
            foreach ($products as $product) {
            echo '<option value="', $product->ID, '"', $productid == $product->ID ? ' selected="selected"' : '', '>', $product->post_title, '</option>';
            }
            echo '</select>';
        ?>
        </div>
        <br><br><br>
           <?php if( !current_user_can('support_role')){ ?>
        <div class="user-list">
            <label><?php echo __('Select Supporter :','mdsuppot'); ?></label>
            <?php
            $supportusername   = get_post_meta( $post->ID, 'mdsupport_support_user', true );
            $blogusers  = get_users( 'role=support_role' );
                echo '<select name="user_name" id="user-name">';
                echo '<option value = "" >Select Supporter </option>';
                foreach ($blogusers as $user) {
                    echo '<option value="' . esc_html($user->user_login) . '"', $supportusername == $user->user_login ? 'selected="selected"' : '', ' >' . esc_html($user->user_login) . '</option>';
                }
                echo '</select>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     */
    function mdsupport_save_meta_box( $post_id ) {
        // Save logic goes here. Don't forget to include nonce checks!

        // Check if our nonce is set.
        if ( ! isset( $_POST['mdsupport_inner_custom_box_nonce'] ) ) {
            return $post_id;
        }

        $nonce = $_POST['mdsupport_inner_custom_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'mdsupport_inner_custom_box' ) ) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        $uploadedfile = $_FILES['upload'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile && !isset($movefile['error'])) {
            update_post_meta($post_id, 'mdsupport_attachement_file_url', $movefile['url']);
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            echo $movefile['error'];
        }

        if(!empty($_POST['product_id'])){
            update_post_meta($post_id, 'mdsupport_procuct_id',$_POST['product_id']);
        }

        if(!empty($_POST['user_name'])) {
            update_post_meta($post_id, 'mdsupport_support_user', $_POST['user_name']);
        }

    }
    /**
     * Callback for WordPress 'post_edit_form_tag' action.
     *
     * Append enctype - multipart/form-data and encoding - multipart/form-data
     * to allow image uploads for post type 'post'
     *
     * @global type $post
     * @return type
     */
    function mdsupport_edit_form_tag(){

        global $post;

        //  if invalid $post object, return
        if(!$post)
            return;

        //  get the current post type
        $post_type = get_post_type($post->ID);

        //  if post type is not 'post', return
        if('support' != $post_type)
            return;

        //  append our form attributes
        printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');

    }


    function mdsupport_term_radio_checklist($args) {
        if (!empty($args['taxonomy']) && $args['taxonomy'] === 'Query' ||  $args['taxonomy'] === 'Priority' || $args['taxonomy'] === 'Status' /* <== Change to your required taxonomy */) {
            if (empty($args['walker']) || is_a($args['walker'], 'Walker')) { // Don't override 3rd party walkers.
                if (!class_exists('WPSE_139269_Walker_Category_Radio_Checklist')) {

                    /**
                     * Custom walker for switching checkbox inputs to radio.
                     *
                     * @see Walker_Category_Checklist
                     */


                }

                $args['walker'] = new WPSE_139269_Walker_Category_Radio_Checklist;
            }
        }

        return $args;
    }

    /**
    // * if submitted filter by post meta
    // *
    // * make sure to change META_KEY to the actual meta key
    // * and POST_TYPE to the name of your custom post type
    // * @author Ohad Raz
    // * @param  (wp_query object) $query
    // *
    // * @return Voids
    // */
    function mdsupport_posts_filter( $query ){
        global $pagenow;
        $current_user = wp_get_current_user();
       // echo '<pre>';
        //print_r($current_user);
        //echo '</pre>';
       // echo $current_user->user_login;
        $type = 'support';
        if( current_user_can('support_role')){
        if ($_GET['post_type'] == $type) {
                $query->query_vars['meta_key'] = 'mdsupport_support_user';
                $query->query_vars['meta_value'] = $current_user->user_login;
            }
        }

    }



}
