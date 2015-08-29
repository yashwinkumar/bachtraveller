<div class="booking-item-details no-border-top">
	<h2 class="lh1em featured_single"><?php the_title(); ?></h2>
	<?php 
		$hotel_id = get_post_meta(get_the_ID(), 'room_parent', true);

		$adult_number = intval(get_post_meta(get_the_ID(), 'adult_number', true));
		$children_number = intval(get_post_meta(get_the_ID(), 'children_number', true));
		$bed_number = intval(get_post_meta(get_the_ID(), 'bed_number', true));
		$room_footage = intval(get_post_meta(get_the_ID(), 'room_footage', true));

		if(!empty($hotel_id)){
			$hotel_id = intval($hotel_id);
			$hotel_star = get_post_meta($hotel_id, 'hotel_star', true);
			if(!empty($hotel_star)){
				$hotel_star = intval($hotel_star);
				?>

				<div class="booking-item-rating" style="border:none">
			        <ul class="icon-group booking-item-rating-stars">
			            <?php
			            echo '<div class="pull-left" style="margin-right: 20px;"><strong>'.get_the_title($hotel_id).'</strong></div>';
			            echo  TravelHelper::rate_to_string($hotel_star).'('.$hotel_star.')';
			            ?>
			        </ul>
		    	</div>
				<?php
			}
			?>
			<ul class="booking-item-features booking-item-features-sign clearfix">
				<li rel="tooltip" data-placement="top" title="" data-original-title="Adults Occupancy">
					<i class="fa fa-male"></i>
					<span class="booking-item-feature-sign"><?php echo $adult_number; ?></span>
              	</li>
              	<li rel="tooltip" data-placement="top" title="" data-original-title="Childs">
              		<i class="im im-children"></i>
              		<span class="booking-item-feature-sign"><?php echo $children_number; ?></span>
                </li>
                <li rel="tooltip" data-placement="top" title="" data-original-title="Beds">
                	<i class="im im-bed"></i>
                	<span class="booking-item-feature-sign"><?php echo $bed_number; ?></span>
                </li>
                <li rel="tooltip" data-placement="top" title="" data-original-title="Room footage (square feet)">
                	<i class="im im-width"></i>
                	<span class="booking-item-feature-sign"><?php echo $room_footage; ?></span>
                </li>
			</ul>	
			<?php
		}
	?>
</div>