//  Adding element to product detail page
add_action( 'woocommerce_before_add_to_cart_button','progress_bar' );
function progress_bar($product){
    global $post;
	$product_post = wc_get_product($post->ID);
echo '<div class="w3-light-grey" style="margin-bottom: 23px;margin-top: -22px;"><p><span>'.($product_post->get_meta( '_total_tickets') - $product_post->get_stock_quantity()).'</span>/<span> '.$product_post->get_meta( '_total_tickets') .'</span></p><div class="w3-grey" style="height:24px;width:'.((($product_post->get_meta( '_total_tickets') - $product_post->get_stock_quantity())/$product_post->get_meta( '_total_tickets'))*100).'%"></div></div>';
	
}

//change add to cart button text
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
    return __( 'Enter Now', 'woocommerce' ); 
}


<!-- plus mins button to prodduct single page -->



// add pluse minus buttons 
add_action( 'woocommerce_after_add_to_cart_quantity', 'ts_quantity_plus_sign' );

function ts_quantity_plus_sign() {
echo '<button type="button" class="plus" >+</button>';
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'ts_quantity_minus_sign' );

function ts_quantity_minus_sign() {
echo '<button type="button" class="minus" >-</button>';
}
add_action( 'wp_footer', 'ts_quantity_plus_minus' );

function ts_quantity_plus_minus() {
// To run this on the single product page
if ( ! is_product() ) return;
?>
<script type="text/javascript">

jQuery(document).ready(function($){


	if(parseFloat($( 'form.cart' ).find( '.qty' ).val()) == 1 ){

		$(document).find('button.plus, button.minus').remove();
				$('button.single_add_to_cart_button.button.alt').before('<span class="last_ticket" >There is only ONE ticket remaining</span>');
		$('button.single_add_to_cart_button.button.alt').css({'width':'98%'});

	   }
	

	
$('form.cart').on( 'click', 'button.plus, button.minus', function() {
	

// Get current quantity values
var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
var val = parseFloat(qty.val());
var max = parseFloat(qty.attr( 'max' ));
var min = parseFloat(qty.attr( 'min' ));
var step = parseFloat(qty.attr( 'step' ));

// Change the value if plus or minus
if ( $( this ).is( '.plus' ) ) {
// 	if((parseFloat($(".w3-light-grey p span:last-child").text())) >= (val+parseFloat($(".w3-light-grey p span:first-child").text())) ){
		if ( max && ( max <= val ) ) {
				qty.val( max );
			} else {
				qty.val( val + step );
			}
// 	   }else{
// alert("Limit exceed. Remaining Tickets: ("+(parseFloat($(".w3-light-grey p span:last-child").text())- parseFloat($(".w3-light-grey p span:first-child").text()) )+")" );
// }
	
} else {
	if ( min && ( min >= val ) ) {
		
		qty.val( min );
	
	} else if ( val > 1 ) {
	
		qty.val( val - step );
	}
}

});

});

</script>
<?php
}



// countdown block

add_action( 'woocommerce_after_add_to_cart_form','countdown_block' );
function countdown_block($product){
        global $post;
	$product_post = wc_get_product($post->ID);
    
echo '<script src="'.get_stylesheet_directory_uri().'/js/jquery.countdown.min.js"></script>';
echo '<div id="clock"></div>';
//date date('d/m/y ',strtotime($product_post->get_meta( '_ticket_end_date')));
$comp_date =  strval(date('Y/m/d H:i',strtotime($product_post->get_meta( '_ticket_end_date'))));
//2021/07/07 01:34:56date('Y-m-d h:i',strtotime($product->get_meta( '_ticket_end_date')))
?>
<script>
var st =  new Date(<?php echo strtotime($product_post->get_meta( '_ticket_end_date'));?>);
	jQuery('#clock').countdown('<?php echo $comp_date; ?>:00', function(event) {
  var $this = jQuery(this).html(event.strftime(''
    + '<ul><li><span>%w</span> weeks </li>'
    + '<li><span>%d</span> days </li>'
    + '<li><span>%H</span> hr </li>'
    + '<li><span>%M</span> min </li>'
    + '<li><span>%S</span> sec</li></ul>'));
});
</script>
	<?php
}



