<?php
/**
 * Frontend display of brands and shortcodes.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Display' ) ) {
    /**
     * Handle frontend display and shortcodes.
     */
    class WBT_Brand_Display {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', array( $this, 'register_shortcodes' ) );
            add_action( 'woocommerce_single_product_summary', array( $this, 'output_single_product_brand' ), 4 );
            add_action( 'woocommerce_archive_description', array( $this, 'output_archive_brand_header' ) );
        }

        /**
         * Register shortcodes.
         */
        public function register_shortcodes() {
            add_shortcode( 'brand_logo', array( $this, 'shortcode_brand_logo' ) );
            add_shortcode( 'brand_name', array( $this, 'shortcode_brand_name' ) );

            if ( WBT_Brand_Settings::get_option( 'enable_list' ) ) {
                add_shortcode( 'brand_list', array( $this, 'shortcode_brand_list' ) );
            }
        }

        /**
         * Output brand on single product.
         */
        public function output_single_product_brand() {
            if ( ! WBT_Brand_Settings::get_option( 'show_single' ) ) {
                return;
            }

            $brand = WBT_Helpers::get_product_brand();

            if ( ! $brand ) {
                return;
            }

            $logo_url = WBT_Helpers::get_brand_logo_url( $brand->term_id, 'medium' );
            if ( ! $logo_url ) {
                return;
            }

            printf(
                '<div class="wbt-brand-wrap"><a href="%1$s"><img src="%2$s" alt="%3$s" class="wbt-brand-logo" /></a></div>',
                esc_url( get_term_link( $brand ) ),
                esc_url( $logo_url ),
                esc_attr( $brand->name )
            );
        }

        /**
         * Output brand header on archive pages.
         */
        public function output_archive_brand_header() {
            if ( ! is_tax( 'product_brand' ) || ! WBT_Brand_Settings::get_option( 'show_archive' ) ) {
                return;
            }

            $term = get_queried_object();

            if ( ! $term instanceof WP_Term ) {
                return;
            }

            $logo_url          = WBT_Helpers::get_brand_logo_url( $term->term_id, 'large' );
            $brand_description = get_term_meta( $term->term_id, 'wbt_brand_description', true );

            if ( ! $logo_url && ! $brand_description ) {
                return;
            }

            echo '<div class="wbt-brand-wrap">';
            if ( $logo_url ) {
                printf( '<img src="%1$s" alt="%2$s" class="wbt-brand-logo" />', esc_url( $logo_url ), esc_attr( $term->name ) );
            }
            if ( $brand_description ) {
                echo '<div class="wbt-brand-description">' . wp_kses_post( wpautop( $brand_description ) ) . '</div>';
            }
            echo '</div>';
        }

        /**
         * Shortcode: brand_logo.
         *
         * @param array $atts Shortcode attributes.
         *
         * @return string
         */
        public function shortcode_brand_logo( $atts ) {
            $atts = shortcode_atts(
                array(
                    'size'   => 'medium',
                    'class'  => 'wbt-brand-logo',
                    'linked' => 'yes',
                ),
                $atts,
                'brand_logo'
            );

            $brand = WBT_Helpers::detect_brand_context();

            if ( ! $brand ) {
                return '';
            }

            $logo_url = WBT_Helpers::get_brand_logo_url( $brand->term_id, $atts['size'] );

            if ( ! $logo_url ) {
                return '';
            }

            $img = sprintf(
                '<img src="%1$s" alt="%2$s" class="%3$s" />',
                esc_url( $logo_url ),
                esc_attr( $brand->name ),
                esc_attr( $atts['class'] )
            );

            if ( 'yes' === strtolower( $atts['linked'] ) ) {
                $img = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_term_link( $brand ) ), $img );
            }

            return '<div class="wbt-brand-wrap">' . $img . '</div>';
        }

        /**
         * Shortcode: brand_name.
         *
         * @return string
         */
        public function shortcode_brand_name() {
            $brand = WBT_Helpers::detect_brand_context();

            if ( ! $brand ) {
                return '';
            }

            return esc_html( $brand->name );
        }

        /**
         * Shortcode: brand_list.
         *
         * @param array $atts Attributes.
         *
         * @return string
         */
        public function shortcode_brand_list( $atts ) {
            $atts = shortcode_atts(
                array(
                    'columns' => 4,
                    'orderby' => 'name',
                    'order'   => 'ASC',
                ),
                $atts,
                'brand_list'
            );

            $columns = max( 1, min( 6, (int) $atts['columns'] ) );
            $orderby = strtolower( sanitize_key( $atts['orderby'] ) );
            if ( ! in_array( $orderby, array( 'name', 'id' ), true ) ) {
                $orderby = 'name';
            }

            $order = strtoupper( $atts['order'] );
            $order = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'ASC';

            $brands = WBT_Helpers::get_all_brands(
                array(
                    'orderby'    => $orderby,
                    'order'      => $order,
                    'hide_empty' => false,
                )
            );

            if ( empty( $brands ) ) {
                return '';
            }

            ob_start();
            printf( '<div class="wbt-brand-grid cols-%d">', $columns );
            foreach ( $brands as $brand ) {
                $logo_url = WBT_Helpers::get_brand_logo_url( $brand->term_id, 'medium' );
                $link     = get_term_link( $brand );

                echo '<div class="wbt-brand-card">';
                if ( ! is_wp_error( $link ) ) {
                    echo '<a href="' . esc_url( $link ) . '" class="wbt-brand-card-link">';
                }

                if ( $logo_url ) {
                    printf( '<span class="wbt-brand-logo"><img src="%1$s" alt="%2$s" /></span>', esc_url( $logo_url ), esc_attr( $brand->name ) );
                } else {
                    $placeholder = function_exists( 'mb_substr' ) ? mb_substr( $brand->name, 0, 1 ) : substr( $brand->name, 0, 1 );
                    $placeholder = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $placeholder ) : strtoupper( $placeholder );
                    printf( '<span class="wbt-brand-placeholder" aria-hidden="true">%s</span>', esc_html( $placeholder ) );
                }

                printf( '<span class="wbt-brand-name">%s</span>', esc_html( $brand->name ) );

                if ( ! is_wp_error( $link ) ) {
                    echo '</a>';
                }
                echo '</div>';
            }
            echo '</div>';

            return ob_get_clean();
        }
    }
}
