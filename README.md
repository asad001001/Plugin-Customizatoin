# WordPress & WooCommerce Snippets

Reusable code I've written for client WordPress / WooCommerce projects — custom product options, AJAX filters, admin pages, REST endpoints, reports and more. Each file is self-contained, starts with a short description, and passes `php -l`.

## WooCommerce

| File | What it does |
|---|---|
| [single-product-hooks.php](woocommerce/single-product-hooks.php) | Ticket progress bar, custom add-to-cart text, qty +/- buttons, countdown |
| [product-addon-checkbox.php](woocommerce/product-addon-checkbox.php) | Paid add-on checkbox carried through cart, checkout and order |
| [admin-product-data-tab.php](woocommerce/admin-product-data-tab.php) | Custom "Ticket" tab with fields on the product edit screen |
| [my-account-tickets-and-qty-limits.php](woocommerce/my-account-tickets-and-qty-limits.php) | "Tickets" My Account endpoint + per-product min/max quantity |
| [ajax-bulk-add-to-cart.php](woocommerce/ajax-bulk-add-to-cart.php) | Add several products/variations to the cart in one AJAX call |
| [product-filter-query.php](woocommerce/product-filter-query.php) | AJAX product filter: search, categories, brands, price, sorting |
| [filtered-term-product-counts.php](woocommerce/filtered-term-product-counts.php) | Product counts per brand for the active filters |
| [ajax-filter-brands-by-category.php](woocommerce/ajax-filter-brands-by-category.php) | Show only brands available in the selected categories |
| [category-tree-checkboxes.php](woocommerce/category-tree-checkboxes.php) | Parent/child category checkbox tree for a sidebar |
| [shop-load-more-button.php](woocommerce/shop-load-more-button.php) | "Load more" button for the shop page |

## WordPress

| File | What it does |
|---|---|
| [rest-api-product-endpoints.php](wordpress/rest-api-product-endpoints.php) | Custom REST routes with seeded random ordering and term counts |
| [ajax-multi-taxonomy-filter.php](wordpress/ajax-multi-taxonomy-filter.php) | Shortcode + AJAX filter across several taxonomies |
| [ajax-file-upload.php](wordpress/ajax-file-upload.php) | Drag-and-drop AJAX upload into the Media Library |
| [admin-menu-pages.php](wordpress/admin-menu-pages.php) | Custom admin pages with DataTables and AJAX delete |
| [excel-reports-phpexcel.php](wordpress/excel-reports-phpexcel.php) | Weekly/monthly Excel reports from a template |
| [shortcode-with-pagination.php](wordpress/shortcode-with-pagination.php) | Custom post type listing with pagination in a shortcode |
| [custom-rewrite-rules.php](wordpress/custom-rewrite-rules.php) | Pretty `/category/post/` URLs for a custom post type |

## BuddyPress

| File | What it does |
|---|---|
| [member-match-charts.php](buddypress/member-match-charts.php) | Compare member profile answers and chart the matches |

## JavaScript

| File | What it does |
|---|---|
| [is-in-viewport.js](javascript/is-in-viewport.js) | Detect when an element is fully visible |
| [input-character-limit.js](javascript/input-character-limit.js) | Cap the length of a text input |

## Notes

- Snippets come from real projects, so post types, meta keys and class names are project-specific — rename them for your site.
- User input is sanitised, AJAX actions that change data check a nonce and capability, and uploads require a logged-in user with `upload_files`.
