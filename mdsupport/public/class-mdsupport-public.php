<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/public
 * @author     multidots <info@multidots.com>
 */
class Mdsupport_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_shortcode( 'mdsupport', array( $this, 'my_custom_endpoint_content' ) );
		add_shortcode( 'querylisting', array( $this, 'mdsupport_query_list' ) );


	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mdsupport-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, false );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_register_script( 'custom-ajax-request', '' );
		wp_localize_script( 'custom-ajax-request', 'mdsajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'custom-ajax-request' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mdsupport-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Md Support Query Listing Plugin
	 * @return  void()
	 *
	 */
	public function mdsupport_query_list() {
		?>


		<?php

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			// Sanitize the received page
			$page     = 1;
			$cur_page = $page;
			$page -= 1;
			// Set the number of results to display
			$per_page     = 10;
			$previous_btn = true;
			$next_btn     = true;
			$first_btn    = true;
			$last_btn     = true;
			$start        = $page * $per_page;
			$args         = array(
				'post_type'      => 'support',
				'post_status'    => 'publish',
				'order'          => 'ASC',
				'paged'          => $_POST['page'],
				'posts_per_page' => $per_page,
				'offset'         => $start,
				'meta_query'     => array(                  //(array) - Custom field parameters (available with Version 3.1).
					array(
						'key'     => 'mdsupport_username',
						'value'   => $current_user->user_login,
						'compare' => 'LIKE',
					),
				),
			);
			$posts        = new WP_Query( $args );
			$count        = $posts->found_posts;
			echo '<h2>' . __( "Support Query Listing", "mdsupport" ) . '</h2>';
			?>
            <div class="mdsupport-status-details">

				<?php
				if ( $posts->have_posts() ) { ?>
                <table>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Query Type</th>
                    <th>Priority Type</th>
					<?php do_action( 'add_custome_header' ); ?>
                    <th>Status</th>
					<?php
					while ( $posts->have_posts() ) : $posts->the_post();
						$query_terms    = wp_get_object_terms( get_the_ID(), 'Query' );
						$priority_terms = wp_get_object_terms( get_the_ID(), 'Priority' );
						$status_terms   = wp_get_object_terms( get_the_ID(), 'Status' );
						if ( $status_terms[0]->name == '' ) {
							$supportstatus = 'pending';
						} else {
							$supportstatus = $status_terms[0]->name;
						}
						echo '<tr>';
						echo '<td>' . get_the_title() . '</td>';
						echo '<td>' . get_the_content() . '</td>';
						echo '<td>' . $query_terms[0]->name . '</td>';
						echo '<td>' . $priority_terms[0]->name . '</td>';
						echo '<td>' . $supportstatus . '</td>';
						do_action( 'add_custome_coloum' );
						echo '</tr>';
					endwhile;
					wp_reset_postdata();
					} else {
						echo __( 'No Query List found', 'mdsupport' );
					} ?>
                </table>
				<?php

				// This is where the magic happens
				$no_of_paginations = ceil( $count / $per_page );

				if ( $no_of_paginations > 1 ) {
					if ( $cur_page >= 7 ) {
						$start_loop = $cur_page - 3;
						if ( $no_of_paginations > $cur_page + 3 ) {
							$end_loop = $cur_page + 3;
						} else if ( $cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6 ) {
							$start_loop = $no_of_paginations - 6;
							$end_loop   = $no_of_paginations;
						} else {
							$end_loop = $no_of_paginations;
						}
					} else {
						$start_loop = 1;
						if ( $no_of_paginations > 7 ) {
							$end_loop = 7;
						} else {
							$end_loop = $no_of_paginations;
						}
					}

					// Pagination Buttons logic
					$pag_container = "<div class='mdsupport-status-pagination' id='mdsupport-status-pagination'>
            <ul class='pagination'>";

					if ( $previous_btn && $cur_page > 1 ) {
						$pre = $cur_page - 1;
						$pag_container .= "<li p='$pre' class='active'>Previous</li>";
					} else if ( $previous_btn ) {
						//$pag_container .= "<li class='inactive'>Previous</li>";
					}
					for ( $i = $start_loop; $i <= $end_loop; $i ++ ) {

						if ( $cur_page == $i ) {
							$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
						} else {
							$pag_container .= "<li p='$i' class='active'>{$i}</li>";
						}
					}

					if ( $next_btn && $cur_page < $no_of_paginations ) {
						$nex = $cur_page + 1;
						$pag_container .= "<li p='$nex' class='active'>Next</li>";
					} else if ( $next_btn ) {
						//$pag_container .= "<li class='inactive'>Next</li>";
					}

					$pag_container = $pag_container . "
            </ul>
        </div>";
				}
				// We echo the final output
				?>
				<?php
				echo
					'<div class = "mdsupport-status-pagination-nav">' . $pag_container . '</div>';
				?>
            </div>
			<?php
		} else {
			echo __( 'You Need To Login First', 'mdsupport' );
		}
	}

	/**
	 * Mdsupport From Short Code Display Functions
	 * @return  void()
	 */

	public function mdsupport_form_content() {
		?>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#supportform">Support Form</a></li>
            <li><a data-toggle="tab" href="#supportformdeatils">Support Form Details</a></li>
        </ul>
        <div class="tab-content">
            <div id="supportform" class="tab-pane fade in active">
				<?php echo do_shortcode( '[mdsupport]' ); ?>
            </div>
            <div id="supportformdeatils" class="tab-pane fade">
                <h3><?php echo do_shortcode( '[querylisting]' ); ?></h3>
            </div>
        </div>
		<?php
	}

	public function my_custom_endpoint_content( $atts ) {
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$terms        = get_terms( [
				'taxonomy'   => 'Query',
				'hide_empty' => false,
			] );

			$priortytype = get_terms( [
				'taxonomy'   => 'Priority',
				'hide_empty' => false,
			] );
			?>
            <div class="woocommerce mdsupporttab">
                <h2><?php echo __( 'Md Support Form', 'mdsupport' ) ?></h2>
				<?php do_action( 'md_support_before_form_filed' ); ?>
                <div class="user-name-tab">
                    <input type="text" placeholder="<?php echo __( 'Title', 'mdsupport' ); ?>" name="support_title"
                           class="support-title">
                    <input type="hidden" value="<?php echo $current_user->user_email; ?>" class="support-email">
                </div>
				<?php do_action( 'md_support_after_title_filed' ); ?>
                <div class="your-name">
                    <input type="text" placeholder="<?php echo __( 'Your Name', 'mdsupport' ); ?>"
                           value="<?php echo $current_user->user_login ?>" name="user_name" class="user-name" readonly>
                </div>
				<?php do_action( 'md_support_user_name_filed' ); ?>
                <div class="query-type">
                    <select name="query_type" class="support-query">
                        <option value="">Select Query type</option>
						<?php
						foreach ( $terms as $term ) {
							echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
						}
						?>
                    </select>
                </div>
				<?php do_action( 'md_support_query_type_filed' ); ?>
                <div class="priorty-sections">
                    <select name="priorty_type" class="priorty-type">
                        <option value="">Select Priorty type</option>
						<?php
						foreach ( $priortytype as $priorty ) {
							echo '<option value="' . $priorty->slug . '">' . $priorty->name . '</option>';
						}
						?>
                    </select>
                </div>
				<?php do_action( 'md_support_priorty_type_filed' ); ?>
                <div class="product-tab" style="display:none">
					<?php
					$posts = get_posts( array( 'post_type'        => 'product',
					                           'post_status'      => 'publish',
					                           'suppress_filters' => false,
					                           'posts_per_page'   => - 1
					) );
					echo '<select name="product_name" class="product-name" >';
					echo '<option value = "" >Select Product </option>';
					foreach ( $posts as $post ) {
						echo '<option value="', $post->ID, '" >', $post->post_title, '</option>';
					}
					echo '</select>';
					?>
                </div>
                <div class="message" class="support-message">
                    <textarea name="support_message" class="support-message"></textarea>
                </div>
				<?php do_action( 'md_support_message_type_filed' ); ?>
                <div class="attachment-file">
                    <input type="file" id="sortpicture" name="upload">
                    <p class="file-require"
                       style="display: none;color:red;"><?php echo __( 'Please upload Your file', 'mdsupport' ); ?></p>
                </div>
				<?php do_action( 'md_support_after_form_filed' ) ?>
                </br>
                <input type="hidden" name="action" value="md_support_save">
                <input class="save-support" name="save_support" type="button"
                       value="<?php echo apply_filters( 'md_support_submit_button_text', __( 'Save', 'mdsupport' ) ); ?>
    " class="submit">
                <input type="reset" class="submit"
                       value="<?php echo apply_filters( 'md_support_reset_button_text', __( 'Clear', 'mdsupport' ) ); ?>">
                </form>
                <div class="Success-div" style="color:green"></div>

                <script type="text/javascript">

                </script>

            </div>
			<?php
		} else {
			echo __( 'You Need To Login First', 'mdsupport' );
		}
	}

	public function mdsupport_my_account_menu_items( $items ) {
		$items = array(
			'dashboard'       => __( 'Dashboard', 'woocommerce' ),
			'orders'          => __( 'Orders', 'woocommerce' ),
			'downloads'       => __( 'Downloads', 'woocommerce' ),
			'edit-address'    => __( 'Addresses', 'woocommerce' ),
			'payment-methods' => __( 'Payment methods', 'woocommerce' ),
			'edit-account'    => __( 'Account details', 'woocommerce' ),
			'md-support'      => __( 'Md Support', 'mdsupport' ),
			'customer-logout' => __( 'Logout', 'woocommerce' ),
		);

		return $items;
	}

	public function mdsupport_custom_endpoints() {
		add_rewrite_endpoint( 'md-support', EP_ROOT | EP_PAGES );
	}


	public function my_custom_query_vars( $vars ) {
		$vars[] = 'md-support';

		return $vars;
	}

	public function my_custom_flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Register a support post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function mdsupport_post() {
		$labels = array(
			'name'               => _x( 'supports', 'post type general name', 'mdsupport' ),
			'singular_name'      => _x( 'Support', 'post type singular name', 'mdsupport' ),
			'menu_name'          => _x( 'Supports', 'admin menu', 'mdsupport' ),
			'name_admin_bar'     => _x( 'Support', 'add new on admin bar', 'mdsupport' ),
			'add_new'            => _x( 'Add New', 'Support', 'mdsupport' ),
			'add_new_item'       => __( 'Add New Support', 'mdsupport' ),
			'new_item'           => __( 'New Support', 'mdsupport' ),
			'edit_item'          => __( 'Edit Support', 'mdsupport' ),
			'view_item'          => __( 'View Support', 'mdsupport' ),
			'all_items'          => __( 'All Supports', 'mdsupport' ),
			'search_items'       => __( 'Search Supports', 'mdsupport' ),
			'parent_item_colon'  => __( 'Parent Supports:', 'mdsupport' ),
			'not_found'          => __( 'No supports found.', 'mdsupport' ),
			'not_found_in_trash' => __( 'No supports found in Trash.', 'mdsupport' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'mdsupport' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'publicly_queryable' => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'support' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'comments' )
		);

		register_post_type( 'support', $args );
	}

