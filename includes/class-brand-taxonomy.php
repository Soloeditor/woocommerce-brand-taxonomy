<?php
/**
 * Register the product brand taxonomy.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Taxonomy' ) ) {
    /**
     * Handles taxonomy registration.
     */
    class WBT_Brand_Taxonomy {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', array( $this, 'register_taxonomy' ) );
        }

        /**
         * Register taxonomy.
         */
        public function register_taxonomy() {
            $labels = array(
                'name'              => _x( 'Brands', 'taxonomy general name', 'woocommerce-brand-taxonomy' ),
                'singular_name'     => _x( 'Brand', 'taxonomy singular name', 'woocommerce-brand-taxonomy' ),
                'search_items'      => __( 'Search Brands', 'woocommerce-brand-taxonomy' ),
                'all_items'         => __( 'All Brands', 'woocommerce-brand-taxonomy' ),
                'parent_item'       => __( 'Parent Brand', 'woocommerce-brand-taxonomy' ),
                'parent_item_colon' => __( 'Parent Brand:', 'woocommerce-brand-taxonomy' ),
                'edit_item'         => __( 'Edit Brand', 'woocommerce-brand-taxonomy' ),
                'update_item'       => __( 'Update Brand', 'woocommerce-brand-taxonomy' ),
                'add_new_item'      => __( 'Add New Brand', 'woocommerce-brand-taxonomy' ),
                'new_item_name'     => __( 'New Brand Name', 'woocommerce-brand-taxonomy' ),
                'menu_name'         => __( 'Brands', 'woocommerce-brand-taxonomy' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'brand' ),
                'show_in_rest'      => true,
            );

            register_taxonomy( 'product_brand', array( 'product' ), $args );
        }
    }
}
