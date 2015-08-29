<?php 
	
/**
* @since 1.1.3
* Hotel Room Header
**/	
if(function_exists('vc_map')){
		vc_map(array(
		'name'                    => __('ST Hotel Room Header',ST_TEXTDOMAIN),
		'base'                    => 'st_hotel_room_header',
		'content_element'         => true,
		'icon'                    => 'icon-st',
		'category'                => 'Hotel',
		'show_settings_on_create' => false,
		'params'                  =>array()
		));
}
if(!function_exists('st_hotel_room_header_ft')){
	function st_hotel_room_header_ft($args){
		if(is_singular('hotel_room')){
			return st()->load_template('vc-elements/st-hotel-room/st_hotel_room_header', false, array('data' => $args));
		}
		return false;
	}
}
st_reg_shortcode('st_hotel_room_header','st_hotel_room_header_ft');

/**
* @since 1.1.3
* Hotel Room Facility
**/
if(function_exists('vc_map')){
	vc_map(array(
		'name' => __('Hotel Room Facility', ST_TEXTDOMAIN),
		'base' => 'st_hotel_room_facility',
		'content_element' => true,
		'icon' => 'icon-st',
		'category' => 'Hotel',
		'show_settings_on_create' => true,
		'params' => array()
	));
}
if(!function_exists('st_hotel_room_facility')){
	function  st_hotel_room_facility_ft($args, $content = null){
		if(is_singular('hotel_room')){

            return st()->load_template('vc-elements/st-hotel-room/st_hotel_room_facility', false, array('args' => $args));

		}
		return false;
	}
}
st_reg_shortcode('st_hotel_room_facility','st_hotel_room_facility_ft');

if(function_exists('vc_map')){
    vc_map( array(
        "name" => __("ST Hotel Room Gallery", ST_TEXTDOMAIN),
        "base" => "st_hotel_room_gallery",
        "content_element" => true,
        "icon" => "icon-st",
        "category"=>'Hotel',
        "params" => array(
            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Style", ST_TEXTDOMAIN),
                "param_name" => "style",
                "description" =>"",
                "value" => array(
                    __('Slide',ST_TEXTDOMAIN)=>'slide',
                    __('Grid',ST_TEXTDOMAIN)=>'grid',
                ),
            )
        )
    ) );
}

if(!function_exists('st_hotel_room_gallery_ft')){
    function st_hotel_room_gallery_ft($attr,$content=false)
    {
        if(is_singular('hotel_room'))
        {
            return st()->load_template('vc-elements/st-hotel-room/st_hotel_room_gallery',null,array('attr'=>$attr));
        }
    }
}
st_reg_shortcode('st_hotel_room_gallery','st_hotel_room_gallery_ft');

if(function_exists('vc_map')){
	vc_map(array(
		'name' => __('ST Hotel Room Form',ST_TEXTDOMAIN),
		'base' => 'st_hotel_room_form',
		'content_element' => true,
		'icon' => 'icon-st',
		'category' => 'Hotel',
		'show_settings_on_create' => false,
		'params' => array()
	));
}
if(!function_exists('st_hotel_room_form_ft')){
	function st_hotel_room_form_ft($args){
		if(is_singular('hotel_room')){
			return st()->load_template('vc-elements/st-hotel-room/st_hotel_room_form',null,array('attr'=>$args));
		}
		return false;
	}
}
st_reg_shortcode('st_hotel_room_form','st_hotel_room_form_ft');

if(function_exists('vc_map')){
	vc_map(array(
		'name' => __('ST Hotel Room Price',ST_TEXTDOMAIN),
		'base' => 'st_hotel_room_price',
		'content_element' => true,
		'icon' => 'icon-st',
		'category' => 'Hotel',
		'show_settings_on_create' => false,
		'params' => array()
	));
}
if(!function_exists('st_hotel_room_price_ft')){
	function st_hotel_room_price_ft($args){
		if(is_singular('hotel_room')){
			return st()->load_template('vc-elements/st-hotel-room/st_hotel_room_price',null,array('attr'=>$args));
		}
		return false;
	}
}
st_reg_shortcode('st_hotel_room_price','st_hotel_room_price_ft');
?>