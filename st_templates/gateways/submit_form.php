<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Submit form
 *
 * Created by ShineTheme
 *
 */
?>
    <div class="text-center">
        <?php $is_guest_booking = st()->get_option('is_guest_booking','off'); ?>
        <?php if(!is_user_logged_in() and $is_guest_booking == 'on'){ ?>
            <?php
            $page_checkout = st()->get_option('page_checkout');
            $page_checkout = esc_url(add_query_arg( array( 'page_id' => $page_checkout ), home_url() ));
            $page_checkout = urlencode($page_checkout);

            $page_login = st()->get_option('page_user_login');
            $page_login = esc_url(add_query_arg( array( 'page_id' => $page_login, 'url' => $page_checkout ), home_url() ));
            ?>
            <a class="btn btn-primary btn-st-big" href="<?php echo esc_url($page_login) ?>"><?php st_the_language('submit_request')?></a>
        <?php }else{ ?>
            <input class="btn btn-primary btn-st-big st_payment_gatewaw_submit" type="submit" name="st_payment_gateway[st_submit_form]" value="<?php st_the_language('submit_request')?>">
        <?php } ?>
    </div>


