<?php
/**
 * Product filter query (category, brand, price, sort)
 *
 * Builds a WP_Query for products from AJAX filter values: search, categories, brands, price range and sorting.
 *
 * Usage: paste into functions.php; call via AJAX action `product_filter`.
 */
add_action( 'wp_ajax_product_filter', 'product_filter_ajax' );
add_action( 'wp_ajax_nopriv_product_filter', 'product_filter_ajax' );

/**
 * Split a "a|b|c" filter string into a clean array of slugs.
 */
function product_filter_split( $key ) {
	$raw = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';

	return array_values( array_filter( explode( '|', $raw ), 'strlen' ) );
}

function product_filter_ajax() {
$pscat    = product_filter_split( 'pscat' );
$psbrand  = product_filter_split( 'psbrand' );
$ps       = sanitize_text_field( wp_unslash( $_POST['ps'] ?? '' ) );
$psprice  = sanitize_text_field( wp_unslash( $_POST['psprice'] ?? '' ) );
$sorting  = sanitize_key( $_POST['pssorting'] ?? '' );
$minprice = isset( $_POST['minprice'] ) && '' !== $_POST['minprice'] ? (float) $_POST['minprice'] : '';
$maxprice = isset( $_POST['maxprice'] ) && '' !== $_POST['maxprice'] ? (float) $_POST['maxprice'] : '';
$datacat   = $pscat;
$databrand = $psbrand;

   //  Asad Code 
	          $args = array(
                            'post_type' => 'product','post_status' => array( 'publish' ), 'posts_per_page' => -1,
                        );
                    $args['tax_query'] = array( 'relation'=> 'AND' );
                    $args['meta_query'] = array( 'relation'=> 'AND' );
                      
                      
            if($sorting == ''){
	            
                        $args['meta_key'] = '_wc_average_rating';
                        $args['orderby'] = 'meta_value_num';
                         $args['order'] = 'desc';
            }
                      
                      
                if(!empty($ps)){
                    
                    $args['s'] = $ps;
            
                }
                
	        
	        
	        if($datacat[0] != ''){
	          array_push($args['tax_query'] , array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $datacat,
                        
                    ));	            
	        }

             if($databrand[0] != ''){
            	            
    	          array_push($args['tax_query'] , array(
                            'taxonomy' => 'pa_merk',
                            'field'    => 'slug',
                            'terms'    => $databrand,
                            
                        ));	            
	        }
	        
	       //  pricess
	       
	       if(!empty($minprice) && !empty($maxprice) ){
	           
	         
	           
	           $prs =  array($minprice, $maxprice);
	           
	            array_push($args['meta_query'] , array(
                    'key' => '_price',
                    'value' => $prs,
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC'
                ));
                
                // array_push($args['meta_query']['value'] , array($minprice, $maxprice));
	       
                
	           
	       }
	        
	        
	       // sorting
	           if($sorting == ''){
	            
                        $args['meta_key'] = '_wc_review_count';
                        $args['orderby'] = 'meta_value';
                         $args['order'] = 'desc';
            }
                if($sorting != '' && $sorting == 'rating'){
	            
                        $args['meta_key'] = '_wc_review_count';
                        $args['orderby'] = 'meta_value';
                         $args['order'] = 'desc';
            }
	        if($sorting != '' && $sorting == 'lowest' ){
	            
	             array_push($args['meta_query'] , array(
                            'key' => '_price',
                            'value' => 10,
                            'compare' => '<=',
                        ));	 
	        }
	        
	        if($sorting != '' && $sorting == 'high' ){
	            
	             array_push($args['meta_query'] , array(
                            'key' => '_price',
                            'value' => 20,
                            'compare' => '>=',
                        ));	 
	        }
	   
            if($sorting != '' && $sorting == 'popularity' ){
	            
	            
                        $args['meta_key'] = 'total_sales';
                        $args['orderby'] = 'meta_value_num';
                        $args['order'] = 'ASC';
            }
            
         
            
            if($sorting != '' && $sorting == 'date' ){
	            
	                    $args['order'] = 'desc';
                        // $args['orderby'] = 'date';
            }
	        
            

if(!empty($ps) || !empty($minprice)  || !empty($maxprice) || !empty($pscat)  || !empty($psbrand) ){
    
$index_query = new WP_Query($args);

$prices = array();

    echo '<div class="row"><div class="col-12 col-sm-12 results_number"><b>Total Results: </b><span>'.count($index_query->posts).'</span></div>';


	 while ($index_query->have_posts()) : $index_query->the_post();
	        $product = wc_get_product( get_the_ID() );
	        
	    array_push($prices,$product->get_price());
	 
	 
    	  echo '<div class="col-12 col-sm-6 col-md-3 col-gl-3"><div class="searchbox_pro">';
            wc_get_template_part( 'content', 'product-list' );
    	   echo '</div></div>';
    	endwhile; wp_reset_postdata(); 
    
    	$min = $prices ? min( $prices ) : 0;
        $max = $prices ? max( $prices ) : 0;

        echo '<input type="hidden" class="max_p" value="' . esc_attr( $max ) . '"><input type="hidden" class="min_p" value="' . esc_attr( $min ) . '">';
   echo '</div>';
}

wp_die();
}
