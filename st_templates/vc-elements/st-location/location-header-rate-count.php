<?php 
$count_car = STLocation::get_info_by_post_type(get_the_ID(), 'st_cars') ; 
$count_tours = STLocation::get_info_by_post_type(get_the_ID(), 'st_tours') ; 
$count_hotel  = STLocation::get_info_by_post_type(get_the_ID(), 'st_hotel');
$count_activity = STLocation::get_info_by_post_type(get_the_ID(), 'st_activity') ; 
$count_rental = STLocation::get_info_by_post_type(get_the_ID(), 'st_rental');
	$array_s = array(
	    'st_cars'=> array(
	            'count'=>$count_car['offers'],
	            "from"=>$count_car['min_max_price']['price_min'],
	            "item_has_min"=>$count_car['min_max_price']['detail']['item_has_min_price']
	            ),
	    'st_tours'=>array(
	            'count'=>$count_tours['offers'],
	            "from"=>$count_tours['min_max_price']['price_min'],
	            "item_has_min"=>$count_tours['min_max_price']['detail']['item_has_min_price']
	            ),
	    'st_hotel'=>array(
	            'count'=>$count_hotel['offers'],
	            "from"=>$count_hotel['min_max_price']['price_min'],
	            "item_has_min"=>$count_hotel['min_max_price']['detail']['item_has_min_price']
	            ),
	    'st_activity'=>array(
	            'count'=>$count_activity['offers'],
	            "from"=>$count_activity['min_max_price']['price_min'],
	            "item_has_min"=>$count_activity['min_max_price']['detail']['item_has_min_price']
	            ),
	    'st_rental'=>array(
	            'count'=>$count_rental['offers'],
	            "from"=>$count_rental['min_max_price']['price_min'],
	            "item_has_min"=>$count_rental['min_max_price']['detail']['item_has_min_price']
	            )
	    );
?>
<?php 
	$custom  = $st_location_header_rate_count_custom;
	$html  = $st_location_header_rate_count_item_custom;
	$list_post_type  = $st_location_header_rate_count_post_type;
	$list_post_type = explode(",",$list_post_type);
	?>
	<ul class="icon-list text-white bgr-opacity" id='location_header_rate_count'>
		<?php 

		if (is_array($array_s) and !empty($array_s)){
		foreach ($array_s as $key => $value) {
			if (in_array($key, $list_post_type)){
				?>
					<li><a href='<?php echo STLocation::get_link_search($key) ;?>'>
						<?php echo esc_attr($value['count'] );?> 
						<?php 
							echo esc_html($text);
							if ($key == "st_cars"){
								echo st_get_language('cars') ; 
							};
							if ($key == "st_tours"){
								echo st_get_language('tours') ; 
							}
							if ($key == "st_hotel"){
								echo st_get_language('hotels') ; 
							}
							if ($key == "st_activity"){
								echo st_get_language('activities') ; 
							}
							if ($key == "st_rental"){
								echo st_get_language('rentals') ; 
							}
						?> </a>
						<?php echo __("from" ,ST_TEXTDOMAIN) ; ?> <a href="<?php echo get_permalink($value['item_has_min']);?>">
						<?php echo TravelHelper::format_money($value['from']) ; ?> 
						</a>
						<br>
					</li>
				<?php
			}
		}}?>
	</ul>
