


// new end 
 
add_action( 'rest_api_init', function () {
  register_rest_route( 'custom-woocommerce/v1', '/dopa/(?P<slug>\S+)', array(
    'methods' => 'GET',
    'callback' => function($data) {

      $page = (isset($_GET['page']) && $_GET['page']) ? $_GET['page'] : 1;
      $seed = (isset($_GET['seed']) && $_GET['seed']) ? $_GET['seed'] : rand(11111,99999);

      $args = [
        'post_type' => 'product',
        'posts_per_page' => 12,
        'orderby' => 'RAND(' . $seed . ')',
        'tax_query' => ['relation' => 'AND'],
        'page' => $page
      ];

      $response = [
        'seed' => $seed
      ];

      /**
       * Search product attributes
       */

      $public_wc_taxonomies = array_values(array_map(function($taxonomy) {
        return 'pa_' . $taxonomy->attribute_name;
      }, array_filter(wc_get_attribute_taxonomies(), function($taxonomy) {
        return $taxonomy->attribute_public;
      })));

      $taxonomies = [];

       foreach($_GET as $getParameter => $attribute_slugs) {

          if(substr( $getParameter, 0, 7 ) === "filter_") {
            if(!isset($attribute_tax_query))
              $attribute_tax_query = ['relation' => 'OR'];


            $attribute_slugs = explode(',', $attribute_slugs);

            $taxonomy = str_replace('filter_', 'pa_', $getParameter);

            foreach($attribute_slugs as $attribute_slug) {
              $taxonomies[$taxonomy][] = get_term_by('slug', $attribute_slug, $taxonomy);

              $attribute_tax_query[] = [
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $attribute_slug
              ];
            }
          }
       }

       if(isset($attribute_tax_query))
        $args['tax_query'][] = $attribute_tax_query;

      /**
       * Search categories
       */

        if(isset($_GET['categories'])) {
          $cats = explode(',', $_GET['categories']);
          $categories = [];

         $category_tax_query = ['relation' => 'OR'];


          foreach($cats as $category_slug) {
            $category = get_term_by('slug', $category_slug, 'product_cat');

            if(!$category)
              continue; // we couldn't find a category with that slug

            $categories[] = [
              'name' => $category->name,
              'slug' => $category->slug,
              'count' => $category->count,
            ];

            $category_tax_query[] = [
              'taxonomy' => 'product_cat',
              'field' => 'slug',
              'terms' => $category_slug
            ];
          }

          $args['tax_query'][] = $category_tax_query;

          $response['categories'] = $categories;
        }


      $query = new WP_Query($args);


      $response['products'] = $query->posts;

      /**
       * If this is the first page, output attributes related to this search
       */

        if($args['page'] == 1) {
          global $wp_the_query;
          $wp_the_query = $query; // little hack to get interals of WooCommerce to function properly
          (wc())->query->pre_get_posts($wp_the_query);

          foreach($public_wc_taxonomies as $taxonomy) {
            $terms = get_terms( $taxonomy, array( 'hide_empty' => '1' ) );

            $response['filters'][$taxonomy] = get_terms_and_count_for_current_query($terms, $taxonomy, (isset($args['tax_query'][0]['relation'])) ? $args['tax_query'][0]['relation'] : 'OR');

            if(!$response['filters'][$taxonomy]) {
              unset($response['filters'][$taxonomy]);
            }
          }
        }

      /**
       * Outut categories if they are not yet requested
       */

      if(!isset($_GET['categories'])) {
        $response['filters']['product_cat'] = get_terms_and_count_for_current_query(get_terms( 'product_cat', array( 'hide_empty' => '1' ) ), 'product_cat', 'or');
      }

      return $response;
    }));
});

/**
 * Code mostly copied from WooCommerce internals, then adjusted.
 * The reason for this is, that their API is private, so you can't access it outside without even nastier hacks
 * So. I did a bit of workarounds to get the right response..
 */

function wc_get_filtered_term_product_counts($term_ids, $taxonomy, $query_type = 'or') {
  global $wpdb;

  $tax_query  = WC_Query::get_main_tax_query();
  $meta_query = WC_Query::get_main_meta_query();

  if ( 'or' === $query_type ) {
    foreach ( $tax_query as $key => $query ) {
      if ( isset($query['taxonomy']) && is_array( $query ) && $taxonomy === $query['taxonomy'] ) {
        unset( $tax_query[ $key ] );
      }
    }
  }

  $meta_query     = new WP_Meta_Query( $meta_query );
  $tax_query      = new WP_Tax_Query( $tax_query );
  $meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
  $tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

  // Generate query.
  $query           = array();
  $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
  $query['from']   = "FROM {$wpdb->posts}";
  $query['join']   = "
    INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
    INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
    INNER JOIN {$wpdb->terms} AS terms USING( term_id )
    " . $tax_query_sql['join'] . $meta_query_sql['join'];

  $query['where'] = "
    WHERE {$wpdb->posts}.post_type IN ( 'product' )
    AND {$wpdb->posts}.post_status = 'publish'"
    . $tax_query_sql['where'] . $meta_query_sql['where'] .
    'AND terms.term_id IN (' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';

  $search = WC_Query::get_main_search_query_sql();
  if ( $search ) {
    $query['where'] .= ' AND ' . $search;
  }

  $query['group_by'] = 'GROUP BY terms.term_id';
  $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
  $query             = implode( ' ', $query );

  // We have a query - let's see if cached results of this query already exist.
  $query_hash = md5( $query );

  // Maybe store a transient of the count values.
  $cache = apply_filters( 'woocommerce_layered_nav_count_maybe_cache', true );
  if ( true === $cache ) {
    $cached_counts = (array) get_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ) );
  } else {
    $cached_counts = array();
  }

  if ( ! isset( $cached_counts[ $query_hash ] ) ) {
    $results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
    $counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
    $cached_counts[ $query_hash ] = $counts;
    if ( true === $cache ) {
      set_transient( 'wc_layered_nav_counts_' . sanitize_title( $taxonomy ), $cached_counts, DAY_IN_SECONDS );
    }
  }

  return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
}

function get_terms_and_count_for_current_query($terms, $taxonomy, $query_type = 'or') {

    $term_counts        = wc_get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
    $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
	$found              = false;

    $return_terms = [];

		foreach ( $terms as $index => $term ) {
			$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
      $option_is_set  = in_array( $term->slug, $current_values, true );
			$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

      if ( 0 < $count ) {
				$found = true;
			} elseif ( 0 === $count && ! $option_is_set ) {
				continue;
			}

			$return_terms[] = [
				'name' => html_entity_decode ($term->name),
				'slug' => $term->slug,
				'taxonomy' => $term->taxonomy,
				'count' => $count,
				'is_set' => $option_is_set
			];
		}

		return $return_terms;
}
