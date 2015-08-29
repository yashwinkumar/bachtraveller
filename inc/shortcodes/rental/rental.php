<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/30/14
 * Time: 5:13 PM
 */

/**
* ST Rental Detail Map
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Rental Map", ST_TEXTDOMAIN),
            'base' => 'st_rental_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_detail_map'))
{
    function st_rental_detail_map()
    {
        if(is_singular('st_rental'))
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
    st_reg_shortcode('st_rental_map','st_rental_detail_map');
}

/**
* ST Rental Detail Review Summary
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Review Summary", ST_TEXTDOMAIN),
            'base' => 'st_rental_review_summary',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_detail_review_summary'))
{
    function st_rental_detail_review_summary()
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/review_summary');
        }
    }

    st_reg_shortcode('st_rental_review_summary','st_rental_detail_review_summary');
}

/**
* ST Rental Detail Review Detail
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Rental Review", ST_TEXTDOMAIN),
            'base' => 'st_rental_review_detail',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_detail_review_detail'))
{
    function st_rental_detail_review_detail()
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/review_detail');
        }
    }

    st_reg_shortcode('st_rental_review_detail','st_rental_detail_review_detail');
}

/**
* ST Rental Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Review", ST_TEXTDOMAIN),
            'base' => 'st_rental_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_review'))
{
    function st_rental_review()
    {
        if(is_singular('st_rental'))
        {
            if(comments_open() and st()->get_option('rental_review')=='on')
            {
                ob_start();
                comments_template('/reviews/reviews.php');
                return @ob_get_clean();
            }
        }
    }
    st_reg_shortcode('st_rental_review','st_rental_review');

}


/**
* ST Rental Nearby
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Nearby", ST_TEXTDOMAIN),
            'base' => 'st_rental_nearby',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_nearby'))
{
    function st_rental_nearby($arg=array())
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/nearby',null,array('arg'=>$arg));
        }
    }
    st_reg_shortcode('st_rental_nearby','st_rental_nearby');

}

/**
* ST Rental Add Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Add Rental Rental Review", ST_TEXTDOMAIN),
            'base' => 'st_rental_add_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
    st_reg_shortcode('st_rental_add_review','st_rental_add_review');

}

if(!function_exists('st_rental_add_review'))
{
    function st_rental_add_review()
    {
        if(is_singular('st_rental'))
        {
            return '<div class="text-right mb10"><a class="btn btn-primary" href="'.get_comments_link().'">'.__('Write a review',ST_TEXTDOMAIN).'</a>
                                        </div>';
        }
    }
}

/**
* ST Rental Price
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Price", ST_TEXTDOMAIN),
            'base' => 'st_rental_price',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_price')){
    function st_rental_price()
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/price');
        }
    }
    st_reg_shortcode('st_rental_price','st_rental_price');

}

/**
* ST Rental Video
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Video", ST_TEXTDOMAIN),
            'base' => 'st_rental_video',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_video'))
{
    function st_rental_video()
    {
        if(is_singular('st_rental'))
        {
            if($video=get_post_meta(get_the_ID(),'video',true)){
                return "<div class='media-responsive'>".wp_oembed_get($video)."</div>";
            }
        }
    }
    st_reg_shortcode('st_rental_video','st_rental_video');

}

/**
* ST Rental Header
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Header", ST_TEXTDOMAIN),
            'base' => 'st_rental_header',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_header'))
{
    function st_rental_header($arg)
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/header',false,array('arg'=>$arg));
        }
        return false;
    }
    st_reg_shortcode('st_rental_header','st_rental_header');

}


/**
* ST Rental Book Form
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Rental Book Form", ST_TEXTDOMAIN),
            'base' => 'st_rental_book_form',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Rental',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_rental_book_form'))
{
    function st_rental_book_form($arg=array())
    {
        if(is_singular('st_rental'))
        {
            return st()->load_template('rental/elements/book_form',false,array('arg'=>$arg));
        }
        return false;
    }
}

st_reg_shortcode('st_rental_book_form','st_rental_book_form');
