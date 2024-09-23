<?php 

// add_shortcode('weekly_report_function','weekly_report_function');

function weekly_report_function(){


/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';

require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

$path_name = dirname(__FILE__)."/jubileeReports/templates/weekly-template.xlsx";

$objPHPExcel = PHPExcel_IOFactory::load($path_name);

$sheet = $objPHPExcel->getActiveSheet();



$args = array(
    'post_type' => 'tribe_events', // Replace 'event' with your custom post type
    'posts_per_page' => -1, // Number of posts to display
    'post_status' => 'publish',
    'tax_query' => array(
        array(
        'taxonomy' => 'tribe_events_cat',
        'field' => 'slug',
        'terms' => array('courses'),
        )
    ),

);
$query = new WP_Query($args);
$total_ticket_sold = 0;
$total_revenue = 0;
$last_date = date("m/d/Y", strtotime("last week ".date('l')));

$mailtr = '';
$title = '';
$per_c_revenue = 0;
$per_c_sold  = 0;

$c_count = 0;

$data_file = [];

	 if ($query->have_posts()) {
	     
               while ($query->have_posts()) {
                    $query->the_post();

                    $title = '';
                    $per_c_revenue = 0;
                    $per_c_sold  = 0;
					 // Loading count of ticket 
                    $attendees = Tribe__Tickets__Tickets::get_attendees_by_args([],get_the_ID());
                        foreach($attendees['attendees'] as $sub_item){
                            
							$past_date = $last_date;
			             	$post_date = date("m/d/Y",strtotime($sub_item['post_date']));
			               
			                 if($past_date < $post_date || true){
			                       
			                     $per_c_revenue += $sub_item['price_paid'];
			                     $per_c_sold  += 1;
			                     
			                    $title = get_the_title();
                             	$total_revenue  += $sub_item['price_paid'];
                            	$total_ticket_sold  += 1;
                            	
			                 
			                 }
			                        
			             }
			             
			             if($title != ''){
			             
					    $mailtr .= '<tr><td>'.$title.'</td><td>'.$per_c_sold.'</td><td>$'.$per_c_revenue.'.00</td></tr>';
			            
			             
			               array_push($data_file,array('title'=>$title,
			                                          'pr_c_sold'=>$per_c_sold,
			                                          'per_c_revenue'=>$per_c_revenue)); 

			                 $c_count +=1; 
			             }
			                 
			 }
			   
	 }




$rows = 7;
$objPHPExcel->getActiveSheet()->insertNewRowBefore(8,(count($data_file)-1)); 

foreach($data_file as $item){
    
    $sheet->getCell('A'.$rows)->setValue($item['title']);
    $sheet->getCell('B'.$rows)->setValue($item['pr_c_sold']);
    $sheet->getCell('C'.$rows)->setValue($item['per_c_revenue']);
    $rows++;
}



$between = date("F j", strtotime("last week ".date('l')))." To ";
$between .= date("F j, Y");

// Format the date
$today = date('l, F j, Y');

$last_date = date("m/d/Y", strtotime("last week tuesday"));


// Set cell value
$sheet->getCell('A3')->setValue($between);
$sheet->getCell('A4')->setValue("Total Number of Courses Offered: ".$c_count);
$sheet->getCell('B4')->setValue("Total Registrations: ".$total_ticket_sold);
$sheet->getCell('C4')->setValue("Total Sales Revenue: $ ".$total_revenue);

$week_month = "Weekly";

	
$to = 'mmoore@jubileemd.org, bkeener@jubileemd.org, hclemmer@jubileemd.org, khatwell@jubileemd.org, mamoon@loebigink.com, brian@loebigink.com, julie@sasseagency.com';
// $to = 'asad.ali@hashehouse.com, mamoon@loebigink.com, mamoonrashid@gmail.com';
$subject = $week_month.' Sales Report JubileeMD';
$message = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>'.$week_month.' Sales Report</title>
    <style>
        .wraping {
            font-family: Poppins, sans-serif;
            background-color: #f2f8fc;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            padding: 0px 0;
            display: table;
            width: 100%;
            vertical-align: middle;
        }
        .header img {
            padding-left: 40px;
            display: table-cell;
            vertical-align: middle;
            width: 170px;
            padding-top: 25px;
        }
        .right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .right h2 {
            margin-bottom: 5px;
       }
       .right h5 {
            margin-top: 0;
            text-align: right;
            color: #0075c9;
        }
        .report-date {
            background-color: #0075c9;
            color: #ffffff;
            text-align: right;
            font-size: 16px;
        }
        .main-title {
    font-size: 20px;
    font-weight: bold;
    text-align: left;
    margin: 26px 0;
    line-height: 1.2;
}
        .overview {
            font-size: 15px;
    color: #000;
    text-align: left;
    margin-bottom: 25px;
        }
        .metrics {
            display: block;
            margin-bottom: 20px;
        }
        .metric-box {
            padding: 1px;
            border-radius: 5px;
            color: #000;
            text-align: left;
          margin-bottom:10px;
        }
        .metric-box.blue {
            background-color: #2777b7;
        }
        .metric-box.green {
            background-color: #2DC8A7;
        }
        .metric-box.orange {
            background-color: #F58B2C;
        }
        .metric-box h3 {
            font-size: 18px;
            margin: 5px 0;
        }
        .metric-box h3 img {
            width: 25px;
            padding-right: 5px;
        }
        .key-insights, .course-sales, .footer {
            margin: 20px 0;
        }
        .course-sales h2 {
            font-size: 20px;
    font-weight: bold;
    margin-bottom: 30px;
    margin-top: 35px;
    line-height: 1.2;
        }
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .sales-table th, .sales-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 12px;
            border-left: 0px;
            border-right: 0px;
        }
        .sales-table th {
            background-color: unset;
            border-color: #0075c9;
            border-width: 1px;
            color: #0075c9;
        }
        table.sales-table td {
            color: #1e2327;
            border-color: #1e2327;
            line-height: 1.5;
        }
        table.sales-table td:nth-child(2){
            text-align: center;
        }
      
        .footer {
               text-align: left;
            font-size: 14px;
            background: #2c70c0;
            padding: 20px 0px;
            color: #fff;
        }
        .footer a {
            color: #fff;
            text-decoration: none;
        }
        .footer .contact-info {
            margin-bottom: 20px;
            display: table;
            align-items: center;
            line-height: 1.6;
        }
        .contact-info div {
            display: table-cell;
            vertical-align: middle;
        }
        .contact-info img {
            display: table-cell;
        }
        .contact-info {
            width: 100%;
        }

    </style>
