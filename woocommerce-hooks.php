


 class TickectsComp{

	 
    function __construct(){
    
        
        // Seting tabs
        
          add_action( 'init', array($this,'SetingTabs') );   
          add_filter( 'query_vars', array($this,'SetingTabsVars'), 0 );
          add_filter( 'woocommerce_account_menu_items', array($this,'SetingTabsLink') );
          add_action( 'woocommerce_account_tickets_endpoint', array($this,'SetingTabPage') );
          add_filter( 'woocommerce_add_to_cart_validation', array($this,'wc_qty_add_to_cart_validation'), 1, 5 );
 		  add_filter( 'woocommerce_quantity_input_args', array($this,'wc_qty_input_args'), 10, 2 );
		  add_filter( 'woocommerce_update_cart_validation', array($this,'wc_qty_update_cart_validation'), 10, 4 );
	}
    
	 
	 
	 
function wc_qty_get_cart_qty2( $product_id , $cart_item_key = '' ) {
	global $woocommerce;
	$running_qty = 0; // iniializing quantity to 0

	// search the cart for the product in and calculate quantity.
	foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
		if ( $product_id == $values['product_id'] ) {

			if ( $cart_item_key == $other_cart_item_keys ) {
				continue;
			}

			$running_qty += (int) $values['quantity'];
		}
	}

	return $running_qty;
}

/*
* Validate product quantity when cart is UPDATED
*/

function wc_qty_update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {
	
	$product_max = $this->wc_get_product_max_limit_cart( $values['product_id'] );





	if ( ! empty( $product_max ) ) {
		// min is empty
		if ( false !== $product_max ) {
			$new_max = $product_max;
		} else {
			// neither max is set, so get out
			return $passed;
		}
	}

	$product = wc_get_product( $values['product_id'] );
	$already_in_cart = $this->wc_qty_get_cart_qty2( $values['product_id'], $cart_item_key );
	

	
	if ( isset( $new_max) && ( $already_in_cart + $quantity ) > $new_max ) {
		wc_add_notice( apply_filters( 'wc_qty_error_message', sprintf( __( 'You can add maximum     %1$s  items of this  " %2$s\'s " to %3$s.', 'woocommerce-max-quantity' ),
					$new_max,
					$product->get_name(),
					'<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>'),
				$new_max ),
		'error' );
		$passed = false;
	}



	return $passed;
}


function wc_get_product_max_limit_cart( $product_id ) {
	
	
	
		$product_item = wc_get_product($product_id);
	
		$product_max = $product_item->get_stock_quantity();
	
		$price = $product_item->get_price();
	
	
	
	if (  $price  <= 2.99 && $price >  0.00 ) {
	
		if( $product_max >= 200){
			
			$new_max = 200;
		}else{
			
			$new_max = $product_max;
		}
	
		
			
	}elseif($price >= 3.00 && $price  <= 5.00 ){

			if( $product_max >= 150){
			
				$new_max = 150;
				
			}else{
			
				$new_max = $product_max;

			}
	
	}elseif($price >= 5.00 && $price  <= 7.50){
		
		if( $product_max >= 100){
			
				$new_max = 100;
				
			}else{
			
				$new_max = $product_max;

			}
		
	}elseif($price  >= 7.50){
		
		if( $product_max >= 50){
			
				$new_max = 50;
				
			}else{
			
				$new_max = $product_max;

			}
	}else{
		// neither max is set, so get out
		 $new_max = false;
	}
	
	

	return $new_max;
}

	 
	 /*
* Setting minimum and maximum for quantity input args. 
*/

function wc_qty_input_args( $args, $product ) {
	
	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	
	
	$product_max = $this->wc_get_product_max_limit( $product_id );	

	if ( ! empty( $product_min ) ) {
		// min is empty
		if ( false !== $product_min ) {
			$args['min_value'] = $product_min;
		}
	}

	if ( ! empty( $product_max ) ) {
		// max is empty
		if ( false !== $product_max ) {
			$args['max_value'] = $product_max;
		}
	}

	if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
		$stock = $product->get_stock_quantity();

		$args['max_value'] = min( $stock, $args['max_value'] );	
	}

	return $args;
}


