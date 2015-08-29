<?php
    /**
     * Template Name: Activity Search Result
     */
    global $wp_query, $st_search_query;
    $old_page_content = '';
    while (have_posts()) {
        the_post();
        $old_page_content = get_the_content();
    }
    get_header();
    $activity = new STActivity();
    add_action('pre_get_posts', array($activity, 'change_search_activity_arg'));
    query_posts(
        array(
            'post_type' => 'st_activity',
            's'         => '',
            'paged'     => get_query_var('paged')
        )
    );
    $st_search_query = $wp_query;
    remove_action('pre_get_posts', array($activity, 'change_search_activity_arg'));
    echo st()->load_template('search-loading');
    get_template_part('breadcrumb');
    $result_string = '';
?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
        <?php echo st()->load_template('activity/search-form-2'); ?>
    </div>
    <div class="container mb20">
        <?php echo apply_filters('the_content', $old_page_content); ?>
    </div>
<?php
    wp_reset_query();
    get_footer();
?>