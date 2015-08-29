<div class="facility_des" style="margin-bottom: 20px;">
	<?php  echo get_the_excerpt();?>
</div>
<div class="room-facility">
	<h4 class="booking-item-title"><?php echo __('About This Listing', ST_TEXTDOMAIN); ?></h4>
	<?php 
		$adult_number = intval(get_post_meta(get_the_ID(), 'adult_number', true));
		$children_number = intval(get_post_meta(get_the_ID(), 'children_number', true));
		$bed_number = intval(get_post_meta(get_the_ID(), 'bed_number', true));
		$room_footage = intval(get_post_meta(get_the_ID(), 'room_footage', true));
		$html_price = get_post_meta(get_the_ID(), 'price', true);
		$discount = get_post_meta(get_the_ID(), 'discount_rate', true);
		$sale_price_from = get_post_meta(get_the_ID(), 'sale_price_from', true);
		$sale_price_to = get_post_meta(get_the_ID(), 'sale_price_to', true);

	?>
	<table class="" style="margin-top: 20px;">
		<tr>
			<th>
				Facilities
			</th>
			<td>
				<p>Adult number: <?php echo $adult_number; ?> </p>
				<p>Children number: <?php echo $children_number; ?> </p>
			</td>
			<td>
				<p>Bed number: <?php echo $bed_number; ?> </p>
				<p>Room Footage: <?php echo $room_footage; ?> </p>
			</td>
		</tr>
		<tr>
			<th>
				Prices:
			</th>
			<td>
                <p>Price: <?php echo TravelHelper::format_money($html_price)?> / night</p>
                <p>Discount: <?php echo $discount; ?> %</p>
			</td>
			<td>
				<p>Sale price from: <?php if(!empty($sale_price_from)) echo date(TravelHelper::getDateFormat(),strtotime($sale_price_from)); ?></p>
				<p>Sale price to: <?php if(!empty($sale_price_to)) echo date(TravelHelper::getDateFormat(),strtotime($sale_price_to)); ?></p>
			</td>
		</tr>
	</table>
</div>