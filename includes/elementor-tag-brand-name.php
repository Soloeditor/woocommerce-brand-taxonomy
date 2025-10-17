<?php
/**
 * Elementor dynamic tag for brand name.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

use Elementor\Core\DynamicTags\Tag;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Elementor_Tag_Brand_Name' ) ) {
    /**
     * Elementor dynamic tag for brand name output.
     */
    class WBT_Elementor_Tag_Brand_Name extends Tag {

        /**
         * Get name.
         */
        public function get_name() {
            return 'wbt-brand-name';
        }

        /**
         * Get title.
         */
        public function get_title() {
            return __( 'Brand Name', 'woocommerce-brand-taxonomy' );
        }

        /**
         * Get group.
         */
        public function get_group() {
            return 'woocommerce';
        }

        /**
         * Categories.
         */
        public function get_categories() {
            return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
        }

        /**
         * Render tag.
         */
        public function render() {
            $brand = WBT_Helpers::detect_brand_context();

            if ( $brand ) {
                echo esc_html( $brand->name );
            }
        }
    }
}
