<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STActivity
 *
 * Created by ShineTheme
 *
 */
if(!class_exists( 'STActivity' )) {
    class STActivity extends TravelerObject
    {
        protected $orderby;
        protected $post_type = "st_activity";

        function __construct( $tours_id = false )
        {

            $this->orderby = array(
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
                    'name' => __( 'Activity Name (A-Z)' , ST_TEXTDOMAIN )
                ) ,
                'name_z_a'   => array(
                    'key'  => 'name_z_a' ,
                    'name' => __( 'Activity Name (Z-A)' , ST_TEXTDOMAIN )
                ) ,

            );

        }

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
            add_filter( 'st_activity_detail_layout' , array( $this , 'custom_activity_layout' ) );

            // add to cart
            add_action( 'wp_loaded' , array( $this , 'activity_add_to_cart' ) , 20 );

            //custom search Activity template
            add_filter( 'template_include' , array( $this , 'choose_search_template' ) );

            //Filter the search Activity
            //add_action('pre_get_posts',array($this,'change_search_activity_arg'));

            //add Widget Area
            add_action( 'widgets_init' , array( $this , 'add_sidebar' ) );

            //

            add_filter( 'st_search_preload_page' , array( $this , '_change_preload_search_title' ) );

            // Woocommerce cart item information
            add_action( 'st_wc_cart_item_information_st_activity' , array( $this , '_show_wc_cart_item_information' ) );

            add_action( 'st_before_cart_item_st_activity' , array( $this , '_show_wc_cart_post_type_icon' ) );

            add_filter( 'st_add_to_cart_item_st_activity' , array( $this , '_deposit_calculator' ) , 10 , 2 );

        }

        /**
         *
         *
         * @since 1.1.1
         * */

        function _show_wc_cart_post_type_icon()
        {
            echo '<span class="booking-item-wishlist-title"><i class="fa fa-bolt"></i> ' . __( 'activity' , ST_TEXTDOMAIN ) . ' <span></span></span>';
        }

        /**
         *
         *
         * @since 1.1.1
         * */

        function _show_wc_cart_item_information( $st_booking_data = array() )
        {
            echo st()->load_template( 'activity/wc_cart_item_information' , false , array( 'st_booking_data' => $st_booking_data ) );
        }

        /**
         *
         *
         * @update 1.1.1
         * */
        static function get_search_fields_name()
        {
            return array(
                'address'       => array(
                    'value' => 'address' ,
                    'label' => __( 'Address' , ST_TEXTDOMAIN )
                ) ,
                'address-2'     => array(
                    'value' => 'address-2' ,
                    'label' => __( 'Address (geobytes.com)' , ST_TEXTDOMAIN )
                ) ,
                'list_location' => array(
                    'value' => 'list_location' ,
                    'label' => __( 'Location List' , ST_TEXTDOMAIN )
                ) ,
                array(
                    'value' => 'check_in' ,
                    'label' => __( 'Check In' , ST_TEXTDOMAIN )
                ) ,
                array(
                    'value' => 'check_out' ,
                    'label' => __( 'Check Out' , ST_TEXTDOMAIN )
                ) ,
                'taxonomy'      => array(
                    'value' => 'taxonomy' ,
                    'label' => __( 'Taxonomy' , ST_TEXTDOMAIN )
                ) ,
                'item_name'     => array(
                    'value' => 'item_name' ,
                    'label' => __( 'Activity Name' , ST_TEXTDOMAIN )
                )
            );
        }

        function _change_preload_search_title( $return )
        {
            if(get_query_var( 'post_type' ) == 'st_activity') {
                $return = __( " Activities in %s" , ST_TEXTDOMAIN );

                if(STInput::request( 'location_id' )) {
                    $return = sprintf( $return , get_the_title( STInput::request( 'location_id' ) ) );
                } elseif(STInput::request( 'location_name' )) {
                    $return = sprintf( $return , STInput::request( 'location_name' ) );
                } elseif(STInput::request( 'address' )) {
                    $return = sprintf( $return , STInput::request( 'address' ) );
                } else {
                    $return = __( " Activities" , ST_TEXTDOMAIN );
                }

                $return .= '...';
            }


            return $return;
        }

        function add_sidebar()
        {
            register_sidebar( array(
                'name'          => __( 'Activity Search Sidebar 1' , ST_TEXTDOMAIN ) ,
                'id'            => 'activity-sidebar' ,
                'description'   => __( 'Widgets in this area will be shown on Activity' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );

            register_sidebar( array(
                'name'          => __( 'Activity Search Sidebar 2' , ST_TEXTDOMAIN ) ,
                'id'            => 'activity-sidebar-2' ,
                'description'   => __( 'Widgets in this area will be shown on Activity' , ST_TEXTDOMAIN ) ,
                'before_title'  => '<h4>' ,
                'after_title'   => '</h4>' ,
                'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                'after_widget'  => '</div>' ,
            ) );

        }



        function change_search_activity_arg( $query )
        {

            $post_type = get_query_var( 'post_type' );

            if($query->is_search && $post_type == 'st_activity') {

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
                        'key'     => 'id_location' ,
                        'value'   => $ids_in ,
                        'compare' => 'IN'
                    );
                    $query->set( 'meta_query' , $meta_query );
                    //$query->set('s','');
                } else {
                    if(STInput::request( 'location_name' )) {

                        $ids_location = TravelerObject::_get_location_by_name(STInput::get( 'location_name' ));
                        if(!empty($ids_location)){
                            $meta_query[ ] = array(
                                'key'     => 'id_location' ,
                                'value'   => $ids_location ,
                                'compare' => "IN" ,
                            );
                        }else{
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

                $is_featured = st()->get_option( 'is_featured_search_activity' , 'off' );
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
                        'key'     => 'price' ,
                        'value'   => $priceobj[ 0 ] ,
                        'compare' => '>=' ,
                        'type'    => "NUMERIC"
                    );
                    if(isset( $priceobj[ 1 ] )) {
                        $meta_query[ ] = array(
                            'key'     => 'price' ,
                            'value'   => $priceobj[ 1 ] ,
                            'compare' => '<=' ,
                            'type'    => "NUMERIC"
                        );
                    }
                    $meta_query[ 'relation' ] = 'and';
                }

                if($price = STInput::get( 'start' )) {

                    $meta_query[ ]            = array(
                        'key'       => 'check_in' ,
                        'value'     => date( 'Y-m-d' , strtotime( $price = STInput::get( 'start' ) ) ) ,
                        'compare'   => '<=' ,
                        'meta_type' => 'DATE'
                    );
                    $meta_query[ 'relation' ] = 'and';
                }
                if($price = STInput::get( 'end' )) {

                    $meta_query[ ]            = array(
                        'key'       => 'check_out' ,
                        'value'     => date( 'Y-m-d' , strtotime( $price = STInput::get( 'end' ) ) ) ,
                        'compare'   => '>=' ,
                        'meta_type' => 'DATE'
                    );
                    $meta_query[ 'relation' ] = 'and';
                }

                if($star = STInput::get( 'star_rate' )) {
                    $meta_query[ ] = array(
                        'key'     => 'rate_review' ,
                        'value'   => explode( ',' , $star ) ,
                        'compare' => "IN"
                    );
                }

                if(!empty( $meta_query )) {
                    $query->set( 'meta_query' , $meta_query );
                }
            }
        }

        function choose_search_template( $template )
        {
            global $wp_query;
            $post_type = get_query_var( 'post_type' );
            if($wp_query->is_search && $post_type == 'st_activity') {
                return locate_template( 'search-activity.php' );  //  redirect to archive-search.php
            }
            return $template;
        }

        function get_result_string()
        {
            global $wp_query , $st_search_query;
            if($st_search_query) {
                $query = $st_search_query;
            } else $query = $wp_query;

            $result_string = '';
            if($query->found_posts > 1) {
                $result_string .= esc_html( $query->found_posts ) . __( ' activities ' , ST_TEXTDOMAIN );
            } else {
                $result_string .= esc_html( $query->found_posts ) . __( ' activity ' , ST_TEXTDOMAIN );
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

            return '<span>' . $result_string . '</span>';

        }

        /**
         * @since 1.0.9
         **/
        function activity_add_to_cart()
        {
            if(STInput::get( 'action' ) == 'activity_add_to_cart') {

                $item_id = STInput::get( 'item_id' );
                $number  = STInput::get( 'number' );;
                $discount = STInput::get( 'discount' );
                if(!empty( $discount )) {
                    $price = STInput::get( 'price' );
                    $price = $price - $price * ( $discount / 100 );
                    $data  = array(
                        'discount'   => $discount ,
                        'price_old'  => STInput::get( 'price' ) ,
                        'price_sale' => $price ,
                    );
                } else {
                    $price = STInput::get( 'price' );
                }

                $data[ 'check_in' ]   = STInput::get( 'check_in' );
                $data[ 'check_out' ]  = STInput::get( 'check_out' );
                $data[ 'type_price' ] = STInput::request( 'type_price' );

                if($data[ 'type_price' ] == 'people_price') {
                    $prices                 = self::get_price_person( $item_id );
                    $data[ 'adult_price' ]  = $prices[ 'adult' ];
                    $data[ 'child_price' ]  = $prices[ 'child' ];
                    $data[ 'discount' ]     = $prices[ 'discount' ];
                    $data[ 'adult_number' ] = STInput::request( 'adult_number' , 1 );
                    $data[ 'child_number' ] = STInput::request( 'children_number' , 0 );
                }

                /* Check booking period */
                $today = strtotime( date( 'm/d/Y' ) );

                $check_out = strtotime( STInput::get( 'check_out' ) );

                //$period = STDate::date_diff($today,$check_in);
                $expired = $today - $check_out;

                if($expired >= 0) {

                    STTemplate::set_message( __( 'This activity has expired' , ST_TEXTDOMAIN ) , 'danger' );
                    return;
                } else {

                    STCart::add_cart( $item_id , $number , $price , $data );

                    $link = STCart::get_cart_link();
                    wp_safe_redirect( $link );
                    die;
                }
            }

        }

        function do_add_to_cart()
        {
            $pass_validate = true;

            $item_id = STInput::request( 'item_id' );
            $number  = STInput::request( 'number' );;
            $discount = STInput::request( 'discount' );
            if(!empty( $discount )) {
                $price = STInput::request( 'price' );
                $price = $price - $price * ( $discount / 100 );
                $data  = array(
                    'discount'   => $discount ,
                    'price_old'  => STInput::request( 'price' ) ,
                    'price_sale' => $price ,
                );
            } else {
                $price = STInput::request( 'price' );
            }
            $data[ 'check_in' ]   = STInput::request( 'check_in' );
            $data[ 'check_out' ]  = STInput::request( 'check_out' );
            $data[ 'type_price' ] = STInput::request( 'type_price' );

            if($data[ 'type_price' ] == 'people_price') {
                $prices                 = self::get_price_person( $item_id );
                $data[ 'adult_price' ]  = $prices[ 'adult' ];
                $data[ 'child_price' ]  = $prices[ 'child' ];
                $data[ 'discount' ]     = $prices[ 'discount' ];
                $data[ 'adult_number' ] = STInput::request( 'adult_number' , 1 );
                $data[ 'child_number' ] = STInput::request( 'children_number' , 0 );
            }

            if($pass_validate)
                $pass_validate = apply_filters( 'st_activity_add_cart_validate' , $pass_validate );


            if($pass_validate) {
                STCart::add_cart( $item_id , $number , $price , $data );
            }

            return $pass_validate;

        }

        function get_cart_item_html( $item_id = false )
        {
            return st()->load_template( 'activity/cart_item_html' , null , array( 'item_id' => $item_id ) );
        }

        function custom_activity_layout( $old_layout_id )
        {
            if(is_singular( 'st_activity' )) {
                $meta = get_post_meta( get_the_ID() , 'st_custom_layout' , true );

                if($meta) {
                    return $meta;
                }
            }
            return $old_layout_id;
        }

        function get_search_fields()
        {
            $fields = st()->get_option( 'activity_search_fields' );
            return $fields;
        }

        static function get_info_price( $post_id = null )
        {

            if(!$post_id)
                $post_id = get_the_ID();
            $price     = get_post_meta( $post_id , 'price' , true );
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

            return $data;
        }

        function get_near_by( $post_id = false , $range = 20 , $limit = 5 )
        {

            $this->post_type = 'st_activity';
            return parent::get_near_by( $post_id , $range , $limit = 5 );
        }

        static function get_owner_email( $item_id )
        {
            return get_post_meta( $item_id , 'contact_email' , true );
        }

        public static function activity_external_booking_submit()
        {
            /*
             * since 1.1.1 
             * filter hook activity_external_booking_submit
            */
            $post_id = get_the_ID();
            if(STInput::request( 'post_id' )) {
                $post_id = STInput::request( 'post_id' );
            }

            $activity_external_booking      = get_post_meta( $post_id , 'st_activity_external_booking' , "off" );
            $activity_external_booking_link = get_post_meta( $post_id , 'st_activity_external_booking_link' , true );
            if($activity_external_booking == "on" and $activity_external_booking_link !== "") {
                if(get_post_meta( $post_id , 'st_activity_external_booking_link' , true )) {
                    ob_start();
                    ?>
                    <a class='btn btn-primary'
                       href='<?php echo get_post_meta( $post_id , 'st_activity_external_booking_link' , true ) ?>'> <?php st_the_language( 'book_now' ) ?></a>
                    <?php
                    $return = ob_get_clean();
                }
            } else {
                $return = TravelerObject::get_book_btn();
            }
            return apply_filters( 'activity_external_booking_submit' , $return );
        }

        static function get_price_html( $post_id = false , $get = false , $st_mid = '' , $class = '' )
        {
            /*
             * since 1.1.3
             * filter hook get_price_html
            */
            if(!$post_id)
                $post_id = get_the_ID();

            $html = '';


            $type_price = get_post_meta( $post_id , 'type_price' , true );
            if($type_price == 'people_price') {
                $prices = self::get_price_person( $post_id );

                $adult_html = '';

                $adult_new_html = '<span class="text-lg lh1em item ">' . TravelHelper::format_money( $prices[ 'adult_new' ] ) . '</span>';

                // Check on sale
                if(isset( $prices[ 'adult' ] ) and $prices[ 'adult' ] and $prices[ 'discount' ]) {
                    $adult_html = '<span class="text-small lh1em  onsale">' . TravelHelper::format_money( $prices[ 'adult' ] ) . '</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                    $html .= sprintf( __( 'Adult: %s %s' , ST_TEXTDOMAIN ) , $adult_html , $adult_new_html );
                } else {
                    $html .= sprintf( __( 'Adult: %s' , ST_TEXTDOMAIN ) , $adult_new_html );
                }

                $child_new_html = '<span class="text-lg lh1em item ">' . TravelHelper::format_money( $prices[ 'child_new' ] ) . '</span>';


                // Price for child
                if($prices[ 'child_new' ]) {
                    $html .= ' ' . $st_mid . ' ';

                    // Check on sale
                    if(isset( $prices[ 'child' ] ) and $prices[ 'child' ] and $prices[ 'discount' ]) {
                        $child_html = '<span class="text-small lh1em  onsale">' . TravelHelper::format_money( $prices[ 'child' ] ) . '</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                        $html .= sprintf( __( 'Children: %s %s' , ST_TEXTDOMAIN ) , $child_html , $child_new_html );
                    } else {
                        $html .= sprintf( __( 'Children: %s' , ST_TEXTDOMAIN ) , $child_new_html );
                    }

                }

            } else {
                $prices = self::get_info_price( $post_id );

                if(isset( $prices[ 'price_old' ] ) and $prices[ 'price_old' ] and $prices[ 'discount' ]) {
                    $html .= '<span class="text-small lh1em  onsale">' . TravelHelper::format_money( $prices[ 'price_old' ] ) . '</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                    $html .= '<span class="text-lg lh1em  ' . $class . '">' . TravelHelper::format_money( $prices[ 'price' ] ) . '</span>';

                } else {
                    $html .= '<span class="text-lg lh1em  ' . $class . '">' . TravelHelper::format_money( $prices[ 'price' ] ) . '</span>';

                }
            }

            return apply_filters( 'st_get_tour_price_html' , $html );
        }

        static function get_price_person( $post_id = null )
        {
            /* @since 1.1.3 */
            if(!$post_id)
                $post_id = get_the_ID();
            $adult_price = get_post_meta( $post_id , 'adult_price' , true );
            $child_price = get_post_meta( $post_id , 'child_price' , true );

            $adult_price = apply_filters( 'st_apply_tax_amount' , $adult_price );
            $child_price = apply_filters( 'st_apply_tax_amount' , $child_price );

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

                $adult_price_new = $adult_price - ( $adult_price / 100 ) * $discount;
                $child_price_new = $child_price - ( $child_price / 100 ) * $discount;
                $data            = array(
                    'adult'     => $adult_price ,
                    'adult_new' => $adult_price_new ,
                    'child'     => $child_price ,
                    'child_new' => $child_price_new ,
                    'discount'  => $discount ,

                );
            } else {
                $data = array(
                    'adult_new' => $adult_price ,
                    'adult'     => $adult_price ,
                    'child'     => $child_price ,
                    'child_new' => $child_price ,
                    'discount'  => $discount ,
                );
            }

            return $data;
        }

        static function get_cart_item_total( $item_id , $item )
        {
            /* @since 1.1.3 */
            $count_sale = 0;
            $price_sale = $item[ 'price' ];
            if(!empty( $item[ 'data' ][ 'discount' ] )) {
                $count_sale = $item[ 'data' ][ 'discount' ];
                $price_sale = $item[ 'data' ][ 'price_sale' ] * $item[ 'number' ];
            }

            $type_price = $item[ 'data' ][ 'type_price' ];

            if($type_price == 'people_price') {
                $adult_num   = $item[ 'data' ][ 'adult_number' ];
                $child_num   = $item[ 'data' ][ 'child_number' ];
                $adult_price = $item[ 'data' ][ 'adult_price' ];
                $child_price = $item[ 'data' ][ 'child_price' ];

                if($get_array_discount_by_person_num = self::get_array_discount_by_person_num( $item_id )) {
                    if($array_adult = $get_array_discount_by_person_num[ 'adult' ]) {
                        if(is_array( $array_adult ) and !empty( $array_adult )) {
                            foreach( $array_adult as $key => $value ) {
                                if($adult_num >= (int)$key) {
                                    $adult_price2 = $adult_price * $value;
                                }
                            }
                            $adult_price -= $adult_price2;
                        }
                    };
                    if($array_child = $get_array_discount_by_person_num[ 'child' ]) {
                        if(is_array( $array_child ) and !empty( $array_child )) {
                            foreach( $array_child as $key => $value ) {
                                if($child_num >= (int)$key) {
                                    $child_price2 = $child_price * $value;
                                }
                            }
                            $child_price -= $child_price2;
                        }
                    };
                }

                $adult_price = round( $adult_price );
                $child_price = round( $child_price );
                $total_price = $adult_num * st_get_discount_value( $adult_price , $count_sale , false );
                $total_price += $child_num * st_get_discount_value( $child_price , $count_sale , false );

                return $total_price;
            } else {
                $price = $price_sale * $item[ 'number' ];
                return $price;
            }

        }

        static function get_array_discount_by_person_num( $item_id = false )
        {
            /* @since 1.1.3 */
            $return = array();

            $discount_by_adult = get_post_meta( $item_id , 'discount_by_adult' , true );
            $discount_by_child = get_post_meta( $item_id , 'discount_by_child' , true );

            if(!$discount_by_adult and !$discount_by_child) {
                return false;
            }
            if(is_array( $discount_by_adult ) and !empty( $discount_by_adult )) {
                foreach( $discount_by_adult as $row ) {
                    $key                       = (int)$row[ 'key' ];
                    $value                     = (int)$row[ 'value' ] / 100;
                    $return[ 'adult' ][ $key ] = $value;
                }
            }
            if(is_array( $discount_by_child ) and !empty( $discount_by_child )) {
                foreach( $discount_by_child as $row ) {
                    $key                       = (int)$row[ 'key' ];
                    $value                     = (int)$row[ 'value' ] / 100;
                    $return[ 'child' ][ $key ] = $value;
                }
            }

            return $return;
        }

        /* @since 1.1.3 */
        static function get_taxonomy_and_id_term_activity()
        {
            $list_taxonomy = st_list_taxonomy( 'st_activity' );
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

        /**
         *
         * @since 1.1.3
         * */
        function is_available()
        {

            return st_check_service_available('st_activity');

        }

    }

    $a = new STActivity();
    $a->init();
}