</head>
<body>
<div class="wraping" >
    <div class="container">
        <div class="header">
            <img src="'.home_url().'/wp-content/uploads/2024/08/Layer-3.png" alt="Jubilee Association of Maryland">
          <div class="right" style>
            <h2>'.$week_month.' Sales Report</h2>
            <h5>'.$between.'</h5>
          </div>
        </div>
    </div>
    <div class="report-date">
        <div class="container">
            Date: '.$today.'
        </div>
    </div>
    <div class="container">
        <div class="main-title">
            Summary of Academy <br>Courses Sales Report
        </div>
        <div class="overview">
            Overview of Sales Performance and Key Metrics
        </div>
        <div class="metrics">
            <div class="metric-box blue1">
                <h3> Total Number of Courses Offered: '.$c_count.'</h3>
            </div>
            <div class="metric-box green1">
                <h3> Total Tickets Sold: '.$total_ticket_sold.'</h3>
            </div>
            <div class="metric-box orange1">
                <h3> Total Sales Revenue: $'.$total_revenue.'</h3>
            </div>
        </div>
        
        <div class="course-sales">
            <h2>Course Sales Report
            <br>Ticket Sold and Sales Amounts</h2>
            <table class="sales-table">
                <tr>
                    <th>Course Name</th>
                    <th>Tickets Sold</th>
                    <th>Sales Amount</th>
                </tr>
               '.$mailtr.'
            </table>
        </div>
        
    </div>
  <div class="footer">
    <div class="container">
            <div class="contact-info">
               <div> 
                    <span>10408 Montgomery Avenue, Kensington, MD 20895</span><br>
                    Phone: <a href="tel:301-949-8628">301-949-8628</a> | Email: <a href="mailto:info@jubileemd.org">info@jubileemd.org</a><br>
                    &copy; 2024 The Jubilee Association of Maryland, Inc.
                </div>
                <img src="'.home_url().'/wp-content/uploads/2024/08/Layer-4.png" alt="Jubilee Logo">
            </div>
          
        </div>
    </div>
    </div>
