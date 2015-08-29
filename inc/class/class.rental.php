<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STRental
 *
 * Created by ShineTheme
 *
 */
if(!class_exists( 'STRental' )) {
    class STRental extends TravelerObject
    {
        protected $post_type = 'st_rental';

        function __construct()
        {
            $this->orderby = array(
                'ID'         => array(
                    'key'  => 'ID' ,
                    'name' => __( 'Date' , ST_TEXTDOMAIN )
                ) ,
                'price_asc'  => array(
                    'key'  => 'price_asc' ,
                    'name' => __( 'Price (low to high)' , ST_TEXTDOMAIN )
                ) ,
                'price_desc' => array(
                    'key'  => 'price_desc' ,
                    'name' => __( 'Price (hight to low)' , ST_TEXTDOMAIN )
                ) ,
                'name_asc'   => array(
                    'key'  => 'name_asc' ,
                    'name' => __( 'Name (A-Z)' , ST_TEXTDOMAIN )
                ) ,
                'name_desc'  => array(
                    'key'  => 'name_desc' ,
                    'name' => __( 'Name (Z-A)' , ST_TEXTDOMAIN )
                ) ,
            );
        }

        /**
         * @return array
         */
        public function getOrderby()
        {
            return $this->orderby;
        }

        /**
         *
         *
         * @update 1.1.3
         * */
        function init()
        {

            if(!$this->is_available())
                return;
            parent::init();


            //Filter change layout of rental detail if choose in metabox
            add_filter( 'rental_single_layout' , array( $this , 'custom_rental_layout' ) );

            add_filter( 'template_include' , array( $this , 'choose_search_template' ) );

            //add Widget Area
            add_action( 'widgets_init' , array( $this , 'add_sidebar' ) );

            //Sidebar Pos for SEARCH
            add_filter( 'st_rental_sidebar' , array( $this , 'change_sidebar' ) );

            //Filter the search hotel

            //add_action('pre_get_posts',array($this,'change_search_arg'));

            add_action( 'save_post' , array( $this , 'update_sale_price' ) );

            add_action( 'wp_loaded' , array( $this , 'add_to_cart' ) , 20 );

            add_filter( 'st_search_preload_page' , array( $this , '_change_preload_search_title' ) );

            add_action( 'wp_enqueue_scripts' , array( $this , '_add_script' ) );


            //Save Rental Review Stats
            add_action( 'comment_post' , array( $this , 'save_review_stats' ) );

            //        Change rental review arg
            add_filter( 'st_rental_wp_review_form_args' , array( $this , 'comment_args' ) , 10 , 2 );

            add_action('wp_ajax_st_getOrderByYear', array($this, 'getOrderByYear'));
            add_action('wp_ajax_nopriv_st_getOrderByYear', array($this, 'getOrderByYear'));

            // Woocommerce cart item information
            add_action( 'st_wc_cart_item_information_st_rental' , array( $this , '_show_wc_cart_item_information' ) );
            add_action( 'st_before_cart_item_st_rental' , array( $this , '_show_wc_cart_post_type_icon' ) );


            add_filter( 'st_add_to_cart_item_st_rental' , array( $this , '_deposit_calculator' ) , 10 , 2 );

            // add_filter('st_data_custom_price',array($this,'_st_data_custom_price'));

        }


        function _st_data_custom_price()
        {
            return array( 'title' => 'Price Custom Settings' , 'post_type' => 'st_rental' );
        }

        /**
         *
         *
         *
         * @since 1.1.1
         * */

        function _show_wc_cart_item_information( $st_booking_data = array() )
        {
            echo st()->load_template( 'rental/wc_cart_item_information' , false , array( 'st_booking_data' => $st_booking_data ) );
        }

        /**
         *
         *
         *
         * @since 1.1.1
         * */

        function _show_wc_cart_post_type_icon()
        {
            echo '<span class="booking-item-wishlist-title"><i class="fa fa-home"></i> ' . __( 'rental' , ST_TEXTDOMAIN ) . ' <span></span></span>';
        }

        /**
         * Get rental order by month
         * @since 1.0.9
         * */
        function getOrderByYear(){
            global $wpdb;
            global $wp_query;

            $year = STInput::post('year', date('Y'));
            $item_post_type = STInput::post('item_post_type','');
            $_st_st_booking_post_type = STInput::post('_st_st_booking_post_type','');
            if($item_post_type == '' or $_st_st_booking_post_type== ''){
                echo '';
                die();
            }
            $sql = "SELECT DISTINCT ".$wpdb->posts.".* FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID=".$wpdb->postmeta.".post_id
                    INNER JOIN ".$wpdb->postmeta." as mt1 ON mt1.post_id=".$wpdb->posts.".ID AND mt1.meta_key='item_post_type' AND mt1.meta_value='{$item_post_type}'
                    INNER JOIN ".$wpdb->postmeta." as mt2 ON mt2.post_id=".$wpdb->posts.".ID AND mt2.meta_key='check_in' AND YEAR(mt2.meta_value)='{$year}'
                    INNER JOIN ".$wpdb->postmeta." as mt3 ON mt3.post_id=".$wpdb->posts.".ID AND mt3.meta_key='check_out' AND YEAR(mt3.meta_value)='{$year}'
                    WHERE ".$wpdb->posts.".post_type='st_order'";
            $posts = $wpdb->get_results( $sql , OBJECT );       
            $result = array();
            if(is_array($posts) && count($posts)){
                foreach($posts as $post){
                    $start = date('Y/m/d', strtotime(get_post_meta($post->ID, 'check_in', 'true')));
                    $end = date('Y/m/d', strtotime(get_post_meta($post->ID, 'check_out', 'true')));
                    $result =array_merge($this->getListDate($start, $end),$result);
                }
            }
            $sql = "SELECT DISTINCT * FROM ".$wpdb->prefix."woocommerce_order_items 
                    INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta 
                        ON ".$wpdb->prefix."woocommerce_order_items.order_item_id = ".$wpdb->prefix."woocommerce_order_itemmeta.order_item_id 
                    INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta as mt1 
                        ON mt1.order_item_id = ".$wpdb->prefix."woocommerce_order_items.order_item_id 
                        AND mt1.meta_key = '_st_st_booking_post_type' 
                        AND mt1.meta_value = '{$_st_st_booking_post_type}' 
                    INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta as mt2  
                        ON mt2.order_item_id = ".$wpdb->prefix."woocommerce_order_items.order_item_id 
                        AND mt2.meta_key = '_st_check_in' 
                        AND YEAR(DATE_FORMAT(STR_TO_DATE(mt2.meta_value, '%m/%d/%Y'), '%Y/%m/%d')) = '{$year}' 
                    INNER JOIN ".$wpdb->prefix."woocommerce_order_itemmeta as mt3 
                        ON mt3.order_item_id = ".$wpdb->prefix."woocommerce_order_items.order_item_id 
                        AND mt3.meta_key = '_st_check_out' 
                        AND YEAR(DATE_FORMAT(STR_TO_DATE(mt2.meta_value, '%m/%d/%Y'), '%Y/%m/%d')) = '{$year}' 
                    GROUP BY ".$wpdb->prefix."woocommerce_order_itemmeta.order_item_id";

            $posts = $wpdb->get_results( $sql);  
            if(is_array($posts) && count($posts)){
                foreach($posts as $post){
                    $item = $post->order_item_id;
                    $start = date('Y/m/d',strtotime(wc_get_order_item_meta($item, '_st_check_in')));
                    $end = date('Y/m/d',strtotime(wc_get_order_item_meta($item, '_st_check_out')));
                    $result =array_merge($this->getListDate($start, $end),$result);
                }
            }        
            echo json_encode($result);
            
            die();
        }

        function getListDate($start, $end){

            $start = new DateTime($start);
            $end = new DateTime($end . ' +1 day'); 
            $list = array();
            foreach (new DatePeriod($start, new DateInterval('P1D'), $end) as $day) {
                    $list[] = $day->format(TravelHelper::getDateFormat());
            }
            return $list;
        }

        function comment_args($comment_form,$post_id=false)
        {
            if(!$post_id)
                $post_id = get_the_ID();
            if(get_post_type( $post_id ) == 'st_rental') {
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

        function save_review_stats( $comment_id )
        {

            $comemntObj = get_comment( $comment_id );
            $post_id    = $comemntObj->comment_post_ID;

            if(get_post_type( $post_id ) == 'st_rental') {
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
            //Class hotel do the rest
        }

        function  _alter_search_query( $where )
        {
            global $wp_query;
            if(is_search()) {
                $post_type = $wp_query->query_vars[ 'post_type' ];

                if($post_type == 'st_rental') {
                    //Alter From NOW
                    global $wpdb;

                    $check_in  = TravelHelper::convertDateFormat( STInput::get( 'start' ) );
                    $check_out = TravelHelper::convertDateFormat( STInput::get( 'end' ) );


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
                                                JOIN {$wpdb->postmeta}  as st_meta5 on st_meta5.post_id=st_meta6.meta_value and st_meta5.meta_key='rental_number'
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


        function _add_script()
        {
            if(is_singular( 'st_rental' )) {
                wp_enqueue_script( 'single-rental' , get_template_directory_uri() . '/js/init/single-rental.js' );
            }
        }


        function _change_preload_search_title( $return )
        {
            if(get_query_var( 'post_type' ) == 'st_rental') {
                $return = __( " Rentals in %s" , ST_TEXTDOMAIN );

                if(STInput::get( 'location_id' )) {
                    $return = sprintf( $return , get_the_title( STInput::get( 'location_id' ) ) );
                } elseif(STInput::get( 'location_name' )) {
                    $return = sprintf( $return , STInput::get( 'location_name' ) );
                } elseif(STInput::get( 'address' )) {
                    $return = sprintf( $return , STInput::get( 'address' ) );
                } else {
                    $return = __( " Rentals" , ST_TEXTDOMAIN );
                }

                $return .= '...';
            }


            return $return;
        }

        function get_cart_item_html( $item_id )
        {
            return st()->load_template( 'rental/cart_item_html' , null , array( 'item_id' => $item_id ) );
        }

        /**
         * @since 1.1.0
         **/
        function add_to_cart()
        {
            if(STInput::request( 'action' ) == 'rental_add_cart') {


                if(!STInput::request( 'start' ) or !STInput::request( 'end' )) {
                    STTemplate::set_message( st_get_language( 'check_in_and_check_out_are_required' ) , 'danger' );
                    return;
                }
                $today = strtotime( date( 'm/d/Y' ) );

                $check_in = TravelHelper::convertDateFormat(STInput::request('start'));

                $rental_id = STInput::request( 'item_id' );

                $booking_period = get_post_meta( $rental_id , 'rentals_booking_period' , true );

                $period = STDate::date_diff( $today , $check_in );

                if($booking_period && $period < $booking_period) {
                    STTemplate::set_message( sprintf( __( 'Booking is only accepted %d day(s) before today.' , ST_TEXTDOMAIN ) , $booking_period ) , 'danger' );
                    return;
                }

                $adult = intval( STInput::request( 'adult' ) );

                $children = intval( STInput::request( 'children' ) );

                if(get_post_meta( $rental_id , 'rental_max_adult' , true )) {

                    $max_adult = intval( get_post_meta( $rental_id , 'rental_max_adult' , true ) );

                    if($adult > $max_adult) {
                        STTemplate::set_message( sprintf( __( 'A maximum number of adult(s): %d' , ST_TEXTDOMAIN ) , $max_adult ) , 'danger' );
                        return;
                    }
                }

                if(get_post_meta( $rental_id , 'rental_max_children' , true )) {
                    $max_children = intval( get_post_meta( $rental_id , 'rental_max_children' , true ) );

                    if($children > $max_children) {
                        STTemplate::set_message( sprintf( __( 'A maximum number of children: %d' , ST_TEXTDOMAIN ) , $max_children ) , 'danger' );
                        return;
                    }
                }
                $validate=$this->do_add_to_cart(array(
                    'item_id'=>STInput::request('item_id'),
                    'number_room'=>STInput::request('number_room'),
                    'price'=>STInput::request('price'),
                    'start'=>TravelHelper::convertDateFormat(STInput::request('start')),
                    'end'=>TravelHelper::convertDateFormat(STInput::request('end')),
                    'adult'             =>STInput::request('adult'),
                    'children'          =>STInput::request('children')
                ));

                if($validate)
                {
                    $link=STCart::get_cart_link();
                    wp_safe_redirect($link);
                    die;
                }


            }
        }

        function do_add_to_cart( $array = array() )
        {

            $form_validate = true;
            if(empty( $array ))
                $array = STInput::post();
            $default = array(
                'item_id'     => '' ,
                'number_room' => 1 ,
                'price'       => '' ,
                'start'       => '' ,
                'end'         => '' ,
                'adult'       => 1 ,
                'children'    => 0 ,
            );

            $array = wp_parse_args( $array , $default );

            extract( $array );

            $data = array(
                'check_in'  => $start ,
                'check_out' => $end ,
                'currency'  => TravelHelper::get_default_currency( 'symbol' ) ,
                'adult'     => $adult ,
                'children'  => $children
            );

            //Validate available number
            $form_validate = $this->_add_cart_check_available( $item_id , $data );

            if($form_validate)
                $form_validate = apply_filters( 'st_rental_add_cart_validate' , $form_validate );

            if($form_validate) {
                STCart::add_cart( $item_id , $number_room , $price , $data );
            }

            return $form_validate;

        }

        function _add_cart_check_available( $post_id = false , $data = array() )
        {
            if(!$post_id or get_post_status( $post_id ) != 'publish') {
                STTemplate::set_message( __( 'Rental doese not exists' , ST_TEXTDOMAIN ) , 'danger' );
                return false;
            }


            $validator = new STValidate();

            $validator->set_rules( 'start' , __( 'Check in' , ST_TEXTDOMAIN ) , 'required' );
            $validator->set_rules( 'end' , __( 'Check out' , ST_TEXTDOMAIN ) , 'required' );

            if(!$validator->run()) {
                STTemplate::set_message( $validator->error_string() , 'danger' );
                return false;
            }

            $check_in  = date( 'Y-m-d H:i:s' , strtotime( STInput::post( 'start' ) ) );
            $check_out = date( 'Y-m-d H:i:s' , strtotime( STInput::post( 'end' ) ) );

            if(!$this->_is_slot_available( $post_id , $check_in , $check_out )) {
                STTemplate::set_message( __( 'Sorry! This rental is not available.' , ST_TEXTDOMAIN ) , 'danger' );
                return false;
            }

            return true;

        }

        function _is_slot_available( $post_id , $check_in , $check_out )
        {

            global $wpdb;

            $query = "
SELECT count(booked_id) as total_booked from (
SELECT st_meta6.meta_value as booked_id ,st_meta2.meta_value as check_in,st_meta3.meta_value as check_out
                                         FROM {$wpdb->posts}
                                                JOIN {$wpdb->postmeta}  as st_meta2 on st_meta2.post_id={$wpdb->posts}.ID and st_meta2.meta_key='check_in'
                                                JOIN {$wpdb->postmeta}  as st_meta3 on st_meta3.post_id={$wpdb->posts}.ID and st_meta3.meta_key='check_out'
                                                JOIN {$wpdb->postmeta}  as st_meta6 on st_meta6.post_id={$wpdb->posts}.ID and st_meta6.meta_key='item_id'
                                                WHERE {$wpdb->posts}.post_type='st_order'
                                                AND st_meta6.meta_value={$post_id}
                                          GROUP BY {$wpdb->posts}.id HAVING  (

                                                    ( CAST(st_meta2.meta_value AS DATE)<'{$check_in}' AND  CAST(st_meta3.meta_value AS DATE)>'{$check_in}' )
                                                    OR ( CAST(st_meta2.meta_value AS DATE)>='{$check_in}' AND  CAST(st_meta2.meta_value AS DATE)<='{$check_out}'))) as object_booked
        ";

            $total_booked = (int)$wpdb->get_var( $query );
            $total        = (int)get_post_meta( $post_id , 'rental_number' , true );

            if($total > $total_booked)
                return true;
            else return false;

        }

        function update_sale_price( $post_id )
        {
            if(get_post_type( $post_id ) == $this->post_type) {
                $price = STRental::get_price( $post_id );
                update_post_meta( $post_id , 'sale_price' , $price );
            }
        }

        function get_search_fields()
        {
            $fields = st()->get_option( 'rental_search_fields' );

            return $fields;
        }

        function get_search_adv_fields()
        {
            $fields = st()->get_option( 'rental_search_advance' );

            return $fields;
        }

        /**
         *
         *
         * @update 1.1.1
         * */
        static function get_search_fields_name()
        {
            return array(
                'location'      => array(
                    'value' => 'location' ,
                    'label' => __( 'Location' , ST_TEXTDOMAIN )
                ) ,
                'list_location' => array(
                    'value' => 'list_location' ,
                    'label' => __( 'Location List' , ST_TEXTDOMAIN )
                ) ,
                'address'       => array(
                    'value' => 'address' ,
                    'label' => __( 'Address (geobytes.com)' , ST_TEXTDOMAIN )
                ) ,
                'checkin'       => array(
                    'value' => 'checkin' ,
                    'label' => __( 'Check in' , ST_TEXTDOMAIN )
                ) ,
                'checkout'      => array(
                    'value' => 'checkout' ,
                    'label' => __( 'Check out' , ST_TEXTDOMAIN )
                ) ,
                'adult'         => array(
                    'value' => 'adult' ,
                    'label' => __( 'Adult' , ST_TEXTDOMAIN )
                ) ,
                'children'      => array(
                    'value' => 'children' ,
                    'label' => __( 'Children' , ST_TEXTDOMAIN )
                ) ,
                'room_num'      => array(
                    'value' => 'room_num' ,
                    'label' => __( 'Room(s)' , ST_TEXTDOMAIN )
                ) ,
                'taxonomy'      => array(
                    'value' => 'taxonomy' ,
                    'label' => __( 'Taxonomy' , ST_TEXTDOMAIN )
                ) ,
                'item_name'     => array(
                    'value' => 'item_name' ,
                    'label' => __( 'Rental Name' , ST_TEXTDOMAIN )
                )

            );
        }

        /**
         * Add query meta max adult, children
         * @since 1.1.0
         **/
        function change_search_arg( $query )
        {
            $post_type = get_query_var( 'post_type' );

            $meta_query = array();

            if($query->is_search && $post_type == 'st_rental') {
                add_filter( 'posts_where' , array( $this , '_alter_search_query' ) );

                $tax = STInput::get( 'taxonomy' );

                if(!empty( $tax ) and is_array( $tax )) {
                    $tax_query = array();
                    foreach( $tax as $key => $value ) {
                        if($value) {
                            $tax_query[ ] = array(
                                'taxonomy' => $key ,
                                'terms'    => explode( ',' , $value ) ,
                                'COMPARE'  => "IN"
                            );
                        }
                    }

                    $query->set( 'tax_query' , $tax_query );
                }

                if($location_id = STInput::get( 'location_id' )) {
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
                        'key'     => 'location_id' ,
                        'value'   => $ids_in ,
                        'compare' => 'IN'
                    );

//                    $query->set('s', '');
                } else {
                    if(STInput::request( 'location_name' )) {
                        $ids_location = TravelerObject::_get_location_by_name( STInput::get( 'location_name' ) );
                        if(!empty( $ids_location )) {
                            $meta_query[ ] = array(
                                'key'     => 'location_id' ,
                                'value'   => $ids_location ,
                                'compare' => "IN" ,
                            );
                        } else {
                            $meta_query[ ] = array(
                                'key'     => 'address' ,
                                'value'   => STInput::get( 'location_name' ) ,
                                'compare' => "like" ,
                            );
                        }
                    } elseif(STInput::request( 'address' )) {
                        $value = STInput::request( 'address' );
                        $value = explode( "," , $value );
                        if(!empty( $value[ 0 ] ) and !empty( $value[ 2 ] )) {
                            $meta_query[ ] = array(
                                array(
                                    'key'     => 'address' ,
                                    'value'   => $value[ 0 ] ,
                                    'compare' => 'like' ,
                                ) ,
                                array(
                                    'key'     => 'address' ,
                                    'value'   => $value[ 2 ] ,
                                    'compare' => 'like' ,
                                ) ,
                                "relation" => 'OR'
                            );
                        } else {
                            $meta_query[ ] = array(
                                'key'     => 'address' ,
                                'value'   => STInput::request( 'address' ) ,
                                'compare' => "like" ,
                            );
                        }
                    }
                }
                $is_featured = st()->get_option( 'is_featured_search_rental' , 'off' );
                if(!empty( $is_featured ) and $is_featured == 'on') {
                    $query->set( 'meta_key' , 'is_featured' );
                    $query->set( 'orderby' , 'meta_value' );
                    $query->set( 'order' , 'DESC' );
                }

                if($orderby = STInput::get( 'orderby' )) {
                    switch( $orderby ) {
                        case "price_asc":
                            $query->set( 'meta_key' , 'sale_price' );
                            $query->set( 'orderby' , 'meta_value' );
                            $query->set( 'order' , 'asc' );

                            break;
                        case "price_desc":
                            $query->set( 'meta_key' , 'sale_price' );
                            $query->set( 'orderby' , 'meta_value' );
                            $query->set( 'order' , 'desc' );

                            break;
                        case "avg_rate":
                            $query->set( 'meta_key' , 'rate_review' );
                            $query->set( 'orderby' , 'meta_value' );
                            $query->set( 'order' , 'desc' );

                            break;
                        case "name_asc":
                            $query->set( 'orderby' , 'title' );
                            $query->set( 'order' , 'asc' );

                            break;
                        case "name_desc":
                            $query->set( 'orderby' , 'title' );
                            $query->set( 'order' , 'desc' );

                            break;
                    }
                }


                $adult = intval( STInput::get( 'adult_num' ) );

                if($adult) {
                    $meta_query[ ] = array(
                        'key'     => 'rental_max_adult' ,
                        'value'   => $adult ,
                        'compare' => '>='
                    );
                }
                $children = intval( STInput::get( 'children_num' ) );

                if($children) {
                    $meta_query[ ] = array(
                        'key'     => 'rental_max_children' ,
                        'value'   => $children ,
                        'compare' => '>='
                    );
                }

                if($star = STInput::get( 'star_rate' )) {
                    $meta_query[ ] = array(
                        'key'     => 'rate_review' ,
                        'value'   => explode( ',' , $star ) ,
                        'compare' => "IN"
                    );
                }
                if($price = STInput::get( 'price_range' )) {
                    $priceobj      = explode( ';' , $price );
                    $meta_query[ ] = array(
                        'key'     => 'sale_price' ,
                        'value'   => $priceobj[ 0 ] ,
                        'compare' => '>=' ,
                        'type'    => "NUMERIC"
                    );
                    if(isset( $priceobj[ 1 ] )) {
                        $meta_query[ ] = array(
                            'key'     => 'sale_price' ,
                            'value'   => $priceobj[ 1 ] ,
                            'compare' => '<=' ,
                            'type'    => "NUMERIC"
                        );
                    }

                    $meta_query[ 'relation' ] = 'and';
                }
                if(!empty( $meta_query )) {
                    $query->set( 'meta_query' , $meta_query );
                }
            } else {
                remove_filter( 'posts_where' , array( $this , '_alter_search_query' ) );
            }

        }

        function change_sidebar()
        {
            return st()->get_option( 'rental_sidebar_pos' , 'left' );
        }

        function choose_search_template( $template )
        {
            global $wp_query;
            $post_type = get_query_var( 'post_type' );
            if($wp_query->is_search && $post_type == 'st_rental') {
                return locate_template( 'search-rental.php' );  //  redirect to archive-search.php
            }
            return $template;
        }

        function add_sidebar()
        {
            register_sidebar( array(
                'name'          => __( 'Rental Search Sidebar 1' , ST_TEXTDOMAIN ) ,
                'id'            => 'rental-sidebar' ,
                'description'   => __( 'Widgets in this area will be shown on Rental' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );

            register_sidebar( array(
                'name'          => __( 'Rental Search Sidebar 2' , ST_TEXTDOMAIN ) ,
                'id'            => 'rental-sidebar-2' ,
                'description'   => __( 'Widgets in this area will be shown on Rental' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );


        }


        function  get_result_string()
        {
            global $wp_query;
            $result_string = '';

            if($wp_query->found_posts) {
                $result_string .= sprintf( _n( '%s vacation rental ' , '%s vacation rentals ' , $wp_query->found_posts , ST_TEXTDOMAIN ) , $wp_query->found_posts );
            } else {
                $result_string .= __( 'No rental found' , ST_TEXTDOMAIN );
            }

            $location_id = STInput::get( 'location_id' );
            if($location_id and $location = get_post( $location_id )) {
                $result_string .= sprintf( __( ' in %s' , ST_TEXTDOMAIN ) , get_the_title( $location_id ) );
            } elseif(STInput::request( 'location_name' )) {
                $result_string .= sprintf( __( ' in %s' , ST_TEXTDOMAIN ) , STInput::request( 'location_name' ) );
            } elseif(STInput::request( 'address' )) {
                $result_string .= sprintf( __( ' in %s' , ST_TEXTDOMAIN ) , STInput::request( 'address' ) );
            }

            $start = STInput::get( 'start' );
            $end   = STInput::get( 'end' );

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


        function custom_rental_layout( $old_layout_id = false )
        {
            if(is_singular( 'st_rental' )) {
                $meta = get_post_meta( get_the_ID() , 'custom_layout' , true );

                if($meta) {
                    return $meta;
                }
            }
            return $old_layout_id;
        }

        function get_near_by( $post_id = false , $range = 20 , $limit = 5 )
        {
            $this->post_type = 'st_rental';
            return parent::get_near_by( $post_id , $range , $limit );
        }

        function get_review_stats()
        {
            $review_stat = st()->get_option( 'rental_review_stats' );

            return $review_stat;
        }

        function get_custom_fields()
        {
            return st()->get_option( 'rental_custom_fields' , array() );
        }


        static function get_price( $post_id = false )
        {
            if(!$post_id)
                $post_id = get_the_ID();

            $price     = get_post_meta( $post_id , 'price' , true );
            $price     = apply_filters( 'st_apply_tax_amount' , $price );
            $new_price = 0;

            $discount         = get_post_meta( $post_id , 'discount_rate' , true );
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
            } else {
                $new_price = $price;
            }

            return $new_price;

        }

        static function get_orgin_price( $post_id = false )
        {
            if(!$post_id)
                $post_id = get_the_ID();

            $price = get_post_meta( $post_id , 'price' , true );


            return $price = apply_filters( 'st_apply_tax_amount' , $price );


        }

        static function is_sale( $post_id = false )
        {
            if(!$post_id)
                $post_id = get_the_ID();
            $discount         = get_post_meta( $post_id , 'discount_rate' , true );
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
                return true;
            }
            return false;
        }

        function change_post_class( $class )
        {
            if(self::is_sale()) {
                $class[ ] = 'is_sale';
            }

            return $class;
        }


        static function get_owner_email( $item_id )
        {
            return get_post_meta( $item_id , 'agent_email' , true );
        }

        /**
         *
         *
         * @since 1.0.9
         * */
        function is_available()
        {
            return true;
        }

        public static function rental_external_booking_submit()
        {
            /*
             * since 1.1.1 
             * filter hook rental_external_booking_submit
            */
            $post_id = get_the_ID();
            if(STInput::request( 'post_id' )) {
                $post_id = STInput::request( 'post_id' );
            }

            $rental_external_booking      = get_post_meta( $post_id , 'st_rental_external_booking' , "off" );
            $rental_external_booking_link = get_post_meta( $post_id , 'st_rental_external_booking_link' , true );
            if($rental_external_booking == "on" && $rental_external_booking_link !== "") {
                if(get_post_meta( $post_id , 'st_rental_external_booking_link' , true )) {
                    ob_start();
                    ?>
                    <a class='btn btn-primary'
                       href='<?php echo get_post_meta( $post_id , 'st_rental_external_booking_link' , true ) ?>'> <?php st_the_language( 'rental_book_now' ) ?></a>
                    <?php
                    $return = ob_get_clean();
                }
            } else {
                $return = TravelerObject::get_book_btn();
            }
            return apply_filters( 'rental_external_booking_submit' , $return );
        }

    }

    $rental = new STRental();
    $rental->init();
}
