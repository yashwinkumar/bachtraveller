<?php
    if(function_exists('vc_map')){
        vc_map( array(
            "name" => __("ST Rental Search Results", ST_TEXTDOMAIN),
            "base" => "st_search_rental_result",
            "content_element" => true,
            "icon" => "icon-st",
            "category"=>"Shinetheme",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Style", ST_TEXTDOMAIN),
                    "param_name" => "style",
                    "description" =>"",
                    "value" => array(
                        __('Grid',ST_TEXTDOMAIN)=>'grid',
                        __('List',ST_TEXTDOMAIN)=>'list',
                    ),
                )
            )
        ) );
    }

    if(!function_exists('st_search_rental_result')){
        function st_search_rental_result($arg=array())
        {
            if(!get_post_type()=='st_rental' and get_query_var('post_type')!="st_rental") return;

            return st()->load_template('rental/search-elements/result',false,array('arg'=>$arg));
        }
    }

    st_reg_shortcode('st_search_rental_result','st_search_rental_result');