</body>
</html>';


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$path_name = dirname(__FILE__)."/jubileeReports/".date('d-M-y')."-Report.xlsx";

$objWriter->save($path_name);


$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <info@dev.loebigink.com>' . "\r\n";
$headers .= 'Reply-To: <info@dev.loebigink.com>' . "\r\n";

if(wp_mail($to, $subject, $message, $headers,array($path_name))) {
   
}

}


// monthly report

add_action('monthly_reports','monthly_report_function');

function monthly_report_function(){


/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';

require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

$path_name = dirname(__FILE__)."/jubileeReports/templates/monthly-template.xlsx";

$objPHPExcel = PHPExcel_IOFactory::load($path_name);

$sheet = $objPHPExcel->getActiveSheet();



$args = array(
    'post_type' => 'tribe_events', // Replace 'event' with your custom post type
    'posts_per_page' => -1, // Number of posts to display
    'post_status' => 'publish',
    'tax_query' => array(
        array(
        'taxonomy' => 'tribe_events_cat',
        'field' => 'slug',
        'terms' => array('courses'),
        )
    ),

);
$query = new WP_Query($args);
$total_ticket_sold = 0;
$total_revenue = 0;
$last_date = date("m/d/Y", strtotime("-1 month"));

$mailtr = '';
$title = '';
$per_c_revenue = 0;
$per_c_sold  = 0;

$c_count = 0;

$data_file = [];

	 if ($query->have_posts()) {
	     
               while ($query->have_posts()) {
                    $query->the_post();

                    $title = '';
                    $per_c_revenue = 0;
                    $per_c_sold  = 0;
					 // Loading count of ticket 
                    $attendees = Tribe__Tickets__Tickets::get_attendees_by_args([],get_the_ID());
                        foreach($attendees['attendees'] as $sub_item){
                            
							$past_date = $last_date;
			             	$post_date = date("m/d/Y",strtotime($sub_item['post_date']));
			               
			                 if($past_date < $post_date){
			                       
			                     $per_c_revenue += $sub_item['price_paid'];
			                     $per_c_sold  += 1;
			                     
			                    $title = get_the_title();
                             	$total_revenue  += $sub_item['price_paid'];
                            	$total_ticket_sold  += 1;
                            	
			                 
			                 }
			                        
			             }
			             
			             if($title != ''){
			             
					    $mailtr .= '<tr><td>'.$title.'</td><td>'.$per_c_sold.'</td><td>$'.$per_c_revenue.'.00</td></tr>';
			            
			             
			               array_push($data_file,array('title'=>$title,
			                                          'pr_c_sold'=>$per_c_sold,
			                                          'per_c_revenue'=>$per_c_revenue)); 

			                 $c_count +=1; 
			             }
			                 
			 }
			   
	 }




$rows = 7;
$objPHPExcel->getActiveSheet()->insertNewRowBefore(8,(count($data_file)-1)); 

foreach($data_file as $item){
    
    $sheet->getCell('A'.$rows)->setValue($item['title']);
    $sheet->getCell('B'.$rows)->setValue($item['pr_c_sold']);
    $sheet->getCell('C'.$rows)->setValue($item['per_c_revenue']);
    $rows++;
}



$between = date("F j", strtotime("-1 month"))." To ";
$between .= date("F j, Y");

// Format the date
$today = date('l, F j, Y');

// $last_date = $last_date;


// Set cell value
$sheet->getCell('A3')->setValue($between);
$sheet->getCell('A4')->setValue("Total Number of Courses Offered: ".$c_count);
$sheet->getCell('B4')->setValue("Total Registrations: ".$total_ticket_sold);
$sheet->getCell('C4')->setValue("Total Sales Revenue: $ ".$total_revenue);

$week_month = "Monthly";

	
$to = 'mmoore@jubileemd.org, bkeener@jubileemd.org, hclemmer@jubileemd.org, khatwell@jubileemd.org, mamoon@loebigink.com, brian@loebigink.com, julie@sasseagency.com';
// $to = 'asad.ali@hashehouse.com, mamoon@loebigink.com, mamoonrashid@gmail.com';
$subject = $week_month.' Sales Report JubileeMD';
$message = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>'.$week_month.' Sales Report</title>
    <style>
        .wraping {
            font-family: Poppins, sans-serif;
            background-color: #f2f8fc;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            padding: 0px 0;
            display: table;
            width: 100%;
            vertical-align: middle;
        }
        .header img {
            padding-left: 40px;
            display: table-cell;
            vertical-align: middle;
            width: 170px;
            padding-top: 25px;
        }
        .right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .right h2 {
            margin-bottom: 5px;
       }
       .right h5 {
            margin-top: 0;
            text-align: right;
            color: #0075c9;
        }
        .report-date {
            background-color: #0075c9;
            color: #ffffff;
            text-align: right;
            font-size: 16px;
        }
        .main-title {
    font-size: 20px;
    font-weight: bold;
    text-align: left;
    margin: 26px 0;
    line-height: 1.2;
}
        .overview {
            font-size: 15px;
    color: #000;
    text-align: left;
    margin-bottom: 25px;
        }
        .metrics {
            display: block;
            margin-bottom: 20px;
        }
        .metric-box {
            padding: 1px;
            border-radius: 5px;
            color: #000;
            text-align: left;
          margin-bottom:10px;
        }
        .metric-box.blue {
            background-color: #2777b7;
        }
        .metric-box.green {
            background-color: #2DC8A7;
        }
        .metric-box.orange {
            background-color: #F58B2C;
        }
        .metric-box h3 {
            font-size: 18px;
            margin: 5px 0;
        }
        .metric-box h3 img {
            width: 25px;
            padding-right: 5px;
        }
        .key-insights, .course-sales, .footer {
            margin: 20px 0;
        }
        .course-sales h2 {
            font-size: 20px;
    font-weight: bold;
    margin-bottom: 30px;
    margin-top: 35px;
    line-height: 1.2;
        }
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .sales-table th, .sales-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 12px;
            border-left: 0px;
            border-right: 0px;
        }
        .sales-table th {
            background-color: unset;
            border-color: #0075c9;
            border-width: 1px;
            color: #0075c9;
        }
        table.sales-table td {
            color: #1e2327;
            border-color: #1e2327;
            line-height: 1.5;
        }
        table.sales-table td:nth-child(2){
            text-align: center;
        }
      
        .footer {
               text-align: left;
            font-size: 14px;
            background: #2c70c0;
            padding: 20px 0px;
            color: #fff;
        }
        .footer a {
            color: #fff;
            text-decoration: none;
        }
        .footer .contact-info {
            margin-bottom: 20px;
            display: table;
            align-items: center;
            line-height: 1.6;
        }
        .contact-info div {
            display: table-cell;
            vertical-align: middle;
        }
        .contact-info img {
            display: table-cell;
        }
        .contact-info {
            width: 100%;
        }

    </style>
