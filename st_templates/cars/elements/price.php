<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars element price
 *
 * Created by ShineTheme
 *
 */

//check is booking with modal
$st_is_booking_modal=apply_filters('st_is_booking_modal',false);
$car=new STCars();
$field_list=$car->get_search_fields_box();
$field_type=$car->get_search_fields_name();

$col = 12 / $attr['st_style'];

$info_price = STCars::get_info_price();
$cars_price = $info_price['price'];
$count_sale = $info_price['discount'];
if(!empty($count_sale)){
    $cars_price = $info_price['price'];
    $price_sale = $info_price['price_old'];
}

$pick_up_date=TravelHelper::convertDateFormat(STInput::request('pick-up-date'));
if(empty($pick_up_date)) $pick_up_date = date('m/d/Y',strtotime("now"));

$drop_off_date=TravelHelper::convertDateFormat(STInput::request('drop-off-date'));
if(empty($drop_off_date)) $drop_off_date = date('m/d/Y',strtotime("+1 day"));

$pick_up_time = STInput::request('pick-up-time','12:00 PM');

$drop_off_time = STInput::request('drop-off-time','12:00 PM');

$pick_up=STInput::request('pick-up');

$location_id_drop_off = STInput::request('location_id_drop_off');

$drop_off=STInput::request('drop-off');

$location_id_pick_up = STInput::request('location_id_pick_up');

$start = $pick_up_date.' '.$pick_up_time;
$start = strtotime($start);
$end = $drop_off_date.' '.$drop_off_time;
$end = strtotime($end);
$time=STCars::get_date_diff($start,$end);
?>
<?php // $cars_price = get_post_meta(get_the_ID(),'cars_price',true); ?>
<form method="post" class="car_booking_form"  >
<div class="booking-item-price-calc">
    <div class="row row-wrap">
        <div class="col-md-<?php echo esc_attr($col) ?> singe_cars" data-car-id="<?php the_ID()?>">
            <?php $list = get_post_meta(get_the_ID(),'cars_equipment_list',true); ?>
            <?php
            if(!empty($list)){
                foreach($list as $k=>$v){
					$v['cars_equipment_list_price'] = apply_filters('st_apply_tax_amount',$v['cars_equipment_list_price']);

                    $price_unit = isset($v['price_unit'])? $v['price_unit']: '';

                    $price_unit_html='';
                    switch($price_unit)
                    {
                        case "per_hour":
                            $price_unit_html=__('/hour',ST_TEXTDOMAIN);
                            break;
                        case "per_day":
                            $price_unit_html=__('/day',ST_TEXTDOMAIN);
                            break;
                        default:
                            $price_unit_html='';
                            break;
                    }
                    echo '<div class="checkbox">
                            <label>
                                <input class="i-check equipment" data-price-unit="'.$price_unit.'" data-title="'.$v['title'].'" data-price="'.$v['cars_equipment_list_price'].'" type="checkbox" />'.$v['title'].'
                                <span class="pull-right">'.TravelHelper::format_money($v['cars_equipment_list_price']).''.$price_unit_html.'</span>
                            </label>
                       </div>';
                }
            }
            ?>
            <div class="cars_equipment_display"></div>
        </div>
        <div class="col-md-<?php echo esc_attr($col) ?>">
            <ul class="list">
                <li>
                    <p> <?php echo st_get_language('car_price_per').' '.ucfirst(STCars::get_price_unit('label')); ?>
                        <span><?php echo TravelHelper::format_money($cars_price) ?></span>
                    </p>
                </li>
                <li><p>
                        <?php


                        $data_price_tmp=STCars::get_rental_price($cars_price,$start,$end);
                        ?>
                        <?php st_the_language('car_rental_price'); ?>
                        <span class="st_cars_price" data-value="<?php echo esc_html($data_price_tmp) ?>" >
                            <?php echo TravelHelper::format_money($data_price_tmp) ?>
                        </span>
                    </p>
                    <?php
                    $pick_up_date = $drop_off_date = '';
                        $pick_up_date_html = $drop_off_date_html = '';

                    if(!empty($_REQUEST['pick-up-date'])){
                        $pick_up_date_html =  $_REQUEST['pick-up-date'];
                        $pick_up_date =  TravelHelper::convertDateFormat($_REQUEST['pick-up-date']);
                    }else{
                        $pick_up_date_html = date(TravelHelper::getDateFormat(),strtotime("now"));
                        $pick_up_date = date(TravelHelper::getDateFormat(),strtotime("now"));
                    }

                    if(!empty($_REQUEST['drop-off-date'])){
                        $drop_off_date_html =  $_REQUEST['drop-off-date'];
                        $drop_off_date =  TravelHelper::convertDateFormat($_REQUEST['drop-off-date']);
                    }else{
                        $drop_off_date_html = date(TravelHelper::getDateFormat(),strtotime("+1 day"));
                        $drop_off_date = date(TravelHelper::getDateFormat(),strtotime("+1 day"));
                    }

                    if(!empty($pick_up_date_html)){
                    ?>
                      <small><?php echo esc_attr($time) ?> <?php if($time > 1) echo STCars::get_price_unit('plural'); else echo STCars::get_price_unit(); ?> ( <?php echo esc_html($pick_up_date_html) ?> - <?php echo esc_html($drop_off_date_html) ?> )</small>
                    <?php }else{ ?>
                        <small><?php echo esc_attr($time) ?> <?php if($time > 1) echo STCars::get_price_unit('plural'); else STCars::get_price_unit(); ?> </small>
                    <?php } ?>
                </li>
                <li><p>
                        <?php st_the_language('car_equipment') ?>
                        <span class="st_data_car_equipment_total" data-value="0">
                          <?php echo TravelHelper::format_money( 0 ) ?>
                        </span>
                    </p>
                </li>
                <li><p>
                        <?php st_the_language('car_rental_total'); ?>
                        <span class="st_data_car_total"> <?php echo TravelHelper::format_money($data_price_tmp) ?>
                        </span>
                    </p>
                    <div class="spinner cars_price_img_loading ">
                    </div>
                </li>
            </ul>

            <?php if($st_is_booking_modal){ ?>
                <a href="#car_booking_<?php the_ID() ?>" class="btn btn-primary btn_booking_modal" data-target=#car_booking_<?php the_ID() ?>  data-effect="mfp-zoom-out" ><?php st_the_language('book_now') ?></a>
            <?php }else{ ?>

                <?php echo STCars::car_external_booking_submit(); ?>
                
            <?php } ?>
            <?php echo st()->load_template('user/html/html_add_wishlist',null,array("title"=>"")) ?>
        </div>
    </div>
