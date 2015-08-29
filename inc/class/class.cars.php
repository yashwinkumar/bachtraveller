<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STCars
 *
 * Created by ShineTheme
 *
 */

if(!class_exists('STCars')) {
    class STCars extends TravelerObject
    {
        protected $orderby;

        public $post_type = "st_cars";

        function __construct( $hotel_id = false )
        {
            $this->hotel_id = $hotel_id;
            $this->orderby  = array(
                'new'        => array(
                    'key'  => 'new' ,
                    'name' => __( 'New' , ST_TEXTDOMAIN )
                ) ,
                'price_asc'  => array(
                    'key'  => 'price_asc' ,
                    'name' => __( 'Price (low to high)' , ST_TEXTDOMAIN )
                ) ,
                'price_desc' => array(
                    'key'  => 'price_desc' ,
                    'name' => __( 'Price (hight to low)' , ST_TEXTDOMAIN )
                ) ,
                'name_a_z'   => array(
                    'key'  => 'name_a_z' ,
                    'name' => __( 'Car Name (A-Z)' , ST_TEXTDOMAIN )
                ) ,
                'name_z_a'   => array(
                    'key'  => 'name_z_a' ,
                    'name' => __( 'Car Name (Z-A)' , ST_TEXTDOMAIN )
                ) ,

            );


        }
        /**
         *
         * @since 1.1.3
         * */
        static function get_cart_item_total($key,$cart_item)
        {
            $number=$cart_item['number'];

            $selected_equipments=$cart_item['data']['selected_equipments'];
            $check_in_timestamp=$cart_item['data']['check_in_timestamp'];
            $check_out_timestamp=$cart_item['data']['check_out_timestamp'];

            $time=STCars::get_date_diff($check_in_timestamp,$check_out_timestamp);

            if(!$time) $time=1;

            $info_price = STCars::get_info_price($key);
            $cars_price = $info_price['price'];

            $total_price=$cars_price*$time;


            if(!empty($selected_equipments))
            {
                foreach($selected_equipments as $v){
                    switch($v->price_unit)
                    {
                        case "day":
                        case "per_day":
                            $day=STCars::get_date_diff($check_in_timestamp,$check_out_timestamp,$v->price_unit);
                            $total_price+=$v->price*$day;
                            break;
                        case "hour":
                        case "per_hour":

                        $hour=STCars::get_date_diff($check_in_timestamp,$check_out_timestamp,$v->price_unit);
                        $total_price+=$v->price*$hour;
                            break;
                        default:
                            $total_price+=$v->price;
                            break;
                    }
                }
            }


            return $total_price*$number;
        }

        /**
         *
         * @update 1.1.3
         *
         * */
        function init()
        {

            if(!$this->is_available()) return;

            parent::init();


            //Filter change layout of cars detail if choose in metabox
            add_filter( 'st_cars_detail_layout' , array( $this , 'custom_cars_layout' ) );

            // price cars
            add_action( 'wp_ajax_st_price_cars' , array( $this , 'st_price_cars_func' ) );
            add_action( 'wp_ajax_nopriv_st_price_cars' , array( $this , 'st_price_cars_func' ) );

            //custom search cars template
            add_filter( 'template_include' , array( $this , 'choose_search_template' ) );
            //add Widget Area
            add_action( 'widgets_init' , array( $this , 'add_sidebar' ) );
            //Sidebar Pos for SEARCH
            add_filter( 'st_cars_sidebar' , array( $this , 'change_sidebar' ) );

            // ajax add_type_widget
            add_action( 'wp_ajax_add_type_widget' , array( $this , 'add_type_widget_func' ) );
            add_action( 'wp_ajax_nopriv_add_type_widget' , array( $this , 'add_type_widget_func' ) );

            // ajax load_list_taxonomy
            add_action( 'wp_ajax_load_list_taxonomy' , array( $this , 'load_list_taxonomy_func' ) );
            add_action( 'wp_ajax_nopriv_load_list_taxonomy' , array( $this , 'load_list_taxonomy_func' ) );

            //Filter the search hotel
            //add_action('pre_get_posts',array($this,'change_search_cars_arg'));


            //$this->init_metabox();

            add_action( 'wp_loaded' , array( $this , 'cars_add_to_cart' ) , 20 );

            add_filter( 'posts_where' , array( $this , '_alter_search_query' ) );

            add_action( 'st_after_checkout_fields' , array( $this , 'add_checkout_fields' ) );

            add_filter( 'st_checkout_form_validate' , array( $this , 'add_validate_fields' ) );

            add_action( 'st_after_save_order_item' , array( $this , 'save_extra_fields' ) , 10 , 3 );

            //Save car Review Stats
            add_action( 'comment_post' , array( $this , 'st_cars_save_review_stats' ) );

            //Reduce total stats of posts after comment_delete
            add_action( 'delete_comment' , array( $this , 'st_cars_save_post_review_stats' ) );

            // Change cars review arg
            add_filter( 'st_cars_wp_review_form_args' , array( $this , 'comment_args' ) , 10 , 2 );


            add_action( 'wp_enqueue_scripts' , array( $this , 'add_script' ) );

            add_filter( 'st_search_preload_page' , array( $this , '_change_preload_search_title' ) );

            //add_action('st_after')

            //add_filter('st_data_custom_price',array($this,'_st_data_custom_price'));

            // Woocommerce cart item information
            add_action( 'st_wc_cart_item_information_st_cars' , array( $this , '_show_wc_cart_item_information' ) );

            add_action( 'st_before_cart_item_st_cars' , array( $this , '_show_wc_cart_post_type_icon' ) );

            add_filter( 'st_add_to_cart_item_st_cars' , array( $this , '_deposit_calculator' ) , 10 , 2 );

        }

        /**
         *
         *
         *
         *
         * */
        function _show_wc_cart_post_type_icon()
        {
            echo '<span class="booking-item-wishlist-title"><i class="fa fa-car"></i> ' . __( 'car' , ST_TEXTDOMAIN ) . ' <span></span></span>';
        }

        /**
         *
         * Show cart item information for hotel booking
         *
         * @since 1.1.1
         * */

        function _show_wc_cart_item_information( $st_booking_data = array() )
        {
            echo st()->load_template( 'cars/wc_cart_item_information' , false , array( 'st_booking_data' => $st_booking_data ) );
        }

        function _st_data_custom_price()
        {
            return array( 'title' => 'Price Custom Settings' , 'post_type' => 'st_cars' );
        }

        function _change_preload_search_title( $return )
        {
            if(get_query_var( 'post_type' ) == 'st_cars') {
                $return = __( " Cars in %s" , ST_TEXTDOMAIN );

                if(STInput::get( 'location_id' )) {
                    $return = sprintf( $return , get_the_title( STInput::get( 'location_id' ) ) );
                } elseif(STInput::get( 'location_name' )) {
                    $return = sprintf( $return , STInput::get( 'location_name' ) );
                } elseif(STInput::get( 'pick-up' )) {
                    $rs = STInput::get( 'pick-up' );
                    if(STInput::get( 'drop-off' )) {
                        $rs .= __( " to " , ST_TEXTDOMAIN ) . STInput::get( 'drop-off' );
                    }
                    $return = sprintf( $return , $rs );
                } else {
                    $return = __( " Cars" , ST_TEXTDOMAIN );
                }

                $return .= '...';
            }


            return $return;
        }

        function _is_slot_available( $post_id , $check_in , $check_out )
        {
            $check_in  = date( 'Y-m-d H:i:s' , strtotime( $check_in ) );
            $check_out = date( 'Y-m-d H:i:s' , strtotime( $check_out ) );

            global $wpdb;

            $query = "
SELECT count(booked_id) as total_booked from (
SELECT st_meta6.meta_value as booked_id ,st_meta2.meta_value as check_in,st_meta3.meta_value as check_out
                                         FROM {$wpdb->posts}
                                                JOIN {$wpdb->postmeta}  as st_meta2 on st_meta2.post_id={$wpdb->posts}.ID and st_meta2.meta_key='check_in'
                                                JOIN {$wpdb->postmeta}  as st_meta3 on st_meta3.post_id={$wpdb->posts}.ID and st_meta3.meta_key='check_out'
                                                JOIN {$wpdb->postmeta}  as st_meta6 on st_meta6.post_id={$wpdb->posts}.ID and st_meta6.meta_key='item_id'
                                                JOIN {$wpdb->postmeta}  as st_meta7 on st_meta7.post_id={$wpdb->posts}.ID and st_meta7.meta_key='status'
                                                WHERE {$wpdb->posts}.post_type='st_order'
                                                AND st_meta6.meta_value={$post_id}
                                                 AND st_meta7.meta_value='complete'
                                          GROUP BY {$wpdb->posts}.id HAVING  (

                                                    ( CAST(st_meta2.meta_value AS DATE)<'{$check_in}' AND  CAST(st_meta3.meta_value AS DATE)>'{$check_in}' )
                                                    OR ( CAST(st_meta2.meta_value AS DATE)>='{$check_in}' AND  CAST(st_meta2.meta_value AS DATE)<='{$check_out}'))) as object_booked
        ";


            $total_booked = (int)$wpdb->get_var( $query );

            $total = (int)get_post_meta( $post_id , 'number_car' , true );

            if($total > $total_booked)
                return true;
            else return false;

        }


        /**
         *
         *
         * @update 1.1.3
         * */
        function add_script()
        {
            if(is_singular( 'st_cars' )) {
                // add js validate for change location and date
                // Validate required field
                $change_location_date_box = $this->get_search_fields_box();
                $field_types              = $this->get_search_fields_name();

                $q = array();

                if(!empty( $change_location_date_box ) and is_array( $change_location_date_box )) {
                    foreach( $change_location_date_box as $key => $value ) {
                        if($value[ 'is_required' ] == 'on' and isset( $field_types[ $value[ 'field_atrribute' ] ] )) {
                            $field_name = isset( $field_types[ $value[ 'field_atrribute' ] ][ 'field_name' ] ) ? $field_types[ $value[ 'field_atrribute' ] ][ 'field_name' ] : false;

                            if($field_name) {
                                if(is_array( $field_name )) {
                                    if(!empty( $field_name )) {
                                        foreach( $field_name as $v ) {
                                            $q[ ] = $v;
                                        }
                                    }
                                }

                                if(is_string( $field_name )) {
                                    $q[ ] = $field_name;
                                }
                            }
                        }
                    }
                }
                wp_localize_script( 'jquery' , 'st_car_booking_validate' , array( 'required' => $q ) );

                wp_enqueue_script( 'single-car' , get_template_directory_uri() . '/js/init/single-car.js' );

            }
        }

        function save_extra_fields( $order_id , $key , $value )
        {
            if(STInput::post( 'driver_name' )) {
                update_post_meta( $order_id , 'driver_name' , STInput::post( 'driver_name' ) );
            }
            if(STInput::post( 'driver_age' )) {
                update_post_meta( $order_id , 'driver_age' , STInput::post( 'driver_age' ) );
            }

            if(get_post_type( $key ) == 'st_cars') {
                foreach( $value[ 'data' ] as $k => $v ) {

                    if($k == 'data_price_cars') {
                        $date = $v->date_time;
                        update_post_meta( $order_id , 'check_in_time' , $date->pick_up_time );
                        update_post_meta( $order_id , 'check_out_time' , $date->drop_off_time );
                        update_post_meta( $order_id , 'pick_up' , $v->pick_up );
                        update_post_meta( $order_id , 'drop_off' , $v->drop_off );
                    }

                }

                if(get_post_type( $key ) == 'st_cars') {
                    $items = $value[ 'data' ][ 'data_price_items' ];
                    update_post_meta( $order_id , 'item_equipment' , json_encode( $items ) );
                }
            }

        }

        /**
         *
         *
         * @since 1.0.9
         * */
        function _check_booking_period( $validate )
        {

            if($this->check_is_car_booking()) {
                $car_id = '';

                $today   = strtotime( date( 'm/d/Y' ) );
                $pick_up = $today;

                $cart = STCart::get_cart_item();
            }


            return $validate;

        }

        function add_validate_fields( $validate )
        {
            if($this->check_is_car_booking()) {
                $validator = new STValidate();

                $validator->set_rules( array(
                    array(
                        'field' => 'driver_name' ,
                        'label' => 'Driver\'s Name' ,
                        'rules' => 'required|trim|strip_tags'
                    ) ,
                    array(
                        'field' => 'driver_age' ,
                        'label' => 'Driver\'s Age' ,
                        'rules' => 'required|trim|strip_tags'
                    )
                ) );

                if(!$validator->run()) {
                    $validate = false;
                    STTemplate::set_message( $validator->error_string() , 'danger' );
                }
            }

            return $validate;
        }

        function check_is_car_booking()
        {
            $item = STCart::get_cart_item();
            if(isset( $item[ 'key' ] ) and get_post_type( $item[ 'key' ] ) == 'st_cars') {
                return true;
            }

            return false;
        }

        function add_checkout_fields()
        {
            if($this->check_is_car_booking() or is_singular( 'st_cars' )) {
                echo st()->load_template( 'cars/checkout_fields' );
            }

        }

        /**
         * @return array
         */
        public function getOrderby()
        {
            return $this->orderby;
        }


        function cars_add_to_cart()
        {
            if(STInput::post( 'action' ) == 'cars_add_to_cart') {
                if($this->do_add_to_cart()) {

                    $link = STCart::get_cart_link();

                    $link = apply_filters( 'st_car_added_cart_redirect_link' , $link );

                    wp_safe_redirect( $link );
                    die;
                }

            }

        }

        /**
         * @since 1.0.9
         * @update 1.1.3
         **/
        function do_add_to_cart()
        {
            $pass_validate = true;

            $data_price_cars     = json_decode( str_ireplace( "\\" , '' , STInput::request( 'data_price_cars' ) ) );
            $data_price_items    = json_decode( str_ireplace( "\\" , '' , STInput::request( 'data_price_items' ) ) );
            $selected_equipments = json_decode( str_ireplace( "\\" , '' , STInput::request( 'selected_equipments' ) ) );
            $discount            = STInput::request( 'discount' );
            $price_unit          = STInput::request( 'price' );
            $price_old           = STInput::request( 'price_old' );
            $item_id             = STInput::request( 'item_id' );
            $number              = 1;

            $price_total = STInput::request( 'data_price_total' );
            $check_in    = $data_price_cars->date_time->pick_up_date . ' ' . $data_price_cars->date_time->pick_up_time;

            $check_in = date( 'Y-m-d H:i:s' , strtotime( $check_in ) );

            $check_out = $data_price_cars->date_time->drop_off_date . ' ' . $data_price_cars->date_time->drop_off_time;

            $check_out = date( 'Y-m-d H:i:s' , strtotime( $check_out ) );

            $data = array(
                'data_price_cars'     => $data_price_cars ,
                'data_price_items'    => $data_price_items ,
                'discount'            => $discount ,
                'price_old'           => $price_old ,
                'check_in'            => STInput::request( 'check_in' ) ,
                'check_in_timestamp'  => STInput::request( 'check_in_timestamp' ) ,
                'check_out'           => STInput::request( 'check_out' ) ,
                'check_out_timestamp' => STInput::request( 'check_out_timestamp' ) ,
                'price_total'         => $price_total ,
                'price_unit'          => self::get_price_unit() ,
                'selected_equipments' => $selected_equipments
            );

            // Validate required field
            $change_location_date_box = $this->get_search_fields_box();
            $field_types              = $this->get_search_fields_name();

            if(!empty( $change_location_date_box )) {
                $message = '';
                foreach( $change_location_date_box as $key => $value ) {
                    if(isset( $field_types[ $value[ 'field_atrribute' ] ] ) and $value[ 'is_required' ] == 'on') {
                        $field_name = isset( $field_types[ $value[ 'field_atrribute' ] ][ 'field_name' ] ) ? $field_types[ $value[ 'field_atrribute' ] ][ 'field_name' ] : false;

                        if($field_name) {
                            if(is_array( $field_name )) {
                                foreach( $field_name as $v ) {
                                    if(!STInput::request( $v )) {
                                        $message .= sprintf( __( '%s is required' , ST_TEXTDOMAIN ) , $value[ 'title' ] ) . '<br>';
                                        $pass_validate = false;
                                    }
                                }
                            } elseif(is_string( $field_name )) {

                                if(!STInput::request( $field_name )) {
                                    $message .= sprintf( __( '%s is required' , ST_TEXTDOMAIN ) , $value[ 'title' ] ) . '<br>';
                                    $pass_validate = false;
                                }
                            }

                        }
                    }
                }

                if($message) {
                    $message = substr( $message , 0 , -4 );
                    STTemplate::set_message( $message , 'danger' );
                }
            }

            $is_required_country = st()->get_option( 'is_required_country' , 'off' );
            if($is_required_country == 'on') {
                if(STInput::request( 'county_pick_up' ) != STInput::request( 'county_drop_off' ) or !STInput::request( 'county_drop_off' ) or !STInput::request( 'county_pick_up' )) {
                    STTemplate::set_message( __( 'Pick-up and Drop-off are not in the same country. Please re-check it.' , ST_TEXTDOMAIN ) , 'danger' );
                    $pass_validate = false;
                }
            }

            $today = strtotime( date( 'm/d/Y' , time() ) );

            $check_in_unix = strtotime( TravelHelper::convertDateFormat( $data_price_cars->date_time->pick_up_date ) );

            $booking_period = intval( get_post_meta( $item_id , 'cars_booking_period' , true ) );

            $period = STDate::date_diff( $today , $check_in_unix );

            $var = $check_in_unix - $today;
            if($var < 0) {
                STTemplate::set_message( __( 'You can not set check-in date in the past' ) , 'danger' );
                $pass_validate = false;
            } else {
                if($booking_period && $period < $booking_period) {

                    STTemplate::set_message( sprintf( __( 'Booking is only accepted %d day(s) before today.' , ST_TEXTDOMAIN ) , $booking_period ) , 'danger' );
                    $pass_validate = false;
                }
            }
            if(!$this->_is_slot_available( $item_id , $check_in , $check_out )) {
                STTemplate::set_message( __( 'Sorry! This Car is not available.' , ST_TEXTDOMAIN ) , 'danger' );
                $pass_validate = false;
            }

            //if($pickup)


            // Allow to be filtered
            $pass_validate = apply_filters( 'st_car_add_cart_validate' , $pass_validate , $item_id , $number , $price_unit , $data );


            if($pass_validate) {
                STCart::add_cart( $item_id , $number , $price_unit , $data );

            }

            return $pass_validate;
        }


        function get_cart_item_html( $item_id = false )
        {
            return st()->load_template( 'cars/cart_item_html' , null , array( 'item_id' => $item_id ) );
        }

        /**
         * Change location and date box
         *
         *
         * */
        function get_search_fields_box()
        {
            $fields = st()->get_option( 'car_search_fields_box' );

            return $fields;
        }

        function get_search_fields()
        {
            $fields = st()->get_option( 'car_search_fields' );

            return $fields;
        }


        /**
         *
         *
         * @update 1.1.1
         * */
        function change_search_cars_arg( $query )
        {

            $post_type = get_query_var( 'post_type' );
            if($query->is_search && $post_type == 'st_cars') {
                $tax = STInput::request( 'filter_taxonomy' );

                if(!empty( $tax ) and is_array( $tax )) {
                    $tax_query = array();
                    foreach( $tax as $key => $value ) {
                        if($value) {
                            $ids = array();
                            foreach( $value as $k => $v ) {
                                if($v) {
                                    array_push( $ids , $v );
                                }
                            }
                            if(!empty( $ids )) {
                                $tax_query[ ] = array(
                                    'taxonomy' => $key ,
                                    'field'    => 'id' ,
                                    'terms'    => $ids
                                );
                            }
                        }
                    }
                    $query->set( 'tax_query' , $tax_query );
                }

                if($location_id = STInput::get( 'location_id_drop_off' )) {
                    $ids_in  = array();
                    $parents = get_posts( array(
                        'numberposts' => -1 ,
                        'post_status' => 'publish' ,
                        'post_type'   => 'location' ,
                        'post_parent' => $location_id
                    ) );

                    $ids_in[ ] = $location_id;

                    foreach( $parents as $child ) {
                        $ids_in[ ] = $child->ID;
                    }
                    $meta_query[ ] = array(
                        'key'     => 'id_location' ,
                        'value'   => $ids_in ,
                        'compare' => 'IN'
                    );
//                    $query->set('s','');
                } else {
                    $value = STInput::get( 'pick-up' );
                    $value = explode( "," , $value );
                    if(!empty( $value[ 0 ] ) and !empty( $value[ 2 ] )) {
                        $meta_query[ ] = array(
                            array(
                                'key'     => 'cars_address' ,
                                'value'   => $value[ 0 ] ,
                                'compare' => 'like' ,
                            ) ,
                            array(
                                'key'     => 'cars_address' ,
                                'value'   => $value[ 2 ] ,
                                'compare' => 'like' ,
                            ) ,
                            "relation" => 'OR'
                        );
                    } else {
                        $value         = STInput::request( 'pick-up' );
                        $meta_query[ ] = array(
                            'key'     => 'cars_address' ,
                            'value'   => $value ,
                            'compare' => 'like' ,
                        );
                    }


                }

                $is_featured = st()->get_option( 'is_featured_search_car' , 'off' );
                if(!empty( $is_featured ) and $is_featured == 'on') {
                    $query->set( 'meta_key' , 'is_featured' );
                    $query->set( 'orderby' , 'meta_value' );
                    $query->set( 'order' , 'DESC' );
                }
                if($orderby = STInput::get( 'orderby' )) {
                    switch( $orderby ) {
                        case "price_asc":
                            $query->set( 'meta_key' , 'sale_price' );
                            $query->set( 'orderby' , 'meta_value_num' );
                            $query->set( 'order' , 'ASC' );

                            break;
                        case "price_desc":
                            $query->set( 'meta_key' , 'sale_price' );
                            $query->set( 'orderby' , 'meta_value_num' );
                            $query->set( 'order' , 'DESC' );
                            break;
                        case "name_a_z":
                            $query->set( 'orderby' , 'name' );
                            $query->set( 'order' , 'asc' );
                            break;
                        case "name_z_a":
                            $query->set( 'orderby' , 'name' );
                            $query->set( 'order' , 'desc' );
                            break;
                    }
                }
                if($price = STInput::get( 'price_range' )) {
                    $priceobj      = explode( ';' , $price );
                    $meta_query[ ] = array(
                        'key'     => 'cars_price' ,
                        'value'   => $priceobj[ 0 ] ,
                        'compare' => '>=' ,
                        'type'    => "NUMERIC"
                    );
                    if(isset( $priceobj[ 1 ] )) {
                        $meta_query[ ] = array(
                            'key'     => 'cars_price' ,
                            'value'   => $priceobj[ 1 ] ,
                            'compare' => '<=' ,
                            'type'    => "NUMERIC"
                        );
                    }

                    $meta_query[ 'relation' ] = 'and';
                }
                if ($location_id = STInput::request('location_id') and $post_type = STInput::request('post_type')){
                    $location_text_key = "id_location";
                    if ($post_type == "st_rental") {$location_text_key = "location_id"; }
                    $meta_query[] = array(
                        'key'     => $location_text_key,
                        'value'   => $location_id,
                        'compare' => 'IN'
                    );
                }
                if (!empty($meta_query)) {
                    $query->set('meta_query', $meta_query); 
                }
            }
        }

        function add_type_widget_func()
        {
            $data_type         = $_REQUEST[ 'data_type' ];
            $data_value        = $_REQUEST[ 'data_value' ];
            $data_json         = $_REQUEST[ 'data_json' ];
            $data_title_filter = $_REQUEST[ 'title_filter' ];

            $data_text = '<div><h4> - ' . $data_title_filter . '</h4></div>';

            if($data_type == 'price') {
                $data_value == 'price';
            }

            if(!empty( $data_json )) {

                $tmp_json = $data_json[ 'data_json' ];
                array_push( $tmp_json , array(
                    'title' => $data_title_filter ,
                    'type'  => $data_type ,
                    'value' => $data_value
                ) );

                $data_return = array(
                    'data_html' => $data_text ,
                    'data_json' => $tmp_json
                );

            } else {
                $tmp_json    = array(
                    array(
                        'title' => $data_title_filter ,
                        'type'  => $data_type ,
                        'value' => $data_value
                    )
                );
                $data_return = array(
                    'data_html' => $data_text ,
                    'data_json' => $tmp_json
                );

            }
            echo json_encode( $data_return );
            die();
        }

        function choose_search_template( $template )
        {
            global $wp_query;
            $post_type = get_query_var( 'post_type' );
            if($wp_query->is_search && $post_type == 'st_cars') {
                return locate_template( 'search-cars.php' );  //  redirect to archive-search.php
            }

            return $template;
        }

        function add_sidebar()
        {
            register_sidebar( array(
                'name'          => __( 'Cars Search Sidebar 1' , ST_TEXTDOMAIN ) ,
                'id'            => 'cars-sidebar' ,
                'description'   => __( 'Widgets in this area will be shown on Cars' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );

            register_sidebar( array(
                'name'          => __( 'Cars Search Sidebar 2' , ST_TEXTDOMAIN ) ,
                'id'            => 'cars-sidebar-2' ,
                'description'   => __( 'Widgets in this area will be shown on Cars' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );

        }

        function change_sidebar( $sidebar = false )
        {
            return st()->get_option( 'cars_sidebar_pos' , 'left' );
        }


        function st_price_cars_func()
        {

            $price_total_item = $_REQUEST[ 'price_total_item' ];

            $form_data           = STInput::request( 'form_data' );
            $selected_equipments = $form_data[ 'selected_equipments' ];

            $check_in_timestamp  = $form_data[ 'check_in_timestamp' ];
            $check_out_timestamp = $form_data[ 'check_out_timestamp' ];

            $car_item = $form_data[ 'item_id' ];

            $info_price = STCars::get_info_price( $car_item );
            $cars_price = $info_price[ 'price' ];

            $price_total = self::get_rental_price( $cars_price , $check_in_timestamp , $check_out_timestamp );


            $total_equipment_price = 0;
            //Equipment Caculator

            $selected_equipments = json_decode( $selected_equipments );

            if(!empty( $selected_equipments ) and is_array( $selected_equipments )) {
                foreach( $selected_equipments as $key => $value ) {
                    switch( $value[ 'price_unit' ] ) {
                        case "per_day":
                            $diff = STDate::timestamp_diff_day( $check_in_timestamp , $check_out_timestamp );
                            if(!$diff)
                                $diff = 1;
                            $total_equipment_price += (float)$value[ 'price' ] * $diff;

                            break;
                        case "per_hour":

                            $diff = STDate::timestamp_diff( $check_in_timestamp , $check_out_timestamp );
                            if(!$diff)
                                $diff = 1;
                            $total_equipment_price += (float)$value[ 'price' ] * $diff;

                            break;
                        default:
                            $total_equipment_price += (float)$value[ 'price' ];
                            break;
                    }
                }
            }


            $price_total += $total_equipment_price;
            echo json_encode( array(
                'price_total_number'      => $price_total ,
                'price_total_text'        => TravelHelper::format_money( $price_total ) ,
                'price_total_item_number' => $total_equipment_price ,
                'price_total_item_text'   => TravelHelper::format_money( $total_equipment_price ) ,
            ) );
            die();
        }

        function custom_cars_layout( $old_layout_id )
        {
            if(is_singular( 'st_cars' )) {
                $meta = get_post_meta( get_the_ID() , 'st_custom_layout' , true );

                if($meta) {
                    return $meta;
                }
            }

            return $old_layout_id;
        }

        function get_result_string()
        {
            global $wp_query , $st_search_query;

            if($st_search_query) {
                $query = $st_search_query;
            } else $query = $wp_query;



            $result_string='';
            if($query->post_count > 1){
                $result_string.=esc_html( $query->found_posts).__(' cars ',ST_TEXTDOMAIN);
            }else{
                $result_string.=esc_html( $query->found_posts).__(' car ',ST_TEXTDOMAIN);
            }
            
            $location_id=STInput::get('location_id');
            if($location_id and $location=get_post($location_id))
            {
                $result_string.=sprintf(__(' in %s',ST_TEXTDOMAIN),get_the_title($location_id));
            }else{
                if(!empty($_REQUEST['pick-up'])){
                    $result_string.=sprintf(__(' in %s',ST_TEXTDOMAIN),STInput::request('pick-up'));
                }

            }

            $start = STInput::get( 'pick-up-date' );
            $end   = STInput::get( 'drop-off-date' );

            $start = strtotime( $start );

            $end = strtotime( $end );

            if($start and $end) {
                $result_string .= __( ' on ' , ST_TEXTDOMAIN ) . date_i18n( 'M d' , $start ) . ' - ' . date_i18n( 'M d' , $end );
            }

            if($adult_num = STInput::get( 'adult_num' )) {
                if($adult_num > 1) {
                    $result_string .= sprintf( __( ' for %s adults' , ST_TEXTDOMAIN ) , $adult_num );
                } else {

                    $result_string .= sprintf( __( ' for %s adult' , ST_TEXTDOMAIN ) , $adult_num );
                }

            }

            return $result_string;

        }


        /**
         *
         *
         * @update 1.1.1
         * */
        static function get_search_fields_name()
        {
            return array(
                'pick-up-form'         => array(
                    'value'      => 'pick-up-form' ,
                    'label'      => __( 'Pick-up From' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'pick-up'
                ) ,
                'pick-up-form-list'    => array(
                    'value'      => 'pick-up-form-list' ,
                    'label'      => __( 'Pick-up From (dropdown)' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'location_id_pick_up'
                ) ,
                'pick-up-form-address' => array(
                    'value'      => 'pick-up-form-address' ,
                    'label'      => __( 'Pick-up From Address (geobytes.com)' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'pick-up'
                ) ,
                'drop-off-to'          => array(
                    'value'      => 'drop-off-to' ,
                    'label'      => __( 'Drop-off To' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'drop-off'

                ) ,
                'drop-off-to-list'     => array(
                    'value'      => 'drop-off-to-list' ,
                    'label'      => __( 'Drop-off To (dropdown)' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'location_id_drop_off'
                ) ,
                'drop-off-to-address'  => array(
                    'value'      => 'drop-off-to-address' ,
                    'label'      => __( 'Drop-off To Address (geobytes.com)' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'drop-off'
                ) ,
                'pick-up-date'         => array(
                    'value'      => 'pick-up-date' ,
                    'label'      => __( 'Pick-up Date' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'pick-up-date'
                ) ,
                'drop-off-time'        => array(
                    'value'      => 'drop-off-time' ,
                    'label'      => __( 'Drop-off Time' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'drop-off-time'
                ) ,
                'drop-off-time-list'   => array(
                    'value'      => 'drop-off-time-list' ,
                    'label'      => __( 'Drop-off Time ( dropdown )' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'drop-off-time'
                ) ,
                'drop-off-date'        => array(
                    'value'      => 'drop-off-date' ,
                    'label'      => __( 'Drop-off Date' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'drop-off-date'
                ) ,
                'pick-up-time'         => array(
                    'value'      => 'pick-up-time' ,
                    'label'      => __( 'Pick-up Time' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'pick-up-time'
                ) ,
                'pick-up-time-list'    => array(
                    'value'      => 'pick-up-time-list' ,
                    'label'      => __( 'Pick-up Time ( dropdown )' , ST_TEXTDOMAIN ) ,
                    'field_name' => 'pick-up-time'
                ) ,
                'pick-up-date-time'    => array(
                    'value'      => 'pick-up-date-time' ,
                    'label'      => __( 'Pick-up Date Time' , ST_TEXTDOMAIN ) ,
                    'field_name' => array( 'pick-up-date' , 'pick-up-time' )
                ) ,
                'drop-off-date-time'   => array(
                    'value'      => 'drop-off-date-time' ,
                    'label'      => __( 'Drop-off Date Time' , ST_TEXTDOMAIN ) ,
                    'field_name' => array( 'drop-off-date' , 'drop-off-time' )
                ) ,
                'taxonomy'             => array(
                    'value' => 'taxonomy' ,
                    'label' => __( 'Taxonomy' , ST_TEXTDOMAIN ) ,
                ) ,
                'item_name'            => array(
                    'value'      => 'item_name' ,
                    'label'      => __( 'Car Name' , ST_TEXTDOMAIN ) ,
                    'field_name' => 's'
                )

            );
        }

        function  _alter_search_query( $where )
        {
            global $wp_query;
            if(is_search()) {
                $post_type = $wp_query->query_vars[ 'post_type' ];

                if($post_type == 'st_cars') {
                    //Alter From NOW
                    global $wpdb;

                    $check_in  = STInput::get( 'pick-up-date' );
                    $check_out = STInput::get( 'drop-off-date' );


                    //Alter WHERE for check in and check out
                    if($check_in and $check_out) {
                        $check_in  = @date( 'Y-m-d H:i:s' , strtotime( $check_in ) );
                        $check_out = @date( 'Y-m-d H:i:s' , strtotime( $check_out ) );

                        $check_in  = esc_sql( $check_in );
                        $check_out = esc_sql( $check_out );


                        $where .= " AND $wpdb->posts.ID NOT IN
                            (
                                SELECT booked_id FROM (
                                    SELECT count(st_meta6.meta_value) as total_booked, st_meta5.meta_value as total,st_meta6.meta_value as booked_id ,st_meta2.meta_value as check_in,st_meta3.meta_value as check_out
                                         FROM {$wpdb->posts}
                                                JOIN {$wpdb->postmeta}  as st_meta2 on st_meta2.post_id={$wpdb->posts}.ID and st_meta2.meta_key='check_in'
                                                JOIN {$wpdb->postmeta}  as st_meta3 on st_meta3.post_id={$wpdb->posts}.ID and st_meta3.meta_key='check_out'
                                                JOIN {$wpdb->postmeta}  as st_meta6 on st_meta6.post_id={$wpdb->posts}.ID and st_meta6.meta_key='item_id'
                                                JOIN {$wpdb->postmeta}  as st_meta5 on st_meta5.post_id=st_meta6.meta_value and st_meta5.meta_key='number_car'
                                                WHERE {$wpdb->posts}.post_type='st_order'
                                        GROUP BY st_meta6.meta_value HAVING total<=total_booked AND (

                                                    ( CAST(st_meta2.meta_value AS DATE)<'{$check_in}' AND  CAST(st_meta3.meta_value AS DATE)>'{$check_in}' )
                                                    OR ( CAST(st_meta2.meta_value AS DATE)>='{$check_in}' AND  CAST(st_meta2.meta_value AS DATE)<='{$check_out}' )

                                        )
                                ) as item_booked
                            )

                    ";
                    }
                }
            }

            return $where;
        }

        static function get_price_car_by_order_item( $id_item = null )
        {
            if(empty( $id_item ))
                $id_item = get_the_ID();


            return get_post_meta( $id_item , 'price_total' , true );
//
//            $total=0;
//            $price = get_post_meta($id_item,'item_price',true);
//            $check_in=get_post_meta($id_item,'check_in',true);
//            $date = new DateTime($check_in);
//            $check_in = $date->format('m/d/Y');
//
//            $check_out=get_post_meta($id_item,'check_out',true);
//            $date = new DateTime($check_out);
//            $check_out = $date->format('m/d/Y');
//
//            $check_in_time=get_post_meta($id_item,'check_in_time',true);
//            $check_out_time=get_post_meta($id_item,'check_out_time',true);
//            $time = ( strtotime($check_out.' '.$check_out_time) - strtotime($check_in.' '.$check_in_time)  ) / 3600 ;
//            $item_equipment= get_post_meta($id_item,'item_equipment',true);
//            $price_item = 0;
//            if(!empty($item_equipment)){
//                $json_decode=json_decode($item_equipment);
//
//                // Check null for get_object_vars() function
//                if($json_decode){
//                    $item_equipment = get_object_vars($json_decode);
//                    foreach($item_equipment as $k=>$v){
//                        $price_item += $v;
//                    }
//                }
//
//            }
//            $number=get_post_meta($id_item,'item_number',true);
//            if(!$number) $number=1;
//            $total+= $price*$number * $time + $price_item;
//            return $total;
        }


        static function get_info_price( $post_id = null )
        {

            if(!$post_id)
                $post_id = get_the_ID();
            $price     = get_post_meta( $post_id , 'cars_price' , true );
            $new_price = 0;

            $discount         = get_post_meta( $post_id , 'discount' , true );
            $is_sale_schedule = get_post_meta( $post_id , 'is_sale_schedule' , true );

            if($is_sale_schedule == 'on') {
                $sale_from = get_post_meta( $post_id , 'sale_price_from' , true );
                $sale_to   = get_post_meta( $post_id , 'sale_price_to' , true );
                if($sale_from and $sale_from) {

                    $today     = date( 'Y-m-d' );
                    $sale_from = date( 'Y-m-d' , strtotime( $sale_from ) );
                    $sale_to   = date( 'Y-m-d' , strtotime( $sale_to ) );
                    if(( $today >= $sale_from ) && ( $today <= $sale_to )) {

                    } else {

                        $discount = 0;
                    }

                } else {
                    $discount = 0;
                }
            }
            if($discount) {
                if($discount > 100)
                    $discount = 100;

                $new_price = $price - ( $price / 100 ) * $discount;
                $data      = array(
                    'price'     => apply_filters( 'st_apply_tax_amount' , $new_price ) ,
                    'price_old' => apply_filters( 'st_apply_tax_amount' , $price ) ,
                    'discount'  => $discount ,

                );
            } else {
                $new_price = $price;
                $data      = array(
                    'price'    => apply_filters( 'st_apply_tax_amount' , $new_price ) ,
                    'discount' => $discount ,
                );
            }

            return apply_filters( 'st_car_info_price' , $data , $post_id );
        }

        static function get_rental_price( $price , $start , $end , $unit = false )
        {

            $diff_number = self::get_date_diff( $start , $end , $unit );

            $rental_price = $price * $diff_number;
            $rental_price = apply_filters( 'st_car_rental_price' , $rental_price , $price , $start , $end , $unit );
            return $rental_price;

        }

        static function check_booking_days_included()
        {
            return ( st()->get_option( 'booking_days_included' , "off" ) == "on" );
        }

        static function get_date_diff( $start , $end , $unit = false )
        {

            if(!$unit)
                $unit = self::get_price_unit();

            $format = '%H';

            $datediff = STDate::timestamp_diff( $start , $end );

            switch( $unit ) {
                case "day":
                case "per_day":
                    $diff_number = ceil( $datediff / 24 );
                    break;

                case "hour":
                case "per_hour":
                default:
                    $diff_number = $datediff;
                    break;
            }
            if($diff_number < 0)
                $diff_number = 0;

            if(self::check_booking_days_included()) {
                $diff_number += 1;
            }

            return $diff_number;
        }


        /**
         * Remove check if $need=value
         *
         * @update 1.1.3
         * */
        static function get_price_unit( $need = 'value' )
        {
            $unit   = st()->get_option( 'cars_price_unit' , 'day' );
            $return = false;

            if($need == 'label') {
                $all = self::get_option_price_unit();

                if(!empty( $all )) {
                    foreach( $all as $key => $value ) {
                        if($value[ 'value' ] == $unit) {
                            $return = $value[ 'label' ];
                        }
                    }
                } else $return = $unit;
            } elseif($need == 'plural') {
                switch( $unit ) {
                    case "hour":
                        $return = __( "hours" , ST_TEXTDOMAIN );
                        break;
                    case "day":
                        $return = __( "days" , ST_TEXTDOMAIN );
                        break;
                }

            } else {
                $return = $unit;
            }

            return apply_filters( 'st_get_price_unit' , $return , $need );
        }

        /**
         *
         *
         *
         *
         * @since 1.0.9
         * */

        static function get_price_unit_by_unit_id( $unit , $need = 'value' )
        {
            switch( $need ) {
                case "value":
                    return $unit;
                    break;

                case "label":
                    $all = self::get_option_price_unit();

                    if(!empty( $all )) {
                        foreach( $all as $key => $value ) {
                            if($value[ 'value' ] == $unit) {
                                return $value[ 'label' ];
                            }
                        }
                    }
                    break;

                case "plural":
                    switch( $unit ) {
                        case "hour":
                            return __( "hours" , ST_TEXTDOMAIN );
                            break;
                        case "day":
                            return __( "days" , ST_TEXTDOMAIN );
                            break;
                    }
                    break;

                default:
                    return $unit;

                    break;
            }

        }

        static function get_option_price_unit()
        {
            return apply_filters( 'st_car_price_units' , array(

                    array(
                        'value' => 'day' ,
                        'label' => __( 'Day' , ST_TEXTDOMAIN )
                    ) ,
                    array(
                        'value' => 'hour' ,
                        'label' => __( 'Hour' , ST_TEXTDOMAIN )
                    ) ,
                )
            );
        }

        static function get_owner_email( $car_id )
        {
            return get_post_meta( $car_id , 'cars_email' , true );
        }

        static function get_taxonomy_and_id_term_car()
        {
            $list_taxonomy = st_list_taxonomy( 'st_cars' );
            $list_id_vc    = array();
            $param         = array();
            foreach( $list_taxonomy as $k => $v ) {
                $term = get_terms( $v );
                if(!empty( $term ) and is_array( $term )) {
                    foreach( $term as $key => $value ) {
                        $list_value[ $value->name ] = $value->term_id;
                    }
                    $param[ ]                      = array(
                        "type"       => "checkbox" ,
                        "holder"     => "div" ,
                        "heading"    => $k ,
                        "param_name" => "id_term_" . $v ,
                        "value"      => $list_value ,
                        'dependency' => array(
                            'element' => 'sort_taxonomy' ,
                            'value'   => array( $v )
                        ) ,
                    );
                    $list_value                    = "";
                    $list_id_vc[ "id_term_" . $v ] = "";
                }
            }

            return array(
                "list_vc"    => $param ,
                'list_id_vc' => $list_id_vc
            );
        }

        function st_cars_save_review_stats( $comment_id )
        {
            $comemntObj = get_comment( $comment_id );
            $post_id    = $comemntObj->comment_post_ID;

            if(get_post_type( $post_id ) == 'st_cars') {
                $all_stats       = $this->get_review_stats();
                $st_review_stats = STInput::post( 'st_review_stats' );

                if(!empty( $all_stats ) and is_array( $all_stats )) {
                    $total_point = 0;
                    foreach( $all_stats as $key => $value ) {
                        if(isset( $st_review_stats[ $value[ 'title' ] ] )) {
                            $total_point += $st_review_stats[ $value[ 'title' ] ];
                            //Now Update the Each Stat Value
                            update_comment_meta( $comment_id , 'st_stat_' . sanitize_title( $value[ 'title' ] ) , $st_review_stats[ $value[ 'title' ] ] );
                        }
                    }

                    $avg = round( $total_point / count( $all_stats ) , 1 );

                    //Update comment rate with avg point
                    $rate = wp_filter_nohtml_kses( $avg );
                    if($rate > 5) {
                        //Max rate is 5
                        $rate = 5;
                    }
                    update_comment_meta( $comment_id , 'comment_rate' , $rate );
                    //Now Update the Stats Value
                    update_comment_meta( $comment_id , 'st_review_stats' , $st_review_stats );
                }


            }


            if(STInput::post( 'comment_rate' )) {
                update_comment_meta( $comment_id , 'comment_rate' , STInput::post( 'comment_rate' ) );

            }
            //review_stats
            $avg = STReview::get_avg_rate( $post_id );

            update_post_meta( $post_id , 'rate_review' , $avg );
        }

        function st_cars_save_post_review_stats( $comment_id )
        {
            /*since 1.1.0*/
            $comemntObj = get_comment( $comment_id );
            $post_id    = $comemntObj->comment_post_ID;

            $avg = STReview::get_avg_rate( $post_id );
            update_post_meta( $post_id , 'rate_review' , $avg );
        }

        function get_review_stats()
        {
            $review_stat = st()->get_option( 'car_review_stats' );

            return $review_stat;
        }

        function comment_args( $comment_form , $post_id = false )
        {
            /*since 1.1.0*/

            if(!$post_id)
                $post_id = get_the_ID();
            if(get_post_type( $post_id ) == 'st_cars') {
                $stats = $this->get_review_stats();

                if($stats and is_array( $stats )) {
                    $stat_html = '<ul class="list booking-item-raiting-summary-list stats-list-select">';

                    foreach( $stats as $key => $value ) {
                        $stat_html .= '<li class=""><div class="booking-item-raiting-list-title">' . $value[ 'title' ] . '</div>
                                                    <ul class="icon-group booking-item-rating-stars">
                                                    <li class=""><i class="fa fa-smile-o"></i>
                                                    </li>
                                                    <li class=""><i class="fa fa-smile-o"></i>
                                                    </li>
                                                    <li class=""><i class="fa fa-smile-o"></i>
                                                    </li>
                                                    <li class=""><i class="fa fa-smile-o"></i>
                                                    </li>
                                                    <li><i class="fa fa-smile-o"></i>
                                                    </li>
                                                </ul>
                                                <input type="hidden" class="st_review_stats" value="0" name="st_review_stats[' . $value[ 'title' ] . ']">
                                                    </li>';
                    }
                    $stat_html .= '</ul>';


                    $comment_form[ 'comment_field' ] = "
                        <div class='row'>
                            <div class=\"col-sm-8\">
                    ";
                    $comment_form[ 'comment_field' ] .= '<div class="form-group">
                                            <label>' . __( 'Review Title' , ST_TEXTDOMAIN ) . '</label>
                                            <input class="form-control" type="text" name="comment_title">
                                        </div>';

                    $comment_form[ 'comment_field' ] .= '<div class="form-group">
                                            <label>' . __( 'Review Text' ) . '</label>
                                            <textarea name="comment" id="comment" class="form-control" rows="6"></textarea>
                                        </div>
                                        </div><!--End col-sm-8-->
                                        ';

                    $comment_form[ 'comment_field' ] .= '<div class="col-sm-4">' . $stat_html . '</div></div><!--End Row-->';
                }
            }

            return $comment_form;
        }

        /**
         * @since 1.1.1
         * @update 1.1.2
         * filter hook car_external_booking_submit
         */
        public static function car_external_booking_submit()
        {

            $post_id = get_the_ID();
            if(STInput::request( 'post_id' )) {
                $post_id = STInput::request( 'post_id' );
            }

            $car_external_booking      = get_post_meta( $post_id , 'st_car_external_booking' , "off" );
            $car_external_booking_link = get_post_meta( $post_id , 'st_car_external_booking_link' , true );
            if($car_external_booking == "on" && $car_external_booking_link !== "") {
                if(get_post_meta( $post_id , 'st_car_external_booking_link' , true )) {
                    ob_start();
                    ?>
                    <a class='btn btn-primary'
                       href='<?php echo get_post_meta( $post_id , 'st_car_external_booking_link' , true ) ?>'> <?php st_the_language( 'book_now' ) ?></a>
                    <?php
                    $return = ob_get_clean();
                }
            } else {
                $return = TravelerObject::get_book_btn();
            }

            return apply_filters( 'car_external_booking_submit' , $return );
        }

        /**
         *
         *
         * @since 1.1.3
         * */
        static function get_equipment_line_item( $price , $unit , $start_timestamp , $end_timestamp )
        {
            switch( $unit ) {
                case "per_day":
                    $diff = STDate::timestamp_diff_day( $start_timestamp , $end_timestamp );
                    if(!$diff)
                        $diff = 1;
                    return (float)$unit * $diff;

                    break;
                case "per_hour":

                    $diff = STDate::timestamp_diff( $start_timestamp , $end_timestamp );
                    if(!$diff)
                        $diff = 1;
                    return (float)$price * $diff;

                    break;
                default:
                    return (float)$price;
                    break;
            }
        }

    }

    $a = new STCars();
    $a->init();
};
