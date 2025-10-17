<?php
/**
 * Plugin Name:       WooCommerce Brand Taxonomy
 * Description:       Adds a WooCommerce brand taxonomy with Elementor integrations.
 * Version:           1.0.0
 * Author:            OpenAI Assistant
 * Text Domain:       woocommerce-brand-taxonomy
 * Domain Path:       /languages
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WBT_PLUGIN_FILE' ) ) {
    define( 'WBT_PLUGIN_FILE', __FILE__ );
}

define( 'WBT_PLUGIN_PATH', plugin_dir_path( WBT_PLUGIN_FILE ) );
define( 'WBT_PLUGIN_URL', plugin_dir_url( WBT_PLUGIN_FILE ) );
/**
 * Load plugin textdomain.
 */
function wbt_load_textdomain() {
    load_plugin_textdomain( 'woocommerce-brand-taxonomy', false, dirname( plugin_basename( WBT_PLUGIN_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wbt_load_textdomain' );

/**
 * Autoloader.
 *
 * @param string $class Class name.
 */
function wbt_autoload_classes( $class ) {
    if ( 0 !== strpos( $class, 'WBT_' ) ) {
        return;
    }

    $class = str_replace( 'WBT_', '', $class );
    $class = strtolower( str_replace( '_', '-', $class ) );
    $file  = WBT_PLUGIN_PATH . 'includes/class-' . $class . '.php';

    if ( file_exists( $file ) ) {
        include $file;
    }
}
spl_autoload_register( 'wbt_autoload_classes' );

/**
 * Helpers file.
 */
require_once WBT_PLUGIN_PATH . 'includes/helpers.php';

/**
 * Initialize plugin.
 */
function wbt_init_plugin() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    new WBT_Brand_Taxonomy();
    new WBT_Brand_Meta();
    new WBT_Brand_Settings();
    new WBT_Brand_Display();

    if ( did_action( 'elementor/loaded' ) ) {
        wbt_init_elementor_integration();
    } else {
        add_action( 'elementor/loaded', 'wbt_init_elementor_integration' );
    }
}
add_action( 'plugins_loaded', 'wbt_init_plugin', 20 );

/**
 * Initialize Elementor-specific integrations.
 */
function wbt_init_elementor_integration() {
    static $initialized = false;

    if ( $initialized ) {
        return;
    }

    $initialized = true;

    new WBT_Brand_Elementor_Tags();
    new WBT_Brand_Elementor_Widgets();
}

/**
 * Enqueue frontend assets.
 */
function wbt_enqueue_frontend_assets() {
    wp_enqueue_style( 'wbt-frontend', WBT_PLUGIN_URL . 'assets/frontend.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'wbt_enqueue_frontend_assets' );

/**
 * Enqueue admin assets for taxonomy screens.
 *
 * @param string $hook_suffix Hook suffix.
 */
function wbt_enqueue_admin_assets( $hook_suffix ) {
    $allowed_hooks = array( 'product_page_wbt-brand-settings' );

    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

    $is_brand_taxonomy = $screen && in_array( $screen->id, array( 'edit-product_brand', 'term-product_brand' ), true );

    if ( ! in_array( $hook_suffix, $allowed_hooks, true ) && ! $is_brand_taxonomy ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style( 'wbt-admin', WBT_PLUGIN_URL . 'assets/admin.css', array(), '1.0.0' );
    wp_enqueue_script( 'wbt-admin', WBT_PLUGIN_URL . 'assets/admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'wbt_enqueue_admin_assets' );
