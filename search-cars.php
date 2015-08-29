<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Search custom cars
     *
     * Created by ShineTheme
     *
     */
    get_header();
    global $wp_query,$st_search_query;
    $cars=new STCars();
    add_action('pre_get_posts',array($cars,'change_search_cars_arg'));
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    query_posts(
        array(
            'post_type'=>'st_cars',
            's'=>get_query_var('s'),
            'paged'     => $paged
        )
    );
$st_search_query=$wp_query;
    remove_action('pre_get_posts',array($cars,'change_search_cars_arg'));
    get_template_part('breadcrumb');
    $result_string='';
    echo st()->load_template('search-loading');


?>
<?php ?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('cars/search-form');?>
    </div>
    <div class="container">
        <h3 class="booking-title"><?php echo balanceTags($cars->get_result_string())?>
            <small><a class="popup-text" href="#search-dialog" data-effect="mfp-zoom-out"><?php st_the_language('change_search')?></a></small>
        </h3>
        <?php
            $cars_search_layout=st()->get_option('cars_layout_layout');
            if(!empty($_REQUEST['layout_id'])){
                $cars_search_layout = $_REQUEST['layout_id'];
            }
            if($cars_search_layout)
            {
                echo  STTemplate::get_vc_pagecontent($cars_search_layout);
            }else{
                echo st()->load_template('cars/search-default');
            }
        ?>
    </div>
<?php
    get_footer();
?>