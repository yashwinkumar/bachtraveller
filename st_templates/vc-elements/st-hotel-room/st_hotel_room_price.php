<?php 

$check_in = TravelHelper::convertDateFormat(STInput::request('start'));
if(!$check_in){
	$check_in = date('m/d/Y', strtotime("now"));
}

$check_out = TravelHelper::convertDateFormat(STInput::request('end'));
if(!$check_out){
	$check_out = date('m/d/Y', strtotime("+1 day"));
}

$room_num_search = STInput::request('room_num_search');
if(!$room_num_search){
	$room_num_search = 1;
}

$data_price=STRoom::get_room_price(get_the_ID(),$check_in,$check_out);

$html_price = $data_price['price'] * $room_num_search;

$default = array(
    'align' => 'right'
);
if (isset($attr)) {
    extract(wp_parse_args($attr, $default));
} else {
    extract($default);
}
?>
<div class="booking-item-details no-border-top">
	<p class="booking-item-header-price text-<?php echo esc_html($align) ?>">
	    <small><?php _e("price", ST_TEXTDOMAIN) ?></small>
	    <span class="text-lg"><?php echo TravelHelper::format_money($html_price) ?></span>/<?php st_the_language('night') ?>
	</p>
	<div class="gap gap-small"></div>
</div>