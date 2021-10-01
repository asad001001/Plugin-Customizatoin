function to_put_in_cart(){
    
global $woocommerce;    
    
    $dd='';
    $with = ''; 
$ids = array_filter(explode('|',$_POST['id']), function($value) { return !is_null($value) && $value !== ''; });
    
foreach($ids as $id){
    
    $with = explode('/',$id);
    
    $product = wc_get_product($with[0]);

    if($product->get_type() != 'variable'){
     
 
     $dd = $woocommerce->cart->add_to_cart($with[0], 1 );
      
    }else{
        
        $variations = $product->get_available_variations();
        
        

        
        $variations_id = wp_list_pluck( $variations, 'variation_id' );
        $variations_name = wp_list_pluck( $variations, 'attributes' );
            //   print_r($variations_id);
            //   print_r($variations_name);
              
            //   print_r($with);
              
            $vkey = 0;
            
            foreach($variations_name as $key => $val){
                foreach($val as $df){
                     if($df == $with[1]){
                        $vkey = $key;
                    }                    
                }

            }
            
   
    
        $dd = $woocommerce->cart->add_to_cart( $id, 1 ,$variations_id[$vkey]);
        
    }
    
    
    
    
}    
    

echo($dd);  
die;
    
}


add_action('wp_ajax_to_put_in_cart', 'to_put_in_cart');
add_action('wp_ajax_nopriv_to_put_in_cart', 'to_put_in_cart');
