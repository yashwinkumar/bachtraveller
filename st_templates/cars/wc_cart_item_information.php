<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 02/06/2015
 * Time: 3:32 CH
 */
$selected_equipments=$st_booking_data['selected_equipments'];
$price_data=$st_booking_data['data_price_cars'];


$pick_up_date = $price_data->date_time->pick_up_date;
$pick_up_time = $price_data->date_time->pick_up_time;

$drop_off_date=$price_data->date_time->drop_off_date;
$drop_off_time=$price_data->date_time->drop_off_time;
?>
<p class="booking-item-description">
    <?php echo __('Date:',ST_TEXTDOMAIN);?> <?php  echo date_i18n(get_option('date_format'),strtotime($pick_up_date.' '.$pick_up_time)) ?> <i class="fa fa-long-arrow-right"></i> <?php echo date_i18n(get_option('date_format'),strtotime($drop_off_date.' '.$drop_off_time)) ?>
    </br>
    <?php echo __('Location:',ST_TEXTDOMAIN);?> <?php echo esc_html($price_data->pick_up); ?> <i class="fa fa-long-arrow-right"></i> <?php echo esc_html($price_data->drop_off) ?>
    <?php

    if($selected_equipments and !empty($selected_equipments)){
        echo "</br>";
        echo __('Equipment(s):',ST_TEXTDOMAIN);
        echo "</br>";
        foreach($selected_equipments as $key=>$data){
            $price_unit=$data->price_unit;
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
            echo "&nbsp;&nbsp;&nbsp;- ".$data->title.": ".TravelHelper::format_money($data->price).$price_unit_html." <br>";

        }
        echo "";
    }
    ?>
</p>