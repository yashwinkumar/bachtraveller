<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Activity element loop 1
     *
     * Created by ShineTheme
     *
     */
    $info_price = STActivity::get_info_price();
    $price = $info_price['price'];
    $count_sale = $info_price['discount'];
    if(!empty($count_sale)){
        $price = $info_price['price'];
        $price_sale = $info_price['price_old'];
    }

?>
<li <?php post_class('booking-item') ?>>
    <?php echo STFeatured::get_featured(); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="booking-item-img-wrap">
                <?php the_post_thumbnail(array(360, 270, 'bfi_thumb' => true)) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="booking-item-rating">
                <ul class="icon-group booking-item-rating-stars">
                    <?php
                        $avg = STReview::get_avg_rate();
                        echo TravelHelper::rate_to_string($avg);
                    ?>
                </ul>
                                <span
                                    class="booking-item-rating-number"><b><?php echo esc_html( $avg) ?></b> <?php st_the_language('of') ?> 5</span>
                <small>
                    (<?php comments_number(st_get_language('no_review'),st_get_language('1_review'),"% ".st_get_language('reviews') )?>)
                </small>
            </div>
            <a class="" href="<?php the_permalink() ?>">
                <h5 class="booking-item-title"><?php the_title() ?></h5>
            </a>
            <?php if ($address = get_post_meta(get_the_ID(), 'address', true)): ?>
                <p class="booking-item-address"><i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
                </p>
            <?php endif; ?>

            <div class="package-info">
                <i class="fa fa-calendar"></i>
                <span class=""><?php st_the_language('date') ?> : </span>
                <?php
                    $check_in = get_post_meta(get_the_ID() , 'check_in' ,true);
                    if(!empty($check_in)){
                        $check_in = strtotime($check_in);
                        echo date('m/d/Y',$check_in);
                    }

                    $check_out = get_post_meta(get_the_ID() , 'check_out' ,true);
                    if(!empty($check_out)){
                        $check_out = strtotime($check_out);
                        echo ' <i class="fa fa-arrow-right"></i> '.date('m/d/Y',$check_out);
                    }
                ?>
            </div>
            <p class="booking-item-description">
                <?php echo st_get_the_excerpt_max_charlength(100) ?>
            </p>
        </div>
        <div class="col-md-3">

            <span class="booking-item-price-from"><?php st_the_language('from') ?></span>
            <?php echo STActivity::get_price_html(get_the_ID(),false,'<br>','booking-item-price'); ?>

            <a href="<?php the_permalink()?>"><span class="btn btn-primary btn_book"><?php st_the_language('book_now')?></span></a>
            <?php if(!empty($count_sale)){ ?>
                <span class="box_sale sale_small btn-primary"> <?php echo esc_html($count_sale) ?>% </span>
            <?php } ?>
        </div>
    </div>
</li>
