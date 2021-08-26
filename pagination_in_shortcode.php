<?php
function Winners(){
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	 $args = array(  
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => 20, 
        'order' => 'DSC',
		'paged' => $paged
    );

    $loop = new WP_Query( $args ); 

	$html="";      
	
    while($loop->have_posts() ) : $loop->the_post();

	   $html.= '<div class="winner_box"><div class="img_con"> <span>Winner</span>'.get_the_post_thumbnail(get_the_ID(),"full").'<h3> '.get_the_title().'</h3> </div><h6>'.get_the_excerpt().'<span>winner</span></h6></div>';
	
endwhile;


	$html .= '<div class="pagination">';
    
        $html .= paginate_links( array(
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'total'        => $loop->max_num_pages,
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
            'end_size'     => 2,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
    
$html .= '</div>';


	return $html;



     wp_reset_postdata(); 

}
