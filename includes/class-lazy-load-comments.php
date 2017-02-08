<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @category   Core
 * @package    LLC
 * @subpackage Core
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://wordpress.org/plugins/lazy-load-for-comments
 */
class Lazy_Load_Comments {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    LLC_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Initialize the core class and set properties.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct() {

        $this->dependencies();
        $this->set_locale();
        $this->admin_hooks();
        $this->public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - LLC_Loader. Orchestrates the hooks of the plugin.
     * - LLC_Admin. Defines all hooks for the dashboard.
     * - LLC_Public. Defines all hooks for the public functions.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     * 
     * @return void
     */
    private function dependencies() {

        require_once LLC_PLUGIN_DIR . '/includes/class-llc-loader.php';
        require_once LLC_PLUGIN_DIR . '/includes/class-llc-i18n.php';
        require_once LLC_PLUGIN_DIR . '/admin/class-llc-admin.php';
        require_once LLC_PLUGIN_DIR . '/public/class-llc-public.php';

        $this->loader = new LLC_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the LLC_I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     * 
     * @return void
     */
    private function set_locale() {

        $plugin_i18n = new LLC_I18n();

        $plugin_i18n->set_domain();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin functionality
     * of the plugin.
     *
     * @since  1.0.0
     * @access private
     * @uses   add_action()
     * 
     * @return void
     */
    private function admin_hooks() {

        // No need to execute if public side.
        if ( ! is_admin() ) {
            return;
        }

        $plugin_admin = new LLC_Admin();

        $this->loader->add_action( 'admin_init', $plugin_admin, 'options_page' );
    }

    /**
     * Register all of the hooks related to handle 404 actions of the plugin.
     *
     * @since  1.0.0
     * @access private
     * @uses   add_filter()
     * 
     * @return void
     */
    private function public_hooks() {

        $plugin_public = new LLC_Public();
        
        $this->loader->add_filter( 'comments_template', $plugin_public, 'llc_template' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'comments_script', 100 );
        $this->loader->add_action( 'wp_ajax_llc_load_comments', $plugin_public, 'comments_content' );
        $this->loader->add_action( 'wp_ajax_nopriv_llc_load_comments', $plugin_public, 'comments_content' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function run() {

        $this->loader->run();
    }

}