// create two taxonomies, Querys for the post type "Support"
	public function mdsupport_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Querytype', 'taxonomy general name', 'mdsupport' ),
			'singular_name'     => _x( 'Query type', 'taxonomy singular name', 'mdsupport' ),
			'search_items'      => __( 'Search Querys', 'mdsupport' ),
			'all_items'         => __( 'All Querys', 'mdsupport' ),
			'parent_item'       => __( 'Parent Query', 'mdsupport' ),
			'parent_item_colon' => __( 'Parent Query:', 'mdsupport' ),
			'edit_item'         => __( 'Edit Query', 'mdsupport' ),
			'update_item'       => __( 'Update Query', 'mdsupport' ),
			'add_new_item'      => __( 'Add New Query', 'mdsupport' ),
			'new_item_name'     => __( 'New Query Name', 'mdsupport' ),
			'menu_name'         => __( 'Query', 'mdsupport' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'Query' ),
		);
		register_taxonomy( 'Query', array( 'support' ), $args );


		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Query Type', 'taxonomy general name', 'mdsupport' ),
			'singular_name'     => _x( 'Query Type', 'taxonomy singular name', 'mdsupport' ),
			'search_items'      => __( 'Search Querys', 'mdsupport' ),
			'all_items'         => __( 'All Querys', 'mdsupport' ),
			'parent_item'       => __( 'Parent Query', 'mdsupport' ),
			'parent_item_colon' => __( 'Parent Query:', 'mdsupport' ),
			'edit_item'         => __( 'Edit Query', 'mdsupport' ),
			'update_item'       => __( 'Update Query', 'mdsupport' ),
			'add_new_item'      => __( 'Add New Query', 'mdsupport' ),
			'new_item_name'     => __( 'New Query Name', 'mdsupport' ),
			'menu_name'         => __( 'Query', 'mdsupport' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'Query' ),
		);
		register_taxonomy( 'Query', array( 'support' ), $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Priority type', 'taxonomy general name', 'mdsupport' ),
			'singular_name'     => _x( 'Priority', 'taxonomy singular name', 'mdsupport' ),
			'search_items'      => __( 'Search Prioritys', 'mdsupport' ),
			'all_items'         => __( 'All Prioritys', 'mdsupport' ),
			'parent_item'       => __( 'Parent Priority', 'mdsupport' ),
			'parent_item_colon' => __( 'Parent Priority:', 'mdsupport' ),
			'edit_item'         => __( 'Edit Priority', 'mdsupport' ),
			'update_item'       => __( 'Update Priority', 'mdsupport' ),
			'add_new_item'      => __( 'Add New Priority', 'mdsupport' ),
			'new_item_name'     => __( 'New Priority Name', 'mdsupport' ),
			'menu_name'         => __( 'Priority', 'mdsupport' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'Priority_var'      => true,
			'rewrite'           => array( 'slug' => 'Priority' ),
		);
		register_taxonomy( 'Priority', array( 'support' ), $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Status type', 'taxonomy general name', 'mdsupport' ),
			'singular_name'     => _x( 'Status', 'taxonomy singular name', 'mdsupport' ),
			'search_items'      => __( 'Search Status ', 'mdsupport' ),
			'all_items'         => __( 'All Status ', 'mdsupport' ),
			'parent_item'       => __( 'Parent Status', 'mdsupport' ),
			'parent_item_colon' => __( 'Parent Status:', 'mdsupport' ),
			'edit_item'         => __( 'Edit Status', 'mdsupport' ),
			'update_item'       => __( 'Update Status', 'mdsupport' ),
			'add_new_item'      => __( 'Add New Status', 'mdsupport' ),
			'new_item_name'     => __( 'New Status Name', 'mdsupport' ),
			'menu_name'         => __( 'Status', 'mdsupport' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'status_var'        => true,
			'rewrite'           => array( 'slug' => 'Status' ),
		);
		register_taxonomy( 'Status', array( 'support' ), $args );

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Roles type', 'taxonomy general name', 'mdsupport' ),
			'singular_name'     => _x( 'Roles', 'taxonomy singular name', 'mdsupport' ),
			'search_items'      => __( 'Search Roles ', 'mdsupport' ),
			'all_items'         => __( 'All Roles ', 'mdsupport' ),
			'parent_item'       => __( 'Parent Roles', 'mdsupport' ),
			'parent_item_colon' => __( 'Parent Roles:', 'mdsupport' ),
			'edit_item'         => __( 'Edit Roles', 'mdsupport' ),
			'update_item'       => __( 'Update Roles', 'mdsupport' ),
			'add_new_item'      => __( 'Add New Roles', 'mdsupport' ),
			'new_item_name'     => __( 'New Roles Name', 'mdsupport' ),
			'menu_name'         => __( 'Roles', 'mdsupport' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'Roles_var'         => true,
			'rewrite'           => array( 'slug' => 'Roles' ),
		);
		register_taxonomy( 'Roles', array( 'support' ), $args );

	}

	/**
	 * Save Support Form Data using ajax
	 * @return  void();
	 */

	public function md_support_save() {
		$current_user       = wp_get_current_user();
		$support_title      = ! empty( $_POST['supporttitle'] ) ? $_POST['supporttitle'] : 'Support Title';
		$support_message    = ! empty( $_POST['supportmessage'] ) ? $_POST['supportmessage'] : 'No Text';
		$support_query_type = ! empty( $_POST['querytype'] ) ? $_POST['querytype'] : '';
		$priorty_query_type = ! empty( $_POST['priortytype'] ) ? $_POST['priortytype'] : '';
		$productid          = ! empty( $_POST['productid'] ) ? $_POST['productid'] : '';
		$username           = ! empty( $_POST['username'] ) ? $_POST['username'] : '';
		//echo $_POST['user_name'];
		// Array Support FormFileds object
		$support_post = array(
			'post_title'   => $support_title,
			'post_content' => $support_message,
			'post_status'  => 'publish',
			'post_type'    => 'support',
			'post_author'  => 1,
		);
		// Insert the Support  post into the database
		$postid = wp_insert_post( $support_post );
		wp_set_object_terms( $postid, $support_query_type, 'Query', true );
		wp_set_object_terms( $postid, $priorty_query_type, 'Priority', true );
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		// echo $_FILES["upload"]["name"];
		$uploadedfile     = $_FILES['file'];
		$upload_overrides = array( 'test_form' => false );
		$movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );

		$filename = $movefile['file'];
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			update_post_meta( $postid, 'mdsupport_attachement_file_url', $movefile['url'] );
			echo "File Upload Successfully";
		} else {
			/**
			 * Error generated by _wp_handle_upload()
			 * @see _wp_handle_upload() in wp-admin/includes/file.php
			 */
			echo $movefile['error'];
		}
		update_post_meta( $postid, 'mdsupport_procuct_id', $productid );
		update_post_meta( $postid, 'mdsupport_username', $username );


		// Mail Send After Submit Button

		do_action( 'mdsupport_email_send', $support_title, $filename, $support_message, $support_query_type, $priorty_query_type, $productid );

		// Update Your custome fileds
		do_action( 'mdsupport_custome_filed_update', $postid );
		die();

	}


	public function mdsupport_emall( $support_title, $filename, $support_message, $support_query_type, $priorty_query_type, $productid ) {
		$current_user = wp_get_current_user();
		$headers      = array( 'Content-Type: text/html; charset=UTF-8' );
		$message      = '<table>' . $current_user->user_login . " Submited Support Mail<br>";
		$message .= '<tr><td><b>' . __( "Support Title :", "mdsupport" ) . '</b>' . $support_title . '</td></tr>';
		$message .= '<tr><td><b>' . __( "Message :", "mdsupport" ) . '</b>' . $support_message . '</td></tr>';
		$message .= '<tr><td><b>' . __( "Query Type :", "mdsupport" ) . '</b>' . $support_query_type . '</td></tr>';
		$message .= '<tr><td><b>' . __( "Priorty Type :", "mdsupport" ) . '</b>' . $priorty_query_type . '</td></tr>';

		$adminmessage = apply_filters( 'md_support_admin_mail_message', $message );
		if ( $productid == '' ) {
			$adminmessage .= '</table>';
		} else {
			$adminmessage .= '<tr><td><b>' . __( "Product Name :", "mdsupport" ) . '</b><a  href="' . site_url() . '/wp-admin/post.php?post=' . $productid . '&action=edit">' . get_the_title( $productid ) . '</td></tr></table>';
		}
		$usermessage = apply_filters( 'md_support_user_mail_message', $message );
		if ( $productid == '' ) {
			$usermessage .= '</table>';
		} else {
			$usermessage .= '<tr><td><b>' . __( "Product Name :", "mdsupport" ) . '</b><a  href="' . get_permalink( $productid ) . '">' . get_the_title( $productid ) . '</td></tr></table>';
		}
		$attachments = array( $filename );
		$subject     = apply_filters( 'md_support_mail_message', 'Support Mail Recive' );
		wp_mail( get_option( 'admin_email', false ), $subject, $adminmessage, $headers, $attachments );

		//$message = "Support mail Recevie";
		wp_mail( $current_user->user_email, 'Support Submit Successfully', $usermessage, $headers );

	}

	public function mdsupport_load_posts() {
		$page     = $_POST['page'];
		$cur_page = $page;
		$page -= 1;
		// Set the number of results to display
		$per_page     = 10;
		$previous_btn = true;
		$next_btn     = true;
		$first_btn    = true;
		$last_btn     = true;
		$start        = $page * $per_page;
		$args         = array(
			'post_type'      => 'support',
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'paged'          => $_POST['page'],
			'posts_per_page' => $per_page,
			'offset'         => $start
		);
		$posts        = new WP_Query( $args );
		$count        = $posts->found_posts;
		?>
        <table>
            <th>Title</th>
            <th>Message</th>
            <th>Query Type</th>
            <th>Priority Type</th>
			<?php do_action( 'add_custome_header' ); ?>
            <th>Status</th>
			<?php
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) : $posts->the_post();
					$query_terms    = wp_get_object_terms( get_the_ID(), 'Query' );
					$priority_terms = wp_get_object_terms( get_the_ID(), 'Priority' );
					$status_terms   = wp_get_object_terms( get_the_ID(), 'Status' );
					if ( $status_terms[0]->name == '' ) {
						$supportstatus = 'pending';
					} else {
						$supportstatus = $status_terms[0]->name;
					}
					echo '<tr>';
					echo '<td>' . get_the_title() . '</td>';
					echo '<td>' . get_the_content() . '</td>';
					echo '<td>' . $query_terms[0]->name . '</td>';
					echo '<td>' . $priority_terms[0]->name . '</td>';
					echo '<td>' . $supportstatus . '</td>';
					do_action( 'add_custome_coloum' );
					echo '</tr>';
				endwhile;
				wp_reset_postdata();
			} else {
				echo __( 'No Query List found', 'mdsupport' );
			} ?>
        </table>
		<?php


		// Optional, wrap the output into a container
		$msg = "<div class='category_archive_sera-universal-content'>" . $msg . "</div><br class = 'clear' />";

		// This is where the magic happens
		$no_of_paginations = ceil( $count / $per_page );


		if ( $cur_page >= 7 ) {
			$start_loop = $cur_page - 3;
			if ( $no_of_paginations > $cur_page + 3 ) {
				$end_loop = $cur_page + 3;
			} else if ( $cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6 ) {
				$start_loop = $no_of_paginations - 6;
				$end_loop   = $no_of_paginations;
			} else {
				$end_loop = $no_of_paginations;
			}
		} else {
			$start_loop = 1;
			if ( $no_of_paginations > 7 ) {
				$end_loop = 7;
			} else {
				$end_loop = $no_of_paginations;
			}
		}

		// Pagination Buttons logic
		$pag_container .= "
        <div class='mdsupport-status-pagination' id='mdsupport-status-pagination'>
            <ul class='pagination'>";

		if ( $previous_btn && $cur_page > 1 ) {
			$pre = $cur_page - 1;
			$pag_container .= "<li p='$pre' class='active'>Previous</li>";
		} else if ( $previous_btn ) {
			//$pag_container .= "<li class='inactive'>Previous</li>";
		}
		for ( $i = $start_loop; $i <= $end_loop; $i ++ ) {

			if ( $cur_page == $i ) {
				$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
			} else {
				$pag_container .= "<li p='$i' class='active'>{$i}</li>";
			}
		}

		if ( $next_btn && $cur_page < $no_of_paginations ) {
			$nex = $cur_page + 1;
			$pag_container .= "<li p='$nex' class='active'>Next</li>";
		} else if ( $next_btn ) {
			//$pag_container .= "<li class='inactive'>Next</li>";
		}

		$pag_container = $pag_container . "
            </ul>
        </div>";

		// We echo the final output
		echo '<div class = "mdsupport-status-pagination-nav">' . $pag_container . '</div>';
		die();
	}

}