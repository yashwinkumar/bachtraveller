<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Search custom rental
     *
     * Created by ShineTheme
     *
     */
    get_header();
    global $wp_query,$st_search_query;
    $object=new STRental();
    add_action('pre_get_posts',array($object,'change_search_arg'));
    query_posts(
        array(
            'post_type'=>'st_rental',
            's'=>get_query_var('s'),
            'paged'     => get_query_var('paged')
        )
    );
$st_search_query=$wp_query;
    remove_action('pre_get_posts',array($object,'change_search_arg'));
    get_template_part('breadcrumb');
    $result_string='';
    echo st()->load_template('search-loading');
?>
<?php ?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('rental/search-form');?>
    </div>
    <div class="container mb20">
        <?php
            $layout=STInput::get('layout');
            if(!$layout){
                $layout=st()->get_option('rental_search_layout');
            }
            if($layout and is_string( get_post_status( $layout ) )){
                $content=STTemplate::get_vc_pagecontent($layout);
                echo balanceTags( $content);
            }else{
                ?>
                <h3 class="booking-title"><?php echo balanceTags($object->get_result_string())?>
                    <small><a class="popup-text" href="#search-dialog" data-effect="mfp-zoom-out"><?php st_the_language('change_search')?></a></small>
                </h3>
                <div class="row">
                    <?php $sidebar_pos=apply_filters('st_rental_sidebar','no');
                        if($sidebar_pos=="left"){
                            get_sidebar('rental');
                        }
                    ?>
                    <div class="<?php echo apply_filters('st_hotel_sidebar','no')=='no'?'col-md-12':'col-md-9'; ?> col-sm-7">
                        <?php echo st()->load_template('rental/search-elements/result')?>
                    </div>
                    <?php
                        $sidebar_pos=apply_filters('st_rental_sidebar','no');
                        if($sidebar_pos=="right"){
                            get_sidebar('rental');
                        }
                    ?>
                </div>
            <?php }?>
    </div>
<?php

    get_footer();
?>