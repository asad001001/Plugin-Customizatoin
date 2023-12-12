
// short code for filters

add_shortcode('show_therapist','show_therapist');

function show_therapist($attr){
    ?>
        <div class="wraper_therapist">
            <div class="therappist_filters">
                <!--cities-->
                <div class="therapist_type">
                    <?php 
                    $terms = get_terms(array(
                        'taxonomy' => 'city',
                        'hide_empty' => true, // Set to true if you want to hide empty terms
                    ));
                    
                    // Check if any terms are found
                if (!empty($terms) && !is_wp_error($terms)) {
                    ?>
                    <select name="city" class="filter_select">
                        <option value="">Select an city</option>
                        <?php
                        foreach ($terms as $term) {
                            ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
                </div>
                <!--cities-->
                <!--state-->
                <div class="therapist_type">
                                        <?php 
                    $terms = get_terms(array(
                        'taxonomy' => 'state',
                        'hide_empty' => true, // Set to true if you want to hide empty terms
                    ));
                    
                    // Check if any terms are found
                if (!empty($terms) && !is_wp_error($terms)) {
                    ?>
                    <select name="state" class="filter_select">
                        <option value="">Select an state</option>
                        <?php
                        foreach ($terms as $term) {
                            ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
                </div>
                <!--state-->
                <!-- method-->
                <div class="therapist_type">
                                        <?php 
                    $terms = get_terms(array(
                        'taxonomy' => 'therapy-method',
                        'hide_empty' => true, // Set to true if you want to hide empty terms
                    ));
                    
                    // Check if any terms are found
                if (!empty($terms) && !is_wp_error($terms)) {
                    ?>
                    <select name="therapy-method" class="filter_select">
                        <option value="">Select an method</option>
                        <?php
                        foreach ($terms as $term) {
                            ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
                </div>
                <!-- method-->
                <!--type-->
                <div class="therapist_type">
                                        <?php 
                    $terms = get_terms(array(
                        'taxonomy' => 'therapy-type',
                        'hide_empty' => true, // Set to true if you want to hide empty terms
                    ));
                    
                    // Check if any terms are found
                if (!empty($terms) && !is_wp_error($terms)) {
                    ?>
                    <select name="therapy-type" class="filter_select">
                        <option value="">Select an type</option>
                        <?php
                        foreach ($terms as $term) {
                            ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
                </div>
                <!--type-->
            </div>
            <?php     // Define your query parameters
            $args = array(
                'post_type' => 'therapists', // Set the post type
                'posts_per_page' => 10, // Number of posts to display
             
            );
        
        
            // Instantiate the WP_Query class
            $query = new WP_Query($args);
        
         ?>
            <!-- to show listing-->
            <div class="show_therapist_listing">
                <div class="therapist_items">
                    <?php    // The Loop
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    // Display post content here 
                    ?>
                    <!--single item-->
                    <div class="therapist_item">
                        <div class="therapist_img">
                            <?php 
                         $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); // Change 'thumbnail' to the desired image size

                        if ($thumbnail_url) {
                            ?>
                            <img src="<?php echo esc_url($thumbnail_url); ?>" class="therapist_thumbnail" alt="<?php echo esc_attr(get_the_title()); ?>">
                            <?php } ?>
                        </div>
                        <div class"therapist_content">
                            <h2><?php the_title();?> </h2>
                            <p><?php the_excerpt(); ?></p>
                        </div>
                    </div>
                    <!-- end single item-->
                    <?php
                }
                // Restore original post data
                wp_reset_postdata();
                } else {
                    // No posts found
                    echo 'No posts found.';
                }
            ?>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function(){
            // pp.ajax_url;
                jQuery(".filter_select").on("change",function($){
                    
                    
                     var selectValues = [];

                            // Iterate over each select box
                            jQuery('.filter_select').each(function() {
                                 var selectName = jQuery(this).attr('name');
                                var selectValue = jQuery(this).val();
                                if(selectValue != ""){
                               
                                // Push an object containing the name and value to the array
                                selectValues.push({
                                    
                                    name: selectName,
                                    value: selectValue
                                });
                                    
                                }
                            });
                    console.log(selectValues);
                    
                    
                    
                      // AJAX request
                    jQuery.ajax({
                        url: pp.ajax_url, // ajaxurl is a global variable in WordPress
                        type: 'POST',
                        data: {
                            action: 'filter_therapy_ajax_action', // Action name defined in the PHP function
                            selectdata: selectValues,
                        },
                        success: function (response) {
                            // console.log(response); // Process the response
                            
                            jQuery('.therapist_items').html(response);
                        },
                        error: function (error) {
                            console.error('AJAX error:', error);
                        },
                    });
                    
                    
                });
            
            
            });
        </script>
<?php 
    
}

// ajax handle for filter_SSL
function filter_therapy_ajax_action() {
    // Your AJAX logic here
    
        $args = array(
            
        'post_type' => 'therapists', // Set the post type
        'posts_per_page' => 200, // Number of posts to display
     
    );
    $args['tax_query'] = array( 'relation'=> 'AND' );
    foreach($_POST['selectdata'] as $item){
        
        array_push($args['tax_query'] , array(
                        'taxonomy' => $item['name'],
                        'field'    => 'term_id',
                        'terms'    => $item['value'],
                        
                    ));	 
    }
                     

$query = new WP_Query($args);

        // The Loop
            if ($query->have_posts()) {
                while ($query->have_posts()) { $query->the_post();
        ?>
                            <!--single item-->
                    <div class="therapist_item">
                        <div class="therapist_img">
                            <?php 
                         $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full'); // Change 'thumbnail' to the desired image size

                        if ($thumbnail_url) {
                            ?>
                            <img src="<?php echo esc_url($thumbnail_url); ?>" class="therapist_thumbnail" alt="<?php echo esc_attr(get_the_title()); ?>">
                            <?php } ?>
                        </div>
                        <div class"therapist_content">
                            <h2><?php the_title();?> </h2>
                            <p><?php the_excerpt(); ?></p>
                        </div>
                    </div>
                    <!-- end single item-->
                    <?php
                }
                // Restore original post data
                wp_reset_postdata();
                } else {
                    // No posts found
                    echo 'No posts found.';
                }
            
    // Always exit to avoid extra output
    wp_die();
}
add_action('wp_ajax_filter_therapy_ajax_action', 'filter_therapy_ajax_action'); // For logged-in users
add_action('wp_ajax_nopriv_filter_therapy_ajax_action', 'filter_therapy_ajax_action'); // For non-logged-in users
