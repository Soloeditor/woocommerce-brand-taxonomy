<?php
/**
 * Elementor dynamic tags for brand data.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Elementor_Tags' ) ) {
    /**
     * Registers Elementor dynamic tags and controls category.
     */
    class WBT_Brand_Elementor_Tags {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'elementor/dynamic_tags/register', array( $this, 'register_group' ) );
            add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_tags' ) );
        }

        /**
         * Register dynamic tags group.
         */
        public function register_group() {
            if ( class_exists( '\Elementor\Plugin' ) ) {
                $plugin = \Elementor\Plugin::instance();

                if ( isset( $plugin->dynamic_tags ) ) {
                    $plugin->dynamic_tags->register_group( 'woocommerce', array(
                        'title' => __( 'WooCommerce', 'woocommerce-brand-taxonomy' ),
                    ) );
                }
            }
        }

        /**
         * Register custom dynamic tags.
         *
         * @param \Elementor\DynamicTags_Manager|\Elementor\Core\DynamicTags\Manager $dynamic_tags_manager Manager instance.
         */
        public function register_tags( $dynamic_tags_manager ) {
            $is_legacy_manager = $dynamic_tags_manager instanceof \Elementor\DynamicTags_Manager;
            $is_core_manager   = $dynamic_tags_manager instanceof \Elementor\Core\DynamicTags\Manager;

            if ( ! $is_legacy_manager && ! $is_core_manager ) {
                return;
            }

            require_once WBT_PLUGIN_PATH . 'includes/elementor-tag-brand-name.php';
            require_once WBT_PLUGIN_PATH . 'includes/elementor-tag-brand-logo.php';

            $dynamic_tags_manager->register_tag( 'WBT_Elementor_Tag_Brand_Name' );
            $dynamic_tags_manager->register_tag( 'WBT_Elementor_Tag_Brand_Logo' );
        }
    }
}