</head>
<body>
<div class="wraping" >
    <div class="container">
        <div class="header">
            <img src="'.home_url().'/wp-content/uploads/2024/08/Layer-3.png" alt="Jubilee Association of Maryland">
          <div class="right" style>
            <h2>'.$week_month.' Sales Report</h2>
            <h5>'.$between.'</h5>
          </div>
        </div>
    </div>
    <div class="report-date">
        <div class="container">
            Date: '.$today.'
        </div>
    </div>
    <div class="container">
        <div class="main-title">
            Summary of Academy <br>Courses Sales Report
        </div>
        <div class="overview">
            Overview of Sales Performance and Key Metrics
        </div>
        <div class="metrics">
            <div class="metric-box blue1">
                <h3> Total Number of Courses Offered: '.$c_count.'</h3>
            </div>
            <div class="metric-box green1">
                <h3> Total Tickets Sold: '.$total_ticket_sold.'</h3>
            </div>
            <div class="metric-box orange1">
                <h3> Total Sales Revenue: $'.$total_revenue.'</h3>
            </div>
        </div>
        
        <div class="course-sales">
            <h2>Course Sales Report
            <br>Ticket Sold and Sales Amounts</h2>
            <table class="sales-table">
                <tr>
                    <th>Course Name</th>
                    <th>Tickets Sold</th>
                    <th>Sales Amount</th>
                </tr>
               '.$mailtr.'
            </table>
        </div>
        
    </div>
  <div class="footer">
    <div class="container">
            <div class="contact-info">
               <div> 
                    <span>10408 Montgomery Avenue, Kensington, MD 20895</span><br>
                    Phone: <a href="tel:301-949-8628">301-949-8628</a> | Email: <a href="mailto:info@jubileemd.org">info@jubileemd.org</a><br>
                    &copy; 2024 The Jubilee Association of Maryland, Inc.
                </div>
                <img src="'.home_url().'/wp-content/uploads/2024/08/Layer-4.png" alt="Jubilee Logo">
            </div>
          
        </div>
    </div>
    </div>
