<?php
/**
 * Elementor widget for brand grid.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Widget_Brand_Grid' ) ) {
    /**
     * Brand grid widget.
     */
    class WBT_Widget_Brand_Grid extends Widget_Base {

        /**
         * Widget name.
         */
        public function get_name() {
            return 'wbt-brand-grid';
        }

        /**
         * Widget title.
         */
        public function get_title() {
            return __( 'Brand Grid', 'woocommerce-brand-taxonomy' );
        }

        /**
         * Icon.
         */
        public function get_icon() {
            return 'eicon-gallery-grid';
        }

        /**
         * Categories.
         */
        public function get_categories() {
            return array( 'wbt-woocommerce-elements' );
        }

        /**
         * Register controls.
         */
        protected function register_controls() {
            $this->start_controls_section(
                'section_content',
                array(
                    'label' => __( 'Content', 'woocommerce-brand-taxonomy' ),
                )
            );

            $this->add_control(
                'columns',
                array(
                    'label'   => __( 'Columns', 'woocommerce-brand-taxonomy' ),
                    'type'    => Controls_Manager::NUMBER,
                    'default' => 4,
                    'min'     => 1,
                    'max'     => 6,
                )
            );

            $this->add_control(
                'show_name',
                array(
                    'label'        => __( 'Show Name', 'woocommerce-brand-taxonomy' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', 'woocommerce-brand-taxonomy' ),
                    'label_off'    => __( 'No', 'woocommerce-brand-taxonomy' ),
                    'return_value' => 'yes',
                    'default'      => 'yes',
                )
            );

            $this->add_control(
                'link_to_archive',
                array(
                    'label'        => __( 'Link to Archive', 'woocommerce-brand-taxonomy' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', 'woocommerce-brand-taxonomy' ),
                    'label_off'    => __( 'No', 'woocommerce-brand-taxonomy' ),
                    'return_value' => 'yes',
                    'default'      => 'yes',
                )
            );

            $this->add_control(
                'orderby',
                array(
                    'label'   => __( 'Order By', 'woocommerce-brand-taxonomy' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'name',
                    'options' => array(
                        'name' => __( 'Name', 'woocommerce-brand-taxonomy' ),
                        'id'   => __( 'ID', 'woocommerce-brand-taxonomy' ),
                    ),
                )
            );

            $this->add_control(
                'order',
                array(
                    'label'   => __( 'Order', 'woocommerce-brand-taxonomy' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'ASC',
                    'options' => array(
                        'ASC'  => __( 'Ascending', 'woocommerce-brand-taxonomy' ),
                        'DESC' => __( 'Descending', 'woocommerce-brand-taxonomy' ),
                    ),
                )
            );

            $this->add_control(
                'hide_if_empty',
                array(
                    'label'        => __( 'Hide if Empty', 'woocommerce-brand-taxonomy' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', 'woocommerce-brand-taxonomy' ),
                    'label_off'    => __( 'No', 'woocommerce-brand-taxonomy' ),
                    'return_value' => 'yes',
                    'default'      => 'no',
                )
            );

            $this->end_controls_section();
        }

        /**
         * Render widget.
         */
        protected function render() {
            $settings = $this->get_settings_for_display();

            $columns = isset( $settings['columns'] ) ? max( 1, min( 6, (int) $settings['columns'] ) ) : 4;
            $order   = isset( $settings['order'] ) && 'DESC' === strtoupper( $settings['order'] ) ? 'DESC' : 'ASC';
            $orderby = ( isset( $settings['orderby'] ) && 'id' === $settings['orderby'] ) ? 'id' : 'name';

            $brands = WBT_Helpers::get_all_brands(
                array(
                    'orderby'    => $orderby,
                    'order'      => $order,
                    'hide_empty' => false,
                )
            );

            $hide_if_empty = isset( $settings['hide_if_empty'] ) && 'yes' === $settings['hide_if_empty'];

            if ( empty( $brands ) ) {
                if ( ! $hide_if_empty ) {
                    echo '<div class="wbt-brand-grid cols-' . esc_attr( $columns ) . '"></div>';
                }
                return;
            }

            $show_name  = isset( $settings['show_name'] ) && 'yes' === $settings['show_name'];
            $link_cards = isset( $settings['link_to_archive'] ) && 'yes' === $settings['link_to_archive'];

            echo '<div class="wbt-brand-grid cols-' . esc_attr( $columns ) . '">';
            foreach ( $brands as $brand ) {
                $logo_url = WBT_Helpers::get_brand_logo_url( $brand->term_id, 'medium' );
                $link     = $link_cards ? get_term_link( $brand ) : '';

                echo '<div class="wbt-brand-card">';
                if ( $link && ! is_wp_error( $link ) ) {
                    echo '<a href="' . esc_url( $link ) . '" class="wbt-brand-card-link">';
                }

                if ( $logo_url ) {
                    printf( '<span class="wbt-brand-logo"><img src="%1$s" alt="%2$s" /></span>', esc_url( $logo_url ), esc_attr( $brand->name ) );
                } else {
                    $placeholder = function_exists( 'mb_substr' ) ? mb_substr( $brand->name, 0, 1 ) : substr( $brand->name, 0, 1 );
                    $placeholder = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $placeholder ) : strtoupper( $placeholder );
                    printf( '<span class="wbt-brand-placeholder" aria-hidden="true">%s</span>', esc_html( $placeholder ) );
                }

                if ( $show_name ) {
                    printf( '<span class="wbt-brand-name">%s</span>', esc_html( $brand->name ) );
                }

                if ( $link && ! is_wp_error( $link ) ) {
                    echo '</a>';
                }

                echo '</div>';
            }
            echo '</div>';
        }
    }
}
