<div class="row row-wrap">
<?php
$col = 12 / $st_col;
while(have_posts()){
    the_post();
?>
        <div class="col-md-<?php echo esc_attr($col); ?> col-sm-6 col-xs-12">
            <div class="thumb">
                <?php
                $page_search = st_get_page_search_result($st_type);
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type='.$st_type.'&location_id='.get_the_ID()));
                }
                ?>
                <a class="hover-img" href="<?php echo esc_url($link) ?>">
                    <?php
                    $img = get_the_post_thumbnail( get_the_ID() , array(800,600,'bfi_thumb'=>true)) ;
                    if(!empty($img)){
                        echo balanceTags($img);
                    }else{
                        echo '<img width="800" height="600" alt="no-image" class="wp-post-image" src="'.bfi_thumb(get_template_directory_uri().'/img/no-image.png',array('width'=>800,'height'=>600)) .'">';
                    }
                    ?>
                    <div class="hover-inner hover-inner-block hover-inner-bottom hover-inner-bg-black hover-hold">
                        <?php
                        $img = get_post_meta(get_the_ID(),'logo',true);
                        if($st_show_logo == 'yes' && !empty($img)){
                            ?>
                            <div class="img-<?php echo esc_attr($st_logo_position) ?>">
                                <img title="logo" alt="logo" src="<?php echo balanceTags($img) ?>">
                            </div>
                        <?php } ?>
                        <div class="text-small img-left">
                            <h5><?php the_title() ?></h5>
                            <?php
                            $min_price = get_post_meta(get_the_ID() , 'min_price_'.$st_type , true );
                            $offer = get_post_meta(get_the_ID() , 'offer_'.$st_type , true );
                            $review = get_post_meta(get_the_ID() , 'review_'.$st_type , true );
                            ?>
                            <?php if(!empty($review)){ ?>
                                <p>
                                    <?php echo esc_html($review) ?>
                                    <?php if($review > 1) _e('reviews',ST_TEXTDOMAIN); else _e('review',ST_TEXTDOMAIN); ?>
                                </p>
                            <?php } ?>
                            <?php  if(!empty($offer)){ ?>
                                <p class="mb0">
                                    <?php echo esc_html($offer) ?>
                                    <?php if($review > 1) _e('offers',ST_TEXTDOMAIN); else _e('offer',ST_TEXTDOMAIN);   ?>
                                    <?php _e('from',ST_TEXTDOMAIN); ?>
                                    <?php echo TravelHelper::format_money($min_price) ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>
<?php } ?>
</div>