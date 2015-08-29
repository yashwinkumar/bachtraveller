<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdminActivity
 *
 * Created by ShineTheme
 *
 */
if(!class_exists('STAdminActivity'))
{
    class STAdminActivity extends STAdmin
    {

        static $booking_page;
        static $_is_inited;
        protected $post_type = "st_activity";

        /**
         *
         *
         * @update 1.1.3
         * */
        function __construct()
        {

            add_action('init', array($this, 'init_post_type'));

            if (!st_check_service_available($this->post_type)) return;

            // Check class init
            if (self::$_is_inited) return;

            self::$_is_inited = TRUE;


            //Add metabox
            add_action('init', array($this, 'init_metabox'));

            // add_action( 'save_post', array($this,'activity_update_location') );
            add_filter('manage_st_activity_posts_columns', array($this, 'add_col_header'), 10);
            add_action('manage_st_activity_posts_custom_column', array($this, 'add_col_content'), 10, 2);


            //===============================================================

            self::$booking_page = admin_url('edit.php?post_type=st_activity&page=st_activity_booking');


            add_action('admin_menu', array($this, 'add_menu_page'));

            //Check booking edit and redirect
            if (self::is_booking_page()) {
                add_action('admin_enqueue_scripts', array(__CLASS__, 'add_edit_scripts'));
                $section = isset($_GET['section']) ? $_GET['section'] : FALSE;
                switch ($section) {
                    case "edit_order_item":
                        $this->is_able_edit();
                        break;
                    case "add_booking":
                        if (isset($_POST['submit']) and $_POST['submit']) $this->_add_booking();
                        break;

                    case 'resend_email_activity':
                        add_action('init', array($this, '_resend_mail'));
                        //$this->_resend_mail();
                        break;
                }
            }

            if (isset($_GET['send_mail']) and $_GET['send_mail'] == 'success') {
                self::set_message(__('Email sent', ST_TEXTDOMAIN), 'updated');
            }

            add_action('wp_ajax_st_room_select_ajax', array(__CLASS__, 'st_room_select_ajax'));
            add_action('save_post', array($this, 'meta_update_sale_price'), 10, 4);

            parent::__construct();
        }

        /**
         *
         *
         *
         * */
        function init_post_type()
        {
            if(!st_check_service_available($this->post_type))
            {
                return;
            }

            if(!function_exists('st_reg_post_type')) return;
            // Activity ==============================================================

            $labels = array(
                'name'               => __( 'Activity', ST_TEXTDOMAIN ),
                'singular_name'      => __( 'Activity', ST_TEXTDOMAIN ),
                'menu_name'          => __( 'Activity', ST_TEXTDOMAIN ),
                'name_admin_bar'     => __( 'Activity', ST_TEXTDOMAIN ),
                'add_new'            => __( 'Add New', ST_TEXTDOMAIN ),
                'add_new_item'       => __( 'Add New Activity', ST_TEXTDOMAIN ),
                'new_item'           => __( 'New Activity', ST_TEXTDOMAIN ),
                'edit_item'          => __( 'Edit Activity', ST_TEXTDOMAIN ),
                'view_item'          => __( 'View Activity', ST_TEXTDOMAIN ),
                'all_items'          => __( 'All Activity', ST_TEXTDOMAIN ),
                'search_items'       => __( 'Search Activity', ST_TEXTDOMAIN ),
                'parent_item_colon'  => __( 'Parent Activity:', ST_TEXTDOMAIN ),
                'not_found'          => __( 'No Activity found.', ST_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No Activity found in Trash.', ST_TEXTDOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => get_option( 'activity_permalink' ,'st_activity' ) ),
                'capability_type'    => 'post',
                'hierarchical'       => false,
                //'menu_position'      => null,
                'supports'           => array( 'author','title','editor' , 'excerpt','thumbnail', 'comments' ),
                'menu_icon'         =>'dashicons-tickets-alt-st'
            );
            st_reg_post_type( 'st_activity', $args );
        }

        /**
         *
         *
         * @since 1.1.1
         * */
        function init_metabox()
        {
            //Room
            $this->metabox[] = array(
                'id'          => 'room_metabox',
                'title'       => __( 'Activity Setting', ST_TEXTDOMAIN),
                'pages'       => array( 'st_activity' ),
                'context'     => 'normal',
                'priority'    => 'high',
                'fields'      => array(
                    array(
                        'label'       => __( 'General', ST_TEXTDOMAIN),
                        'id'          => 'room_reneral_tab',
                        'type'        => 'tab'
                    ),
                    array(
                        'label'       => __( 'Set as Featured', ST_TEXTDOMAIN),
                        'id'          => 'is_featured',
                        'type'        => 'on-off',
                        'desc'        => __( 'Set this location is featured', ST_TEXTDOMAIN),
                        'std'         =>'off'
                    ),
                    array(
                        'label'       => __( 'Custom Layout', ST_TEXTDOMAIN),
                        'id'          => 'st_custom_layout',
                        'post_type'   =>'st_layouts',
                        'desc'        => __( 'Detail Tour Layout', ST_TEXTDOMAIN),
                        'type'        => 'select',
                        'choices'     => st_get_layout('st_activity')
                    ),

                    array(
                        'label'       => __( 'Gallery', ST_TEXTDOMAIN),
                        'id'          => 'gallery',
                        'type'        => 'gallery',
                    ),
                    array(
                        'label'       => __( 'Gallery style', ST_TEXTDOMAIN),
                        'id'          => 'gallery_style',
                        'type'        => 'select',
                        'choices'   =>array(
                            array(
                                'value'=>'grid',
                                'label'=>__('Grid',ST_TEXTDOMAIN)
                            ),
                            array(
                                'value'=>'slider',
                                'label'=>__('Slider',ST_TEXTDOMAIN)
                            ),
                        )
                    ),


                    array(
                        'label'       => __( 'Contact email addresses', ST_TEXTDOMAIN),
                        'id'          => 'contact_email',
                        'type'        => 'text',
                        'desc'        => __( 'Contact email addresses', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Website', ST_TEXTDOMAIN),
                        'id'          => 'contact_web',
                        'type'        => 'text',
                        'desc'        => __( 'Website', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Phone', ST_TEXTDOMAIN),
                        'id'          => 'contact_phone',
                        'type'        => 'text',
                        'desc'        => __( 'Phone', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label'       => __( 'Video', ST_TEXTDOMAIN),
                        'id'          => 'video',
                        'type'        => 'text',
                        'desc'        => __('Please use youtube or vimeo video',ST_TEXTDOMAIN)
                    ),
                    array(
                        'label'       => __( 'Location', ST_TEXTDOMAIN),
                        'id'          => 'location_reneral_tab',
                        'type'        => 'tab'
                    ),
                    array(
                        'label'       => __( 'Location', ST_TEXTDOMAIN),
                        'id'          => 'id_location',
                        'type'        => 'post_select_ajax',
                        'post_type'   =>'location'
                    ),
                    array(
                        'label'       => __( 'Address', ST_TEXTDOMAIN),
                        'id'          => 'address',
                        'type'        => 'text',
                    ),
                    array(
                        'label'       => __( 'Latitude', ST_TEXTDOMAIN),
                        'id'          => 'map_lat',
                        'type'        => 'text',
                        'desc'        => __( 'Latitude <a href="http://www.latlong.net/" target="_blank">Get here</a>', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label'       => __( 'Longitude', ST_TEXTDOMAIN),
                        'id'          => 'map_lng',
                        'type'        => 'text',
                        'desc'        => __( 'Longitude', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label'       => __( 'Map Zoom', ST_TEXTDOMAIN),
                        'id'          => 'map_zoom',
                        'type'        => 'text',
                        'desc'        => __( 'Map Zoom', ST_TEXTDOMAIN),
                        'std'         =>13
                    ),
                    array(
                        'label'       => __( 'Info setting', ST_TEXTDOMAIN),
                        'id'          => 'activity_time_number_tab',
                        'type'        => 'tab'
                    ),
                    array(
                        'label'       => __( 'Check In', ST_TEXTDOMAIN),
                        'id'          => 'check_in',
                        'type'        => 'date_picker',
                    ),
                    array(
                        'label'       => __( 'Check Out', ST_TEXTDOMAIN),
                        'id'          => 'check_out',
                        'type'        => 'date_picker',
                    ),
                    array(
                        'label'       => __( 'Activity Time', ST_TEXTDOMAIN),
                        'id'          => 'activity-time',
                        'type'        => 'text',
                        'desc'        => __( 'The departure time of an activity', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Duration', ST_TEXTDOMAIN),
                        'id'          => 'duration',
                        'type'        => 'text',
                        'desc'        => __( 'The total time to take each activity package', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Venue Facilities', ST_TEXTDOMAIN),
                        'id'          => 'venue-facilities',
                        'type'        => 'text',
                        'desc'        => __( 'The facilities that customer may experience during activities', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label'       => __( 'Price setting', ST_TEXTDOMAIN),
                        'id'          => 'price_number_tab',
                        'type'        => 'tab'
                    ),
                    /*array(
                        'label'       => __( 'Number', ST_TEXTDOMAIN),
                        'id'          => 'number_activity',
                        'type'        => 'text',
                        'std'         =>0
                    ),*/
                    array(
                        'label'       => __( 'Type Price', ST_TEXTDOMAIN),
                        'id'          => 'type_price',
                        'type'        => 'select',
                        'desc'        => __( 'Type Price', ST_TEXTDOMAIN),
                        'choices'   =>array(
                            array(
                                'value'=>'activity_price',
                                'label'=>__('Price / Activity',ST_TEXTDOMAIN)
                            ),
                            array(
                                'value'=>'people_price',
                                'label'=>__('Price / Person',ST_TEXTDOMAIN)
                            ),
                        )
                    ),
                    array(
                        'label'       => sprintf( __( 'Price (%s)', ST_TEXTDOMAIN),TravelHelper::get_default_currency('symbol')),
                        'id'          => 'price',
                        'type'        => 'text',
                        'desc'        => __( 'Price of this Activity', ST_TEXTDOMAIN),
                        'std'         =>0,
                        'condition'   =>'type_price:is(activity_price)'
                    ),
                    array(
                        'label'       => __( 'Adult Price', ST_TEXTDOMAIN),
                        'id'          => 'adult_price',
                        'type'        => 'text',
                        'desc'        => __( 'Price per Adult', ST_TEXTDOMAIN),
                        'std'         =>0,
                        'condition'   =>'type_price:is(people_price)'
                    ),
                    array(
                        'label'       => __( 'Child Price', ST_TEXTDOMAIN),
                        'id'          => 'child_price',
                        'type'        => 'text',
                        'desc'        => __( 'Price per Child', ST_TEXTDOMAIN),
                        'std'         =>0,
                        'condition'   =>'type_price:is(people_price)'
                    ),
                    array(
                        'label'       => __( 'Discount', ST_TEXTDOMAIN),
                        'desc'       =>  __( 'Discount', ST_TEXTDOMAIN),
                        'id'          => 'discount',
                        'type'        => 'text',
                        'std'         =>0
                    ),
                    array(
                        'label'       =>  __( 'Sale Schedule', ST_TEXTDOMAIN),
                        'id'          => 'is_sale_schedule',
                        'type'        => 'on-off',
                        'std'        => 'off',
                    ),
                    array(
                        'label'       =>  __( 'Sale Price Date From', ST_TEXTDOMAIN),
                        'desc'       =>  __( 'Sale Price Date From', ST_TEXTDOMAIN),
                        'id'          => 'sale_price_from',
                        'type'        => 'date-picker',
                        'condition'   =>'is_sale_schedule:is(on)'
                    ),

                    array(
                        'label'       =>  __( 'Sale Price Date To', ST_TEXTDOMAIN),
                        'desc'       =>  __( 'Sale Price Date To', ST_TEXTDOMAIN),
                        'id'          => 'sale_price_to',
                        'type'        => 'date-picker',
                        'condition'   =>'is_sale_schedule:is(on)'
                    ),

                    array(
                        'label'       => __( 'Best Price Guarantee', ST_TEXTDOMAIN),
                        'id'          => 'best-price-guarantee',
                        'type'        => 'on-off',
                        'std'         =>'off'
                    ),
                    array(
                        'label'       => __( 'Best Price Guarantee Text', ST_TEXTDOMAIN),
                        'id'          => 'best-price-guarantee-text',
                        'type'        => 'textarea',
                        'rows'         => '2',
                        'condition'   => 'best-price-guarantee:is(on)',
                    ),
                    array(
                        'label' => __('Activity external booking',ST_TEXTDOMAIN),
                        'id' => 'st_activity_external_booking',
                        'type'        => 'on-off',
                        'std' => "off",
                    ),
                    array(
                        'label' => __('Activity external booking',ST_TEXTDOMAIN),
                        'id' => 'st_activity_external_booking_link',
                        'type'        => 'text',
                        'std' => "",
                        'condition'   =>'st_activity_external_booking:is(on)',
                        'desc'=>"<em>".__('Notice: Must be http://...',ST_TEXTDOMAIN)."</em>",
                    ),

                    array(
                        'id'      => 'deposit_payment_status',
                        'label'   => __("Deposit payment options", ST_TEXTDOMAIN),
                        'desc'    => __('You can select <code>Disallow Deposit</code>, <code>Deposit by percent</code>, <code>Deposit by amount</code>'),
                        'type'    => 'select',
                        'choices' => array(
                            array(
                                'value' => '',
                                'label' => __('Disallow Deposit', ST_TEXTDOMAIN)
                            ),
                            array(
                                'value' => 'percent',
                                'label' => __('Deposit by percent', ST_TEXTDOMAIN)
                            ),
                            array(
                                'value' => 'amount',
                                'label' => __('Deposit by amount', ST_TEXTDOMAIN)
                            ),
                        )
                    ),
                    array(
                        'label'      => __('Deposit payment amount', ST_TEXTDOMAIN),
                        'desc'       => __('Leave empty for disallow deposit payment', ST_TEXTDOMAIN),
                        'id'         => 'deposit_payment_amount',
                        'type'       => 'text',
                        'condition' => 'deposit_payment_status:not()'
                    ),


                    /*  array(
                          'label'       => __( 'Rate setting', ST_TEXTDOMAIN),
                          'id'          => 'rate_number_tab',
                          'type'        => 'tab'
                      ),

                      array(
                          'label'       => __( 'Avg Rate Review', ST_TEXTDOMAIN),
                          'id'          => 'rate_review',
                          'type'        => 'numeric-slider',
                          'min_max_step'=> '1,5,1',
                      ),*/
                )
            );
            $data_paypment = STPaymentGateways::$_payment_gateways;
            if(!empty($data_paypment) and is_array($data_paypment)){
                $this->metabox[0]['fields'][] = array(
                    'label'       => __( 'Payment', ST_TEXTDOMAIN),
                    'id'          => 'payment_detail_tab',
                    'type'        => 'tab'
                );
                foreach($data_paypment as $k=>$v){
                    $this->metabox[0]['fields'][] = array(
                        'label'       =>$v->get_name() ,
                        'id'          => 'is_meta_payment_gateway_'.$k,
                        'type'        => 'on-off',
                        'desc'        => $v->get_name(),
                        'std'         => 'on'
                    );
                }
            }
            $custom_field = st()->get_option('st_activity_unlimited_custom_field');
            if(!empty($custom_field) and is_array($custom_field)){
                $this->metabox[0]['fields'][]=array(
                    'label'       => __( 'Custom fields', ST_TEXTDOMAIN),
                    'id'          => 'custom_field_tab',
                    'type'        => 'tab'
                );

                foreach($custom_field as $k => $v){
                    $key = str_ireplace('-','_','st_custom_'.sanitize_title($v['title']));
                    $this->metabox[0]['fields'][]=array(
                        'label'       => $v['title'],
                        'id'          => $key,
                        'type'        => $v['type_field'],
                        'desc'        => '<input value=\'[st_custom_meta key="'.$key.'"]\' type=text readonly />',
                        'std'         =>$v['default_field']
                    );
                }
            }


            parent::register_metabox($this->metabox);
        }


        function meta_update_sale_price($post_id)
        {
            if ( wp_is_post_revision( $post_id ) )
                return;
            $post_type=get_post_type($post_id);
            if($post_type=='st_activity')
            {
                $sale_price=get_post_meta($post_id,'price',true);
                $discount=get_post_meta($post_id,'discount',true);
                $is_sale_schedule=get_post_meta($post_id,'is_sale_schedule',true);
                if($is_sale_schedule=='on')
                {
                    $sale_from=get_post_meta($post_id,'sale_price_from',true);
                    $sale_to=get_post_meta($post_id,'sale_price_to',true);
                    if($sale_from and $sale_from){

                        $today=date('Y-m-d');
                        $sale_from = date('Y-m-d', strtotime($sale_from));
                        $sale_to = date('Y-m-d', strtotime($sale_to));
                        if (($today >= $sale_from) && ($today <= $sale_to))
                        {

                        }else{

                            $discount=0;
                        }

                    }else{
                        $discount=0;
                    }
                }
                if($discount){
                    $sale_price= $sale_price - ($sale_price/100)*$discount;
                }
                update_post_meta($post_id,'sale_price',$sale_price);
            }
        }
        function _resend_mail()
        {
            $order_item=isset($_GET['order_item_id'])?$_GET['order_item_id']:false;

            $test=isset($_GET['test'])?$_GET['test']:false;
            if($order_item){

                $order=$order_item;

                if($test){
                    echo '<meta charset="UTF-8" 1>';
                    $message=st()->load_template('email/booking_infomation_activity',null,array('order_id'=>$order));

                    echo($message);die;
                }


                if($order){
                    STCart::send_mail_after_booking($order);
                }
            }

            wp_safe_redirect(self::$booking_page.'&send_mail=success');
        }
        static  function  st_room_select_ajax()
        {
            extract( wp_parse_args($_GET,array(
                'room_parent'=>'',
                'post_type'=>'',
                'q'=>''
            )));


            query_posts(array('post_type'=>$post_type,'posts_per_page'=>10,'s'=>$q,'meta_key'=>'room_parent','meta_value'=>$room_parent));

            $r=array(
                'items'=>array(),
            );
            while(have_posts())
            {
                the_post();
                $r['items'][]=array(
                    'id'=>get_the_ID(),
                    'name'=>get_the_title(),
                    'description'=>''
                );
            }

            wp_reset_query();

            echo json_encode($r);
            die;

        }
        static function  add_edit_scripts()
        {
            wp_enqueue_script('select2');
            wp_enqueue_script('st-edit-booking',get_template_directory_uri().'/js/admin/edit-booking.js',array('jquery'),null,true);
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jjquery-ui.theme.min.css',get_template_directory_uri().'/css/admin/jquery-ui.min.css');
        }
        static function is_booking_page()
        {
            if(is_admin()
                and isset($_GET['post_type'])
                and $_GET['post_type']=='st_activity'
                and isset($_GET['page'])
                and $_GET['page']='st_activity_booking'
            ) return true;
            return false;
        }

        function add_menu_page()
        {
            //Add booking page
            add_submenu_page('edit.php?post_type=st_activity',__('Activity Booking',ST_TEXTDOMAIN), __('Activity Booking',ST_TEXTDOMAIN), 'manage_options', 'st_activity_booking', array($this,'__activity_booking_page'));
        }

        function  __activity_booking_page(){

            $section=isset($_GET['section'])?$_GET['section']:false;

            if($section){
                switch($section)
                {
                    case "edit_order_item":
                        $this->edit_order_item();
                        break;
                    case 'add_booking':
                        $this->add_booking();
                        break;
                }
            }else{

                $action=isset($_POST['st_action'])?$_POST['st_action']:false;
                switch($action){
                    case "delete":
                        $this->_delete_items();
                        break;
                }
                echo balanceTags($this->load_view('activity/booking_index',false));
            }

        }
        function add_booking()
        {

            echo balanceTags($this->load_view('activity/booking_edit',false,array('page_title'=>__('Add new Activity Booking',ST_TEXTDOMAIN))));
        }
        function _delete_items(){

            if ( empty( $_POST ) or  !check_admin_referer( 'shb_action', 'shb_field' ) ) {
                //// process form data, e.g. update fields
                return;
            }
            $ids=isset($_POST['post'])?$_POST['post']:array();
            if(!empty($ids))
            {
                foreach($ids as $id)
                    wp_delete_post($id,true);

            }

            STAdmin::set_message(__("Delete item(s) success",ST_TEXTDOMAIN),'updated');

        }

        function edit_order_item()
        {
            $item_id=isset($_GET['order_item_id'])?$_GET['order_item_id']:false;
            if(!$item_id or get_post_type($item_id)!='st_order')
            {
                //wp_safe_redirect(self::$booking_page); die;
                return false;
            }
            if(isset($_POST['submit']) and $_POST['submit']) $this->_save_booking($item_id);

            echo balanceTags($this->load_view('activity/booking_edit'));
        }
        function _add_booking()
        {
            if(!check_admin_referer( 'shb_action', 'shb_field' )) die('shb_action');

            //Create Order
            $order=array(
                'post_title'=>__('Order',ST_TEXTDOMAIN).' - '.date(get_option( 'date_format' )).' @ '.date(get_option('time_format')),
                'post_type'=>'st_order',
                'post_status'=>'publish'
            );

            $order_id=wp_insert_post($order);

            if($order_id){
                $check_out_field=STCart::get_checkout_fields();

                if(!empty($check_out_field))
                {
                    foreach($check_out_field as $field_name=>$field_desc)
                    {
                        update_post_meta($order_id,$field_name,STInput::post($field_name));
                    }
                }
                $user_fields=array(

                    'id_user'=>'',
                    'status'=>'',
                    'st_tax'=>''

                );
                $data=wp_parse_args($_POST,$user_fields);
                if($order_id){
                    foreach($user_fields as $val=>$value){
                        update_post_meta($order_id,$val,$data[$val]);
                    }
                }
                update_post_meta($order_id,'payment_method','submit_form');

                //Save Items

                $item_data=array(
                    'item_number'=>'',
                    'item_id'=>'',
                    'item_price'=>'',
                    'check_in'=>'',
                    'check_out'=>''
                );
                $data=wp_parse_args($_POST,$item_data);

                $item_id=$order_id;
                if($item_id){
                    foreach($item_data as $val=>$value){

                        if($val=='check_in' or $val=='check_out'){
                            update_post_meta($item_id,$val,date('Y-m-d',strtotime($data[$val])));
                        }else
                            update_post_meta($item_id,$val,$data[$val]);
                    }


                    do_action('st_booking_success',$order_id);

                    //Success
                    wp_safe_redirect(self::$booking_page);
                }


            }
            //STAdmin::set_message('Update Success','updated');

        }
        function _save_booking($order_id)
        {
            if(!check_admin_referer( 'shb_action', 'shb_field' )) die('shb_action');

            //Update Order
            $orderitem=array(
                'item_number',
                'item_id',
                'item_price',
                'check_in',
                'check_out'
            );

            $data=wp_parse_args($_POST,$orderitem);

            foreach($orderitem as $val){

                if($val=='check_in' or $val=='check_out'){
                    update_post_meta($order_id,$val,date('Y-m-d',strtotime($data[$val])));
                }else
                    update_post_meta($order_id,$val,$data[$val]);
            }

            //Update User
            $order_parent=$order_id;
            $id_user=isset($_POST['id_user'])?$_POST['id_user']:false;
            if($order_parent and $id_user){

                update_post_meta($order_parent,'id_user',$id_user);
            }

            $check_out_field=STCart::get_checkout_fields();

            if(!empty($check_out_field))
            {
                foreach($check_out_field as $field_name=>$field_desc)
                {
                    update_post_meta($order_id,$field_name,STInput::post($field_name));
                }
            }

            $user_fields=array(

                'status'=>'',
                'st_tax'=>''
            );
            $data=wp_parse_args($_POST,$user_fields);
            if($order_parent){
                foreach($user_fields as $val=>$value){
                    update_post_meta($order_parent,$val,$data[$val]);
                }
            }


            STAdmin::set_message('Update Success','updated');
        }
        function is_able_edit()
        {
            $item_id=isset($_GET['order_item_id'])?$_GET['order_item_id']:false;
            if(!$item_id or get_post_type($item_id)!='st_order')
            {
                wp_safe_redirect(self::$booking_page); die;
            }
            return true;
        }



        // =================================================================

        function activity_update_location($post_id)
        {
            if ( wp_is_post_revision( $post_id ) )
                return;
            $post_type=get_post_type($post_id);

            if($post_type=='st_activity')
            {
                $location_id = get_post_meta( $post_id ,'id_location',true);
                $ids_in=array();
                $parents = get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'post_type' => 'location', 'post_parent' => $location_id ));

                $ids_in[]=$location_id;

                foreach( $parents as $child ){
                    $ids_in[]=$child->ID;
                }

                $arg = array(
                    'post_type'=>'st_activity',
                    'posts_per_page'=>'-1',
                    'meta_query' => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => $ids_in,
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query=new WP_Query($arg);
                /* $total=0;
                 if($query->have_posts()) {
                     $query->the_post();
                     $total +=get_comments_number();
                 }*/
                $offer_tours = $query->post_count;

                // get total review
                $arg = array(
                    'post_type'=>'st_activity',
                    'posts_per_page'=>'-1',
                    'meta_query' => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => array( $location_id ),
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query=new WP_Query($arg);
                $total=0;
                if($query->have_posts()) {
                    while($query->have_posts()){
                        $query->the_post();
                        $total +=get_comments_number();
                    }
                }

                // get min price
                $arg = array(
                    'post_type'=>'st_activity',
                    'posts_per_page'=>'1',
                    'order'=>'ASC',
                    'meta_key'=>'price',
                    'orderby'=>'meta_value_num',
                    'meta_query' => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => array( $location_id ),
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query=new WP_Query($arg);
                if($query->have_posts()) {
                    $query->the_post();
                    $price_min = get_post_meta(get_the_ID(),'price',true);
                    update_post_meta($location_id,'review_st_activity',$total);
                    update_post_meta($location_id,'min_price_st_activity',$price_min);
                    update_post_meta($location_id,'offer_st_activity',$offer_tours);
                }
                wp_reset_postdata();

            }
        }
        function add_col_header($defaults)
        {

            $this->array_splice_assoc($defaults,2,0,array(
                'activity_date'=>__('Date',ST_TEXTDOMAIN),
                // 'activity_time'=>__('Activity Time',ST_TEXTDOMAIN),
                'price'=>__('Price',ST_TEXTDOMAIN),

            ));

            return $defaults;
        }
        function array_splice_assoc(&$input, $offset, $length = 0, $replacement = array()) {
            $tail = array_splice($input, $offset);
            $extracted = array_splice($tail, 0, $length);
            $input += $replacement + $tail;
            return $extracted;
        }
        function add_col_content($column_name, $post_ID)
        {
            if ($column_name == 'activity_date') {
                $check_in = get_post_meta($post_ID , 'check_in' ,true);
                $check_out = get_post_meta($post_ID , 'check_out' ,true);
                $date = mysql2date('d/m/Y',$check_in).' <i class="fa fa-long-arrow-right"></i> '.mysql2date('d/m/Y',$check_out);
                if(!empty($check_in) and !empty($check_out)){
                    echo balanceTags($date);
                }else{
                    _e('none',ST_TEXTDOMAIN);
                }

            }

            if ($column_name == 'duration') {
                $parent=get_post_meta($post_ID,'duration',true);
                if($parent)
                {
                    echo esc_html($parent);
                }
            }
            if ($column_name == 'price') {
                $price=get_post_meta($post_ID,'price',true);
                $type_price = get_post_meta($post_ID ,'type_price',true);
                if($type_price != 'people_price'){
                    echo '<strong>'.TravelHelper::format_money($price).'</strong>';
                }else{
                    $adult_price = get_post_meta($post_ID,'adult_price',true);
                    $child_price = get_post_meta($post_ID,'child_price',true);
                    echo '<span>'.__('Adult Price',ST_TEXTDOMAIN).' : '.TravelHelper::format_money($adult_price).'</span><br>';
                    echo '<span>'.__('Child Price',ST_TEXTDOMAIN).' : '.TravelHelper::format_money($child_price).'</span><br>';
                }
            }
            if ($column_name == 'activity_time') {
                $time=get_post_meta($post_ID,'activity-time',true);
                if($time)
                {
                    echo esc_html($time);
                }
            }

        }

    }
    $a=new STAdminActivity();
}

