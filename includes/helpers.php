<?php
/**
 * Helper functions for WooCommerce Brand Taxonomy.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Helpers' ) ) {
    /**
     * Helper utilities for brands.
     */
    class WBT_Helpers {

        /**
         * Get a product's brand term.
         *
         * @param int $product_id Product ID.
         *
         * @return WP_Term|null
         */
        public static function get_product_brand( $product_id = 0 ) {
            if ( ! $product_id && class_exists( 'WC_Product' ) ) {
                global $product;

                if ( $product instanceof WC_Product ) {
                    $product_id = $product->get_id();
                }
            }

            if ( ! $product_id && function_exists( 'wc_get_product' ) ) {
                $maybe_product = wc_get_product();
                if ( $maybe_product ) {
                    $product_id = $maybe_product->get_id();
                }
            }

            if ( ! $product_id ) {
                $product_id = get_the_ID();
            }

            if ( ! $product_id ) {
                return null;
            }

            $terms = wp_get_post_terms( $product_id, 'product_brand' );

            if ( is_wp_error( $terms ) || empty( $terms ) ) {
                return null;
            }

            return $terms[0];
        }

        /**
         * Retrieve brand logo URL.
         *
         * @param int    $brand_id Brand term ID.
         * @param string $size     Image size.
         *
         * @return string
         */
        public static function get_brand_logo_url( $brand_id, $size = 'medium' ) {
            $attachment_id = (int) get_term_meta( $brand_id, 'wbt_brand_logo_id', true );

            if ( ! $attachment_id ) {
                return '';
            }

            $image = wp_get_attachment_image_src( $attachment_id, $size );

            return $image ? $image[0] : '';
        }

        /**
         * Display brand logo img element.
         *
         * @param int   $product_id Product ID.
         * @param array $args       Args.
         */
        public static function display_brand_logo_img( $product_id = 0, $args = array() ) {
            $defaults = array(
                'size'  => 'medium',
                'class' => 'wbt-brand-logo',
            );

            $args = wp_parse_args( $args, $defaults );

            $brand = self::get_product_brand( $product_id );

            if ( ! $brand ) {
                return;
            }

            $logo_url = self::get_brand_logo_url( $brand->term_id, $args['size'] );

            if ( ! $logo_url ) {
                return;
            }

            printf(
                '<img src="%1$s" alt="%2$s" class="%3$s" />',
                esc_url( $logo_url ),
                esc_attr( $brand->name ),
                esc_attr( $args['class'] )
            );
        }

        /**
         * Get all brand terms.
         *
         * @param array $args Args for get_terms.
         *
         * @return WP_Term[]
         */
        public static function get_all_brands( $args = array() ) {
            $defaults = array(
                'taxonomy'   => 'product_brand',
                'hide_empty' => false,
            );

            $args = wp_parse_args( $args, $defaults );

            $terms = get_terms( $args );

            if ( is_wp_error( $terms ) ) {
                return array();
            }

            return $terms;
        }

        /**
         * Get brand terms formatted for Elementor controls.
         *
         * @return array
         */
        public static function get_brand_terms_for_control() {
            $options = array();

            $terms = self::get_all_brands( array(
                'hide_empty' => false,
                'fields'     => 'all',
            ) );

            foreach ( $terms as $term ) {
                $options[ $term->term_id ] = sprintf( '%1$s (%2$s)', $term->name, $term->slug );
            }

            return $options;
        }

        /**
         * Detect brand in current context.
         *
         * @return WP_Term|null
         */
        public static function detect_brand_context() {
            if ( is_tax( 'product_brand' ) ) {
                $term = get_queried_object();

                if ( $term instanceof WP_Term ) {
                    return $term;
                }
            }

            return self::get_product_brand();
        }
    }
}
