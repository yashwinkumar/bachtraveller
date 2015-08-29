<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 12/15/14
 * Time: 11:23 AM
 */

if(function_exists('vc_map')){
    vc_map(
        array(
            'name' => __("ST Post Data", ST_TEXTDOMAIN),
            'base' => 'st_post_data',
            'content_element' => true,
            'icon' => 'icon-st',
            'category' => 'Shinetheme',
            'show_settings_on_create' => true,
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Data Type", ST_TEXTDOMAIN),
                    "param_name" => "field",
                    "description" =>"",
                    "value"=>array(
                        __("Content",ST_TEXTDOMAIN)=>'content',
                        __("Excerpt",ST_TEXTDOMAIN)=>'excerpt',
                        __("Title",ST_TEXTDOMAIN)=>'title',
                    )
                ),
            ),
        )
    );
}
if(!function_exists('st_post_data'))
{
    function st_post_data($attr=array())
    {
        $default=array(
            'field'=>'title',
            'post_id'=>false
        );


        extract(wp_parse_args($attr,$default));

        if(!$post_id and is_single())
        {
            $post_id=get_the_ID();
        }

        if($post_id and is_single()){
            switch($field)
             {
                    case "content":
                        $post=get_post($post_id);
                        $content=$post->post_content;
                        $content = apply_filters('the_content', $content);
                        $content = str_replace(']]>', ']]&gt;', $content);
                    return $content;
                    break;

                case "excerpt":
                    $post=get_post($post_id);
                    if(isset($post->post_excerpt))
                    {
                        return $post->post_excerpt;
                    }
                break;

                case "title":
                    return get_the_title($post_id);
                    break;

            }
        }

    }
}

st_reg_shortcode('st_post_data','st_post_data');