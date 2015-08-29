<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Single foods
 *
 * Created by ShineTheme
 *
 */
get_header();
get_template_part('breadcrumb');
?>
<div class="container">
    <div class="booking-item-details no-border-top">
        <header class="booking-item-header">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="lh1em featured_single featured_single_food">
                        <?php the_title()?><?php echo STFeatured::get_featured(); ?>
                    </h2>
                    <p class="lh1em text-small"><i class="fa fa-map-marker"></i> <?php echo get_post_meta(get_the_ID(),'address',true) ?></p>
                    <ul class="list list-inline text-small">
                        <?php if($email=get_post_meta(get_the_ID(),'contact_email',true)):?>
                        <li><a href="mailto:<?php echo esc_url($email)?>"><i class="fa fa-envelope"></i> <?php st_the_language('Email')?></a>
                        </li>
                        <?php endif;?>
                        <?php if($website=get_post_meta(get_the_ID(),'website',true)):?>
                            <li><a target="_blank" href="<?php echo esc_url( $website )?>"> <i class="fa fa-home"></i> <?php st_the_language('Food Website')?></a>
                            </li>
                        <?php endif;?>
                        
                    </ul>
                </div>

            </div>
        </header>
        <?php
        echo $detail_tour_layout=apply_filters('st_tours_detail_layout',st()->get_option('tours_layout'));
        if($detail_tour_layout)
        {
            echo STTemplate::get_vc_pagecontent($detail_tour_layout);
        }else{
            //Default Layout
            echo st()->load_template('food/single','default');
        }
        ?>
    </div><!-- End .booking-item-details-->
</div>
<?php get_footer( ) ?>