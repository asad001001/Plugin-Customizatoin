
$ps = $_POST['ps'];
 $args = array(
           'post_type' => 'product','post_status' => array( 'publish' ), 'posts_per_page' => -1,
        );
    
    $datacat = array_filter(explode('|',$_POST['pscat']), function($value) { return !is_null($value) && $value !== ''; });
    $databrand = array_filter(explode('|',$_POST['psbrand']), function($value) { return !is_null($value) && $value !== ''; });
    $pscat = $datacat;    
    if($_POST['pscat'] != ''){
        
        $catformData = array_filter(explode('|',$_POST['pscat'] ), function($value) { return !is_null($value) && $value !== ''; });
            
    }
    
        if($_POST['ps'] != ''){
            
            $args['s'] = $ps;
            
        }   
           $index_query = new WP_Query($args);
        $data = array();
         $datacheck = array();
         $countd = array();
	     $i=0;
	    while ($index_query->have_posts()) : $index_query->the_post();
	     //end 
	     $terms = get_the_terms( get_the_ID(), 'product_cat' );
	            
	            foreach($terms as $term){
                    if(!in_array($term->name,$datacheck)){
                    
                        array_push($datacheck,$term->name);
                        $countd[$term->slug] = 1;
                        $data[$i] =  $term->name."%".$term->slug; 
    	                $i++;
                    
                        
                    }else{
                        
                        $countd[$term->slug] += 1;
                        
                    }
	           
	                
	            }   
	     
	     
	     
	     endwhile; wp_reset_postdata(); 
	     
  
	     $ht ='<div class="con_tain"><ul class="parent_ul" style="">';
	     $uniq = array();
	     foreach($data as $d){
	         $uniq = explode('%',$d);
    
    	  $ht .= '<li class="child_li"><label><input type="checkbox" name="child_sorting_cat" '.((in_array($uniq[1],$catformData))? "checked" :"").' value="'.$uniq[1].'" class="child pscat-check" data-tag="'.$uniq[0].'">'.$uniq[0].'<span class="count-category-num"> ('.$countd[$uniq[1]].')</span></label></li>';
                          
	         
	     }
	     
	     $ht.='</ul></div>';
	     
