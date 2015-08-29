<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdminRental
 *
 * Created by ShineTheme
 *
 */
if(!class_exists('STAdminRental'))
{

    class STAdminRental extends STAdmin
    {

        static $booking_page;
        public $metabox;
        public $post_type='st_rental';


        function __construct()
        {


            add_action('init',array($this,'_reg_post_type'));

            if (!st_check_service_available($this->post_type)) return;

            //add colum for rooms
            add_filter('manage_st_rental_posts_columns', array($this,'add_col_header'), 10);
            add_action('manage_st_rental_posts_custom_column', array($this,'add_col_content'), 10, 2);

            self::$booking_page=admin_url('edit.php?post_type=st_rental&page=st_rental_booking');
            //rental Hook
            /*
             * todo Re-cal rental min price
             * */

            add_action( 'save_post', array($this,'meta_update_sale_price') ,10,4);
            add_action('admin_menu',array($this,'new_menu_page'));

            //Check booking edit and redirect
            if(self::is_booking_page())
            {
                add_action('admin_enqueue_scripts',array(__CLASS__,'add_edit_scripts'));
                $section=isset($_GET['section'])?$_GET['section']:false;
                switch($section){
                    case "edit_order_item":
                        $this->is_able_edit();
                        break;
                    case "add_booking":
                        if(isset($_POST['submit']) and $_POST['submit']) $this->_add_booking();
                        break;

                    case 'resend_email':
                        add_action('init',array($this,'_resend_mail'));
                        //$this->_resend_mail();
                        break;
                }
            }

            if(isset($_GET['send_mail']) and $_GET['send_mail']=='success')
            {
                self::set_message(__('Email sent',ST_TEXTDOMAIN),'updated');
            }

            add_action('wp_ajax_st_room_select_ajax',array(__CLASS__,'st_room_select_ajax'));


            add_action('init',array($this,'_add_metabox'));

            add_action('st_search_fields_name',array($this,'get_search_fields_name'),10,2);

            parent::__construct();
        }


        /**
         *
         *
         * @since 1.1.3
         * */
        function _reg_post_type()
        {
            if(!st_check_service_available($this->post_type))
            {
                return;
            }
            if(!function_exists('st_reg_post_type')) return;
            // Rental ==============================================================
            $labels = array(
                'name'               => __( 'Rental', ST_TEXTDOMAIN ),
                'singular_name'      => __( 'Rental', ST_TEXTDOMAIN ),
                'menu_name'          => __( 'Rental', ST_TEXTDOMAIN ),
                'name_admin_bar'     => __( 'Rental', ST_TEXTDOMAIN ),
                'add_new'            => __( 'Add Rental', ST_TEXTDOMAIN ),
                'add_new_item'       => __( 'Add New Rental', ST_TEXTDOMAIN ),
                'new_item'           => __( 'New Rental', ST_TEXTDOMAIN ),
                'edit_item'          => __( 'Edit Rental', ST_TEXTDOMAIN ),
                'view_item'          => __( 'View Rental', ST_TEXTDOMAIN ),
                'all_items'          => __( 'All Rental', ST_TEXTDOMAIN ),
                'search_items'       => __( 'Search Rental', ST_TEXTDOMAIN ),
                'parent_item_colon'  => __( 'Parent Rental:', ST_TEXTDOMAIN ),
                'not_found'          => __( 'No Rental found.', ST_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No Rental found in Trash.', ST_TEXTDOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => get_option( 'rental_permalink' ,'st_rental' ) ),
                'capability_type'    => 'post',
                'hierarchical'       => false,
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'menu_icon'          =>'dashicons-admin-home-st'
            );
            st_reg_post_type( 'st_rental', $args );// post type rental

            /**
             *@since 1.1.3
             * Rental room
             **/
            $labels = array(
                'name'               => __( 'Room(s)', ST_TEXTDOMAIN ),
                'singular_name'      => __( 'Room', ST_TEXTDOMAIN ),
                'menu_name'          => __( 'Room(s)', ST_TEXTDOMAIN ),
                'name_admin_bar'     => __( 'Room', ST_TEXTDOMAIN ),
                'add_new'            => __( 'Add New', ST_TEXTDOMAIN ),
                'add_new_item'       => __( 'Add New Room', ST_TEXTDOMAIN ),
                'new_item'           => __( 'New Room', ST_TEXTDOMAIN ),
                'edit_item'          => __( 'Edit Room', ST_TEXTDOMAIN ),
                'view_item'          => __( 'View Room', ST_TEXTDOMAIN ),
                'all_items'          => __( 'All Rooms', ST_TEXTDOMAIN ),
                'search_items'       => __( 'Search Rooms', ST_TEXTDOMAIN ),
                'parent_item_colon'  => __( 'Parent Rooms:', ST_TEXTDOMAIN ),
                'not_found'          => __( 'No rooms found.', ST_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No rooms found in Trash.', ST_TEXTDOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'query_var'          => true,
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'menu_icon'               =>'dashicons-admin-home-st',
                'exclude_from_search'=>true,
                'show_in_menu'       => 'edit.php?post_type=st_rental',
            );

            st_reg_post_type( 'rental_room', $args );
        }

        /**
         *
         * @since 1.1.0
         * */
        function get_search_fields_name($fields,$post_type)
        {
            if($post_type==$this->post_type)
            {
                $fields=array(
                    'location'=>array(
                        'value'=>'location',
                        'label'=>__('Location',ST_TEXTDOMAIN)
                    ),
                    'list_location'=>array(
                        'value'=>'list_location',
                        'label'=>__('Location List',ST_TEXTDOMAIN)
                    ),
                    'checkin'=>array(
                        'value'=>'checkin',
                        'label'=>__('Check in',ST_TEXTDOMAIN)
                    ),
                    'checkout'=>array(
                        'value'=>'checkout',
                        'label'=>__('Check out',ST_TEXTDOMAIN)
                    ),
                    'adult'=>array(
                        'value'=>'adult',
                        'label'=>__('Adult',ST_TEXTDOMAIN)
                    ),
                    'children'=>array(
                        'value'=>'children',
                        'label'=>__('Children',ST_TEXTDOMAIN)
                    ),
                    'room_num'=>array(
                        'value'=>'room_num',
                        'label'=>__('Room(s)',ST_TEXTDOMAIN)
                    ),
                    'taxonomy'=>array(
                        'value'=>'taxonomy',
                        'label'=>__('Taxonomy',ST_TEXTDOMAIN)
                    )

                );
            }
            return $fields;
        }

        /**
         *
         * @since 1.0.9
         * */
        function _add_metabox()
        {
            $this->metabox[] = array(
                'id'          => 'st_location',
                'title'       => __( 'Rental Details', ST_TEXTDOMAIN),
                'desc'        => '',
                'pages'       => array( 'st_rental' ),
                'context'     => 'normal',
                'priority'    => 'high',
                'fields'      => array(
                    array(
                        'label'       => __( 'Rental Information', ST_TEXTDOMAIN),
                        'id'          => 'detail_tab',
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
                        'id'          =>'rental_number',
                        'label'       =>__('Numbers',ST_TEXTDOMAIN),
                        'desc'        =>__('Number of rental available for booking',ST_TEXTDOMAIN),
                        'type'        =>'text',
                        'std'         =>1
                    ),
                    array(
                        'id'          =>'rental_max_adult',
                        'label'       =>__('Max of Adult',ST_TEXTDOMAIN),
                        'desc'       =>__('Max of Adult',ST_TEXTDOMAIN),
                        'type'        =>'numeric-slider',
                        'min_max_step'=>'1,20,1',
                        'std'         => 1
                    ),
                    array(
                        'id'          =>'rental_max_children',
                        'label'       =>__('Max of Children',ST_TEXTDOMAIN),
                        'desc'       =>__('Max of Children',ST_TEXTDOMAIN),
                        'type'        =>'numeric-slider',
                        'min_max_step'=>'1,20,1',
                        'std'         => 1
                    ),
                    array(
                        'label'       => __( 'Custom Layout', ST_TEXTDOMAIN),
                        'id'          => 'custom_layout',
                        'post_type'        => 'st_layouts',
                        'desc'        => __( 'Address of Rental', ST_TEXTDOMAIN),
                        'type'        => 'select',
                        'choices'     => st_get_layout('st_rental')
                    ),
                    array(
                        'label'       => __( 'Location', ST_TEXTDOMAIN),
                        'id'          => 'location_id',
                        'type'        => 'post_select_ajax',
                        'desc'        => __( 'Location of Rental', ST_TEXTDOMAIN),
                        'post_type'   =>'location'
                    ),
                    array(
                        'label'       => __( 'Address', ST_TEXTDOMAIN),
                        'id'          => 'address',
                        'type'        => 'text',
                        'desc'        => __( 'Address of Rental', ST_TEXTDOMAIN),
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
                        'label'       => __( 'Gallery', ST_TEXTDOMAIN),
                        'id'          => 'gallery',
                        'type'        => 'gallery',
                        'desc'        => __( 'Rental Gallery', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Video', ST_TEXTDOMAIN),
                        'id'          => 'video',
                        'type'        => 'text',
                        'desc'        => __( 'Youtube or Video url', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Agent Information', ST_TEXTDOMAIN),
                        'id'          => 'agent_tab',
                        'type'        => 'tab'
                    ),array(
                        'label'       => __( 'Agent Email', ST_TEXTDOMAIN),
                        'id'          => 'agent_email',
                        'type'        => 'text',
                        'desc'        => __( 'Agent Email. This email will received email about new booking', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Agent Website', ST_TEXTDOMAIN),
                        'id'          => 'agent_website',
                        'type'        => 'text',
                        'desc'        => __( 'Agent Website', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       => __( 'Agent Phone', ST_TEXTDOMAIN),
                        'id'          => 'agent_phone',
                        'type'        => 'text',
                        'desc'        => __( 'Agent Phone', ST_TEXTDOMAIN),
                    )
                ,array(
                        'label'       => __( 'Rental Price', ST_TEXTDOMAIN),
                        'id'          => 'price_tab',
                        'type'        => 'tab'
                    )
                ,array(
                        'label'       => sprintf( __( 'Price (%s)', ST_TEXTDOMAIN),TravelHelper::get_default_currency('symbol')),
                        'id'          => 'price',
                        'type'        => 'text',
                        'desc'        =>__('Regular Price',ST_TEXTDOMAIN)
                    )
                ,array(
                        'label'       => __( 'Discount Rate', ST_TEXTDOMAIN),
                        'id'          => 'discount_rate',
                        'type'        => 'text',
                        'desc'        =>__('Discount Rate By %',ST_TEXTDOMAIN)
                    )
                ,array(
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
                    array(
                        'label' => __('Rentals Options',ST_TEXTDOMAIN),
                        'id' => 'rental_options',
                        'type' => 'tab'
                    ),
                    array(
                        'label' => __('Booking Period',ST_TEXTDOMAIN),
                        'id' => 'rentals_booking_period',
                        'type'        => 'numeric-slider',
                        'min_max_step'=> '0,30,1',
                        'std' => 0,
                        'desc'        => __( 'The time period allowed booking.', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Rental external booking',ST_TEXTDOMAIN),
                        'id' => 'st_rental_external_booking',
                        'type'        => 'on-off',
                        'std' => "off",
                    ),
                    array(
                        'label' => __('Rental external booking link',ST_TEXTDOMAIN),
                        'id' => 'st_rental_external_booking_link',
                        'type'        => 'text',
                        'std' => "",
                        'condition'   =>'st_rental_external_booking:is(on)',
                        'desc'  =>"<em>".__('Notice: Must be http://...',ST_TEXTDOMAIN)."</em>",
                    )
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
            $custom_field = self::get_custom_fields();
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
        /**
         *
         * @since 1.0.9
         * */
        static function get_custom_fields()
        {
            return st()->get_option('rental_unlimited_custom_field',array());
        }

        function add_col_header($defaults)
        {
            $this->array_splice_assoc($defaults,2,0,array('layout_id'=>__('Layout',ST_TEXTDOMAIN)));

            return $defaults;
        }
        function add_col_content($column_name, $post_ID)
        {
            if ($column_name == 'layout_id') {
                // show content of 'directors_name' column
                $parent=get_post_meta($post_ID,'custom_layout',true);

                if($parent){
                    echo "<a href='".get_edit_post_link($parent)."' target='_blank'>".get_the_title($parent)."</a>";
                }else
                {
                    $layout=st()->get_option('rental_single_layout');
                    if($layout){
                        echo "<a href='".get_edit_post_link($layout)."' target='_blank'>".get_the_title($layout)."</a>";
                    }else{

                    }
                }

            }
        }
        function meta_update_sale_price($post_id)
        {
            if ( wp_is_post_revision( $post_id ) )
                return;
            $post_type=get_post_type($post_id);
            if($post_type=='st_rental')
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
                    $message=st()->load_template('email/booking_infomation',null,array('order_id'=>$order));

                    echo ($message);die;
                }


                if($order){
                    $check=STCart::send_mail_after_booking($order);
                }
            }

            wp_safe_redirect(self::$booking_page.'&send_mail=success');
        }
        static  function  st_room_select_ajax()
        {
            extract( wp_parse_args($_GET,array(
                'post_type'=>'',
                'q'=>''
            )));


            query_posts(array('post_type'=>$post_type,'posts_per_page'=>10,'s'=>$q));

            $r=array(
                'items'=>array(),
                't'=>array('post_type'=>$post_type,'posts_per_page'=>10,'s'=>$q)
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
                and $_GET['post_type']=='st_rental'
                and isset($_GET['page'])
                and $_GET['page']='st_rental_booking'
            ) return true;
            return false;
        }

        function new_menu_page()
        {
            //Add booking page
            add_submenu_page('edit.php?post_type=st_rental',__('Rental Booking',ST_TEXTDOMAIN), __('Rental Booking',ST_TEXTDOMAIN), 'manage_options', 'st_rental_booking', array($this,'__rental_booking_page'));
        }

        function  __rental_booking_page(){

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
                echo balanceTags($this->load_view('rental/booking_index',false));
            }

        }
        function add_booking()
        {

            echo balanceTags($this->load_view('rental/booking_edit',false,array('page_title'=>__('Add new Rental Booking',ST_TEXTDOMAIN))));
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

            echo balanceTags($this->load_view('rental/booking_edit'));
        }
        function _add_booking()
        {
            if(!check_admin_referer( 'shb_action', 'shb_field' )) die;

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
                    'st_tax'=>'',

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
            if(!check_admin_referer( 'shb_action', 'shb_field' )) die;
            //Update Order
            $orderitem=array(
                'item_number',
                'item_id',
                'item_price',
                'check_in',
                'check_out',
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


            STAdmin::set_message(__('Update Success',ST_TEXTDOMAIN),'updated');
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



    }
    new STAdminRental();
}