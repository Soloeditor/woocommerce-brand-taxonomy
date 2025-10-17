<?php
/**
 * Brand settings page.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Settings' ) ) {
    /**
     * Admin settings for brand displays.
     */
    class WBT_Brand_Settings {

        /**
         * Option key.
         */
        const OPTION_KEY = 'wbt_options';

        /**
         * Default options.
         *
         * @return array
         */
        public static function get_defaults() {
            return array(
                'show_single'   => 1,
                'show_archive'  => 1,
                'enable_list'   => 1,
            );
        }

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'register_menu' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
        }

        /**
         * Register admin menu.
         */
        public function register_menu() {
            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Brand Settings', 'woocommerce-brand-taxonomy' ),
                __( 'Brand Settings', 'woocommerce-brand-taxonomy' ),
                'manage_woocommerce',
                'wbt-brand-settings',
                array( $this, 'render_settings_page' )
            );
        }

        /**
         * Register settings.
         */
        public function register_settings() {
            register_setting( 'wbt_brand_settings', self::OPTION_KEY, array( $this, 'sanitize_options' ) );

            add_settings_section(
                'wbt_brand_display_section',
                __( 'Brand Display Options', 'woocommerce-brand-taxonomy' ),
                '__return_false',
                'wbt_brand_settings'
            );

            add_settings_field(
                'wbt_show_single',
                __( 'Show brand on single product pages', 'woocommerce-brand-taxonomy' ),
                array( $this, 'render_toggle_field' ),
                'wbt_brand_settings',
                'wbt_brand_display_section',
                array(
                    'label_for' => 'wbt_show_single',
                    'option'    => 'show_single',
                )
            );

            add_settings_field(
                'wbt_show_archive',
                __( 'Show brand on brand archive pages', 'woocommerce-brand-taxonomy' ),
                array( $this, 'render_toggle_field' ),
                'wbt_brand_settings',
                'wbt_brand_display_section',
                array(
                    'label_for' => 'wbt_show_archive',
                    'option'    => 'show_archive',
                )
            );

            add_settings_field(
                'wbt_enable_list',
                __( 'Enable [brand_list] shortcode', 'woocommerce-brand-taxonomy' ),
                array( $this, 'render_toggle_field' ),
                'wbt_brand_settings',
                'wbt_brand_display_section',
                array(
                    'label_for' => 'wbt_enable_list',
                    'option'    => 'enable_list',
                )
            );
        }

        /**
         * Sanitize options.
         *
         * @param array $options Options.
         *
         * @return array
         */
        public function sanitize_options( $options ) {
            $defaults = self::get_defaults();
            $options  = is_array( $options ) ? $options : array();

            foreach ( $defaults as $key => $default ) {
                $options[ $key ] = isset( $options[ $key ] ) ? (int) (bool) $options[ $key ] : $default;
            }

            return $options;
        }

        /**
         * Render toggle field.
         *
         * @param array $args Field args.
         */
        public function render_toggle_field( $args ) {
            $options = get_option( self::OPTION_KEY, self::get_defaults() );
            $value   = ! empty( $options[ $args['option'] ] );
            ?>
            <label>
                <input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( self::OPTION_KEY . '[' . $args['option'] . ']' ); ?>" value="1" <?php checked( $value ); ?> />
            </label>
            <?php
        }

        /**
         * Render settings page.
         */
        public function render_settings_page() {
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Brand Settings', 'woocommerce-brand-taxonomy' ); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'wbt_brand_settings' );
                    do_settings_sections( 'wbt_brand_settings' );
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Retrieve option value.
         *
         * @param string $key Option key.
         *
         * @return bool
         */
        public static function get_option( $key ) {
            $options  = get_option( self::OPTION_KEY, self::get_defaults() );
            $defaults = self::get_defaults();

            if ( ! isset( $options[ $key ] ) ) {
                return ! empty( $defaults[ $key ] );
            }

            return (bool) $options[ $key ];
        }
    }
}
