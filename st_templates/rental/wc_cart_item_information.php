<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 03/06/2015
 * Time: 3:53 CH
 */

?>

<p class="booking-item-description">
    <?php echo __('Date:',ST_TEXTDOMAIN);?> <?php  echo date_i18n(get_option('date_format'),strtotime($st_booking_data['check_in'])) ?> <i class="fa fa-long-arrow-right"></i> <?php echo date_i18n(get_option('date_format'),strtotime($st_booking_data['check_out'])) ?>
    </br>
    <?php echo __('Adult:',ST_TEXTDOMAIN);?> <?php echo esc_html($st_booking_data['adult']); ?>, <?php echo __('Children:',ST_TEXTDOMAIN);?> <?php echo esc_html($st_booking_data['children']); ?>

</p>