<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STGatewayPaypal
 *
 * Created by ShineTheme
 *
 */
if(!class_exists('STGatewayPaypal'))
{
    class STGatewayPaypal extends STAbstactPaymentGateway
    {

        function __construct()
        {

        }


        function _pre_checkout_validate()
        {
            $validate=true;

            if(!STCart::get_total())
            {
                STTemplate::set_message(__('Can not process free payment',ST_TEXTDOMAIN),'danger');
                return false;
            }

            if($this->is_available())
            {

                $paypal = new STPaypal();

                $pp = $paypal->test_authorize();


                if(isset($pp['redirect_url']) and $pp['redirect_url'])
                    $pp_link=$pp['redirect_url'];

                if(!isset($pp_link))
                {
                    STTemplate::set_message(isset($pp['message'])?$pp['message']:__('Paypal Payment Gateway Validate Fail'),'danger');
                    $validate=false;
                }
            }

            return $validate;

        }
        function html()
        {
            echo st()->load_template('gateways/paypal');
        }

        /**
         * Validate if order is available to show booking infomation
         *
         * @since 1.0.8
         *
         * */
        function success_page_validate()
        {
            $order_code=STInput::get('order_code');
            $order_token_code=STInput::get('order_token_code');
            if($order_token_code)
            {
                $order_code=STOrder::get_order_id_by_token($order_token_code);

            }

            $status=get_post_meta($order_code,'status',true);

            $result=true;


            if($status=='incomplete')
            {
                // try to check payment complete
                $paypal=new STPaypal();

                $r=$paypal->check_completePurchase($order_code);

                if($r)
                {
                    if(isset($r['status'])){
                        if($r['status'])
                        {
                            $result=true;
                            update_post_meta($order_code,'status','complete');
                            $status='complete';
                            do_action('st_email_after_booking',$order_code);
                            do_action('st_booking_submit_form_success',$order_code);

                        }elseif(isset($r['message']) and $r['message'])
                        {
                            $result=false;
                            STTemplate::set_message($r['message'],'danger');
                        }

                        if(isset($r['redirect_url']) and $r['redirect_url'])
                        {
                            echo "<script>window.location.href='".$r['redirect_url']."'</script>";die;
                        }
                    }
                }

            }

            if($status=='incomplete')
            {
                $result=false;
                STTemplate::set_message(__("Sorry! Your payment is incomplete."));
            }


            return $result;
        }

        function do_checkout($order_id)
        {
            update_post_meta($order_id,'status','incomplete');

            $paypal=new STPaypal();

            $pp=$paypal->get_authorize_url($order_id);


            if(isset($pp['redirect_url']) and $pp['redirect_url'])
                $pp_link=$pp['redirect_url'];


            do_action('st_before_redirect_paypal');

            if(!isset($pp_link))
            {
                return array(
                    'status'=>false,
                    'message'=>isset($pp['message'])?$pp['message']:false,
                    'data'=>isset($pp['data'])?$pp['data']:false,
                );
            }
            //Destroy cart on success
            STCart::destroy_cart();

            if(!$pp_link)
            {
                return array(
                    'status'=>false,
                    'message'=>__('Can not get Paypal Authorize URL.',ST_TEXTDOMAIN)
                );
            }else{

                return array(
                    'status' => true,
                    'redirect' => $pp_link
                );
            }
        }

        function get_name()
        {
            return __('Paypal',ST_TEXTDOMAIN);
        }

        static function paypal_checkout($transaction=array(),$order_id=false)
        {
            $default=array(
                'EMAIL'=>false,
                'CHECKOUTSTATUS'=>'',
                'PAYERID'=>'',
                'FIRSTNAME'=>'',
                'LASTNAME'=>'',
                'DESC'=>''
            );


            $data=wp_parse_args($transaction,$default);

            if(isset($transaction['PAYMENTREQUEST_0_TRANSACTIONID']))
            {
                $data['transaction_id']=$transaction['PAYMENTREQUEST_0_TRANSACTIONID'];
            }

            if($order_id){
                if(!empty($data)){
                    foreach($data as $key=>$value){
                        if(array_key_exists($key,$default))
                        update_post_meta($order_id,'pp_'.strtolower($key),$value);
                    }

                    update_post_meta($order_id,'status','complete');
                }
                return array('status'=>true);
            }

        }

        function is_available()
        {
            if(!class_exists('STPaypal'))
            {
                return false;

            }

            if(st()->get_option('pm_gway_st_paypal_enable')=='on')
            {
                return true;
            }
            return false;
        }

        function get_option_fields()
        {
            return array(
                array(
                    'id'            =>'paypal_email',
                    'label'         =>__('Paypal Email',ST_TEXTDOMAIN),
                    'type'          =>'text',
                    'section'       =>'option_pmgateway',
                    'desc'          =>__('Your Payal Email Account',ST_TEXTDOMAIN)
                ),
                array(
                    'id'            =>'paypal_enable_sandbox',
                    'label'         =>__('Paypal Enable Sandbox',ST_TEXTDOMAIN),
                    'type'          =>'on-off',
                    'section'       =>'option_pmgateway',
                    'std'           =>'on',
                    'desc'          =>__('Allow you to enable sandbox mod for testing',ST_TEXTDOMAIN)
                )
            ,
                array(
                    'id'            =>'paypal_api_username',
                    'label'         =>__('Paypal API Username',ST_TEXTDOMAIN),
                    'type'          =>'text',
                    'section'       =>'option_pmgateway',
                    'desc'          =>__('Your Paypal API Username',ST_TEXTDOMAIN)
                ),
                array(
                    'id'            =>'paypal_api_password',
                    'label'         =>__('Paypal API Password',ST_TEXTDOMAIN),
                    'type'          =>'text',
                    'section'       =>'option_pmgateway',
                    'desc'          =>__('Your Paypal API Password',ST_TEXTDOMAIN)
                ),
                array(
                    'id'            =>'paypal_api_signature',
                    'label'         =>__('Paypal API Signature',ST_TEXTDOMAIN),
                    'type'          =>'text',
                    'section'       =>'option_pmgateway',
                    'desc'          =>__('Your Paypal API Signature',ST_TEXTDOMAIN)
                )
            );
        }

        function get_default_status()
        {
            return true;
        }
    }
}

