<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdminCars
 *
 * Created by ShineTheme
 *
 */
if (!class_exists('STAdminCars')) {

    class STAdminCars extends STAdmin
    {
        static $booking_page;
        static $data_term;
        protected $post_type="st_cars";

        /**
         *
         *
         * @update 1.1.3
         * */
        function __construct()
        {
            add_action('init',array($this,'_init_post_type'));

            if (!st_check_service_available($this->post_type)) return;

            add_action('init', array($this, 'get_list_value_taxonomy'), 98);
            add_action('init', array($this, 'init_metabox'), 99);

            //add_action( 'save_post', array($this,'cars_update_location') );
            //===============================================================
            self::$booking_page = admin_url('edit.php?post_type=st_cars&page=st_car_booking');
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

                    case 'resend_email_cars':
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
         * @since 1.1.3
         * */
        function _init_post_type()
        {
            if(!st_check_service_available($this->post_type))
            {
                return;
            }

            if(!function_exists('st_reg_post_type')) return;
            // Cars ==============================================================
            $labels = array(
                'name'               => __( 'Cars', ST_TEXTDOMAIN ),
                'singular_name'      => __( 'Car', ST_TEXTDOMAIN ),
                'menu_name'          => __( 'Cars', ST_TEXTDOMAIN ),
                'name_admin_bar'     => __( 'Car', ST_TEXTDOMAIN ),
                'add_new'            => __( 'Add New', ST_TEXTDOMAIN ),
                'add_new_item'       => __( 'Add New Car', ST_TEXTDOMAIN ),
                'new_item'           => __( 'New Car', ST_TEXTDOMAIN ),
                'edit_item'          => __( 'Edit Car', ST_TEXTDOMAIN ),
                'view_item'          => __( 'View Car', ST_TEXTDOMAIN ),
                'all_items'          => __( 'All Cars', ST_TEXTDOMAIN ),
                'search_items'       => __( 'Search Cars', ST_TEXTDOMAIN ),
                'parent_item_colon'  => __( 'Parent Cars:', ST_TEXTDOMAIN ),
                'not_found'          => __( 'No Cars found.', ST_TEXTDOMAIN ),
                'not_found_in_trash' => __( 'No Cars found in Trash.', ST_TEXTDOMAIN )
            );

            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => get_option( 'car_permalink' ,'st_car' ) ),
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                //'menu_position'      => null,
                'supports'           => array( 'author','title','editor','excerpt','thumbnail','comments' ),
                'menu_icon'          =>'dashicons-dashboard-st'
            );
            st_reg_post_type( 'st_cars', $args );

            // category cars
            $labels = array(
                'name'                       => __( 'Car Category', 'taxonomy general name', ST_TEXTDOMAIN ),
                'singular_name'              => __( 'Car Category', 'taxonomy singular name', ST_TEXTDOMAIN ),
                'search_items'               => __( 'Search Car Category' , ST_TEXTDOMAIN),
                'popular_items'              => __( 'Popular Car Category' , ST_TEXTDOMAIN),
                'all_items'                  => __( 'All Car Category', ST_TEXTDOMAIN ),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __( 'Edit Car Category' , ST_TEXTDOMAIN),
                'update_item'                => __( 'Update Car Category' , ST_TEXTDOMAIN),
                'add_new_item'               => __( 'Add New Car Category', ST_TEXTDOMAIN ),
                'new_item_name'              => __( 'New Pickup Car Category', ST_TEXTDOMAIN ),
                'separate_items_with_commas' => __( 'Separate Car Category  with commas' , ST_TEXTDOMAIN),
                'add_or_remove_items'        => __( 'Add or remove Car Category', ST_TEXTDOMAIN ),
                'choose_from_most_used'      => __( 'Choose from the most used Car Category', ST_TEXTDOMAIN ),
                'not_found'                  => __( 'No Car Category found.', ST_TEXTDOMAIN ),
                'menu_name'                  => __( 'Car Category', ST_TEXTDOMAIN ),
            );

            $args = array(
                'hierarchical'          => true,
                'labels'                => $labels,
                'show_ui'               => true,
                'show_admin_column'     => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'st_category_cars' ),
            );

            st_reg_taxonomy( 'st_category_cars', 'st_cars', $args );

            $labels = array(
                'name'                       => st_get_language('car_pickup_features'),
                'singular_name'              => st_get_language('car_pickup_features'),
                'search_items'               => st_get_language('car_search_pickup_features'),
                'popular_items'              => __( 'Popular Pickup Features' , ST_TEXTDOMAIN),
                'all_items'                  => __( 'All Pickup Features', ST_TEXTDOMAIN ),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __( 'Edit Pickup Feature' , ST_TEXTDOMAIN),
                'update_item'                => __( 'Update Pickup Feature' , ST_TEXTDOMAIN),
                'add_new_item'               => __( 'Add New Pickup Feature', ST_TEXTDOMAIN ),
                'new_item_name'              => __( 'New Pickup Feature Name', ST_TEXTDOMAIN ),
                'separate_items_with_commas' => __( 'Separate Pickup Features with commas' , ST_TEXTDOMAIN),
                'add_or_remove_items'        => __( 'Add or remove Pickup Features', ST_TEXTDOMAIN ),
                'choose_from_most_used'      => __( 'Choose from the most used Pickup Features', ST_TEXTDOMAIN ),
                'not_found'                  => __( 'No Pickup Features found.', ST_TEXTDOMAIN ),
                'menu_name'                  => __( 'Pickup Features', ST_TEXTDOMAIN ),
            );

            $args = array(
                'hierarchical'          => true,
                'labels'                => $labels,
                'show_ui'               => true,
                'show_admin_column'     => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'st_cars_pickup_features' ),
            );

            st_reg_taxonomy( 'st_cars_pickup_features', 'st_cars', $args );

        }
        /**
         *
         *
         *
         * @since 1.1.1
         * */
        static function get_list_value_taxonomy()
        {
            $data_value = array();
            $taxonomy = get_object_taxonomies('st_cars', 'object');

            foreach ($taxonomy as $key => $value) {
                if ($key != 'st_category_cars') {
                    if ($key != 'st_cars_pickup_features') {
                        if (is_admin() and !empty($_REQUEST['post'])) {
                            $data_term = get_the_terms($_REQUEST['post'], $key, TRUE);

                            if (!empty($data_term)) {
                                foreach ($data_term as $k => $v) {
                                    array_push(
                                        $data_value, array(
                                            'value'    => $v->term_id,
                                            'label'    => $v->name,
                                            'taxonomy' => $v->taxonomy
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
            }
            self::$data_term = $data_value;
        }

        /**
         *
         *
         * @since 1.1.1
         * */
        function init_metabox()
        {
            $this->metabox[] = array(
                'id'       => 'cars_metabox',
                'title'    => __('Cars Setting', ST_TEXTDOMAIN),
                'desc'     => '',
                'pages'    => array('st_cars'),
                'context'  => 'normal',
                'priority' => 'high',
                'fields'   => array(


                    array(
                        'label' => __('Car Details', ST_TEXTDOMAIN),
                        'id'    => 'room_car_tab',
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
                        'label'     => __('Location', ST_TEXTDOMAIN),
                        'id'        => 'id_location',
                        'type'      => 'post_select_ajax',
                        'desc'      => __('Search location here', ST_TEXTDOMAIN),
                        'post_type' => 'location',
                    ),

                    array(
                        'label'     => __('Detail Cars Layout', ST_TEXTDOMAIN),
                        'id'        => 'st_custom_layout',
                        'post_type' => 'st_layouts',
                        'desc'      => __('Detail Cars Layout', ST_TEXTDOMAIN),
                        'type'      => 'select',
                        'choices'   => st_get_layout('st_cars')
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
                        'label'    => __('Equipment Price List', ST_TEXTDOMAIN),
                        'desc'    => __('Equipment Price List', ST_TEXTDOMAIN),
                        'id'       => 'cars_equipment_list',
                        'type'     => 'list-item',
                        'settings' => array(
                            array(
                                'id'    => 'cars_equipment_list_price',
                                'label' => __('Price', ST_TEXTDOMAIN),
                                'type'  => 'text',
                            ),
                            array(
                                'id'    => 'price_unit',
                                'label' => __('Price Unit', ST_TEXTDOMAIN),
                                'desc' => __('You can choose <code>Fixed Price</code>, <code>Price per Hour</code>, <code>Price per Day</code>', ST_TEXTDOMAIN),
                                'type'  => 'select',
                                'choices'=>array(
                                    array(
                                        'value'=>'',
                                        'label'=>__('Fixed Price',ST_TEXTDOMAIN)
                                    ),
                                    array(
                                        'value'=>'per_hour',
                                        'label'=>__('Price per Hour',ST_TEXTDOMAIN)
                                    ),
                                    array(
                                        'value'=>'per_day',
                                        'label'=>__('Price per Day',ST_TEXTDOMAIN)
                                    ),
                                )
                            )
                        )
                    ),
                    array(
                        'label'    => __('Features', ST_TEXTDOMAIN),
                        'desc'    => __('Features', ST_TEXTDOMAIN),
                        'id'       => 'cars_equipment_info',
                        'type'     => 'list-item',
                        'settings' => array(
                            array(
                                'id'       => 'cars_equipment_taxonomy_id',
                                'label'    => __('Taxonomy', ST_TEXTDOMAIN),
                                'type'     => 'select',
                                'operator' => 'and',
                                'choices'  => self::$data_term
                            ),
                            array(
                                'id'    => 'cars_equipment_taxonomy_info',
                                'label' => __('Taxonomy Info', ST_TEXTDOMAIN),
                                'type'  => 'text',
                            )
                        )
                    ),
                    array(
                        'label' => __('Video', ST_TEXTDOMAIN),
                        'id'    => 'video',
                        'type'  => 'text',
                        'desc'  => __('Please use youtube or vimeo video', ST_TEXTDOMAIN)
                    ),
                    array(
                        'label' => __('Set as featured', ST_TEXTDOMAIN),
                        'id'    => 'cars_set_as_featured',
                        'type'  => 'on-off',
                        'desc'  => __('Set as featured', ST_TEXTDOMAIN),
                        'std'   => 'off'
                    ),
                    array(
                        'label' => __('Contact Details', ST_TEXTDOMAIN),
                        'id'    => 'room_contact_tab',
                        'type'  => 'tab'
                    ),
                    array(
                        'label' => __('Logo', ST_TEXTDOMAIN),
                        'id'    => 'cars_logo',
                        'type'  => 'upload',
                        'desc'  => __('Logo', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Car manufacturer name', ST_TEXTDOMAIN),
                        'id'    => 'cars_name',
                        'type'  => 'text',
                        'desc'  => __('Car manufacturer name', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Address', ST_TEXTDOMAIN),
                        'id'    => 'cars_address',
                        'type'  => 'text',
                        'desc'  => __('Address', ST_TEXTDOMAIN),
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
                        'label' => __('Email', ST_TEXTDOMAIN),
                        'id'    => 'cars_email',
                        'type'  => 'text',
                        'desc'  => __('E-mail Car Agent, this address will received email when have new booking', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Phone', ST_TEXTDOMAIN),
                        'id'    => 'cars_phone',
                        'type'  => 'text',
                        'desc'  => __('Phone', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('About', ST_TEXTDOMAIN),
                        'desc' => __('About', ST_TEXTDOMAIN),
                        'id'    => 'cars_about',
                        'type'  => 'textarea',
                    ),
                    array(
                        'label' => __('Price setting', ST_TEXTDOMAIN),
                        'id'    => '_price_car_tab',
                        'type'  => 'tab'
                    ),
                    array(
                        'label' => sprintf(__('Price (%s)', ST_TEXTDOMAIN), TravelHelper::get_default_currency('symbol')),
                        'id'    => 'cars_price',
                        'type'  => 'text',
                        'desc'  => __('Price', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Discount', ST_TEXTDOMAIN),
                        'id'    => 'discount',
                        'type'  => 'text',
                        'desc'  => __('%', ST_TEXTDOMAIN),
                        'std'   => 0
                    ),
                    array(
                        'label' => __('Sale Schedule', ST_TEXTDOMAIN),
                        'id'    => 'is_sale_schedule',
                        'type'  => 'on-off',
                        'std'   => 'off',
                    ),
                    array(
                        'label'     => __('Sale Price Date From', ST_TEXTDOMAIN),
                        'desc'      => __('Sale Price Date From', ST_TEXTDOMAIN),
                        'id'        => 'sale_price_from',
                        'type'      => 'date-picker',
                        'condition' => 'is_sale_schedule:is(on)'
                    ),
                    array(
                        'label'     => __('Sale Price Date To', ST_TEXTDOMAIN),
                        'desc'      => __('Sale Price Date To', ST_TEXTDOMAIN),
                        'id'        => 'sale_price_to',
                        'type'      => 'date-picker',
                        'condition' => 'is_sale_schedule:is(on)'
                    ),
                    array(
                        'label' => __('Number of car for Rent', ST_TEXTDOMAIN),
                        'desc'  => __('Number of car for Rent', ST_TEXTDOMAIN),
                        'id'    => 'number_car',
                        'type'  => 'text',
                        'std'   => 1
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
                        'label'     => __('Deposit payment amount', ST_TEXTDOMAIN),
                        'desc'      => __('Leave empty for disallow deposit payment', ST_TEXTDOMAIN),
                        'id'        => 'deposit_payment_amount',
                        'type'      => 'text',
                        'condition' => 'deposit_payment_status:not()'
                    ),
                    array(
                        'label' => __('Cars Options', ST_TEXTDOMAIN),
                        'id'    => 'cars_options',
                        'type'  => 'tab'
                    ),
                    array(
                        'label'        => __('Booking Period', ST_TEXTDOMAIN),
                        'id'           => 'cars_booking_period',
                        'type'         => 'numeric-slider',
                        'min_max_step' => '0,30,1',
                        'std'          => 0,
                        'desc'         => __('The time period allowed booking.', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label' => __('Car external booking', ST_TEXTDOMAIN),
                        'id'    => 'st_car_external_booking',
                        'type'  => 'on-off',
                        'std'   => "off",
                    ),
                    array(
                        'label'     => __('Car external booking link', ST_TEXTDOMAIN),
                        'id'        => 'st_car_external_booking_link',
                        'type'      => 'text',
                        'std'       => "",
                        'condition' => 'st_car_external_booking:is(on)'
                    ),

                )
            );
            $data_paypment = STPaymentGateways::$_payment_gateways;
            if (!empty($data_paypment) and is_array($data_paypment)) {
                $this->metabox[0]['fields'][] = array(
                    'label' => __('Payment', ST_TEXTDOMAIN),
                    'id'    => 'payment_detail_tab',
                    'type'  => 'tab'
                );
                foreach ($data_paypment as $k => $v) {
                    $this->metabox[0]['fields'][] = array(
                        'label' => $v->get_name(),
                        'id'    => 'is_meta_payment_gateway_' . $k,
                        'type'  => 'on-off',
                        'desc'  => $v->get_name(),
                        'std'   => 'on'
                    );
                }
            }
            $custom_field = st()->get_option('st_cars_unlimited_custom_field');
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

        function meta_update_sale_price($post_id)
        {
            if (wp_is_post_revision($post_id))
                return;
            $post_type = get_post_type($post_id);
            if ($post_type == 'st_cars') {
                $sale_price = get_post_meta($post_id, 'cars_price', TRUE);
                $discount = get_post_meta($post_id, 'discount', TRUE);
                $is_sale_schedule = get_post_meta($post_id, 'is_sale_schedule', TRUE);
                if ($is_sale_schedule == 'on') {
                    $sale_from = get_post_meta($post_id, 'sale_price_from', TRUE);
                    $sale_to = get_post_meta($post_id, 'sale_price_to', TRUE);
                    if ($sale_from and $sale_from) {

                        $today = date('Y-m-d');
                        $sale_from = date('Y-m-d', strtotime($sale_from));
                        $sale_to = date('Y-m-d', strtotime($sale_to));
                        if (($today >= $sale_from) && ($today <= $sale_to)) {

                        } else {

                            $discount = 0;
                        }

                    } else {
                        $discount = 0;
                    }
                }
                if ($discount) {
                    $sale_price = $sale_price - ($sale_price / 100) * $discount;
                }
                update_post_meta($post_id, 'sale_price', $sale_price);
            }
        }

        function _resend_mail()
        {
            $order_item = isset($_GET['order_item_id']) ? $_GET['order_item_id'] : FALSE;
            $test = isset($_GET['test']) ? $_GET['test'] : FALSE;
            if ($order_item) {
                $order = $order_item;
                if ($test) {
                    echo '<meta charset="UTF-8" 2>';
                    $message = st()->load_template('email/booking_infomation_cars', NULL, array('order_id' => $order));
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
                'items' => array()
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
                and $_GET['post_type'] == 'st_cars'
                and isset($_GET['page'])
                and $_GET['page'] = 'st_car_booking'
            ) return TRUE;

            return FALSE;
        }

        function add_menu_page()
        {
            //Add booking page
            add_submenu_page('edit.php?post_type=st_cars', __('Car Booking', ST_TEXTDOMAIN), __('Car Booking', ST_TEXTDOMAIN), 'manage_options', 'st_car_booking', array($this, '__car_booking_page'));
        }

        function  __car_booking_page()
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
                echo balanceTags($this->load_view('car/booking_index', FALSE));
            }
        }

        function add_booking()
        {
            echo balanceTags($this->load_view('car/booking_edit', FALSE, array('page_title' => __('Add new Car Booking', ST_TEXTDOMAIN))));
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
                return FALSE;
            }

            if (isset($_POST['submit']) and $_POST['submit']) $this->_save_booking($item_id);
            echo balanceTags($this->load_view('car/booking_edit'));
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

                    'id_user'     => '',
                    'status'      => '',
                    'st_tax'      => '',
                    'driver_age'  => '',
                    'driver_name' => ''
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
                    'item_number'    => '',
                    'item_id'        => '',
                    'item_price'     => '',
                    'check_in'       => '',
                    'check_in_time'  => '',
                    'check_out'      => '',
                    'check_out_time' => '',
                    'item_equipment' => '',
                    'pick_up'        => '',
                    'drop_off'       => '',
                    'driver_age'     => '',
                    'driver_name'    => ''
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
                'item_number'    => '',
                'item_id'        => '',
                'item_price'     => '',
                'check_in'       => '',
                'check_in_time'  => '',
                'check_out'      => '',
                'check_out_time' => '',
                'item_equipment' => '',
                'pick_up'        => '',
                'drop_off'       => '',
                'driver_age'     => '',
                'driver_name'    => ''
            );

            $data = wp_parse_args($_POST, $orderitem);
            foreach ($orderitem as $val) {
                if ($val == 'check_in' or $val == 'check_out') {
                    update_post_meta($order_id, $val, date('Y-m-d', strtotime($data[ $val ])));
                } else if ($val == 'item_equipment') {
                    $items = $data[ $val ];
                    $list_items = array();
                    if (!empty($items))
                        foreach ($items as $k => $v) {
                            $tmp = explode("|", $v);
                            $list_items[ $tmp[1] ] = $tmp[0];
                        }
                    update_post_meta($order_id, $val, json_encode($list_items));
                } else {
                    if (isset($data[ $val ]))
                        update_post_meta($order_id, $val, $data[ $val ]);
                }
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
                    update_post_meta($order_id, $field_name, STInput::post($field_name));
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

        // =================================================================
        function init()
        {
            $this->add_meta_field();
        }

        function add_meta_field()
        {
            if (is_admin()) {
                $pages = array('st_cars_pickup_features');
                /*
                 * prefix of meta keys, optional
                 */
                $prefix = 'st_';
                /*
                 * configure your meta box
                 */
                $config = array(
                    'id'             => 'st_extra_infomation_cars',          // meta box id, unique per meta box
                    'title'          => __('Extra Information', ST_TEXTDOMAIN),          // meta box title
                    'pages'          => $pages,        // taxonomy name, accept categories, post_tag and custom taxonomies
                    'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
                    'fields'         => array(),            // list of meta fields (can be added by field arrays)
                    'local_images'   => FALSE,          // Use local or hosted images (meta box images for add/remove)
                    'use_with_theme' => FALSE          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
                );

                if (!class_exists('Tax_Meta_Class')) {
                    STFramework::write_log('Tax_Meta_Class not found in class.attribute.php line 121');

                    return;
                }
                /*
                 * Initiate your meta box
                 */
                $my_meta = new Tax_Meta_Class($config);

                /*
                 * Add fields to your meta box
                 */
                //text field
                $my_meta->addText($prefix . 'icon', array('name' => __('Icon', ST_TEXTDOMAIN),
                                                          'desc' => __('Example: <br>Input "fa-desktop" for <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank" >Fontawesome</a>,<br>Input "im-pool" for <a href="https://icomoon.io/" target="_blank">Icomoon</a>  ', ST_TEXTDOMAIN)));

                //Image field
                //$my_meta->addImage($prefix.'image',array('name'=> __('Image ',ST_TEXTDOMAIN),
                // 'desc'=>__('If dont like the icon, you can use image instead',ST_TEXTDOMAIN)));
                //file upload field

                /*
                 * Don't Forget to Close up the meta box decleration
                 */
                //Finish Meta Box Decleration
                $my_meta->Finish();
            }

        }

        function cars_update_location($post_id)
        {
            if (wp_is_post_revision($post_id))
                return;
            $post_type = get_post_type($post_id);
            if ($post_type == 'st_cars') {
                $location_id = get_post_meta($post_id, 'id_location', TRUE);
                $ids_in = array();
                $parents = get_posts(array('numberposts' => -1, 'post_status' => 'publish', 'post_type' => 'location', 'post_parent' => $location_id));

                $ids_in[] = $location_id;

                foreach ($parents as $child) {
                    $ids_in[] = $child->ID;
                }
                $arg = array(
                    'post_type'      => 'st_cars',
                    'posts_per_page' => '-1',
                    'meta_query'     => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => $ids_in,
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query = new WP_Query($arg);
                $offer_tours = $query->post_count;

                // get total review
                $arg = array(
                    'post_type'      => 'st_cars',
                    'posts_per_page' => '-1',
                    'meta_query'     => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => $ids_in,
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query = new WP_Query($arg);
                $total = 0;
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $total += get_comments_number();
                    }
                }
                // get car min price
                $arg = array(
                    'post_type'      => 'st_cars',
                    'posts_per_page' => '1',
                    'order'          => 'ASC',
                    'meta_key'       => 'sale_price',
                    'orderby'        => 'meta_value_num',
                    'meta_query'     => array(
                        array(
                            'key'     => 'id_location',
                            'value'   => $ids_in,
                            'compare' => 'IN',
                        ),
                    ),
                );
                $query = new WP_Query($arg);
                if ($query->have_posts()) {
                    $query->the_post();
                    $price_min = get_post_meta(get_the_ID(), 'cars_price', TRUE);
                    update_post_meta($location_id, 'review_st_cars', $total);
                    update_post_meta($location_id, 'min_price_st_cars', $price_min);
                    update_post_meta($location_id, 'offer_st_cars', $offer_tours);
                }
                wp_reset_postdata();
            }
        }
    }

    $a = new STAdminCars();
    $a->init();
}