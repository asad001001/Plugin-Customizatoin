<div class="con_tain">
                        
                        <?php
                         $args = array(
                              'taxonomy' => 'product_cat',
                              'hide_empty' => false,
                              'parent'   => 0,
                              'hide_empty' => true 
                          );
                      $product_cat = get_terms( $args );
                        foreach( $product_cat as $term ) { 
                                    ?>
                              <ul class="parent_ul">
                                <li class="parent_li"><label><input type="checkbox" name="parent_sorting_cat" value="<?php echo $term->slug; ?>" class="parent pscat-check"  <?php echo ((in_array($term->slug,$catformData))? "checked" :""); ?>  data-tag="<?php echo $term->name; ?> (<?php echo $term->count; ?>)"><?php echo $term->name; ?> <span class="count-category-num">(<?php echo $term->count; ?>)</span></label>
                                <ul class="child_ul">
                                  <?php
                          $child_args = array(
                                      'taxonomy' => 'product_cat',
                                      'hide_empty' => false,
                                      'parent'   => $term->term_id
                                  );
                          $child_product_cats = get_terms( $child_args );
                          foreach ($child_product_cats as $child_product_cat)
                          {?>
                          
                           <li class="child_li"><label><input type="checkbox" name="child_sorting_cat" value="<?php echo $child_product_cat->slug; ?>" class="child pscat-check" <?php echo ((in_array($child_product_cat->slug,$catformData))? "checked" :""); ?>  data-tag="<?php echo $child_product_cat->name; ?> (<?php echo $child_product_cat->count; ?>)"><?php echo $child_product_cat->name; ?> <span class="count-category-num">(<?php echo $child_product_cat->count; ?>)</span></label></li>
                          
                          <?php
                              
                          }
                        ?>
                                </ul>
                              </li>
                            </ul>

                        <?php } ?>
                  </div>
	        
