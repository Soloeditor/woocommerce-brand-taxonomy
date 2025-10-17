<?php
/**
 * Elementor widget for brand name.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Widget_Brand_Name' ) ) {
    /**
     * Brand name widget.
     */
    class WBT_Widget_Brand_Name extends Widget_Base {

        /**
         * Get widget name.
         */
        public function get_name() {
            return 'wbt-brand-name';
        }

        /**
         * Get widget title.
         */
        public function get_title() {
            return __( 'Brand Name', 'woocommerce-brand-taxonomy' );
        }

        /**
         * Get widget icon.
         */
        public function get_icon() {
            return 'eicon-product-title';
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
                'source',
                array(
                    'label'   => __( 'Source', 'woocommerce-brand-taxonomy' ),
                    'type'    => Controls_Manager::SELECT,
                    'default' => 'auto',
                    'options' => array(
                        'auto'    => __( 'Auto Detect', 'woocommerce-brand-taxonomy' ),
                        'product' => __( 'Product', 'woocommerce-brand-taxonomy' ),
                        'archive' => __( 'Archive', 'woocommerce-brand-taxonomy' ),
                        'manual'  => __( 'Manual Selection', 'woocommerce-brand-taxonomy' ),
                    ),
                )
            );

            $this->add_control(
                'manual_brand',
                array(
                    'label'     => __( 'Select Brand', 'woocommerce-brand-taxonomy' ),
                    'type'      => Controls_Manager::SELECT2,
                    'options'   => WBT_Helpers::get_brand_terms_for_control(),
                    'condition' => array( 'source' => 'manual' ),
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
                    'default'      => 'no',
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
                    'default'      => 'yes',
                )
            );

            $this->add_responsive_control(
                'alignment',
                array(
                    'label'     => __( 'Alignment', 'woocommerce-brand-taxonomy' ),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => array(
                        'left'   => array(
                            'title' => __( 'Left', 'woocommerce-brand-taxonomy' ),
                            'icon'  => 'eicon-text-align-left',
                        ),
                        'center' => array(
                            'title' => __( 'Center', 'woocommerce-brand-taxonomy' ),
                            'icon'  => 'eicon-text-align-center',
                        ),
                        'right'  => array(
                            'title' => __( 'Right', 'woocommerce-brand-taxonomy' ),
                            'icon'  => 'eicon-text-align-right',
                        ),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}} .wbt-brand-wrap' => 'text-align: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_section();
        }

        /**
         * Render widget.
         */
        protected function render() {
            $settings = $this->get_settings_for_display();
            $hide_if_empty = isset( $settings['hide_if_empty'] ) && 'yes' === $settings['hide_if_empty'];

            $brand = $this->resolve_brand( $settings );

            if ( ! $brand ) {
                if ( ! $hide_if_empty ) {
                    echo '<div class="wbt-brand-wrap"></div>';
                }
                return;
            }

            $content = esc_html( $brand->name );

            if ( isset( $settings['link_to_archive'] ) && 'yes' === $settings['link_to_archive'] ) {
                $link = get_term_link( $brand );
                if ( ! is_wp_error( $link ) ) {
                    $content = sprintf( '<a href="%1$s" class="wbt-brand-name-link">%2$s</a>', esc_url( $link ), $content );
                }
            }

            echo '<div class="wbt-brand-wrap"><span class="wbt-brand-name">' . $content . '</span></div>';
        }

        /**
         * Resolve brand term based on widget settings.
         *
         * @param array $settings Settings.
         *
         * @return WP_Term|null
         */
        protected function resolve_brand( $settings ) {
            switch ( $settings['source'] ) {
                case 'product':
                    return WBT_Helpers::get_product_brand();
                case 'archive':
                    if ( is_tax( 'product_brand' ) ) {
                        $term = get_queried_object();
                        if ( $term instanceof WP_Term ) {
                            return $term;
                        }
                    }
                    return null;
                case 'manual':
                    if ( ! empty( $settings['manual_brand'] ) ) {
                        $term = get_term_by( 'id', (int) $settings['manual_brand'], 'product_brand' );
                        if ( $term && ! is_wp_error( $term ) ) {
                            return $term;
                        }
                    }
                    return null;
                case 'auto':
                default:
                    return WBT_Helpers::detect_brand_context();
            }
        }
    }
}
