<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 03/06/2015
 * Time: 4:17 CH
 */
?>

<p class="booking-item-description">
    <?php if($st_booking_data['type_tour']=='daily_tour') {
        if ($st_booking_data['duration'] and $st_booking_data['duration']!='none') {
            $checkout = new DateTime(TravelHelper::convertDateFormat($st_booking_data['check_in']));

            $checkout->modify('+ ' . $st_booking_data['duration'] . ' day');
            ?>
            <?php echo __('Date:', ST_TEXTDOMAIN);?> <?php echo date_i18n(get_option('date_format'), strtotime($st_booking_data['check_in'])) ?>
            <i class="fa fa-long-arrow-right"></i> <?php echo date_i18n(get_option('date_format'), $checkout->getTimestamp()) ?>
        <?php
        } else {
            ?>
            <?php echo __('Date:', ST_TEXTDOMAIN);?> <?php echo date_i18n(get_option('date_format'), strtotime($st_booking_data['check_in'])) ?>
        <?php
        }

    }else{

        ?>

        <?php echo __('Date:',ST_TEXTDOMAIN);?> <?php  echo date_i18n(get_option('date_format'),strtotime($st_booking_data['check_in'])) ?> <i class="fa fa-long-arrow-right"></i> <?php echo date_i18n(get_option('date_format'),strtotime($st_booking_data['check_out'])) ?>
    <?php }?>
