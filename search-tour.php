<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Search custom tours
     *
     * Created by ShineTheme
     *
     */
    get_header();
    global $wp_query,$st_search_query;
    $object=new STTour();
    add_action('pre_get_posts',array($object,'change_search_tour_arg'));
    query_posts(
        array(
            'post_type'=>'st_tours',
            's'=>get_query_var('s'),
            'paged'     => get_query_var('paged')
        )
    );
$st_search_query=$wp_query;
    remove_action('pre_get_posts',array($object,'change_search_tour_arg'));
    get_template_part('breadcrumb');
    $result_string='';
    echo st()->load_template('search-loading');
?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('tours/search-form');?>
    </div>
    <div class="container">
        <h3 class="booking-title"><?php echo balanceTags($object->get_result_string())?>
            <small><a class="popup-text" href="#search-dialog" data-effect="mfp-zoom-out"><?php st_the_language('change_search')?></a></small>
        </h3>
        <?php
            $tours_layout_layout=st()->get_option('tours_search_layout');
            if(!empty($_REQUEST['layout_id'])){
                $tours_layout_layout = $_REQUEST['layout_id'];
            }
            if($tours_layout_layout){
                echo  STTemplate::get_vc_pagecontent($tours_layout_layout);
            }
        ?>
    </div>
<?php
    get_footer();
?>