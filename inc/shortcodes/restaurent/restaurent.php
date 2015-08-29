<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 9:44 AM
 */


/**
* ST Restaurent header
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Restaurent Header", ST_TEXTDOMAIN),
            'base' => 'st_restaurent_header',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_restaurent_header'))
{
    function st_restaurent_header($arg)
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/header',false,array('arg'=>$arg));
        }
        return false;
    }
}

st_reg_shortcode('st_restaurent_header','st_restaurent_header');

/**
* ST Restaurent star
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Restaurent Star", ST_TEXTDOMAIN),
            'base' => 'st_restaurent_star',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}


if(!function_exists('st_restaurent_star'))
{
    function st_restaurent_star($attr=array())
    {
        if(is_singular('st_restaurent'))
            {
                return st()->load_template('restaurent/elements/star', false, array());
            }
            return false;
    }
}
st_reg_shortcode('st_restaurent_star','st_restaurent_star');
/**
* ST Restaurent Video
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Video',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_video',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_video'))
{
    function st_restaurent_video($attr=array())
    {
        if(is_singular('st_restaurent'))
        {
            if($video=get_post_meta(get_the_ID(),'video',true)){
                return "<div class='media-responsive'>".wp_oembed_get($video)."</div>";
            }
        }
    }
}

st_reg_shortcode('st_restaurent_video','st_restaurent_video');

/**
* ST Restaurent Price
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Price',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_price',
            'icon' => 'icon-st',
            'category' => 'Hotel',
            "content_element" => true,
            'params'=>array()
            )
        );
}


if(!function_exists('st_restaurent_price_func'))
{
    function st_restaurent_price_func($attr , $content = false)
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/price');
        }
    }
}

st_reg_shortcode('st_restaurent_price','st_restaurent_price_func');

/**
* ST Restaurent Logo
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Logo',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_logo',
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

if(!function_exists('st_restaurent_logo'))
{
    function st_restaurent_logo($attr = array())
    {
        if(is_singular('st_restaurent'))
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

st_reg_shortcode('st_restaurent_logo','st_restaurent_logo');


/**
* ST Restaurent Add Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Add Hotel Review',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_add_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_add_review'))
{
    function st_restaurent_add_review()
    {
        if(is_singular('st_restaurent'))
        {
           return '<div class="text-right mb10">
                      <a class="btn btn-primary" href="'.get_comments_link().'">'.__('Write a review',ST_TEXTDOMAIN).'</a>
                   </div>';
        }
    }
}

st_reg_shortcode('st_restaurent_add_review','st_restaurent_add_review');

/**
* ST Restaurent Nearby
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Nearby',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_nearby',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_nearby'))
{
    function st_restaurent_nearby()
    {
        if(is_singular('st_restaurent'))
        {
           return st()->load_template('restaurent/elements/nearby');
        }
    }
}

st_reg_shortcode('st_restaurent_nearby','st_restaurent_nearby');

/**
* ST Restaurent Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Review',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_review'))
{
    function st_restaurent_review()
    {
        if(is_singular('st_restaurent'))
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

st_reg_shortcode('st_restaurent_review','st_restaurent_review');

/**
* ST Restaurent Detail List Rooms
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed List of Hotel Rooms',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_list_rooms',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}


if(!function_exists('st_restaurent_detail_list_rooms'))
{
    function st_restaurent_detail_list_rooms($attr=array())
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/loop_room',null,array('attr'=>$attr));
        }
    }
}

st_reg_shortcode('st_restaurent_detail_list_rooms','st_restaurent_detail_list_rooms');

/**
* ST Restaurent Detail Card Accept
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Card Accept',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_card_accept',
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

if(!function_exists('st_restaurent_detail_card_accept'))
{
    function st_restaurent_detail_card_accept($arg=array())
    {
        $arg=wp_parse_args($arg,array(
            'title'=>''
        ));
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/card',false,array('arg'=>$arg));
        }
        return false;
    }
}

st_reg_shortcode('st_restaurent_detail_card_accept','st_restaurent_detail_card_accept');

/**
* ST Restaurent Detail Search Room
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Rooms Search Results',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_search_room',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_detail_search_room'))
{
    function st_restaurent_detail_search_room($attr=array())
    {
        if(is_singular('st_restaurent'))
        {
            $a= st()->load_template('restaurent/elements/search_room',null,array('attr'=>$attr));
            return $a;
        }
    }
}

st_reg_shortcode('st_restaurent_detail_search_room','st_restaurent_detail_search_room');

/**
* ST Restaurent Detail Review Detail
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Review',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_review_detail',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}


if(!function_exists('st_restaurent_detail_review_detail'))
{
    function st_restaurent_detail_review_detail()
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/review_detail');
        }
    }
}

st_reg_shortcode('st_restaurent_detail_review_detail','st_restaurent_detail_review_detail');

/**
* ST Restaurent Detail Review Summary
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Restaurent Review Summary',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_review_summary',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_detail_review_summary'))
{
    function st_restaurent_detail_review_summary()
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/review_summary');
        }
    }
}

st_reg_shortcode('st_restaurent_detail_review_summary','st_restaurent_detail_review_summary');

/**
* ST Restaurent Detail Map
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __('ST Detailed Hotel Map',ST_TEXTDOMAIN),
            'base' => 'st_restaurent_detail_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Hotel',
            'show_settings_on_create' => false,
            'params'=>array()
            )
        );
}

if(!function_exists('st_restaurent_detail_map'))
{
    function st_restaurent_detail_map()
    {
        if(is_singular('st_restaurent'))
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

st_reg_shortcode('st_restaurent_detail_map','st_restaurent_detail_map');

if(!function_exists('st_restaurent_detail_photo_func'))
{
    function st_restaurent_detail_photo_func()
    {
        if(is_singular('st_restaurent'))
        {
            return st()->load_template('restaurent/elements/photo');
        }
    }
    st_reg_shortcode('st_restaurent_detail_photo','st_restaurent_detail_photo_func');
}