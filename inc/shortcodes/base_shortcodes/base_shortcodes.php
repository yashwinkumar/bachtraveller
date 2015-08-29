<?php

    if(!function_exists('st_sc_custom_meta'))
    {
        function st_sc_custom_meta($attr,$content=false)
        {
            $data = shortcode_atts(
                array(
                    'key' =>''
                ), $attr, 'st_custom_meta' );
            extract($data);
            if(!empty($key)){
                $data = get_post_meta(get_the_ID() , $key  ,true);
                return balanceTags($data);
            }

        }
        st_reg_shortcode('st_custom_meta','st_sc_custom_meta');
    }