function wc_get_product_max_limit( $product_id ) {
	
		$already_in_cart 	= $this->wc_qty_get_cart_qty( $product_id );
	
		$product_item = wc_get_product($product_id);
	
		$product_max = $product_item->get_stock_quantity();
	
		$price = $product_item->get_price();
	
	
	
	if (  $price  <= 2.99 && $price >  0.00 ) {
	
		if( $product_max >= 200){
			
			$new_max = (200 - $already_in_cart);
		}else{
			
			$new_max = ($product_max - $already_in_cart);
		}
	
		
			
	}elseif($price >= 3.00 && $price  <= 5.00 ){

			if( $product_max >= 150){
			
				$new_max = (150-$already_in_cart);
				
			}else{
			
				$new_max = ($product_max-$already_in_cart);

			}
	
	}elseif($price >= 5.00 && $price  <= 7.50){
		
		if( $product_max >= 100){
			
				$new_max = (100-$already_in_cart);
				
			}else{
			
				$new_max = ($product_max-$already_in_cart);

			}
		
	}elseif($price  >= 7.50){
		
		if( $product_max >= 50){
			
				$new_max = (50-$already_in_cart);
				
			}else{
			
				$new_max = ($product_max-$already_in_cart);

			}
	}else{
		// neither max is set, so get out
		 $limit = false;
	}
	
	

	return $limit;
}

	 
	 
	 
	 
	 
	 

 /*
* Validating the quantity on add to cart action with the quantity of the same product available in the cart. 
*/
function wc_qty_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {

// 	$product_min = 1;
// 	$product_max = 5;
	$product_item = wc_get_product($product_id);
	
// $product_max = round($product_item->get_stock_quantity()/2);
	
	$product_max = $product_item->get_stock_quantity();
	
	
	$price = $product_item->get_price();
	
	
	
	if (  $price  <= 2.99 && $price >  0.00 ) {
	
		if( $product_max >= 200){
			
			$new_max = 200;
		}else{
			
			$new_max = $product_max;
		}
	
		
			
	}elseif($price >= 3.00 && $price  <= 5.00 ){

			if( $product_max >= 150){
			
				$new_max = 150;
				
			}else{
			
				$new_max = $product_max;

			}
	
	}elseif($price >= 5.00 && $price  <= 7.50){
		
		if( $product_max >= 100){
			
				$new_max = 100;
				
			}else{
			
				$new_max = $product_max;

			}
		
	}elseif($price  >= 7.50){
		
		if( $product_max >= 50){
			
				$new_max = 50;
				
			}else{
			
				$new_max = $product_max;

			}
	}else{
		// neither max is set, so get out
			return $passed;
	}
	
	
// 	if ( ! empty( $product_min ) ) {
// 		// min is empty
// 		if ( false !== $product_min ) {
// 			$new_min = $product_min;
// 		} else {
// 			// neither max is set, so get out
// 			return $passed;
// 		}
// 	}

	

	$already_in_cart 	= $this->wc_qty_get_cart_qty( $product_id );
	$product 			= wc_get_product( $product_id );
	$product_title 		= $product->get_title();
	
	if ( !is_null( $new_max ) ) {
		
		if( !empty( $already_in_cart ) ){
			
			if ( ( $already_in_cart + $quantity ) > $new_max ) {
			// oops. too much.
			$passed = false;			

				wc_add_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had', sprintf( __( 'You can add maximum     %1$s  items of this  " %2$s\'s " to %3$s. You already have %4$s.', 'woocommerce-max-quantity' ), 
							$new_max,
							$product_title,
							'<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>',
							$already_in_cart ),
						$new_max,
						$already_in_cart ),
				'error' );

			}
		
			}else{
			
			if (  $quantity > $new_max ) {
			// oops. too much.
			$passed = false;			

			wc_add_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had', sprintf( __( 'You can add maximum     %1$s  items of this  " %2$s\'s " to %3$s. ', 'woocommerce-max-quantity' ), 
						$new_max,
						$product_title,
						'<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>',
						$already_in_cart ),
					$new_max,
					$already_in_cart ),
			'error' );

		}
			
		}
		
		
		
	}

	return $passed;
}


	/*
	* Get the total quantity of the product available in the cart.
	*/ 
	function wc_qty_get_cart_qty( $product_id ) {
		global $woocommerce;
		$running_qty = 0; // iniializing quantity to 0

		// search the cart for the product in and calculate quantity.
		foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
			if ( $product_id == $values['product_id'] ) {				
				$running_qty += (int) $values['quantity'];
			}
		}

		return $running_qty;
	}

    
    
    
     function SetingTabs(){
        add_rewrite_endpoint( 'tickets', EP_ROOT | EP_PAGES );

    }
    
    function SetingTabsVars($vars){
     $vars[] = 'tickets';
    
        return $vars;
        
    } 
    
    function SetingTabsLink($items){
    
            // $user = wp_get_current_user();
    
            // if ( in_array( 'doctor', (array) $user->roles ) ) {
    
                $items['tickets'] = 'Tickets Details';
    
            // }
    
        return $items;
    }
    
    
    function SetingTabPage(){
		
		global $wpdb;
		
        $user = wp_get_current_user();
		
	echo "<div style='padding:4% 0%'>";
	echo "<h1><b>Ticket Information </b></h1></div>";
	
	$q = wc_get_orders(array(
			'limit'=>-1,
			'type'=> 'shop_order',
            'customer_id' => get_current_user_id(),
			'status'=> array( 'wc-completed' )
			)
		);

		
// 		print_r($q);
		
	if(!empty($q)){   
		
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" type="text/css">';
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" type="text/css">';
	echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
	echo '<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>';
	echo "<script>$(document).ready(function() { $('#example').DataTable({
					 dom: 'Bfrtip',	
       });});</script>";
    echo '<table id="example" class="display" style="width:100%">'; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
    echo "<thead>";      
    echo "<tr>";
    echo "<th>Ticket Date</th>";    
    echo "<th>Ticket Info</th>";   
    echo "</tr>";      
    echo "</thead>";      
    echo "<tbody>";   
		$id =1;
		foreach($q as $row){ 
		echo "<tr>";
       echo "<td>";
			$date=date_create($row->get_date_completed());
echo date_format($date,"Y/m/d");
			

		echo "</td>";
		echo "<td>";
			    echo '<table id="example2" class="display" style="width:100%">'; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
    echo "<thead>";      
    echo "<tr>";
    echo "<th>ID</th>";    
    echo "<th>Ticket item</th>";
	echo "<th>Draw Date</th>";
    echo "<th>Ticket Numbers</th>";      
    echo "</tr>";      
    echo "</thead>";      
    echo "<tbody>"; 
			$items = $row->get_items();
			$order_id = $row->ID;
			
			foreach ($items as $item){	

	echo "<tr>";
		echo "<td align='center'>".$id."</td>";

echo "<td align='center'>";
				echo $item->get_name();
				echo "</td>";
				echo "<td align='center'>";
				$dateTicket = get_post_meta( $item['product_id'],'_competion_date');
					
				echo date('d/m/y \@ g:ia', strtotime($dateTicket[0]));
				echo "</td>";


echo "<td align='center'>";
						$product_qty = $item['qty']; // product quantity
				$inc = 1;
				
					for( $i=1; $i <= $product_qty; $i++){
						if(($i) % 4 == 0){
							echo "<br>";
						}
					
							echo (($product_qty == $i )? $order_id.$inc : $order_id.$inc."," );
						
						$inc++;					
					}   
					echo "</td>";
			echo "</tr>";
			$id++;
			}
		
		
		    echo "</tbody>";
    echo "</table>"; 
			echo "</td>";
			echo "<tr>";
		}
		
    echo "</tbody>";
    echo "</table>"; 
	echo "<div style='text-align:center;'>";
		
	echo "<h3 style='margin-top: 30px;font-weight: 500;text-align:center;'>Watch Facebook Live Draw</h3>";
	echo '<a href="https://www.facebook.com/competitiongo" target="_blank" style="background: #e11e26;color: #fff;font-family: Saira; padding:  5px 30px;
">Click Here</a>';
		
		echo "</div>";
	}
}
	
  
}
