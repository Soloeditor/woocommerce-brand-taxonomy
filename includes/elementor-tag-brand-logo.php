<?php
/**
 * Elementor dynamic tag for brand logo URL.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

use Elementor\Core\DynamicTags\Tag;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Elementor_Tag_Brand_Logo' ) ) {
    /**
     * Elementor dynamic tag for brand logo URLs.
     */
    class WBT_Elementor_Tag_Brand_Logo extends Tag {

        /**
         * Get name.
         */
        public function get_name() {
            return 'wbt-brand-logo';
        }

        /**
         * Get title.
         */
        public function get_title() {
            return __( 'Brand Logo URL', 'woocommerce-brand-taxonomy' );
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
            return array( \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY );
        }

        /**
         * Render tag.
         */
        public function render() {
            $brand = WBT_Helpers::detect_brand_context();

            if ( $brand ) {
                $url = WBT_Helpers::get_brand_logo_url( $brand->term_id, 'full' );
                if ( $url ) {
                    echo esc_url( $url );
                }
            }
        }
    }
}
