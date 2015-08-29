<?php
    /**
     * Template Name: Tour Search Result
     */
    global $wp_query, $st_search_query;
    $old_page_content = '';
    while (have_posts()) {
        the_post();
        $old_page_content = get_the_content();
    }
    get_header();
    $tour = new STTour();
    add_action('pre_get_posts', array($tour, 'change_search_tour_arg'));
    query_posts(
        array(
            'post_type' => 'st_tours',
            's'         => '',
            'paged'     => get_query_var('paged')
        )
    );
    $st_search_query = $wp_query;
//var_dump($st_search_query->request);
    remove_action('pre_get_posts', array($tour, 'change_search_tour_arg'));
    echo st()->load_template('search-loading');
    get_template_part('breadcrumb');
    $result_string = '';
?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('tours/search-form-2');?>
    </div>
    <div class="container mb20">
        <?php echo apply_filters('the_content', $old_page_content); ?>
    </div>
<?php
    wp_reset_query();
    get_footer();
?>