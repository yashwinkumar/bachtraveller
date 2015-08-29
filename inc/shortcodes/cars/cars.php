<?php

/**
* ST Thumbnail Cars
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Car Thumbnail", ST_TEXTDOMAIN),
            'base' => 'st_thumbnail_cars',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Car',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_thumbnail_cars_func'))
{
    function st_thumbnail_cars_func()
    {
        if(is_singular('st_cars'))
        {
            return st()->load_template('cars/elements/image','featured');
        }
    }
    st_reg_shortcode('st_thumbnail_cars','st_thumbnail_cars_func');
}

/**
* ST Excerpt Cars
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Car Excerpt", ST_TEXTDOMAIN),
            'base' => 'st_excerpt_cars',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Car',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_excerpt_cars_func'))
{
    function st_excerpt_cars_func()
    {
        if(is_singular('st_cars'))
        {
            return '<p class="text-small">'.get_the_excerpt()."</p><hr>";
        }
    }
    st_reg_shortcode('st_excerpt_cars','st_excerpt_cars_func');
}

/**
* ST Detail Location Cars
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Car Location", ST_TEXTDOMAIN),
            'base' => 'st_detail_date_location_cars',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Car',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Drop Off', ST_TEXTDOMAIN),
                    'param_name' => 'drop-off',
                    'value' => ''
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Pick Up', ST_TEXTDOMAIN),
                    'param_name' => 'pick-up',
                    'value' => ''
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Location ID Drop Off', ST_TEXTDOMAIN),
                    'param_name' => 'location_id_drop_off',
                    'value' => ''
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Location ID Pick Up', ST_TEXTDOMAIN),
                    'param_name' => 'location_id_pick_up',
                    'value' => ''
                ),
            )
        )
    );
}
if(!function_exists('st_detail_date_location_cars_func'))
{
    function st_detail_date_location_cars_func()
    {
        if(is_singular('st_cars'))
        {
            $default=array(
                'drop-off'=>__('none',ST_TEXTDOMAIN),
                'pick-up'=>__('none',ST_TEXTDOMAIN),
                'location_id_drop_off'=>'',
                'location_id_pick_up'=>'',
            );

            $_REQUEST=wp_parse_args($_REQUEST,$default);

            if(!empty($_REQUEST['pick-up-date'])){
                $pick_up_date =  $_REQUEST['pick-up-date'];
            }else{
                $pick_up_date = date( TravelHelper::getDateFormat(), strtotime("now"));
            }
            if(!empty($_REQUEST['pick-up-time'])){
                $pick_up_time = $_REQUEST['pick-up-time'];
            }else{
                $pick_up_time ="12:00 AM";
            }
            if(STInput::request("location_id_pick_up")){
                $address_pick_up = get_the_title(STInput::request("location_id_pick_up"));
            }else{
                $address_pick_up = STInput::request('pick-up');
            }
            $pick_up = '<h5>'.st_get_language('car_pick_up').':</h5>
        <p><i class="fa fa-map-marker box-icon-inline box-icon-gray"></i>'.$address_pick_up.'</p>
        <p><i class="fa fa-calendar box-icon-inline box-icon-gray"></i>'.$pick_up_date.'</p>
        <p><i class="fa fa-clock-o box-icon-inline box-icon-gray"></i>'.$pick_up_time.'</p>';

            if(!empty($_REQUEST['drop-off-date'])){
                $drop_off_date =  $_REQUEST['drop-off-date'];
            }else{$drop_off_date = $pick_up_date = date(TravelHelper::getDateFormat(), strtotime("+1 day"));}

            if(!empty($_REQUEST['drop-off-time'])){
                $drop_off_time = $_REQUEST['drop-off-time'];
            }else{ $drop_off_time ="12:00 AM"; }
            if(STInput::request('location_id_drop_off')){
                $address_drop_off = get_the_title(STInput::request('location_id_drop_off'));
            }else{
                $address_drop_off = STInput::request('drop-off');
            }
            $drop_off = '   <h5>'.st_get_language('car_drop_off').':</h5>
                        <p><i class="fa fa-map-marker box-icon-inline box-icon-gray"></i>'.$address_drop_off.'</p>
                        <p><i class="fa fa-calendar box-icon-inline box-icon-gray"></i>'.$drop_off_date.'</p>
                        <p><i class="fa fa-clock-o box-icon-inline box-icon-gray"></i>'.$drop_off_time.'</p>';

            $logo = get_post_meta(get_the_ID(),'cars_logo',true);
            if(!empty($logo)){
                $logo = '<img src="'.bfi_thumb($logo,array('width'=>'120','height'=>'120')).'" alt="logo" />';
            }
            $about = get_post_meta(get_the_ID(),'cars_about',true);
            if(!empty($about)){
                $about = ' <h5>'.st_get_language('car_about').'</h5>
                      <p>'.get_post_meta(get_the_ID(),'cars_about',true).'</p>';
            }

            return '<div class="booking-item-deails-date-location">
                        <ul>
                            <li class="text-center">
                                '.$logo.'
                            </li>
                            <li>
                                <p class="f-20 text-center">'.get_post_meta(get_the_ID(),'cars_name',true).'</p>
                            </li>
                            <li>
                                <h5>'.st_get_language('car_phone').':</h5>
                                <p><i class="fa fa-phone box-icon-inline box-icon-gray"></i>'.get_post_meta(get_the_ID(),'cars_phone',true).'</p>
                            </li>
                             <li>
                                <h5>'.st_get_language('car_email').':</h5>
                                <p><i class="fa fa-envelope-o box-icon-inline box-icon-gray"></i>'.get_post_meta(get_the_ID(),'cars_email',true).'</p>
                            </li>
                            <li>
                                '.$about.'
                            </li>
                            <li>'.$pick_up.'</li>
                            <li>'.$drop_off.'</li>
                        </ul>
                        <a href="#search-dialog" data-effect="mfp-zoom-out" class="btn btn-primary popup-text" href="#">'.st_get_language('change_location_and_date').'</a>
                    </div>';
        }
    }
    st_reg_shortcode('st_detail_date_location_cars','st_detail_date_location_cars_func');
}

/**
* ST Car Video
* @since 1.1.0
**/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Car Video", ST_TEXTDOMAIN),
            'base' => 'st_car_video',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Car',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}
