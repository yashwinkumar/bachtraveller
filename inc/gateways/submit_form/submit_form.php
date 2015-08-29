<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STGatewaySubmitform
 *
 * Created by ShineTheme
 *
 */
if(!class_exists('STGatewaySubmitform'))
{
    class STGatewaySubmitform extends STAbstactPaymentGateway
    {

        function __construct()
        {

        }

        function html()
        {
            echo st()->load_template('gateways/submit_form');
        }

        /**
         *
         *
         * @update 1.1.1
         * */
        function do_checkout($order_id)
        {
            update_post_meta($order_id,'status','pending');
            $order_token=get_post_meta($order_id,'order_token_code',true);

            //Destroy cart on success
            STCart::destroy_cart();

            $booking_success=STCart::get_success_link();
            do_action('st_email_after_booking',$order_id);
            do_action('st_booking_submit_form_success',$order_id);

            if($order_token){
                $array=array(
                    'order_token_code'=>$order_token
                );
            }else{
                $array=array(
                    'order_code'=>$order_id,

                );
            }

            return array(
                'status'=>true,
                'redirect'=>add_query_arg($array,$booking_success)
            );

        }

        /**
         * Validate if order is available to show booking infomation
         *
         * @since 1.0.8
         *
         * */
        function success_page_validate()
        {
            return true;
        }


        function get_name()
        {
            return __('Submit Form',ST_TEXTDOMAIN);
        }

        function is_available()
        {
            if(st()->get_option('pm_gway_st_submit_form_enable')=='on')
            {
                return true;
            }
            return false;
        }
        function _pre_checkout_validate()
        {
            return true;
        }

        function get_option_fields()
        {
            return array();
        }
        function get_default_status()
        {
            return true;
        }
    }
}
