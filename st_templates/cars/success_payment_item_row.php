<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars success payment item row
 *
 * Created by ShineTheme
 *
 */
$order_token_code=STInput::get('order_token_code');

if($order_token_code)
{
    $order_code=STOrder::get_order_id_by_token($order_token_code);

}
$object_id=$key;
$total=0;
$check_in=get_post_meta($order_code,'check_in',true);
$check_in_timestamp=get_post_meta($order_code,'check_in_timestamp',true);
$check_out=get_post_meta($order_code,'check_out',true);
$check_out_timestamp=get_post_meta($order_code,'check_out_timestamp',true);
$check_in_time=get_post_meta($order_code,'check_in_time',true);
$check_out_time=get_post_meta($order_code,'check_out_time',true);
$price=get_post_meta($order_code,'item_price',true);
$price_total=get_post_meta($order_code,'total_price',true);
$number=get_post_meta($order_code,'item_number',true);
$item_id=get_post_meta($order_code,'item_id',true);

$selected_equipments=get_post_meta($order_code,'selected_equipments',true);


?>
<tr>
    <td><?php echo esc_html($i) ?></td>
    <td  width="35%">
        <a href="<?php echo esc_url(get_the_permalink($object_id))?>" target="_blank">
        <?php echo get_the_post_thumbnail($key,array(360,270,'bfi_thumb'=>true),array('style'=>'max-width:100%;height:auto'))?>
        </a>
    </td>
    <td>
        <p><strong><?php st_the_language('booking_address') ?></strong> <?php echo get_post_meta($object_id,'cars_address',true)?> </p>
        <p><strong><?php st_the_language('booking_email') ?></strong> <?php echo get_post_meta($object_id,'cars_email',true)?> </p>
        <p><strong><?php st_the_language('booking_phone') ?></strong> <?php echo get_post_meta($object_id,'cars_phone',true)?> </p>
        <p><strong><?php st_the_language('booking_car') ?></strong> <?php  echo get_the_title($object_id)?></p>
        <p><strong><?php st_the_language('booking_amount') ?></strong> <?php echo esc_html($number)?></p>
        <p><strong><?php st_the_language('booking_price') ?></strong> <?php
            echo TravelHelper::format_money($price);
            ?> / <?php echo STCars::get_price_unit_by_unit_id(get_post_meta($order_code,'price_unit',true)) ?></p>
       <!-- <p><strong><?php /*st_the_language('booking_check_in') */?></strong> <?php /*echo @date(get_option('date_format'),strtotime($check_in)).' '.$check_in_time */?></p>
        <p><strong><?php /*st_the_language('booking_check_out') */?></strong> <?php /*echo @date(get_option('date_format'),strtotime($check_out)).' '.$check_out_time */?></p>-->
        <?php if($pickup=get_post_meta($order_code,'pick_up',true)): ?>
        <p><strong><?php _e("Pick-up: ") ?></strong> <?php echo esc_html($pickup) ?></p>
        <?php endif;?>
        <?php if($dropoff=get_post_meta($order_code,'drop_off',true)): ?>
        <p><strong><?php _e("Drop-off: ") ?></strong> <?php echo esc_html($dropoff)?></p>
        <?php endif;?>

        <p><strong><?php _e("Pick-up Time: ") ?></strong> <?php echo @date(get_option('date_format').get_option('time_format'),$check_in_timestamp) ?></p>
        <p><strong><?php _e("Drop-off Time: ") ?></strong> <?php echo @date(get_option('date_format').get_option('time_format'),$check_out_timestamp) ?></p>
        <p><strong><?php _e("Driver’s Name: ") ?></strong> <?php echo get_post_meta($order_code,'driver_name',true)?></p>
        <p><strong><?php _e("Driver’s Age: ") ?></strong> <?php echo get_post_meta($order_code,'driver_age',true)?></p>
        <?php if(!empty($selected_equipments)){
            ?>
            <p><strong><?php _e("Equipments: ") ?></strong>
                <ul>
                <?php foreach($selected_equipments as $equipment){
                    $price_unit='';
                    if(isset($equipment->price_unit) and $equipment->price_unit){
                        $price_unit=' ('.TravelHelper::format_money($equipment->price).'/'.st_car_price_unit_title($equipment->price_unit).')';
                    }
                    echo "<li>".$equipment->title.$price_unit." -> ".TravelHelper::format_money(STCars::get_equipment_line_item($equipment->price,$equipment->price_unit,$check_in_timestamp,$check_out_timestamp))."</li>";

            } ?>
                </ul>
            </p>

        <?php
        } ?>
    </td>
</tr>

<!--<tr>
    <td colspan="3">
        <div class="booking-item" style="border-color:#fff ">
            <div class="row">
                <div class="col-md-3">

                    <div class="booking-item-car-img">
                        <a href="<?php /*echo esc_url(get_the_permalink($object_id))*/?>" target="_blank">
                            <?php /*echo get_the_post_thumbnail($key,array(800,400,'bfi_thumb'=>true),array('style'=>'max-width:100%;height:auto'))*/?>
                        </a>
                        <a class="" href="<?php /*echo get_the_permalink($object_id)*/?>">
                            <p class="booking-item-car-title"><?php /* echo get_the_title($object_id)*/?></p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <strong><?php /*_e("Pick-up: ") */?></strong> <?php /*echo get_post_meta($order_code,'pick_up',true)*/?><br>
                    <strong><?php /*_e("Drop-off: ") */?></strong> <?php /*echo get_post_meta($order_code,'drop_off',true)*/?><br>
                    <strong><?php /*_e("Pick-up Time: ") */?></strong> <?php /*echo @date(get_option('date_format'),strtotime($check_in)).' '.$check_in_time */?><br>
                    <strong><?php /*_e("Drop-off Time: ") */?></strong> <?php /*echo @date(get_option('date_format'),strtotime($check_out)).' '.$check_out_time */?><br><br>
                    <strong><?php /*_e("Driver’s Name: ") */?></strong> <?php /*echo get_post_meta($order_code,'driver_name',true)*/?><br>
                    <strong><?php /*_e("Driver’s Age: ") */?></strong> <?php /*echo get_post_meta($order_code,'driver_age',true)*/?><br>
                </div>
                <div class="col-md-3">
                    <p><strong><?php /*st_the_language('booking_amount') */?></strong> <?php /*echo esc_html($number)*/?></p>
                    <p><strong><?php /*st_the_language('booking_price') */?></strong> <?php
/*                        echo TravelHelper::format_money($price);
                        */?> / <?php /*echo STCars::get_price_unit_by_unit_id(get_post_meta($order_code,'price_unit',true)) */?></p>

                </div>
            </div>
        </div>




    </td>

</tr>-->