<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 9:44 AM
 */

/**
* ST Thumbnail Activity
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Thumbnail", ST_TEXTDOMAIN),
            'base' => 'st_thumbnail_activity',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_thumbnail_activity_func'))
{
    function st_thumbnail_activity_func()
    {
        if(is_singular('st_activity'))
        {
            return st()->load_template('activity/elements/image','featured');
        }
    }

    st_reg_shortcode('st_thumbnail_activity','st_thumbnail_activity_func');
}

/**
* ST Form Book
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Booking Form", ST_TEXTDOMAIN),
            'base' => 'st_form_book',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_form_book_func'))
{
    function st_form_book_func()
    {
        if(is_singular('st_activity'))
        {
            return st()->load_template('activity/elements/form','book');
        }
    }
    st_reg_shortcode('st_form_book','st_form_book_func');
}

/**
* ST Excerpt Activity
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Excerpt", ST_TEXTDOMAIN),
            'base' => 'st_excerpt_activity',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_excerpt_activity_func'))
{
    function st_excerpt_activity_func()
    {
        if(is_singular('st_activity'))
        {
            return '<blockquote class="center">'.get_the_excerpt()."</blockquote>";
        }
    }
    st_reg_shortcode('st_excerpt_activity','st_excerpt_activity_func');
}

/**
* ST Activity Content
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Content", ST_TEXTDOMAIN),
            'base' => 'st_activity_content',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_activity_content_func'))
{
    function st_activity_content_func()
    {
        if(is_singular('st_activity'))
        {
             return st()->load_template('activity/elements/content','activity');
        }
    }
    st_reg_shortcode('st_activity_content','st_activity_content_func');
}

/**
* ST Activity Detail Map
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Activity Map", ST_TEXTDOMAIN),
            'base' => 'st_activity_detail_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_activity_detail_map'))
{
    function st_activity_detail_map()
    {
        if(is_singular('st_activity'))
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
    st_reg_shortcode('st_activity_detail_map','st_activity_detail_map');
}

/**
* ST Activity Detail Review Summary
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Review Summary", ST_TEXTDOMAIN),
            'base' => 'st_activity_detail_review_summary',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_activity_detail_review_summary'))
{
    function st_activity_detail_review_summary()
    {

        if(is_singular('st_activity'))
        {
            return st()->load_template('activity/elements/review_summary');
        }
    }
    st_reg_shortcode('st_activity_detail_review_summary','st_activity_detail_review_summary');
}

/**
* ST Activity Detail Review Detail
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Activity Review", ST_TEXTDOMAIN),
            'base' => 'st_activity_detail_review_detail',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_activity_detail_review_detail'))
{
    function st_activity_detail_review_detail()
    {
        if(is_singular('st_activity'))
        {
            return st()->load_template('activity/elements/review_detail');
        }
    }
    st_reg_shortcode('st_activity_detail_review_detail','st_activity_detail_review_detail');
}

/**
* ST Activity Review
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Review", ST_TEXTDOMAIN),
            'base' => 'st_activity_review',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_activity_review'))
{
    function st_activity_review()
    {
        if(is_singular('st_activity'))
        {
            if(comments_open() and st()->get_option('activity_review')!='off')
            {
                ob_start();
                    comments_template('/reviews/reviews.php');
                return @ob_get_clean();
            }
        }
    }
}
st_reg_shortcode('st_activity_review','st_activity_review');

/**
* ST Activity Video
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Video", ST_TEXTDOMAIN),
            'base' => 'st_activity_video',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_activity_video'))
{
    function st_activity_video($attr=array())
    {
        if(is_singular('st_activity'))
        {
            if($video=get_post_meta(get_the_ID(),'video',true)){
                return "<div class='media-responsive'>".wp_oembed_get($video)."</div>";
            }
        }
    }
    st_reg_shortcode('st_activity_video','st_activity_video');
}

/**
* ST Activity Nearby
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Activity Nearby", ST_TEXTDOMAIN),
            'base' => 'st_activity_nearby',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Activity',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_activity_nearby'))
{
    function st_activity_nearby()
    {
        if(is_singular('st_activity'))
        {
            return st()->load_template('activity/elements/nearby');
        }
    }
    st_reg_shortcode('st_activity_nearby','st_activity_nearby');
}
