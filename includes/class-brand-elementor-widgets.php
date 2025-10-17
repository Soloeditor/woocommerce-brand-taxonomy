<?php
/**
 * Elementor widgets registration.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

use Elementor\Widgets_Manager;
use Elementor\Elements_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Elementor_Widgets' ) ) {
    /**
     * Registers Elementor widgets.
     */
    class WBT_Brand_Elementor_Widgets {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
            add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        }

        /**
         * Register widget category.
         */
        public function register_category( Elements_Manager $elements_manager ) {
            $elements_manager->add_category(
                'wbt-woocommerce-elements',
                array(
                    'title' => __( 'WooCommerce Elements', 'woocommerce-brand-taxonomy' ),
                )
            );
        }

        /**
         * Register widgets.
         */
        public function register_widgets( Widgets_Manager $widgets_manager ) {
            require_once WBT_PLUGIN_PATH . 'includes/widgets/class-widget-brand-logo.php';
            require_once WBT_PLUGIN_PATH . 'includes/widgets/class-widget-brand-name.php';
            require_once WBT_PLUGIN_PATH . 'includes/widgets/class-widget-brand-grid.php';

            $widgets_manager->register( new WBT_Widget_Brand_Logo() );
            $widgets_manager->register( new WBT_Widget_Brand_Name() );
            $widgets_manager->register( new WBT_Widget_Brand_Grid() );
        }
    }
}
