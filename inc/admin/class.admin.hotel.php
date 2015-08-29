<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdminHotel
 *
 * Created by ShineTheme
 *
 */
if (!class_exists('STAdminHotel')) {

    class STAdminHotel extends STAdmin
    {

        static $parent_key = 'room_parent';
        static $booking_page;
        protected $post_type='st_hotel';

        /**
         *
         *
         * @update 1.1.3
         * */
        function __construct()
        {

            add_action('init', array($this, 'init_post_type'));

            if (!st_check_service_available($this->post_type)) return;

            add_action('init', array($this, 'init_metabox'));

            self::$booking_page = admin_url('edit.php?post_type=st_hotel&page=st_hotel_booking');


            //add colum for rooms
            add_filter('manage_hotel_room_posts_columns', array($this, 'add_col_header'), 10);
            add_action('manage_hotel_room_posts_custom_column', array($this, 'add_col_content'), 10, 2);

            //add colum for rooms
            add_filter('manage_st_hotel_posts_columns', array($this, 'add_hotel_col_header'), 10);
            add_action('manage_st_hotel_posts_custom_column', array($this, 'add_hotel_col_content'), 10, 2);

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

                    case 'resend_email':
                        add_action('init', array($this, '_resend_mail'));
                        //$this->_resend_mail();
                        break;
                }
            }

            if (isset($_GET['send_mail']) and $_GET['send_mail'] == 'success') {
                self::set_message(__('Email sent', ST_TEXTDOMAIN), 'updated');
            }

            add_action('wp_ajax_st_room_select_ajax', array(__CLASS__, 'st_room_select_ajax'));

            parent::__construct();

            add_action('save_post', array($this, '_update_avg_price'), 50);
        }

        /**
         * Init the post type
         *
         * */
        function init_post_type()
        {
            if(!st_check_service_available($this->post_type))
            {
                return;
            }

            if(!function_exists('st_reg_post_type')) return;

            $labels = array(
                'name'               => __( 'Hotels', ST_TEXTDOMAIN ),
                'singular_name'      => __( 'Hotel Name', ST_TEXTDOMAIN ),
                'menu_name'          => __( 'Hotels', ST_TEXTDOMAIN ),
                'name_admin_bar'     => __( 'Hotel Name', ST_TEXTDOMAIN ),
                'add_new'            => __( 'Add New', ST_TEXTDOMAIN ),
                'add_new_item'       => __( 'Add New Hotel', ST_TEXTDOMAIN ),
                'new_item'           => __( 'New Hotel', ST_TEXTDOMAIN ),
                'edit_item'          => __( 'Edit Hotel', ST_TEXTDOMAIN ),
                'view_item'          => __( 'View Hotel', ST_TEXTDOMAIN ),
                'all_items'          => __( 'All Hotels', ST_TEXTDOMAIN ),
                'search_items'       => __( 'Search Hotels', ST_TEXTDOMAIN ),
                'parent_item_colon'  => __( 'Parent Hotels:', ST_TEXTDOMAIN ),
                'not_found'          => __( 'No hotels found.', ST_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No hotels found in Trash.', ST_TEXTDOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'menu_icon'               =>'dashicons-building-yl',
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => get_option( 'hotel_permalink' ,'st_hotel' ) ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                //'menu_position'      => null,
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
            );

            st_reg_post_type( 'st_hotel', $args );

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
                //'show_in_menu'       => 'edit.php?post_type=hotel',
                'query_var'          => true,
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'menu_icon'               =>'dashicons-building-yl',
                'exclude_from_search'=>true
            );

            st_reg_post_type( 'hotel_room', $args );

            $name=__('Room Type',ST_TEXTDOMAIN);
            $labels = array(
                'name'              => $name ,
                'singular_name'     => $name,
                'search_items'      => sprintf(__( 'Search %s' ,ST_TEXTDOMAIN),$name),
                'all_items'         => sprintf(__( 'All %s' ,ST_TEXTDOMAIN),$name),
                'parent_item'       => sprintf(__( 'Parent %s' ,ST_TEXTDOMAIN),$name),
                'parent_item_colon' => sprintf(__( 'Parent %s' ,ST_TEXTDOMAIN),$name),
                'edit_item'         => sprintf(__( 'Edit %s' ,ST_TEXTDOMAIN),$name),
                'update_item'       => sprintf(__( 'Update %s' ,ST_TEXTDOMAIN),$name),
                'add_new_item'      => sprintf(__( 'New %s' ,ST_TEXTDOMAIN),$name),
                'new_item_name'     => sprintf(__( 'New %s' ,ST_TEXTDOMAIN),$name),
                'menu_name'         => $name,
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           =>true,
                'show_ui'           => 'edit.php?post_type=st_hotel',
                'query_var'         => true,
            );

            st_reg_taxonomy('room_type' ,'hotel_room', $args );
        }
        /**
         *
         * @since 1.1.1
         * */
        function init_metabox()
        {

            $this->metabox[] = array(
                'id'       => 'hotel_metabox',
                'title'    => __('Hotel Information', ST_TEXTDOMAIN),
                'desc'     => '',
                'pages'    => array('st_hotel'),
                'context'  => 'normal',
                'priority' => 'high',
                'fields'   => array(
                    array(
                        'label' => __('Hotel Detail', ST_TEXTDOMAIN),
                        'id'    => 'detail_tab',
                        'type'  => 'tab'
                    ),
                    array(
                        'label' => __('Set as Featured', ST_TEXTDOMAIN),
                        'id'    => 'is_featured',
                        'type'  => 'on-off',
                        'desc'  => __('Set this location is featured', ST_TEXTDOMAIN),
                        'std'   => 'off'
                    ),
                    array(
                        'label' => __('Hotel Logo', ST_TEXTDOMAIN),
                        'id'    => 'logo',
                        'type'  => 'upload',
                        'class' => 'ot-upload-attachment-id',
                        'desc'  => __('Upload your hotel\'s logo; Recommend: 260px x 195px', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'   => __('Card Accepted', ST_TEXTDOMAIN),
                        'desc'    => __('Card Accepted', ST_TEXTDOMAIN),
                        'id'      => 'card_accepted',
                        'type'    => 'checkbox',
                        'choices' => $this->get_card_accepted_list()
                    ),

                    array(
                        'label' => __('Min Price', ST_TEXTDOMAIN),
                        'id'    => 'min_price',
                        'type'  => 'text',
                        'desc'  => __('Min price of this hotel', ST_TEXTDOMAIN),
                        'std'   => 0
                    ),

                    array(
                        'label'     => __('Custom Layout', ST_TEXTDOMAIN),
                        'id'        => 'st_custom_layout',
                        'post_type' => 'st_layouts',
                        'desc'      => __('Detail Hotel Layout', ST_TEXTDOMAIN),
                        'type'      => 'select',
                        'choices'   => st_get_layout('st_hotel')
                    ),

                    array(
                        'label' => __('Hotel Email', ST_TEXTDOMAIN),
                        'id'    => 'email',
                        'type'  => 'text',
                        'desc'  => __('Hotel Email Address, this address will received email when have new booking', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label' => __('Hotel Website', ST_TEXTDOMAIN),
                        'id'    => 'website',
                        'type'  => 'text',
                        'desc'  => __('Hotel Website. Ex: <em>http://domain.com</em>', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Hotel Phone', ST_TEXTDOMAIN),
                        'id'    => 'phone',
                        'type'  => 'text',
                        'desc'  => __('Hotel Phone Number', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Fax Number', ST_TEXTDOMAIN),
                        'id'    => 'fax',
                        'type'  => 'text',
                        'desc'  => __('Hotel Fax Number', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label' => __('Gallery', ST_TEXTDOMAIN),
                        'id'    => 'gallery',
                        'type'  => 'gallery',
                        'desc'  => __('Pick your own image for this hotel', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Hotel Video', ST_TEXTDOMAIN),
                        'id'    => 'video',
                        'type'  => 'text',
                        'desc'  => __('Please use youtube or vimeo video', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'        => __('Star Rating', ST_TEXTDOMAIN),
                        'desc'         => __('Star Rating', ST_TEXTDOMAIN),
                        'id'           => 'hotel_star',
                        'type'         => 'numeric-slider',
                        'min_max_step' => '0,5,1',
                        'std'          => 0
                    ),
                    array(
                        'label' => __('Hotel Location', ST_TEXTDOMAIN),
                        'id'    => 'location_tab',
                        'type'  => 'tab'
                    ),
                    array(
                        'label'     => __('Location', ST_TEXTDOMAIN),
                        'id'        => 'id_location',
                        'type'      => 'post_select_ajax',
                        'desc'      => __('Search for location', ST_TEXTDOMAIN),
                        'post_type' => 'location',
                    ),

                    array(
                        'label' => __('Address', ST_TEXTDOMAIN),
                        'id'    => 'address',
                        'type'  => 'text',
                        'desc'  => __('Hotel Address', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Latitude', ST_TEXTDOMAIN),
                        'id'    => 'map_lat',
                        'type'  => 'text',
                        'desc'  => __('Latitude <a href="http://www.latlong.net/" target="_blank">Get here</a>', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label' => __('Longitude', ST_TEXTDOMAIN),
                        'id'    => 'map_lng',
                        'type'  => 'text',
                        'desc'  => __('Longitude', ST_TEXTDOMAIN),
                    ),

                    array(
                        'label' => __('Map Zoom', ST_TEXTDOMAIN),
                        'id'    => 'map_zoom',
                        'type'  => 'text',
                        'desc'  => __('Map Zoom', ST_TEXTDOMAIN),
                        'std'   => 13
                    ),
                    array(
                        'label' => __('Sale & Price setting', ST_TEXTDOMAIN),
                        'id'    => 'sale_number_tab',
                        'type'  => 'tab'
                    ),

                    array(
                        'label' => __('Total Sale Number', ST_TEXTDOMAIN),
                        'id'    => 'total_sale_number',
                        'type'  => 'text',
                        'desc'  => __('Total Number Booking of this hotel', ST_TEXTDOMAIN),
                        'std'   => 0
                    ),

                    array(
                        'label' => __('Auto calculate price avg', ST_TEXTDOMAIN),
                        'id'    => 'is_auto_caculate',
                        'type'  => 'on-off',
                        'desc'  => __('Auto calculate price avg', ST_TEXTDOMAIN),
                        'std'   => 'on'
                    ),
                    array(
                        'label'      => __('Price AVG', ST_TEXTDOMAIN),
                        'id'         => 'price_avg',
                        'type'       => 'text',
                        'desc'       => __('Price AVG', ST_TEXTDOMAIN),
                        'std'        => 0,
                        'conditions' => 'is_auto_caculate:is(on)'
                    ),
                    array(
                        'label' => __("Min Price", ST_TEXTDOMAIN),
                        'desc'  => __("Min Price", ST_TEXTDOMAIN),
                        'id'    => 'min_price',
                        'type'  => 'text',
                        'std'   => 0,
                    ),


                    array(
                        'label' => __('Other Options', ST_TEXTDOMAIN),
                        'id'    => 'hotel_options',
                        'type'  => 'tab'
                    ),
                    array(
                        'label'        => __('Booking Period', ST_TEXTDOMAIN),
                        'id'           => 'hotel_booking_period',
                        'type'         => 'numeric-slider',
                        'min_max_step' => '0,30,1',
                        'std'          => 0,
                        'desc'         => __('The time period allowed booking.', ST_TEXTDOMAIN),
                    ),

//            array(
//                'label'       => __( 'Avg Rate Review', ST_TEXTDOMAIN),
//                'id'          => 'rate_review',
//                'type'        => 'numeric-slider',
//                'min_max_step'=> '1,5,1',
//            ),


                )
            );

            $custom_field = st()->get_option('hotel_unlimited_custom_field');
            if (!empty($custom_field) and is_array($custom_field)) {
                $this->metabox[0]['fields'][] = array(
                    'label' => __('Custom fields', ST_TEXTDOMAIN),
                    'id'    => 'custom_field_tab',
                    'type'  => 'tab'
                );
                foreach ($custom_field as $k => $v) {
                    $key = str_ireplace('-', '_', 'st_custom_' . sanitize_title($v['title']));
                    $this->metabox[0]['fields'][] = array(
                        'label' => $v['title'],
                        'id'    => $key,
                        'type'  => $v['type_field'],
                        'desc'  => '<input value=\'[st_custom_meta key="' . $key . '"]\' type=text readonly />',
                        'std'   => $v['default_field']
                    );
                }
            }

            parent::register_metabox($this->metabox);
        }

        /**
         *
         *
         * @since 1.1.1
         * */
        function get_card_accepted_list()
        {
            $data = array();

            $options = st()->get_option('booking_card_accepted', array());

            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    $data[] = array(
                        'label' => $value['title'],
                        'src'   => $value['image'],
                        'value' => sanitize_title_with_dashes($value['title'])
                    );
                }
            }

            return $data;
        }

        /**
         *
         *
         * @since 1.0.9
         *
         */
        static function _update_avg_price($post_id = FALSE)
        {
            if (!$post_id) {
                $post_id = get_the_ID();
            }
            $post_type = get_post_type($post_id);
            if ($post_type == 'st_hotel') {
                $hotel_id = $post_id;
                $is_auto_caculate = get_post_meta($hotel_id, 'is_auto_caculate', TRUE);
                if ($is_auto_caculate != 'off') {
                    $query = array(
                        'post_type'      => 'hotel_room',
                        'posts_per_page' => 100,
                        'meta_key'       => 'room_parent',
                        'meta_value'     => $hotel_id
                    );
                    $traver = new WP_Query($query);
                    $price = 0;
                    while ($traver->have_posts()) {
                        $traver->the_post();
                        $price += get_post_meta(get_the_ID(), 'price', TRUE);
                    }
                    wp_reset_query();
                    if ($traver->post_count) {
                        $avg_price = $price / $traver->post_count;
                        update_post_meta($hotel_id, 'price_avg', $avg_price);
                    }
                }
            }
        }


        function _resend_mail()
        {
            $order_item = isset($_GET['order_item_id']) ? $_GET['order_item_id'] : FALSE;

            $test = isset($_GET['test']) ? $_GET['test'] : FALSE;


            if ($order_item) {

                $order = $order_item;

                if ($test) {
                    $message = st()->load_template('email/booking_infomation', NULL, array('order_id' => $order, 'confirm_link' => 'xxx'));

                    echo($message);

                    die;

                }


                if ($order) {
                    STCart::send_mail_after_booking($order);
                }
            }

            wp_safe_redirect(self::$booking_page . '&send_mail=success');
        }

        static function  st_room_select_ajax()
        {
            extract(wp_parse_args($_GET, array(
                'room_parent' => '',
                'post_type'   => '',
                'q'           => ''
            )));


            query_posts(array('post_type' => $post_type, 'posts_per_page' => 10, 's' => $q, 'meta_key' => 'room_parent', 'meta_value' => $room_parent));

            $r = array(
                'items' => array(),
            );
            while (have_posts()) {
                the_post();
                $r['items'][] = array(
                    'id'          => get_the_ID(),
                    'name'        => get_the_title(),
                    'description' => ''
                );
            }

            wp_reset_query();

            echo json_encode($r);
            die;

        }

        static function  add_edit_scripts()
        {
            wp_enqueue_script('select2');
            wp_enqueue_script('st-edit-booking', get_template_directory_uri() . '/js/admin/edit-booking.js', array('jquery'), NULL, TRUE);
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jjquery-ui.theme.min.css', get_template_directory_uri() . '/css/admin/jquery-ui.min.css');
        }

        static function is_booking_page()
        {
            if (is_admin()
                and isset($_GET['post_type'])
                and $_GET['post_type'] == 'st_hotel'
                and isset($_GET['page'])
                and $_GET['page'] = 'st_hotel_booking'
            ) return TRUE;

            return FALSE;
        }

        function add_menu_page()
        {
            //Add booking page

            add_submenu_page('edit.php?post_type=st_hotel',__('Hotel Bookings',ST_TEXTDOMAIN), __('Hotel Bookings',ST_TEXTDOMAIN), 'manage_options', 'st_hotel_booking', array($this,'__hotel_booking_page'));
        }

        function  __hotel_booking_page()
        {

            $section = isset($_GET['section']) ? $_GET['section'] : FALSE;

            if ($section) {
                switch ($section) {
                    case "edit_order_item":
                        $this->edit_order_item();
                        break;
                    case 'add_booking':
                        $this->add_booking();
                        break;
                }
            } else {

                $action = isset($_POST['st_action']) ? $_POST['st_action'] : FALSE;
                switch ($action) {
                    case "delete":
                        $this->_delete_items();
                        break;
                }
                echo balanceTags($this->load_view('hotel/booking_index', FALSE));
            }

        }

        function add_booking()
        {

            echo balanceTags($this->load_view('hotel/booking_edit', FALSE, array('page_title' => __('Add new Hotel Booking', ST_TEXTDOMAIN))));
        }

        function _delete_items()
        {

            if (empty($_POST) or !check_admin_referer('shb_action', 'shb_field')) {
                //// process form data, e.g. update fields
                return;
            }
            $ids = isset($_POST['post']) ? $_POST['post'] : array();
            if (!empty($ids)) {
                foreach ($ids as $id)
                    wp_delete_post($id, TRUE);

            }

            STAdmin::set_message(__("Delete item(s) success", ST_TEXTDOMAIN), 'updated');

        }

        function edit_order_item()
        {
            $item_id = isset($_GET['order_item_id']) ? $_GET['order_item_id'] : FALSE;
            if (!$item_id or get_post_type($item_id) != 'st_order') {
                //wp_safe_redirect(self::$booking_page); die;
                return FALSE;
            }


            if (isset($_POST['submit']) and $_POST['submit']) $this->_save_booking($item_id);

            echo balanceTags($this->load_view('hotel/booking_edit'));
        }

        function _add_booking()
        {
            if (!check_admin_referer('shb_action', 'shb_field')) die;

            //Create Order
            $order = array(
                'post_title'  => __('Order', ST_TEXTDOMAIN) . ' - ' . date(get_option('date_format')) . ' @ ' . date(get_option('time_format')),
                'post_type'   => 'st_order',
                'post_status' => 'publish'
            );

            $order_id = wp_insert_post($order);

            if ($order_id) {

                $check_out_field = STCart::get_checkout_fields();

                if (!empty($check_out_field)) {
                    foreach ($check_out_field as $field_name => $field_desc) {
                        update_post_meta($order_id, $field_name, STInput::post($field_name));
                    }
                }


                $user_fields = array(
                    'id_user' => '',
                    'status'  => '',
                    'st_tax'  => '',
                    'room_id' => ''

                );
                $data = wp_parse_args($_POST, $user_fields);
                if ($order_id) {
                    foreach ($user_fields as $val => $value) {
                        update_post_meta($order_id, $val, $data[ $val ]);
                    }
                }
                update_post_meta($order_id, 'payment_method', 'submit_form');

                //Save Items

                $item_data = array(
                    'item_number' => '',
                    'item_id'     => '',
                    'item_price'  => '',
                    'check_in'    => '',
                    'check_out'   => ''
                );
                $data = wp_parse_args($_POST, $item_data);

                $item_id = $order_id;
                if ($item_id) {
                    foreach ($item_data as $val => $value) {

                        if ($val == 'check_in' or $val == 'check_out') {
                            update_post_meta($item_id, $val, date('Y-m-d', strtotime($data[ $val ])));
                        } else
                            update_post_meta($item_id, $val, $data[ $val ]);
                    }


                    do_action('st_booking_success', $order_id);

                    //Success
                    wp_safe_redirect(self::$booking_page);
                }


            }
            //STAdmin::set_message('Update Success','updated');

        }

        function _save_booking($order_id)
        {
            if (!check_admin_referer('shb_action', 'shb_field')) die;
            //Update Order
            $orderitem = array(
                'item_number' => '',
                'item_id'     => '',
                'item_price'  => '',
                'check_in'    => '',
                'check_out'   => '',
                'room_id'     => ''
            );

            $data = wp_parse_args($_POST, $orderitem);

            foreach ($orderitem as $val => $value) {

                if ($val == 'check_in' or $val == 'check_out') {
                    update_post_meta($order_id, $val, date('Y-m-d', strtotime($data[ $val ])));
                } else
                    update_post_meta($order_id, $val, $data[ $val ]);
            }

            //Update User
            $order_parent = $order_id;
            $id_user = isset($_POST['id_user']) ? $_POST['id_user'] : FALSE;
            if ($order_parent and $id_user) {

                update_post_meta($order_parent, 'id_user', $id_user);
            }

            $check_out_field = STCart::get_checkout_fields();

            if (!empty($check_out_field)) {
                foreach ($check_out_field as $field_name => $field_desc) {
                    update_post_meta($order_parent, $field_name, STInput::post($field_name));
                }
            }

            $user_fields = array(

                'status' => '',
                'st_tax' => ''
            );
            $data = wp_parse_args($_POST, $user_fields);
            if ($order_parent) {
                foreach ($user_fields as $val => $value) {
                    update_post_meta($order_parent, $val, $data[ $val ]);
                }
            }


            STAdmin::set_message('Update Success', 'updated');
        }

        function is_able_edit()
        {
            $item_id = isset($_GET['order_item_id']) ? $_GET['order_item_id'] : FALSE;
            if (!$item_id or get_post_type($item_id) != 'st_order') {
                wp_safe_redirect(self::$booking_page);
                die;
            }

            return TRUE;
        }


        function add_col_header($defaults)
        {

            $this->array_splice_assoc($defaults,2,0,array('room_number'=>__('Room(s)',ST_TEXTDOMAIN)));
            $this->array_splice_assoc($defaults,2,0,array('hotel_parent'=>__('Hotel Name',ST_TEXTDOMAIN)));

            return $defaults;
        }

        function add_hotel_col_header($defaults)
        {
            $this->array_splice_assoc($defaults, 2, 0, array('hotel_layout' => __('Layout', ST_TEXTDOMAIN)));

            return $defaults;
        }

        function array_splice_assoc(&$input, $offset, $length = 0, $replacement = array())
        {
            $tail = array_splice($input, $offset);
            $extracted = array_splice($tail, 0, $length);
            $input += $replacement + $tail;

            return $extracted;
        }

        function add_col_content($column_name, $post_ID)
        {

            if ($column_name == 'hotel_parent') {
                // show content of 'directors_name' column
                $parent = get_post_meta($post_ID, 'room_parent', TRUE);

                if ($parent) {
                    echo "<a href='" . get_edit_post_link($parent) . "'>" . get_the_title($parent) . "</a>";
                }

            }
            if ($column_name == 'room_number') {
                echo get_post_meta($post_ID, 'number_room', TRUE);
            }
        }

        function add_hotel_col_content($column_name, $post_ID)
        {

            if ($column_name == 'hotel_layout') {
                // show content of 'directors_name' column
                $parent = get_post_meta($post_ID, 'st_custom_layout', TRUE);

                if ($parent) {
                    echo "<a href='" . get_edit_post_link($parent) . "'>" . get_the_title($parent) . "</a>";
                } else {
                    echo __('Default', ST_TEXTDOMAIN);
                }


            }
        }

    }

    new STAdminHotel();
}