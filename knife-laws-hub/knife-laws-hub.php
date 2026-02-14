<?php
/**
 * Plugin Name: KnifeInformer State Knife Laws Hub
 * Description: A modern, mobile-first knife law hub with state pages, comparison tool, and normalized data schema.
 * Version: 1.0.0
 * Author: KnifeInformer
 * Text Domain: knife-laws-hub
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KLH_VERSION', '1.0.0' );
define( 'KLH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KLH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once KLH_PLUGIN_DIR . 'includes/class-klh-post-type.php';
require_once KLH_PLUGIN_DIR . 'includes/class-klh-schema.php';
require_once KLH_PLUGIN_DIR . 'includes/class-klh-rest-api.php';
require_once KLH_PLUGIN_DIR . 'includes/class-klh-templates.php';
require_once KLH_PLUGIN_DIR . 'includes/class-klh-seo.php';

/**
 * Main plugin class.
 */
final class Knife_Laws_Hub {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    public function init() {
        KLH_Post_Type::register();
        KLH_REST_API::register_routes();
        KLH_Templates::register();
        KLH_SEO::register();
    }

    public function enqueue_assets() {
        if ( ! $this->is_klh_page() ) {
            return;
        }

        wp_enqueue_style(
            'klh-styles',
            KLH_PLUGIN_URL . 'assets/css/klh-styles.css',
            array(),
            KLH_VERSION
        );

        wp_enqueue_script(
            'klh-hub',
            KLH_PLUGIN_URL . 'assets/js/klh-hub.js',
            array(),
            KLH_VERSION,
            true
        );

        wp_localize_script( 'klh-hub', 'klhData', array(
            'restUrl'  => rest_url( 'klh/v1/' ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'stateUrl' => home_url( '/state-knife-laws/' ),
        ) );
    }

    private function is_klh_page() {
        return is_singular( 'knife_law_state' )
            || is_post_type_archive( 'knife_law_state' )
            || is_page( array( 'state-knife-laws', 'knife-laws-compare', 'knife-laws-methodology', 'knife-laws-glossary' ) );
    }

    public function activate() {
        KLH_Post_Type::register();
        flush_rewrite_rules();
    }
}

Knife_Laws_Hub::instance();
