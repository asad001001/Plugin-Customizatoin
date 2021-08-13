
add_action('admin_menu', 'promo_code');
function promo_code(){
    add_menu_page('Admin Ticket Details ', 'Admin Ticket Details', 'manage_options', 'admin-ticket-details', 'user_methods_by_asad');
    add_submenu_page('admin-ticket-details', 'News Letters', 'News Letters', 'manage_options', 'admin-news-letters', 'user_methods_by_asad_2',1);
}

function user_methods_by_asad_2(){

	global $wpdb;	
	
	
	echo "<div style='padding:4% 0%'>";
	echo "<h1><b>News Letter </b></h1></div>";
	

// 	print("SELECT * FROM tickets order by id desc");
// 	
// 	
// 	print("SELECT * FROM tickets order by id desc");
// 		$q = wc_get_orders(array(
// 			'limit'=>-1,
// 			'type'=> 'shop_order',
//     		'status'=> array( 'wc-completed' )
// 			)
// 		);
	
	$results = $wpdb->get_results("SELECT * FROM tickets order by id desc");  

	if(!empty($results)){    
		
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" type="text/css">';
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" type="text/css">';
	echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
	echo '<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>';
		?>
	<script>$(document).ready(function() { 
	
	 var t = $('#example').DataTable({
					 dom: 'Bfrtip',	
        			 buttons: [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdfHtml5'],
					        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""> Please Select </option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
			
			});
			
			t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
			
			});
			
			</script>
<?php
    echo '<table id="example" class="display" style="width:100%">'; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
    echo "<thead>";      
    echo "<tr>";
    echo "<th>ID</th>"; 
	echo "<th>Email</th>"; 
	echo "<th>User Name</th>";  
    echo "<th>User Phone</th>";   
    echo "</tr>";  
				
    echo "</thead>"; 
	echo "<tfoot>";
	echo "<tr>";
	echo "<th>Email";
	echo "</th>";
	echo "<th>User Name";
	echo "</th>";
	echo "<th>User Phone";
	echo "</th>";
	echo "<th>User Phone";
	echo "</th>";
	echo "</tr>";
	echo "</tfoot>";
    echo "<tbody>";      
   $id =1;
		
		
		  foreach($results as $row){ 
		echo "<tr>";
		echo "<td align='center'>".$id."</td>";
		echo "<td align='center'>".$row->useremail."</td>";
		echo "<td align='center'>".$row->username."</td>";
		echo "<td align='center'>".$row->userphone."</td>";
		echo "</tr>";
			  $id++;
	}
		
    echo "</tbody>";
    echo "</table>"; 

	}
}
add_action( 'wp_ajax_ticket_delete','ticket_delete' );
function ticket_delete (){
	
	global $wpdb;
	$number = $_POST['numbers'];
	foreach($number as $num){
	    
        $wpdb->delete( 'tickets', array( 'ticketnumber' => $num ) );
	    
	}

}


function user_methods_by_asad(){

	global $wpdb;	
	
	
	echo "<div style='padding:4% 0%'>";
	echo "<h1><b>Ticket Information </b></h1></div>";
	

// 	print("SELECT * FROM tickets order by id desc");
// 	
// 	
// 	print("SELECT * FROM tickets order by id desc");
// 		$q = wc_get_orders(array(
// 			'limit'=>-1,
// 			'type'=> 'shop_order',
//     		'status'=> array( 'wc-completed' )
// 			)
// 		);
	
	// $results = $wpdb->get_results("SELECT * FROM tickets WHERE DATEDIFF(expire_date, date( now())) > -2 order by id desc");  

	$results = $wpdb->get_results("SELECT * FROM tickets order by id desc");  
	
	if(!empty($results)){    
		
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" type="text/css">';
	echo '<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" type="text/css">';
	echo '<script src="https://code.jquery.com/jquery-3.5.1.js"></script>';
	echo '<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>';
	echo '<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>';
		?>
	<script>$(document).ready(function() { 
	
	 var t = $('#example').DataTable({
					 dom: 'Bfrtip',	
        			 buttons: [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdfHtml5'],
					        initComplete: function () {
								var i=1;
								
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select class="for_clear_'+i+'"  ><option value=""> Please Select </option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column.search( val ? '^'+val+'$' : '', true, false ).draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option> ' )
                } );
				i++;
            } );
        }
			
			});
			
			t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
		
	
		
			} ).draw();
			
			
			jQuery(document).find('.for_clear_2').after('<button class="clear_btn"> clear </button>');		
			jQuery(document).on('click','.clear_btn',function(){
			    
			    if(confirm("Do You Realy Want To Delete")){
				
				var numbers = [];
				jQuery('table#example tbody tr').each(function(){

					numbers.push(jQuery(this).find('td.ticket_number').text());
		
					

				});
						var data = {
                    		'action': 'ticket_delete',
                       		'numbers': numbers };

                    	jQuery.ajax({
                    		type : "post",
                    		url : ajaxurl,
                    		data : data,
                    		success: function(response) {
                    			console.log(response);
                    			location.reload();
            		}});

			   }else{
			       
			       
			   }

				});
		
			
	
			});
			
			</script>
<?php
    echo '<table id="example" class="display" style="width:100%">'; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
    echo "<thead>";      
    echo "<tr>";
    echo "<th>ID</th>"; 
	echo "<th>Competition Name</th>"; 
    echo "<th>Ticket Number</th>";   
	echo "<th>User Name</th>";  
    echo "</tr>";  
				
    echo "</thead>"; 
	echo "<tfoot>";
	echo "<tr>";
	echo "<th>Competition Name";
	echo "</th>";
	echo "<th>Ticket Number";
	echo "</th>";
	echo "<th>User Name";
	echo "</th>";
	echo "</tr>";
	echo "</tfoot>";
    echo "<tbody>";      
   $id =1;
		
		
		  foreach($results as $row){ 
		echo "<tr>";
		echo "<td align='center'>".$id."</td>";
		echo "<td align='center'>".$row->competion_name."</td>";
		echo "<td align='center' class='ticket_number'>".$row->ticketnumber."</td>";
		echo "<td align='center'>".$row->username."</td>";
		echo "</tr>";
			  $id++;
	}
		
		

    echo "</tbody>";
    echo "</table>"; 

	}
}