add_action( 'woocommerce_email_order_details', 'order_complete_email',10,4);
function order_complete_email( $order, $sent_to_admin, $plain_text, $email ) {
   if ( $email->id == 'customer_completed_order' ) {
			$html = ' ';
$html .='<h2 class="email-upsell-title">Tickets Information</h2><p class="email-upsell-p">Thank you for making this purchase! You can find your ticket numbers:</p>';
	   $html .= "<table style='width: 100%; margin: 0 0 26px 0;'><thead style='box-shadow: inset 0 0 9px 0 #c1c1c1;'><tr><th style='    border: 1px solid #b5b5b5;'>Ticket Name</th><th style='    border: 1px solid #b5b5b5;'>Ticket Numbers</th></tr><thead><tbody>";
	   $products = $order->get_items();
	   $inc = 1;
	   $order_id = $order->get_id();
	   
		foreach ($products as $item) 
				{
			$html .= "<tr><td style='    border: 1px solid #b5b5b5;'>".$item['name']."</td><td style='    border: 1px solid #b5b5b5;'>";
					$product_qty = $item['qty']; // product quantity

					for( $i=1; $i <= $product_qty; $i++){

						 $order_id.$inc;						
							$html .= (($product_qty == $i )? $order_id.$inc : $order_id.$inc."," );
							
						$inc++;					
					}    
						$html .= "</td></tr>";
			}

		$html .= "</tbody></table>";

		$html.=" <p style='padding-top:10px; text-align:center;'>Keep them safe and remember to watch our Live Stream on Facebook to announce the winner of this Competition, Best of luck!
</p><p style='padding-top:10px; text-align:center;'> CompetitionGo Team</p>";

		
   }
	echo $html;
}



// user details to databse 

//  add user information on order completed  
add_action( 'woocommerce_order_status_completed', 'on_order_completed', 10, 1);

function on_order_completed( $order_id ){
global $wpdb;
	
// $tablename = 'tickets'; 
// $main_sql_create = 'CREATE TABLE `tickets` (
//   `ID` int(11) NOT NULL AUTO_INCREMENT,
//   `ticketnumber` varchar(255) NOT NULL,
//   `username` varchar(255) NOT NULL,
//   `useremail` varchar(255) NOT NULL,
//   `userphone` varchar(255) NOT NULL,
//   `createddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
//   `order_id` int(11) NOT NULL,
//   PRIMARY KEY (`ID`)
// ) ENGINE=InnoDB AUTO_INCREMENT=1942 DEFAULT CHARSET=latin1;';    
// maybe_create_table($tablename, $main_sql_create );

  
	$order = wc_get_order( $order_id );
	$products = $order->get_items();
	
	
	$username = $order->get_billing_first_name().' '.$order->get_billing_last_name();
	$email = $order->get_billing_email();
	$phone = $order->get_billing_phone();
	
	$inc = 1;
	
	foreach ($products as $item) 
            {
                $product_id = $item['product_id']; // product id
                $product_qty = $item['qty']; // product quantity
		$productn = wc_get_product( $product_id );



		
		
		
				for( $i=1; $i <= $product_qty; $i++){
						
					$ticket = $order_id.$inc;						
					$wpdb->insert('tickets', array(
						'ticketnumber' => $ticket,
						'competion_name'=> $productn->get_name(),
						'username' => $username,
						'useremail' => $email,
						'userphone' => $phone,
						'order_id' =>$order_id,
						'expire_date' =>get_post_meta($product_id, '_ticket_end_date' )[0],
					));
					$inc++;					
				}    
		}
}






