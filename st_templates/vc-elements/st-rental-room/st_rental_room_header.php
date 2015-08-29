<div class="booking-item-details no-border-top">
	<h2 class="lh1em featured_single"><?php the_title(); ?></h2>
	<?php 
		$rental_id = get_post_meta(get_the_ID(), 'room_parent', true);

		if(!empty($rental_id)){
			$rental_id = intval($rental_id);
			
			?>
			<div class="booking-item-rating" style="border: none">
                <ul class="icon-group booking-item-rating-stars">
                	<?php 
                		if(!empty($rental_id)){

					        echo '<div style="margin-right: 20px; float:left"><a href="'.get_the_permalink($rental_id).'">'.get_the_title($rental_id).'</a></strong></div>';
						}
                	?>
                    <?php echo  TravelHelper::rate_to_string(STReview::get_avg_rate()); ?>
                </ul>
            </div>
			<?php
		}
	?>
</div>