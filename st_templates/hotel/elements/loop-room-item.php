<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel loop room item
 *
 * Created by ShineTheme
 *
 */

//check is booking with modal
$st_is_booking_modal=apply_filters('st_is_booking_modal',false);
?>
<li <?php post_class()?>>
    <div class="booking-item">
        <div class="row">
            <div class="col-md-3">
                <a href="#" class="hover-img">
                <?php
                if(has_post_thumbnail())
                {
                    the_post_thumbnail('full');
                }
                else
                {
                    if(function_exists('st_get_default_image'))
                        echo st_get_default_image();
                }
                ?>
                </a>
            </div>
            <div class="col-md-5">
                <h5 class="booking-item-title"><a href="<?php the_permalink(); ?>" title=""><?php the_title()?></a></h5>
                <div class="text-small">
                    <p style="margin-bottom: 10px;">
                    <?php
                    $excerpt=get_the_excerpt();
                    $excerpt=strip_tags($excerpt);
                    echo TravelHelper::cutnchar($excerpt,120);
                    ?>
                    </p>
                </div>

                <ul class="booking-item-features booking-item-features-sign clearfix">
                    <?php if($adult=get_post_meta(get_the_ID(),'adult_number',true)): ?>
                        <li rel="tooltip" data-placement="top" title="" data-original-title="<?php st_the_language('adults_occupany')?>"><i class="fa fa-male"></i><span class="booking-item-feature-sign">x <?php echo esc_html($adult) ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if($child=get_post_meta(get_the_ID(),'children_number',true)): ?>
                        <li rel="tooltip" data-placement="top" title="" data-original-title="<?php st_the_language('childs')?>"><i class="im im-children"></i><span class="booking-item-feature-sign">x <?php echo esc_html($child) ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if($bed=get_post_meta(get_the_ID(),'bed_number',true)): ?>
                        <li rel="tooltip" data-placement="top" title="" data-original-title="<?php st_the_language('bebs')?>"><i class="im im-bed"></i><span class="booking-item-feature-sign">x <?php echo esc_html($bed) ?></span>
                        </li>
                    <?php endif; ?>


                    <?php if($room_footage=get_post_meta(get_the_ID(),'room_footage',true)): ?>

                        <li rel="tooltip" data-placement="top" title="" data-original-title="<?php st_the_language('room_footage')?>"><i class="im im-width"></i><span class="booking-item-feature-sign"><?php echo esc_html($room_footage) ?></span>
                        </li>
                    <?php endif;?>
                </ul>
                <ul class="booking-item-features booking-item-features-small clearfix">
                    <?php get_template_part('single-hotel/room-facility','list') ;?>

                </ul>
            </div>
            <div class="col-md-4">
                <form method="post" >
                    <input name="check_in" value="<?php echo STInput::request('start') ?>" type="hidden">
                    <input name="check_out" value="<?php
                   echo STInput::request('end') ?>" type="hidden">

                    <input type="hidden" name="item_id" value="<?php echo get_post_meta(get_the_ID(),'room_parent',true)?>">
                    <input type="hidden" name="room_id" value="<?php echo get_the_ID()?>">
                    <input type="hidden" name="action" value="hotel_add_to_cart">
                    <input type="hidden" name="room_num_search" value="<?php echo (STInput::request('room_num_search'))?STInput::request('room_num_search'):1 ?>">
                    <input type="hidden" name="room_num_config" value="<?php echo esc_attr( serialize( STInput::post('room_num_config'))) ?>">


                    <input type="hidden" name="adult_num" value="<?php echo STInput::request('adult_num',1)?>">
                    <input type="hidden" name="child_num" value="<?php echo STInput::request('child_num',0)?>">
                    <?php
                        $start = TravelHelper::convertDateFormat(STInput::request('start'));
                        $end = TravelHelper::convertDateFormat(STInput::request('end'));
                        $is_search_room = STInput::request('is_search_room');
                    ?>
                    <?php if($start and $end and $is_search_room){ ?>
                        <?php
                            $data_price=STRoom::get_room_price(get_the_ID(),$start,$end);
                            $html_price = $data_price['price'] * STInput::request('room_num_search');
                            if($data_price['discount'] == true){
                                $html_price_old = $data_price['price_old'] * STInput::request('room_num_search');
                                echo '<span class="booking-item-old-price">'.TravelHelper::format_money($html_price_old).'</span>';
                            }
                        ?>
                        <input type="hidden" name="data_price" value='<?php echo serialize($data_price) ?>'>
                        <input name="price" value="<?php echo esc_attr($data_price['price']) ?>" type="hidden">
                        <br>
                        <span class="booking-item-price">
                            <?php echo TravelHelper::format_money($html_price)?>
                        </span>
                        <span>/ <?php echo esc_html($data_price['number_day']); ?>
                            <?php if($data_price['number_day'] > 1) _e('nights',ST_TEXTDOMAIN) ;else _e('night',ST_TEXTDOMAIN);?>
                        </span>
                        <br>
                        <?php


                        //Check booking modal
                        if($st_is_booking_modal){
                            echo '<a class="btn btn-primary btn_hotel_booking " data-target=#hotel_booking_'.get_the_ID().' data-effect="mfp-zoom-out" >'.st_get_language('book').'</a>';
                            ?>
                            <?php }else{ ?>
                            <?php echo STRoom::hotel_room_external_booking_submit( get_the_ID());?>
                            <!-- <button class="btn btn-primary btn_hotel_booking" type="submit"><?php st_the_language('book')?></button> -->
                        <?php }?>
                    <?php }else{ ?>
                        <button class="btn btn-primary btn-show-price" type="button"><?php _e("Show Price",ST_TEXTDOMAIN)?></button>
                    <?php } ?>
                </form>
                    <?php
                    if(st()->get_option('booking_modal','off')=='on'){?>
                        <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="hotel_booking_<?php the_ID()?>">
                            <?php echo st()->load_template('hotel/modal_booking');?>
                        </div>

                    <?php }?>

            </div>
        </div>
    </div>
</li>