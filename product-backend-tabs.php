
/*
 * Tab
 */
add_filter('woocommerce_product_data_tabs', 'ticket_product_settings_tabs' );
function ticket_product_settings_tabs( $tabs ){
 
	$tabs['ticket'] = array(
		'label'    => 'Ticket',
		'target'   => 'ticket_product_data',
		'class'    => array('show_if_veriable'),
		'priority' => 21,
	);
	return $tabs;
 
}
 
/*
 * Tab content
 */
add_action( 'woocommerce_product_data_panels', 'ticket_product_panels' );
function ticket_product_panels(){
 
	echo '<div id="ticket_product_data" class="panel woocommerce_options_panel hidden">';
 
		woocommerce_wp_text_input(
        array(
            'id' => '_ticket_end_date',
            'placeholder' => 'Ticket End Date',
			'value'       => get_post_meta( get_the_ID(), '_ticket_end_date', true ),
            'label' => __('Ticket End Date', 'woocommerce'),
            'type' => 'datetime-local',
        )
    );
    //Custom Product Number Field
    woocommerce_wp_text_input(
        array(
            'id' => '_total_tickets',
            'placeholder' => 'Total Tickets',
			'value'       => get_post_meta( get_the_ID(), '_total_tickets', true ),
            'label' => __('Total Tockets', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '1'
            )
        )
    );
woocommerce_wp_text_input(
        array(
            'id' => '_question_tickets',
            'placeholder' => 'Question',
			'value'       => get_post_meta( get_the_ID(), '_question_tickets', true ),
            'label' => __('Question ', 'woocommerce'),
            'type' => 'text',
        )
    );
	woocommerce_wp_text_input(
        array(
            'id' => '_point_tickets',
            'placeholder' => 'Point Text',
			'value'       => get_post_meta( get_the_ID(), '_point_tickets', true ),
            'label' => __('Point Text ', 'woocommerce'),
            'type' => 'text',
        )
    );
	
		woocommerce_wp_text_input(
        array(
            'id' => '_competion_date',
            'placeholder' => 'Live Draw Date',
			'value'       => get_post_meta( get_the_ID(), '_competion_date', true ),
            'label' => __('Live Draw Date', 'woocommerce'),
            'type' => 'datetime-local',
        )
    );
	
 
	echo '</div>';
 
}




/**
 * Save the custom fields.
 */
function save_ticketdetails_option_fields( $post_id ) {
	

	
	if ( isset( $_POST['_ticket_end_date'] ) ) :
		update_post_meta( $post_id, '_ticket_end_date',$_POST['_ticket_end_date'] );
	endif;
	
	if ( isset( $_POST['_total_tickets'] ) ) :
		update_post_meta( $post_id, '_total_tickets', $_POST['_total_tickets']);
	endif;
	
	
	if ( isset( $_POST['_question_tickets'] ) ) :
		update_post_meta( $post_id, '_question_tickets', $_POST['_question_tickets'] );
	endif;
	
	
	if ( isset( $_POST['_point_tickets'] ) ) :
		update_post_meta( $post_id, '_point_tickets',$_POST['_point_tickets'] );
	endif;
	
	if ( isset( $_POST['_competion_date'] ) ) :
		update_post_meta( $post_id, '_competion_date',$_POST['_competion_date'] );
	endif;
	


// get_post_meta($post_id,'total_sales')[0]
$rem = intval($_POST['_total_tickets']) - intval(get_post_meta($post_id,'total_sales')[0]);
		
	if( $rem > 0 ){
		$out_of_stock_staus = 'instock';

		// 1. Updating the stock quantity
		update_post_meta($post_id, '_stock', $rem);

		// 2. Updating the stock quantity
		update_post_meta( $post_id, '_stock_status', wc_clean( $out_of_stock_staus ) );

		// 3. Updating post term relationship
		wp_set_post_terms( $post_id, 'instock', 'product_visibility', true );

		// And finally (optionally if needed)
		wc_delete_product_transients( $post_id ); 
		
	}else{
		
			$out_of_stock_staus = 'outofstock';

			// 1. Updating the stock quantity
			update_post_meta($post_id, '_stock', 0);

			// 2. Updating the stock quantity
			update_post_meta( $post_id, '_stock_status', wc_clean( $out_of_stock_staus ) );

			// 3. Updating post term relationship
			wp_set_post_terms( $post_id, 'outofstock', 'product_visibility', true );

			// And finally (optionally if needed)
			wc_delete_product_transients( $post_id ); 

	}
	


}

add_action( 'woocommerce_process_product_meta_simple', 'save_ticketdetails_option_fields'  );
add_action( 'woocommerce_process_product_meta_variable', 'save_ticketdetails_option_fields'  );