</body>
</html>';


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$path_name = dirname(__FILE__)."/jubileeReports/".date('d-M-y')."-Monthly-Report.xlsx";

$objWriter->save($path_name);
// echo "saved";


$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <info@dev.loebigink.com>' . "\r\n";
$headers .= 'Reply-To: <info@dev.loebigink.com>' . "\r\n";

if(wp_mail($to, $subject, $message, $headers,array($path_name))) {
   
}

}




add_action('wp_enqueue_scripts', 'dequeue_specific_tribe_events_css', 999);


// shortcode for listing 
add_shortcode('post_listing','post_listing');

function post_listing($attr){
 
$html ='';

$html .=	'<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main">

<div id="event-filters" >
    
    <div class="filter_wraper">
        <input type="text" id="event-date-search" placeholder="Search by Event Date">

        <!-- Search by event name -->
        <input type="text" id="event-name-search" placeholder="Search by Event Name">
    
        <!-- Filter by Category -->
        ';
        
        if($attr['cate'] != ''){
        
        $cat = get_term_by( 'slug', $attr['cate'], 'tribe_events_cat' );

        $categories = get_terms( array(
            'taxonomy' => 'tribe_events_cat',
            'hide_empty' => false,
            'parent'     => $cat->term_id,
        ));
    
            
        }else{
            
            $cat = get_term_by( 'slug', 'courses', 'tribe_events_cat' );

            $categories = get_terms( array(
                'taxonomy' => 'tribe_events_cat',
                'hide_empty' => false,
                'parent'     => $cat->term_id,
            ));
            
        }
     
     if(count($categories) < 1){
        $html .='<select id="event-category-filter">
             <option value="'.$cat->slug.'">'.$cat->name.'</option>';
        $html .='</select>';
                 
     }else{
         
        $html .='<select id="event-category-filter">
                 <option value="">All Categories</option>';
                     foreach ( $categories as $category ) : 
                        $html .= '<option value="'.$category->slug.'">'.$category->name.'</option>';
                     endforeach; 
        $html .='</select>';
        }

$html .=' <button id="event-search-btn">Filter</button>
          <button id="event-clear-btn">Clear</button>
    </div>
</div>';

$html .= '<div id="event-results">';
    
    // Initial WP_Query to load events
    $args = array(
       'post_type'      => 'tribe_events',  // The Events Calendar post type
        'posts_per_page' => -1,              // Limit the number of events displayed
        'meta_key'=> '_EventStartDate',
        'orderby'=> '_EventStartDate',
        'order'=> 'ASC',
        'eventDisplay'=> 'custom', 
        'post_status' => 'publish',
        'tax_query' => array(
                array(
                    'taxonomy' => 'tribe_events_cat',
                    'terms' => $cat->slug,
                    'field' => 'slug',
                    
                )
            ),
    );

    // Create a new WP_Query for events
    $query = new WP_Query( $args );

    // Check if there are events to display
    if ( $query->have_posts() ) : 
        while ( $query->have_posts() ) : $query->the_post();

            // Fetch event data
            $event_id = get_the_ID();
            $event_m = tribe_get_start_date( $event_id, false, 'M' );
            $event_date = tribe_get_start_date( $event_id, false, 'd' );
            $event_day = tribe_get_start_date( $event_id, false, 'l' );
            $event_time = tribe_get_start_time( $event_id );
            $event_end_time = tribe_get_end_time( $event_id );
            $event_location = tribe_get_venue();
            $event_price = tribe_get_cost( $event_id );
            $event_link = tribe_get_event_link( $event_id );

            // Output event data in the layout
            $html .= '<div class="event-item">';
            $html .= '<div class="item_head">';
            $html .= '<div class="event-date">';
            $html .= '<div class="event-month"><span>'.esc_html($event_m).'</span>' . esc_html( $event_date ) . '</div>';
            $html .= '<div class="event-day">' . esc_html( $event_day ) . '</div>';
            $html .= '</div>';
            $html .= '<div class="event-details">';
            $html .= '<div class="event-time"> <span>' .$event_time .'</span> <span>-</span><span>' . $event_end_time . '</span></div>';
            $html .= '<div class="title_area"><div class="event-title">' . get_the_title() . '</div>';
            $html .= '<div class="event-location">' . esc_html( $event_location ) . '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="see_details">';
            $html .= '<button>see details <i class="fa-solid fa-chevron-down"></i></button>';    
            $html .= '</div>';
            $html .= '<div class="event-signup">';
            $html .= '<a href="' . esc_url( $event_link ) . '" class="signup-button">REGISTER <i class="fa-solid fa-arrow-right"></i></a>';
            $html .= '</div>';
            $html .= '</div>';
            
            ob_start(); 
            echo '<div class="item_body">';
             echo '<div class="single_bottom_info bg-light ">';
                    tribe_get_template_part( 'modules/meta' ); 
            
            echo '</div>';
             echo do_shortcode('[tribe_tickets post_id="'.$event_id.'"]'); 
           
           
            echo '</div>';
            
             $var = ob_get_clean();
            $html .= $var;
            
            $html .= '</div>';
          
        endwhile;
        wp_reset_postdata(); // Reset post data after query
    else :
        $html .= '<p>No events found.</p>';
    endif;
   
$html .= '</div>
</main>
</div>';

return $html;
    
}




