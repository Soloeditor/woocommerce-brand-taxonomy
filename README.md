# WooCommerce Brand Taxonomy

A WordPress plugin that introduces a WooCommerce-aware "Brand" taxonomy with Elementor-ready dynamic tags and widgets.

## Features

- Hierarchical `product_brand` taxonomy for WooCommerce products.
- Term meta for brand logo and rich description fields.
- Brand display settings under **Products → Brand Settings** with toggles for single product output, archive headers, and the `[brand_list]` shortcode.
- Automatic frontend rendering of brand logo on product pages and brand archive headers.
- Shortcodes: `[brand_logo]`, `[brand_name]`, and `[brand_list]` (with column, order, and orderby arguments).
- Elementor dynamic tags for brand name and logo URL.
- Elementor widgets for brand logo, brand name, and a configurable brand grid.
- Helper utilities and responsive frontend styles for brand grids.

## Requirements

- WordPress 6.5+
- WooCommerce 9.x
- Elementor 3.15+
- PHP 7.4 or higher

## Development

1. Install the plugin into a WordPress environment with WooCommerce and Elementor active.
2. Run `npm` or build steps if needed (none required).
3. Adjust styles in `assets/frontend.css` or extend JavaScript in `assets/admin.js`.

## Testing

Use `php -l` to lint files during development:

```bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l
```
