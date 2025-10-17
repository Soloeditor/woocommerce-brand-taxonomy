<?php
/**
 * Brand meta fields.
 *
 * @package WooCommerce_Brand_Taxonomy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WBT_Brand_Meta' ) ) {
    /**
     * Handle brand meta fields.
     */
    class WBT_Brand_Meta {

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'product_brand_add_form_fields', array( $this, 'add_fields' ) );
            add_action( 'product_brand_edit_form_fields', array( $this, 'edit_fields' ), 10, 2 );
            add_action( 'created_product_brand', array( $this, 'save_meta' ) );
            add_action( 'edited_product_brand', array( $this, 'save_meta' ) );
        }

        /**
         * Add fields on create.
         */
        public function add_fields() {
            ?>
            <div class="form-field term-brand-logo-wrap">
                <label for="wbt_brand_logo_id"><?php esc_html_e( 'Brand Logo', 'woocommerce-brand-taxonomy' ); ?></label>
                <div class="wbt-brand-logo-preview"></div>
                <input type="hidden" id="wbt_brand_logo_id" name="wbt_brand_logo_id" value="" />
                <button type="button" class="button wbt-upload-logo"><?php esc_html_e( 'Upload Logo', 'woocommerce-brand-taxonomy' ); ?></button>
                <button type="button" class="button wbt-remove-logo hidden"><?php esc_html_e( 'Remove Logo', 'woocommerce-brand-taxonomy' ); ?></button>
                <p class="description"><?php esc_html_e( 'Upload a logo representing the brand.', 'woocommerce-brand-taxonomy' ); ?></p>
            </div>
            <div class="form-field term-brand-description-wrap">
                <label for="wbt_brand_description"><?php esc_html_e( 'Brand Description', 'woocommerce-brand-taxonomy' ); ?></label>
                <textarea name="wbt_brand_description" id="wbt_brand_description" rows="5"></textarea>
            </div>
            <?php
        }

        /**
         * Edit fields.
         *
         * @param WP_Term $term Term.
         */
        public function edit_fields( $term ) {
            $logo_id     = (int) get_term_meta( $term->term_id, 'wbt_brand_logo_id', true );
            $description = get_term_meta( $term->term_id, 'wbt_brand_description', true );
            $logo_url    = $logo_id ? wp_get_attachment_thumb_url( $logo_id ) : '';
            ?>
            <tr class="form-field term-brand-logo-wrap">
                <th scope="row"><label for="wbt_brand_logo_id"><?php esc_html_e( 'Brand Logo', 'woocommerce-brand-taxonomy' ); ?></label></th>
                <td>
                    <div class="wbt-brand-logo-preview"><?php if ( $logo_url ) : ?><img src="<?php echo esc_url( $logo_url ); ?>" alt="" /><?php endif; ?></div>
                    <input type="hidden" id="wbt_brand_logo_id" name="wbt_brand_logo_id" value="<?php echo esc_attr( $logo_id ); ?>" />
                    <button type="button" class="button wbt-upload-logo"><?php esc_html_e( 'Upload Logo', 'woocommerce-brand-taxonomy' ); ?></button>
                    <button type="button" class="button wbt-remove-logo <?php echo $logo_id ? '' : 'hidden'; ?>"><?php esc_html_e( 'Remove Logo', 'woocommerce-brand-taxonomy' ); ?></button>
                    <p class="description"><?php esc_html_e( 'Upload a logo representing the brand.', 'woocommerce-brand-taxonomy' ); ?></p>
                </td>
            </tr>
            <tr class="form-field term-brand-description-wrap">
                <th scope="row"><label for="wbt_brand_description"><?php esc_html_e( 'Brand Description', 'woocommerce-brand-taxonomy' ); ?></label></th>
                <td>
                    <?php
                    wp_editor(
                        wp_kses_post( $description ),
                        'wbt_brand_description',
                        array(
                            'textarea_name' => 'wbt_brand_description',
                            'textarea_rows' => 5,
                        )
                    );
                    ?>
                </td>
            </tr>
            <?php
        }

        /**
         * Save meta fields.
         *
         * @param int $term_id Term ID.
         */
        public function save_meta( $term_id ) {
            if ( isset( $_POST['wbt_brand_logo_id'] ) ) {
                update_term_meta( $term_id, 'wbt_brand_logo_id', absint( $_POST['wbt_brand_logo_id'] ) );
            }

            if ( isset( $_POST['wbt_brand_description'] ) ) {
                update_term_meta( $term_id, 'wbt_brand_description', wp_kses_post( wp_unslash( $_POST['wbt_brand_description'] ) ) );
            }
        }
    }
}
