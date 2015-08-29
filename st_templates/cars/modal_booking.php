<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars modal booking
 *
 * Created by ShineTheme
 *
 */
$car=new STCars();
$paypal_allow=false;

if(class_exists('STPaypal'))
{
    $paypal_allow=true;
}

$field_list=$car->get_search_fields_box();
$field_type=$car->get_search_fields_name();

//Logged in User Info
global $firstname , $user_email;
get_currentuserinfo();



$paypal_allow=apply_filters('st_checkout_paypal_allow',$paypal_allow);


?>
<h3><?php printf(st_get_language('you_are_booking_for_s'),get_the_title())?></h3>

<div id="booking_modal_<?php echo get_the_ID() ?>" class="booking_modal_form">
    <?php
        wp_nonce_field('submit_form_order','travel_order');

    ?>

    <?php
    $info_price = STCars::get_info_price();
    $cars_price = $info_price['price'];
    $count_sale = $info_price['discount'];
    if(!empty($count_sale)){
        $price = $info_price['price'];
        $price_sale = $info_price['price_old'];
    }

    $pick_up_date=TravelHelper::convertDateFormat(STInput::request('pick-up-date'));
    if(!$pick_up_date){
        $pick_up_date = date('m/d/Y', strtotime("now"));
    }
    $drop_off_date=TravelHelper::convertDateFormat(STInput::request('drop-off-date'));
    if(!$drop_off_date){
        $drop_off_date = date('m/d/Y', strtotime("+1 day"));
    }
    $pick_up_time = STInput::request('pick-up-time','12:00 PM');
    $drop_off_time = STInput::request('drop-off-time','12:00 PM');
    $pick_up=STInput::request('pick-up');
    $drop_off=STInput::request('drop-off');

    $start = $pick_up_date.' '.$pick_up_time;
    $start = strtotime($start);

    $end = $drop_off_date.' '.$drop_off_time;
    $end = strtotime($end);
    $time=STCars::get_date_diff($start,$end);

    $data_price_tmp = $cars_price * $time;

    $data = array(
        'price_cars'=>$cars_price,
        "pick_up"=>$pick_up,
        "drop_off"=>$drop_off,
        'date_time'=>array(
            "pick_up_date"=>$pick_up_date,
            "pick_up_time"=>$pick_up_time,
            "drop_off_date"=>$drop_off_date,
            "drop_off_time"=>$drop_off_time,
            "total_time"=>$time
        ),
    );
    ?>
    <input type="hidden" name="time" value='<?php echo esc_attr($time) ?>'>
    <input type="hidden" name="data_price_total" class="data_price_total" value='<?php echo esc_html($data_price_tmp) ?>'>
    <input type="hidden" name="item_id" value='<?php echo get_the_ID() ?>'>
    <input type="hidden" name="check_in_timestamp" class="" value="<?php echo esc_attr($start) ?>">
    <input type="hidden" name="check_out_timestamp" class="" value="<?php echo esc_attr($end) ?>">
    <input type="hidden" name="discount" value='<?php echo esc_attr($count_sale) ?>'>
    <input type="hidden" name="price" value='<?php echo esc_attr($cars_price) ?>'>
    <input type="hidden" name="price_old" value='<?php echo get_post_meta(get_the_ID(),'cars_price',true); ?>'>
    <input type="hidden" name="data_price_cars"  class="data_price_cars" value='<?php echo json_encode($data) ?>'>
    <input type="hidden" name="data_price_items"  class="data_price_items" value=''>

    <input type="hidden" name="selected_equipments" value="" class="st_selected_equipments">

    <?php echo st()->load_template('check_out/check_out')?>

    <?php
    if(!empty($field_list) and is_array($field_list))
    {
        foreach($field_list as $key=>$value){
            if(isset($field_type[$value['field_atrribute']]))
            {
                $field_name=isset($field_type[$value['field_atrribute']]['field_name'])?$field_type[$value['field_atrribute']]['field_name']:false;

                if($field_name)
                {
                    if(is_array($field_name) and !empty($field_name))
                    {
                        foreach($field_name as $k){
                            echo "<input name='{$k}' type='hidden' value='".STInput::request($k)."'>";
                        }
                    }
                }
                if(is_string($field_name))
                {
                    echo "<input name='{$field_name}' type='hidden' value='".STInput::request($field_name)."'>";
                }
            }
        }
    }
    ?>
</div>