if(!function_exists('st_car_video'))
{
    function st_car_video($attr=array())
    {
        if(is_singular('st_cars'))
        {
            if($video=get_post_meta(get_the_ID(),'video',true)){
                return "<div class='media-responsive'>".wp_oembed_get($video)."</div>";
            }
        }
    }
}
st_reg_shortcode('st_car_video','st_car_video');

if (!function_exists('st_car_review')){
    function st_car_review(){
        if(is_singular('st_cars')){
            if (comments_open() and st()->get_option('car_review')){
                ob_start();
                    comments_template('/reviews/reviews.php');
                return @ob_get_clean();
            }

        }
    }
}
st_reg_shortcode('st_car_review' , 'st_car_review') ;

/**
 * ST Car Detail Map
 * @since 1.1.3
 **/
if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Detailed Car Map", ST_TEXTDOMAIN),
            'base' => 'st_cars_detail_map',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Car',
            'show_settings_on_create' => false,
            'params'=>array()
        )
    );
}

if(!function_exists('st_cars_detail_map'))
{
    function st_cars_detail_map()
    {
        if(is_singular('st_cars'))
        {
            /*$cars_address=get_post_meta(get_the_ID(),'cars_address',true);
            $html=' <div style="width:100%; height:500px;" class="st_google_map" data-type="1" data-address='.$cars_address.'></div>';
            return $html;*/
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
    st_reg_shortcode('st_cars_detail_map','st_cars_detail_map');
}
