<?php
    if(function_exists('vc_map')){
        vc_map( array(
            "name" => __("ST Tour Detail Attribute", ST_TEXTDOMAIN),
            "base" => "st_tour_detail_attribute",
            "content_element" => true,
            "icon" => "icon-st",
            "category"=>'Shinetheme',
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Select Taxonomy", ST_TEXTDOMAIN),
                    "param_name" => "taxonomy",
                    "description" =>"",
                    "value" => st_list_taxonomy('st_tours'),
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Item Size", ST_TEXTDOMAIN),
                    "param_name" => "item_col",
                    "description" =>"",
                    "value" => array(
                        2=>2,
                        3=>3,
                        4=>4,
                        5=>5,
                        6=>6,
                        7=>7,
                        8=>8,
                        9=>9,
                        10=>10,
                        11=>11,
                        12=>12,
                    ),
                )
            )
        ) );
    }

    if(!function_exists('st_tour_detail_attribute')){
        function st_tour_detail_attribute($attr,$content=false)
        {
            if(is_singular('st_tours'))
            {
                return st()->load_template('tours/elements/attribute',null,array('attr'=>$attr));
            }
        }
    }
    st_reg_shortcode('st_tour_detail_attribute','st_tour_detail_attribute');