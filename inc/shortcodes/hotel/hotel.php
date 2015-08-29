<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 9:44 AM
 */


/**
* ST Hotel header
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Hotel Header", ST_TEXTDOMAIN),
            'base' => 'st_hotel_header',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_hotel_header'))
{
    function st_hotel_header($arg)
    {
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/header',false,array('arg'=>$arg));
        }
        return false;
    }
}

st_reg_shortcode('st_hotel_header','st_hotel_header');

/**
* ST Hotel star
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Hotel Star", ST_TEXTDOMAIN),
            'base' => 'st_hotel_star',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}


if(!function_exists('st_hotel_star'))
{
    function st_hotel_star($attr=array())
    {
        if(is_singular('st_hotel'))
            {
                return st()->load_template('hotel/elements/star', false, array());
            }
            return false;
    }
}
st_reg_shortcode('st_hotel_star','st_hotel_star');
/**
* ST Hotel Video
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Video',ST_TEXTDOMAIN),
            'base' => 'st_hotel_video',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_video'))
{
    function st_hotel_video($attr=array())
    {
        if(is_singular('st_hotel'))
        {
            if($video=get_post_meta(get_the_ID(),'video',true)){
                return "<div class='media-responsive'>".wp_oembed_get($video)."</div>";
            }
        }
    }
}

st_reg_shortcode('st_hotel_video','st_hotel_video');

/**
* ST Hotel Price
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Price',ST_TEXTDOMAIN),
            'base' => 'st_hotel_price',
            'icon' => 'icon-st',
            'category' => 'Hotel',
            "content_element" => true,
            'params'=>array()
            )
        );
}


if(!function_exists('st_hotel_price_func'))
{
    function st_hotel_price_func($attr , $content = false)
    {
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/price');
        }
    }
}

st_reg_shortcode('st_hotel_price','st_hotel_price_func');

/**
* ST Hotel Logo
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Logo',ST_TEXTDOMAIN),
            'base' => 'st_hotel_logo',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Thumbnail Size', ST_TEXTDOMAIN),
                    'param_name' => 'thumbnail_size',
                    'value' => array(
                        'Full' => 'full',
                        'Large' => 'large',
                        'Medium' => 'medium',
                        'Thumbnail' => 'thumbnail'
                        )
                    ),
                )
            )
        );
}

if(!function_exists('st_hotel_logo'))
{
    function st_hotel_logo($attr = array())
    {
        if(is_singular('st_hotel'))
        {
            $default=array(
                'thumbnail_size'=> 'full'
            );

            extract(wp_parse_args($attr,$default));

            $meta=get_post_meta(get_the_ID(),'logo',true);
            if($meta)
            {
                return wp_get_attachment_image($meta,$thumbnail_size,false,array('class'=>'img-responsive','style'=>'margin-bottom:10px;'));
            }
        }
    }
}

st_reg_shortcode('st_hotel_logo','st_hotel_logo');


/**
* ST Hotel Add Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Add Hotel Review',ST_TEXTDOMAIN),
            'base' => 'st_hotel_add_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_add_review'))
{
    function st_hotel_add_review()
    {
        if(is_singular('st_hotel'))
        {
           return '<div class="text-right mb10">
                      <a class="btn btn-primary" href="'.get_comments_link().'">'.__('Write a review',ST_TEXTDOMAIN).'</a>
                   </div>';
        }
    }
}

st_reg_shortcode('st_hotel_add_review','st_hotel_add_review');

/**
* ST Hotel Nearby
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Nearby',ST_TEXTDOMAIN),
            'base' => 'st_hotel_nearby',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_nearby'))
{
    function st_hotel_nearby()
    {
        if(is_singular('st_hotel'))
        {
           return st()->load_template('hotel/elements/nearby');
        }
    }
}

st_reg_shortcode('st_hotel_nearby','st_hotel_nearby');

/**
* ST Hotel Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Review',ST_TEXTDOMAIN),
            'base' => 'st_hotel_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_review'))
{
    function st_hotel_review()
    {
        if(is_singular('st_hotel'))
        {

            if(comments_open() and st()->get_option('hotel_review')=='on')
            {
                ob_start();
                    comments_template('/reviews/reviews.php');
                return @ob_get_clean();
            }
        }
    }
}

st_reg_shortcode('st_hotel_review','st_hotel_review');

/**
* ST Hotel Detail List Rooms
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed List of Hotel Rooms',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_list_rooms',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}


if(!function_exists('st_hotel_detail_list_rooms'))
{
    function st_hotel_detail_list_rooms($attr=array())
    {
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/loop_room',null,array('attr'=>$attr));
        }
    }
}

st_reg_shortcode('st_hotel_detail_list_rooms','st_hotel_detail_list_rooms');

/**
* ST Hotel Detail Card Accept
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Card Accept',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_card_accept',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            "params" => array(
                // add params same as with any other content element
                array(
                    "type" => "textfield",
                    "heading" => __("Title", ST_TEXTDOMAIN),
                    "param_name" => "title",
                    "description" =>"",
                ),
             )
            )
        );
}

if(!function_exists('st_hotel_detail_card_accept'))
{
    function st_hotel_detail_card_accept($arg=array())
    {
        $arg=wp_parse_args($arg,array(
            'title'=>''
        ));
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/card',false,array('arg'=>$arg));
        }
        return false;
    }
}

st_reg_shortcode('st_hotel_detail_card_accept','st_hotel_detail_card_accept');

/**
* ST Hotel Detail Search Room
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Rooms Search Results',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_search_room',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_detail_search_room'))
{
    function st_hotel_detail_search_room($attr=array())
    {
        if(is_singular('st_hotel'))
        {
            $a= st()->load_template('hotel/elements/search_room',null,array('attr'=>$attr));
            return $a;
        }
    }
}

st_reg_shortcode('st_hotel_detail_search_room','st_hotel_detail_search_room');

/**
* ST Hotel Detail Review Detail
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Review',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_review_detail',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}


if(!function_exists('st_hotel_detail_review_detail'))
{
    function st_hotel_detail_review_detail()
    {
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/review_detail');
        }
    }
}

st_reg_shortcode('st_hotel_detail_review_detail','st_hotel_detail_review_detail');

/**
* ST Hotel Detail Review Summary
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Hotel Review Summary',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_review_summary',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_detail_review_summary'))
{
    function st_hotel_detail_review_summary()
    {
        if(is_singular('st_hotel'))
        {
            return st()->load_template('hotel/elements/review_summary');
        }
    }
}

st_reg_shortcode('st_hotel_detail_review_summary','st_hotel_detail_review_summary');

/**
* ST Hotel Detail Map
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Map',ST_TEXTDOMAIN),
            'base' => 'st_hotel_detail_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_hotel_detail_map'))
{
    function st_hotel_detail_map()
    {
        if(is_singular('st_hotel'))
        {
            $lat=get_post_meta(get_the_ID(),'map_lat',true);
            $lng=get_post_meta(get_the_ID(),'map_lng',true);
            $zoom=get_post_meta(get_the_ID(),'map_zoom',true);
            $html=' <div style="width:100%; height:500px;" class="st_google_map" data-type="2" data-lat="'.$lat.'"
                             data-lng="'.$lng.'"
                             data-zoom="'.$zoom.'"
                            ></div>';

            return $html;
        }
    }
}

st_reg_shortcode('st_hotel_detail_map','st_hotel_detail_map');