// options for events new layout ajax search

// Register the AJAX action for logged-in and logged-out users
add_action( 'wp_ajax_filter_events_ajax', 'filter_events_ajax' );
add_action( 'wp_ajax_nopriv_filter_events_ajax', 'filter_events_ajax' );

function filter_events_ajax() {
    // Get the search query and category filter from AJAX
    $event_name = isset( $_POST['event_name'] ) ? sanitize_text_field( $_POST['event_name'] ) : '';
    $event_category = isset( $_POST['event_category'] ) ? sanitize_text_field( $_POST['event_category'] ) : '';

  $args="";
  
if($event_name != ''){
      // Query Arguments
    $args = array(
        'post_type'      => 'tribe_events',  // The Events Calendar post type
        'posts_per_page' => -1,              // Limit the number of events displayed
        'meta_key'=> '_EventStartDate',
        'orderby'=> '_EventStartDate',
        'order'=> 'ASC',
        'eventDisplay'=> 'custom',  
        'post_status' => 'publish',
        's' => $event_name,
    );
    
    if ( ! empty( $event_category ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'tribe_events_cat',
                'field'    => 'slug',
                'terms'    => $event_category,
            ),
        );
    }else{
        
          $args['tax_query'] = array(
            array(
                'taxonomy' => 'tribe_events_cat',
                'field'    => 'slug',
                'terms'    => 'courses',
            ),
        );
        
    }
   
}else if ( !empty( $event_category ) ) {
    $args = array(
        'post_type'      => 'tribe_events',  // The Events Calendar post type
        'posts_per_page' => -1,              // Limit the number of events displayed
        'meta_key'=> '_EventStartDate',
        'orderby'=> '_EventStartDate',
        'order'=> 'ASC',
        'eventDisplay'=> 'custom',  
        'post_status' => 'publish',
    );
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'tribe_events_cat',
                'field'    => 'slug',
                'terms'    => $event_category,
            ),
        );
        
    }else{
        
        $args = array(
        'post_type'      => 'tribe_events',  // The Events Calendar post type
        'posts_per_page' => -1,              // Limit the number of events displayed
        'meta_key'=> '_EventStartDate',
        'orderby'=> '_EventStartDate',
        'order'=> 'ASC',
        'eventDisplay'=> 'custom',  
        'post_status' => 'publish',
        'tax_query' => array(
                array(
                    'taxonomy' => 'tribe_events_cat',
                    'terms' => 'courses',
                    'field' => 'slug',
                    
                )
            ),
    );
    
    
    }

    // Custom WP Query
    $query = new WP_Query( $args );
    $count = 0;
    // Check if there are events
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
             $event_id = get_the_ID();
             
            if($_POST['event_date'] != ''){
                
            $date_array = explode('-',$_POST['event_date']); 
            
            $from_date = date("m/d/Y",strtotime($date_array[0]));
			$to_date = date("m/d/Y",strtotime($date_array[1]));
			
            $event_date_check = tribe_get_start_date( $event_id, false, 'm/d/Y' );

             if( $event_date_check >= $from_date && $event_date_check <= $to_date){
                 
                 $count++;
            // Fetch event data
            $event_id = get_the_ID();
            $event_m = tribe_get_start_date( $event_id, false, 'M' );
            $event_date = tribe_get_start_date( $event_id, false, 'd' );
            $event_day = tribe_get_start_date( $event_id, false, 'l' );
            $event_time = tribe_get_start_time( $event_id );
            $event_end_time = tribe_get_end_time( $event_id );
            $event_location = tribe_get_venue();
            $event_price = tribe_get_cost( $event_id );
            $event_link = tribe_get_event_link( $event_id );

            // Output event data in the layout
            echo '<div class="event-item">';
            echo '<div class="item_head">';
            echo '<div class="event-date">';
            echo '<div class="event-month"><span>'.esc_html($event_m).'</span>' . esc_html( $event_date ) . '</div>';
            echo '<div class="event-day">' . esc_html( $event_day ) . '</div>';
            echo '</div>';
            echo '<div class="event-details">';
            echo '<div class="event-time"> <span>' .$event_time .'</span> <span>-</span><span>' . $event_end_time . '</span></div>';
            echo '<div class="title_area"><div class="event-title">' . get_the_title() . '</div>';
            echo '<div class="event-location">' . esc_html( $event_location ) . '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="see_details">';
            echo '<button>see details <i class="fa-solid fa-chevron-down"></i></button>';    
            echo '</div>';
            echo '<div class="event-signup">';
            echo '<a href="' . esc_url( $event_link ) . '" class="signup-button">REGISTER<i class="fa-solid fa-arrow-right"></i></a>';
            echo '</div>';
            echo '</div>';
            echo '<div class="item_body">';
            
            echo  '<div class="single_bottom_info bg-light ">';
                    do_action( 'tribe_events_single_event_before_the_meta' );
                    tribe_get_template_part( 'modules/meta' ); 
                    do_action( 'tribe_events_single_event_after_the_meta' ); 
            echo    '</div>';
                echo do_shortcode('[tribe_tickets post_id="'.$event_id.'"]'); 
            
            echo '</div>';
            
            echo '</div>';
            
                 
             }
             
            }else{
                   
            // Fetch event data
            $event_id = get_the_ID();
            $event_m = tribe_get_start_date( $event_id, false, 'M' );
            $event_date = tribe_get_start_date( $event_id, false, 'd' );
            $event_day = tribe_get_start_date( $event_id, false, 'l' );
            $event_time = tribe_get_start_time( $event_id );
            $event_end_time = tribe_get_end_time( $event_id );
            $event_location = tribe_get_venue();
            $event_price = tribe_get_cost( $event_id );
            $event_link = tribe_get_event_link( $event_id );

            // Output event data in the layout
            echo '<div class="event-item">';
            echo '<div class="item_head">';
            echo '<div class="event-date">';
            echo '<div class="event-month"><span>'.esc_html($event_m).'</span>' . esc_html( $event_date ) . '</div>';
            echo '<div class="event-day">' . esc_html( $event_day ) . '</div>';
            echo '</div>';
            echo '<div class="event-details">';
            echo '<div class="event-time"> <span>' .$event_time .'</span> <span>-</span><span>' . $event_end_time . '</span></div>';
            echo '<div class="title_area"><div class="event-title">' . get_the_title() . '</div>';
            echo '<div class="event-location">' . esc_html( $event_location ) . '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="see_details">';
            echo '<button>see details <i class="fa-solid fa-chevron-down"></i></button>';    
            echo '</div>';
            echo '<div class="event-signup">';
            echo '<a href="' . esc_url( $event_link ) . '" class="signup-button">REGISTER<i class="fa-solid fa-arrow-right"></i></a>';
            echo '</div>';
            echo '</div>';
            echo '<div class="item_body">';
            
            echo  '<div class="single_bottom_info bg-light ">';
                    do_action( 'tribe_events_single_event_before_the_meta' );
                    tribe_get_template_part( 'modules/meta' ); 
                    do_action( 'tribe_events_single_event_after_the_meta' ); 
            echo    '</div>';
                echo do_shortcode('[tribe_tickets post_id="'.$event_id.'"]'); 
            
            echo '</div>';
            
            echo '</div>';
                
            }
             
        }
        if($count < 1 && $_POST['event_date'] != '' ){

        echo '<p>No events found matching your criteria.</p>';
        
    }
    } else {
        echo '<p>No events found matching your criteria.</p>';
    }
    
   
    
    wp_die();
}


