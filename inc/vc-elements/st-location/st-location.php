<?php
    if(function_exists('vc_map')){
        vc_map( 
            array(
            "name" => __("ST Location", ST_TEXTDOMAIN),
            "base" => "st_location",
            "content_element" => true,
            "icon" => "icon-st",
            "category"=>"Shinetheme",
            "params"    =>array(
                array(
                    "type"  =>"dropdown",
                    "holder"=>"div",
                    "heading"=>__("Location item custom", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_custom_type",
                    "description" =>__("Select <b>Custom</b> if you want custom Location infomation text<br> OR not we get all infomations of location ",ST_TEXTDOMAIN),
                    "value"     =>array(                        
                        __('Normal', ST_TEXTDOMAIN) => 'normal',
                        __('Custom', ST_TEXTDOMAIN) => 'custom',
                        )
                    ),
                array(
                    "type"  =>"textfield",
                    "holder"=>"div",
                    "heading"=>__("From price custom", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_price_custom",
                    "description" =>__("Your custom from price ",ST_TEXTDOMAIN),
                    "dependency"    =>
                        array(
                            "element"   =>"st_location_custom_type",
                            "value"     =>"custom"
                        ),
                ),
                array(
                    "type"  =>"textfield",
                    "holder"=>"div",
                    "heading"=>__("Offers number", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_number_offers",
                    "description" =>__("Your custom offers number ",ST_TEXTDOMAIN),
                    "dependency"    =>
                        array(
                            "element"   =>"st_location_custom_type",
                            "value"     =>"custom"
                        ),
                ),
                array(
                    "type"  =>"textfield",
                    "holder"=>"div",
                    "heading"=>__("Reviews number", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_number_reviews",
                    "description" =>__("Your custom reviews number ",ST_TEXTDOMAIN),
                    "dependency"    =>
                        array(
                            "element"   =>"st_location_custom_type",
                            "value"     =>"custom"
                        ),
                ),
                
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Your post type", ST_TEXTDOMAIN),
                    "param_name" => "st_location_post_type",
                    "description" =>__("Your post type",ST_TEXTDOMAIN),
                    "value" => array(
                        __('Hotel', ST_TEXTDOMAIN) => 'st_hotel',
                        __('Car', ST_TEXTDOMAIN) => 'st_cars',
                        __('Rental', ST_TEXTDOMAIN) => 'st_rental',
                        __('Activity', ST_TEXTDOMAIN) => 'st_activity',
                        __('Tour', ST_TEXTDOMAIN) => 'st_tours',
                    ),
                    "dependency"    =>
                        array(
                            "element"   =>"st_location_custom_type",
                            "value"     =>"normal"
                        ),
                ),
                array(
                    "type" => "attach_image",
                    "holder" => "div",
                    "heading" => __("Your post type thumbnail", ST_TEXTDOMAIN),
                    "param_name" => "st_location_post_type_thumb",
                    "description" =>__("Your post type thumbnail",ST_TEXTDOMAIN),
                ),
            )
            
        ) );
    }

    if (!function_exists('st_location_func')){
        function st_location_func($attr){

            $data = shortcode_atts(
                array(          
                'st_location_custom_type'=>'',
                'st_location_post_type'=>'st_hotel',
                'st_location_number_offers'=>'',
                'st_location_number_reviews'=>'',
                'st_location_price_custom'=>'',
                'st_location_post_type_thumb'=>''
                ), $attr, 'st_location' );
            extract($data);
            
            if (!is_singular('location')){return ; }

            $post_type = $st_location_post_type ; 

            if ($st_location_custom_type =="custom"){
                $array = array(
                    'post_type'=>       $st_location_post_type,
                    'thumb'=>       $st_location_post_type_thumb ,
                    'post_type_name'=>      get_post_type_object($post_type)->labels->name,
                    'reviews'=>     $st_location_number_reviews,
                    'offers'=>      $st_location_number_offers,
                    'min_max_price'=>  array(
                        'price_min'=>$st_location_price_custom ,
                        )     
                    );
            }
            else {
                // get infomation from location ID
                $array = STLocation::get_info_by_post_type(get_the_ID(), $post_type);
                $array['thumb']= $attr['st_location_post_type_thumb'] ;
                $array['post_type']=$attr['st_location_post_type'];
            }
            return st()->load_template('location/location-content-item' , null, $array ) ;
            
        }
        st_reg_shortcode('st_location','st_location_func');
    }
    /**
    * @since 1.1.3
    * @Description build Location page header
    *
    */
    if (function_exists('vc_map')){
        
        vc_map( array(
            "name" => __("ST Location header rate count", ST_TEXTDOMAIN),
            "base" => "st_location_header_rate_count",
            "content_element" => true,
            "icon" => "icon-st",
            "params" => array(
                // add params same as with any other content element
                array(
                    "type"  =>"checkbox",
                    "holder"=>"div",
                    "heading"=>__("Post type select ?", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_header_rate_count_post_type",
                    "description" =>__("Select your post types which you want ?",ST_TEXTDOMAIN),    
                    "value" => array(
                        __('Hotel', ST_TEXTDOMAIN) => 'st_hotel',
                        __('Car', ST_TEXTDOMAIN) => 'st_cars',
                        __('Rental', ST_TEXTDOMAIN) => 'st_rental',
                        __('Activity', ST_TEXTDOMAIN) => 'st_activity',
                        __('Tour', ST_TEXTDOMAIN) => 'st_tours',
                    )            
                ),     
                          

            )
        ) );
        vc_map( array(
            "name" => __("ST statistical Location header", ST_TEXTDOMAIN),
            "base" => "st_location_header_static",
            "content_element" => true,
            "icon" => "icon-st",
            "params" => array(
                // add params same as with any other content element
                array(
                    "type"  =>"checkbox",
                    "holder"=>"div",
                    "heading"=>__("Post type select ?", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_header_rate_count_to",
                    "description" =>__("Select your post types",ST_TEXTDOMAIN),    
                    "value" => array(
                        __('Hotel', ST_TEXTDOMAIN) => 'st_hotel',
                        __('Car', ST_TEXTDOMAIN) => 'st_cars',
                        __('Rental', ST_TEXTDOMAIN) => 'st_rental',
                        __('Activity', ST_TEXTDOMAIN) => 'st_activity',
                        __('Tour', ST_TEXTDOMAIN) => 'st_tours',
                    )            
                ),
                array(
                    "type"  =>"checkbox",
                    "holder"=>"div",
                    "heading"=>__("Select star list ", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_star_list",
                    "description" =>__("Select star list to static and show",ST_TEXTDOMAIN),    
                    "value" => array(
                        __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> (5)<br> ', ST_TEXTDOMAIN) => '5',
                        __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> (4)<br> ', ST_TEXTDOMAIN) => '4',
                        __('<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> (3)<br> ', ST_TEXTDOMAIN) => '3',
                        __('<i class="fa fa-star"></i><i class="fa fa-star"></i> (2) <br> ', ST_TEXTDOMAIN) => '2',
                        __('<i class="fa fa-star"></i> (1)  ', ST_TEXTDOMAIN) => '1',
                    )            
                ),
            )
        ) );
        
    }
    
    if ( class_exists( 'WPBakeryShortCodesContainer' ) and !class_exists('WPBakeryShortCode_st_location_header_rate_count') ) {
        class WPBakeryShortCode_st_location_header_rate_count extends WPBakeryShortCodesContainer {
            protected function content($arg, $content = null) {
                $content_data= st_remove_wpautop($content);
                return st()->load_template('vc-elements/st-location/location' , 'header-rate-count' , $arg); 
            }
        }
    }
    if ( class_exists( 'WPBakeryShortCodesContainer' ) and !class_exists('WPBakeryShortCode_st_location_header_static') ) {
        class WPBakeryShortCode_st_location_header_static extends WPBakeryShortCodesContainer {
            protected function content($arg, $content = null) {
                $content_data= st_remove_wpautop($content);
                return st()->load_template('vc-elements/st-location/location' , 'header-static' , $arg); 
            }
        }
    }

    /**
    * @since 1.1.3
    * @Description build Location page content
    * create a vc_tab and get Shinetheme element into here 
    */
    if (function_exists('vc_map')){
        /**
        * @since 1.1.3
        * St location information slider 
        */
        vc_map(
            array(
            "name" => __("ST Location slider ", ST_TEXTDOMAIN),
            "base" => "st_location_slider",
            "content_element" => true,
            "icon" => "icon-st",
            "category"=>"Shinetheme",
            "params"    =>array(
                array(
                    "type"  =>"attach_images",
                    "holder"=>"div",
                    "heading"=>__("Gallery slider ", ST_TEXTDOMAIN), // 
                    "param_name" => "st_location_list_image"             
                    
                )
            )
        )
        );
        
        if (!function_exists('st_location_infomation_func')){
            function st_location_infomation_func($attr){
                return STLocation::get_slider($attr['st_location_list_image']);
            }
            st_reg_shortcode('st_location_slider','st_location_infomation_func' );

        };   
        $params = array(

            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Style", ST_TEXTDOMAIN),
                "param_name" => "st_location_style",
                "description" =>"Default style",
                'value'=> array(
                    __('List',ST_TEXTDOMAIN)=>'list',
                    __('Grid',ST_TEXTDOMAIN)=>'grid')
            ),
            array(
                "type" => "textfield",
                "holder" => "div",
                "heading" => __("Count item to display", ST_TEXTDOMAIN),
                "param_name" => "st_location_num",
                "description" =>"Number of items display",
                'value'=>4,
            ),
            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Order By", ST_TEXTDOMAIN),
                "param_name" => "st_location_orderby",
                "description" =>"",
                'value'=>st_get_list_order_by()
            ),
            array(
                "type" => "dropdown",
                "holder" => "div",
                "heading" => __("Order",ST_TEXTDOMAIN),
                "param_name" => "st_location_order",
                'value'=>array(
                    __('Asc',ST_TEXTDOMAIN)=>'asc',
                    __('Desc',ST_TEXTDOMAIN)=>'desc'
                ),
                "description" => __("",ST_TEXTDOMAIN)
            )
        );
        vc_map(
            array(
                "name" => __("ST Location list car ", ST_TEXTDOMAIN),
                "base" => "st_location_list_car",
                "content_element" => true,
                "icon" => "icon-st",
                "category"=>"Shinetheme",
                "params"    =>$params
            ));
        vc_map(
            array(
                "name" => __("ST Location list hotel ", ST_TEXTDOMAIN),
                "base" => "st_location_list_hotel",
                "content_element" => true,
                "icon" => "icon-st",
                "category"=>"Shinetheme",
                "params"    =>$params
            ));
        vc_map(
            array(
                "name" => __("ST Location list rental ", ST_TEXTDOMAIN),
                "base" => "st_location_list_rental",
                "content_element" => true,
                "icon" => "icon-st",
                "category"=>"Shinetheme",
                "params"    =>$params
            ));
        vc_map(
            array(
                "name" => __("ST Location list activity ", ST_TEXTDOMAIN),
                "base" => "st_location_list_activity",
                "content_element" => true,
                "icon" => "icon-st",
                "category"=>"Shinetheme",
                "params"    =>$params
            ));
        vc_map(
            array(
                "name" => __("ST Location list tour ", ST_TEXTDOMAIN),
                "base" => "st_location_list_tour",
                "content_element" => true,
                "icon" => "icon-st",
                "category"=>"Shinetheme",
                "params"    =>$params
            )
        );
        if (!function_exists('st_location_list_car_func')){
            function st_location_list_car_func($attr){
                $data = shortcode_atts(
                array( 
                    'st_location_style'=>"",
                    'st_location_num'=>"",
                    'st_location_orderby'=>"",
                    'st_location_order'=>""
                ), $attr, 'st_location_list_car' );
                extract($data);
                $return ; 
                $query=array(
                    'post_type' => 'st_cars',
                    'meta_key' => 'id_location',
                    'meta_value' => get_the_ID(),
                    'posts_per_page'=>$st_location_num,
                    'order'=>$st_location_order,
                    'orderby'=>$st_location_orderby,
                );
                if (STInput::request('style')){$st_location_style = STInput::request('style');};

                if ($st_location_style =="list"){
                    $return .='<ul class="booking-list loop-cars style_list">' ; 
                }else {
                    $return .='<div class="row row-wrap">';
                }
                
                query_posts($query);

                while(have_posts()){
                    the_post();
                    if ($st_location_style =="list"){
                            $return .=st()->load_template('cars/elements/loop/loop-1');
                        }else {
                            $return .=st()->load_template('cars/elements/loop/loop-2');
                        }
                }

                wp_reset_query();

                if ($st_location_style =="list"){
                    $return .='</ul>' ; 
                }else {
                    $return .="</div>";
                }

                $link = STLocation::get_total_text_footer('st_cars');
                $return .= balancetags($link);                

                return $return ;
            }
            st_reg_shortcode('st_location_list_car','st_location_list_car_func' );
        };
        if (!function_exists('st_location_list_hotel_func')){
            function st_location_list_hotel_func($attr){
                $data = shortcode_atts(
                array( 
                    'st_location_style'=>"",
                    'st_location_num'=>"",
                    'st_location_orderby'=>"",
                    'st_location_order'=>""
                ), $attr, 'st_location_list_car' );
                extract($data);

                if (STInput::request('style')){$st_location_style = STInput::request('style');};

                $return ;
                $query=array(
                    'post_type' => 'st_hotel',
                    'posts_per_page'=>$st_location_num,
                    'order'=>$st_location_order,
                    'orderby'=>$st_location_orderby,
                    'post_status'=>'publish',
                    'meta_key'=>'id_location',
                    'meta_value'=>get_the_ID()
                );
                $data['query'] = $query;   
                $data['style'] =$st_location_style;           
                $return .=st()->load_template('vc-elements/st-location/location','list-hotel',$data);              

                $link = STLocation::get_total_text_footer('st_hotel');
                $return .= balancetags($link);

                return $return; 

            }
            st_reg_shortcode('st_location_list_hotel','st_location_list_hotel_func' );
        };
        if (!function_exists('st_location_list_tour_func')){
            function st_location_list_tour_func($attr){
                $data = shortcode_atts(
                array( 
                    'st_location_style'=>"",
                    'st_location_num'=>"",
                    'st_location_orderby'=>"",
                    'st_location_order'=>""
                ), $attr, 'st_location_list_car' );
                extract($data);
                $return ; 
                $query=array(
                    'post_type' => 'st_tours',
                    'meta_key' => 'id_location',
                    'meta_value' => get_the_ID(),
                    'posts_per_page'=>$st_location_num,
                    'order'=>$st_location_order,
                    'orderby'=>$st_location_orderby,
                    'post_status'=>'publish',
                );

                if (STInput::request('style')){$st_location_style = STInput::request('style');};

                if($st_location_style == 'list'){
                    $return .="<ul class='booking-list loop-tours style_list loop-tours-location'>";
                }
                else{
                    $return .='<div class="row row-wrap grid-tour-location">';
                }
                $query = new Wp_Query($query);

                while($query->have_posts()){
                    $query->the_post();
                    if($st_location_style == 'list'){
                        $return .=st()->load_template('tours/elements/loop/loop-1',null , array('tour_id'=>get_the_ID()));
                    }
                    else{
                        $return .=  st()->load_template('tours/elements/loop/loop-2',null, array('tour_id'=>get_the_ID()));
                    }
                }
                wp_reset_query();
                if($st_location_style == 'list'){
                    $return .="</ul>";
                }
                else{
                    $return .="<div>";
                }

                $link = STLocation::get_total_text_footer('st_tours');
                $return .= balancetags($link);
                return $return ;

            }
            st_reg_shortcode('st_location_list_tour','st_location_list_tour_func' );
        };
        if (!function_exists('st_location_list_rental_func')){
            function st_location_list_rental_func($attr){               
                $data = shortcode_atts(
                array( 
                    'st_location_style'=>"",
                    'st_location_num'=>"",
                    'st_location_orderby'=>"",
                    'st_location_order'=>""
                ), $attr, 'st_location_list_car' );
                extract($data);
                $return ; 
                $query=array(
                    'post_type' => 'st_rental',
                    'posts_per_page'=>$st_location_num,
                    'order'=>$st_location_order,
                    'orderby'=>$st_location_orderby,
                    'post_status'=>'publish',
                    'meta_value'=>get_the_ID()
                );
                if (STInput::request('style')){$st_location_style = STInput::request('style');};

                if($st_location_style == 'list'){
                    $return .="<ul class='booking-list loop-tours style_list loop-rental-location'>";
                }
                else{
                    $return .='<div class="row row-wrap grid-rental-location">';
                }
                $data = array (
                    'query'=>$query,
                    'style'=>$st_location_style
                    );
                $return .=st()->load_template('vc-elements/st-location/location-list' , 'rental', $data);

                if($st_location_style == 'list'){
                    $return .="</ul>";
                }
                else{
                    $return .='</div>';
                }    

                $link = STLocation::get_total_text_footer('st_rental');
                $return .= balancetags($link);
                return $return ;
            }
            st_reg_shortcode('st_location_list_rental','st_location_list_rental_func' );
        };
        if (!function_exists('st_location_list_activity_func')){
            function st_location_list_activity_func($attr){
                $data = shortcode_atts(
                array( 
                    'st_location_style'=>"",
                    'st_location_num'=>"",
                    'st_location_orderby'=>"",
                    'st_location_order'=>""
                ), $attr, 'st_location_list_car' );
                extract($data);
                $return ; 
                $query=array(
                    'post_type' => 'st_activity',
                    'meta_key' => 'id_location',
                    'meta_value' => get_the_ID(),
                    'posts_per_page'=>$st_location_num,
                    'order'=>$st_location_order,
                    'orderby'=>$st_location_orderby,
                    'post_status'=>'publish',
                );
                if (STInput::request('style')){$st_location_style = STInput::request('style');};

                if($st_location_style == 'list'){
                    $return .="<ul class='booking-list loop-tours style_list loop-activity-location'>";
                }
                else{
                    $return .='<div class="row row-wrap grid-activity-location">';
                }
                query_posts($query);
                while(have_posts()){
                    the_post();
                    if($st_location_style == 'list'){
                        $return .=st()->load_template('activity/elements/loop/loop-1' ,null , array('is_location'=>true) );
                    }
                    else{
                        $return .=st()->load_template('activity/elements/loop/loop-2' ,null , array('is_location'=>true) );
                    }
                }
                wp_reset_query();

                if($st_location_style == 'list'){
                    $return .="</ul>";
                }
                else{
                    $return .='</div>';
                }

                $link = STLocation::get_total_text_footer('st_activity');
                $return .= balancetags($link);
                return $return ;

            }
            st_reg_shortcode('st_location_list_activity','st_location_list_activity_func' );
        };
                
    }
