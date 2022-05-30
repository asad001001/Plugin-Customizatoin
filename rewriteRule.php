function my_custom_rewrite() {
   $args = array("post_type" => "bunch_property","posts_per_page" => -1);
   $posts = get_posts( $args );
      foreach($posts as $post){
         $cat = get_the_terms($post,"property_category");
         $cat_slug = $cat[0]->slug;
         $post_slug = $post->post_name;
         add_rewrite_rule('^'.$cat_slug.'/'.$post_slug.'?/','index.php/property/'.$cat_slug.'/'.$post_slug.'/', 'top');
      }
}
add_action('init', 'my_custom_rewrite');




function custom_rewrite_rule() {
    // add_rewrite_rule('^cleaners/([^/]*)/?','index.php?page_id=1500','top');
        add_rewrite_rule('new-work/contact/?','index.php?page_id=1798','top');
add_rewrite_rule('old-work/contact/?','index.php?page_id=1798','top');
add_rewrite_rule('just-work/contact/?','index.php?page_id=1798','top');
add_rewrite_rule('trying/individual-therapy/','index.php/individual-therapy','top');


//         add_rewrite_rule('data/contact/','index.php?page_id=1798','top');


//         add_rewrite_rule('cleaner-details/([A-Za-z0-9-]+)/?','index.php?page_id=1721','top');

//     add_rewrite_rule('putzkraft/([a-z0-9-]+)/?','index.php?page_id=1500','top');
//         add_rewrite_rule('reinigung-job/([a-z0-9-]+)/?','index.php?page_id=1703','top');
//         add_rewrite_rule('cleaner-details/([A-Za-z0-9-]+)/?','index.php?page_id=1721','top');

}
add_action('init', 'custom_rewrite_rule', 10, 0);
