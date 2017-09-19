<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 * @author     multidots <info@multidots.com>
 */
class Mdsupport {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mdsupport_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'mdsupport';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mdsupport_Loader. Orchestrates the hooks of the plugin.
	 * - Mdsupport_i18n. Defines internationalization functionality.
	 * - Mdsupport_Admin. Defines all hooks for the admin area.
	 * - Mdsupport_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mdsupport-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mdsupport-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mdsupport-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mdsupport-public.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/mdsupport-admin-display.php';

		$this->loader = new Mdsupport_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mdsupport_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mdsupport_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Mdsupport_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('admin_menu',$plugin_admin, 'theme_options_panel');
        $this->loader->add_action( 'add_meta_boxes',$plugin_admin,'mdsupport_register_meta_boxes' );
        $this->loader->add_action( 'save_post',$plugin_admin, 'mdsupport_save_meta_box' );
        $this->loader->add_action('post_edit_form_tag',$plugin_admin,'mdsupport_edit_form_tag');
        $this->loader->add_action('post_edit_form_tag',$plugin_admin,'mdsupport_edit_form_tag');
        $this->loader->add_filter('wp_terms_checklist_args',$plugin_admin,'mdsupport_term_radio_checklist');
        $this->loader->add_filter( 'parse_query',$plugin_admin,'mdsupport_posts_filter' );
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mdsupport_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		if($mdsupport_enable = get_option('mdsupport_myaccount_page_enble') && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
            $this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'mdsupport_my_account_menu_items', 1);
            $this->loader->add_action('init', $plugin_public, 'mdsupport_custom_endpoints');
            $this->loader->add_filter('query_vars', $plugin_public, 'my_custom_query_vars');
            $this->loader->add_action('after_switch_theme', $plugin_public, 'my_custom_flush_rewrite_rules');
            $this->loader->add_action('woocommerce_account_md-support_endpoint', $plugin_public, 'mdsupport_form_content');
        }
        $this->loader->add_action( 'init',$plugin_public,'mdsupport_post' );
        $this->loader->add_action( 'init',$plugin_public,'mdsupport_taxonomies' );
        $this->loader->add_action( 'wp_ajax_md_support_save',$plugin_public,'md_support_save' );
        $this->loader->add_action( 'wp_ajax_md_support_save',$plugin_public,'md_support_save' );
        $this->loader->add_action( 'wp_ajax_mdsupport_load_posts',$plugin_public,'mdsupport_load_posts' );
        $this->loader->add_action( 'wp_ajax_mdsupport_load_posts',$plugin_public,'mdsupport_load_posts' );
        $this->loader->add_action( 'mdsupport_email_send',$plugin_public,'mdsupport_emall',10,6 );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mdsupport_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