</div>
    <?php
    if(!$pick_up and $location_id_pick_up) $pick_up=get_the_title($location_id_pick_up);
    if(!$drop_off and $location_id_drop_off) $drop_off=get_the_title($location_id_drop_off);
     $data = array(
         'price_cars'=>$cars_price,
         "pick_up"=>$pick_up,
         "location_id_pick_up"=>$location_id_pick_up,
         "drop_off"=>$drop_off,
         "location_id_drop_off"=>$location_id_drop_off,
         'date_time'=>array(
             "pick_up_date"=>$pick_up_date,
             "pick_up_time"=>$pick_up_time,
             "drop_off_date"=>$drop_off_date,
             "drop_off_time"=>$drop_off_time,
             "total_time"=>$time
         ),
     );
    ?>

    <input type="hidden" name="check_in" class="" value="<?php echo date('m/d/Y',$start) ?>">
    <input type="hidden" name="check_in_timestamp" class="" value="<?php echo esc_attr($start) ?>">
    <input type="hidden" name="check_out" class="" value="<?php echo date('m/d/Y',$end) ?>">
    <input type="hidden" name="check_out_timestamp" class="" value="<?php echo esc_attr($end) ?>">
    <input type="hidden" name="county_pick_up" class="county_pick_up" data-address="<?php echo esc_attr($pick_up) ?>" value=''>
    <input type="hidden" name="county_drop_off" class="county_drop_off" data-address="<?php echo esc_attr($drop_off) ?>" value=''>

    <input type="hidden" name="time" value='<?php echo esc_attr($time) ?>'>
    <input type="hidden" name="data_price_total" class="data_price_total" value='<?php echo esc_html($data_price_tmp) ?>'>
    <input type="hidden" name="item_id" value='<?php echo get_the_ID() ?>'>

    <input type="hidden" name="discount" value='<?php echo esc_attr($count_sale) ?>'>
    <input type="hidden" name="price_old" value='<?php echo (isset($price_sale))?$price_sale:$cars_price; ?>'>
    <input type="hidden" name="price" value='<?php echo esc_attr($cars_price) ?>'>
    <input type="hidden" name="price_unit" value="<?php echo esc_attr(STCars::get_price_unit()) ?>">
    <input type="hidden" name="action" value='cars_add_to_cart'>
    <input type="hidden" name="data_price_cars"  class="data_price_cars" value='<?php echo json_encode($data) ?>'>
    <input type="hidden" name="data_price_items"  class="data_price_items" value=''>
    <input type="hidden" name="selected_equipments" value="" class="st_selected_equipments">
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
    <?php
    if(!get_option('permalink_structure'))
    {
        echo '<input type="hidden" name="st_cars"  value="'.st_get_the_slug().'">';
    }
    ?>
</form>
<?php
if($st_is_booking_modal){?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="car_booking_<?php the_ID()?>">
        <?php echo st()->load_template('cars/modal_booking');?>
    </div>

<?php }?>
