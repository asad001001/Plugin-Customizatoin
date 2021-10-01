
function product_searchcategory() {
    $datacat = array_filter(explode('|',$_POST['pscat']), function($value) { return !is_null($value) && $value !== ''; });
$databrand = array_filter(explode('|',$_POST['psbrand']), function($value) { return !is_null($value) && $value !== ''; });
$ps = $_POST['ps'];

    if(empty($_POST['pscat'])){
        
        $categories = get_terms( ['taxonomy' => 'pa_merk', 'hide_empty' => true] );
            foreach( $categories as $term ) { 
            
                echo '<label><input type="checkbox" name="sorting" value="'.$term->slug.'"  '.((in_array($term->slug,$databrand))?"checked": '').' class="psbrand-check"    data-tag="'.$term->name.' ('.$term->count.')" >'.$term->name.'<span class="count-category-num">('.$term->count.')</span></label>';
            }
                        
        
    }else{
        
    


$query_args = array(
    'status'    => 'publish',
    'limit'     => -1,
    'category'  => $datacat,
);


$data = array();
foreach( wc_get_products($query_args) as $product ){
    foreach( $product->get_attributes() as $taxonomy => $attribute ){
        $attribute_name = wc_attribute_label( $taxonomy ); 
        foreach ( $attribute->get_terms() as $term ){
            $data[$taxonomy][$term->slug] = $term->name;
        }
    }
}




$attribute_select = $data['pa_merk'];

foreach ($attribute_select as $val => $result) {

               $query = new WP_Query( array(
                    'post_type' => 'product','post_status' => array( 'publish' ),
                    's'=>$ps,
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => $datacat,
                        ),
                        array(
                            'taxonomy' => 'pa_merk',
                            'field'    => 'slug',
                            'terms'    => $val,
                        )
                      
                    ),
                
                ));
            
            $posts = $query->posts;

           
           echo '<label><input type="checkbox" name="sorting" '.((in_array($val,$databrand))?"checked": '').' value="'.$val.'" class="psbrand-check" data-tag="'.$result.' ('.count($posts).')">'.$result.' ('.count($posts).')</label>';
           
    } 

        }
    
die; 
}

add_action('wp_ajax_product_searchcategory', 'product_searchcategory');
add_action('wp_ajax_nopriv_product_searchcategory', 'product_searchcategory');
