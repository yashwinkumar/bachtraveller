<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Activity cart item html
 *
 * Created by ShineTheme
 *
 */
if(isset($item_id) and $item_id):

    $item=STCart::find_item($item_id);
    $id_activity=$item_id;

    $check_in=strtotime($item['data']['check_in']);
    $check_out=strtotime($item['data']['check_out']);



    $price  = $item['price'];
    if(!empty($item['data']['discount'])){
        $count_sale = $item['data']['discount'];
        $price = $item['data']['price_old'];
        $price_sale = $item['data']['price_sale'];
    }
    $type_price=$item['data']['type_price'];
    if($type_price=='people_price')
    {
        $adult_num=$item['data']['adult_number'];
        $child_num=$item['data']['child_number'];
        $adult_price=$item['data']['adult_price'];
        $child_price=$item['data']['child_price'];
        $adult_price = get_price_by_discount_person($id_tours ,$adult_price, $adult_num , true);
        $child_price = get_price_by_discount_person($id_tours ,$child_price, $child_num , false);
    }
    $price = $price * $item['number'];
    ?>
    <header class="clearfix" style="position: relative">
        <?php if(!empty($count_sale)){ ?>
            <span class="box_sale btn-primary sale_small sale_check_out"> <?php echo esc_html($count_sale) ?>% </span>
        <?php } ?>
        <?php if(get_post_status($id_activity)):?>
        <a class="booking-item-payment-img" href="#">
            <?php echo get_the_post_thumbnail($id_activity,array(98,74,'bfi_thumb'=>true));?>
        </a>
        <h5 class="booking-item-payment-title"><a href="<?php echo get_permalink($id_activity)?>"><?php echo get_the_title($id_activity)?></a></h5>
        <ul class="icon-group booking-item-rating-stars">
            <?php echo TravelHelper::rate_to_string(STReview::get_avg_rate($id_activity)); ?>
        </ul>
        <?php
        else: st_the_language('sorry_activity_not_found');
        endif;?>
    </header>
    <ul class="booking-item-payment-details">
        <?php if(!empty($check_in) and !empty($check_out)){ ?>
        <li>
            <h5><?php  st_the_language('activity'); ?></h5>
            <div class="booking-item-payment-date">
                <p class="booking-item-payment-date-day"><?php echo date('M, d',($check_in) );?></p>
                <p class="booking-item-payment-date-weekday"><?php echo date('l',($check_in) ); ?></p>
            </div>
            <i class="fa fa-arrow-right booking-item-payment-date-separator"></i>
            <div class="booking-item-payment-date">
                <p class="booking-item-payment-date-day"><?php echo date('M, d',($check_out))?></p>
                <p class="booking-item-payment-date-weekday"><?php echo date('l',($check_out)) ?></p>
            </div>
        </li>
        <?php } ?>
        <li>
            <h5><?php  st_the_language('event'); ?></h5>
            <ul class="booking-item-payment-price">
                <?php if($type_price=='people_price'):?>
                    <li>
                        <p class="booking-item-payment-price-title"><?php _e('Adult Price',ST_TEXTDOMAIN) ?> </p>
                        <p class="booking-item-payment-price-amount"><?php echo ($adult_num).' x '.st_get_discount_value($adult_price,$count_sale)  ?></small>
                        </p>
                    </li>
                    <li>
                        <p class="booking-item-payment-price-title"><?php _e('Children Price',ST_TEXTDOMAIN) ?> </p>
                        <p class="booking-item-payment-price-amount"><?php echo ($child_num).' x '.st_get_discount_value($child_price,$count_sale)  ?></small>
                        </p>
                    </li>
                <?php else: ?>
                    <li>
                        <?php if(!empty($item['data']['discount'])){ ?>
                            <p class="booking-item-payment-price-title"><?php st_the_language('guests'); ?><?php st_the_language('price')?> </p>
                            <p class="booking-item-payment-price-amount">
                                <span class="onsale"><?php echo TravelHelper::format_money($price)?></span>
                                <i class="fa fa-arrow-right "></i>
                                <strong><?php echo TravelHelper::format_money($price_sale) ?></strong>
                            </p>
                        <?php }else{ ?>
                            <p class="booking-item-payment-price-title"><?php st_the_language('price')?></p>
                            <p class="booking-item-payment-price-amount"><?php echo TravelHelper::format_money($item['price']) ?><small>/<?php st_the_language('activity')?></small>
                            </p>
                        <?php } ?>
                    </li>
                    <li>
                        <p class="booking-item-payment-price-title"><?php st_the_language('guests'); ?></p>
                        <p class="booking-item-payment-price-amount"><?php echo esc_html($item['number'])?></small>
                        </p>
                    </li>
                 <?php endif ?>
            </ul>
        </li>
    </ul>
    <?php
endif;