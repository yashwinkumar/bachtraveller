<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdminRoom
 *
 * Created by ShineTheme
 *
 */
if(!class_exists('STAdminRoom'))
{

    class STAdminRoom extends STAdmin
    {
        /**
         *
         *
         * @update 1.1.3
         * */
        function __construct()
        {

            if (!st_check_service_available('st_hotel')) return;

            add_filter('st_hotel_room_layout', array($this, 'custom_hotel_room_layout'));

            add_action('init',array($this,'init_metabox'));

            //alter where for search room
            add_filter( 'posts_where' , array(__CLASS__,'_alter_search_query') );


            //Hotel Hook
            /*
             * todo Re-cal hotel min price
             * */
            add_action( 'update_post_meta', array($this,'hotel_update_min_price') ,10,4);
            add_action( 'updated_post_meta', array($this,'meta_updated_update_min_price') ,10,4);
            add_action('added_post_meta',array($this,'hotel_update_min_price') ,10,4);
            add_action('save_post', array($this,'_update_avg_price'),50);
            parent::__construct();
        }

        /**
        *@since 1.1.3
        **/
        public function custom_hotel_room_layout($old_layout_id=false){

            if(is_singular('hotel_room')){

                $meta=get_post_meta(get_the_ID(),'st_custom_layout',true);
                if($meta)
                {
                    return $meta;
                }
            }
            return $old_layout_id;
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
                'title'       => __( 'Room Setting', ST_TEXTDOMAIN),
                'desc'        => '',
                'pages'       => array( 'hotel_room' ),
                'context'     => 'normal',
                'priority'    => 'high',
                'fields'      => array(


                    array(
                        'label'       => __( 'General', ST_TEXTDOMAIN),
                        'id'          => 'room_reneral_tab',
                        'type'        => 'tab'
                    ),

                    array(
                        'label'       => __( 'Hotel', ST_TEXTDOMAIN),
                        'id'          => 'room_parent',
                        'type'        => 'post_select_ajax',
                        'desc'        => __( 'Choose the hotel that the room belong', ST_TEXTDOMAIN),
                        'post_type'   =>'st_hotel',
                        'placeholder' =>__('Search for a Hotel',ST_TEXTDOMAIN)
                    ),

                    array(
                        'label'       => __( 'Number of Room', ST_TEXTDOMAIN),
                        'id'          => 'number_room',
                        'type'        => 'text',
                        'desc'        => __( 'Number of rooms available for book', ST_TEXTDOMAIN),
                        'std'         =>1
                    ),

                    /**
                    ** @since 1.1.3
                    **/

                    array(
                        'label' => __('Gallery',ST_TEXTDOMAIN),
                        'id' => 'gallery',
                        'type' => 'gallery'
                    ),
                    array(
                        'label'     => __('Hotel Room Layout', ST_TEXTDOMAIN),
                        'id'        => 'st_custom_layout',
                        'post_type' => 'st_layouts',
                        'desc'      => __('Hotel Room Layout', ST_TEXTDOMAIN),
                        'type'      => 'select',
                        'choices'   => st_get_layout('hotel_room')
                    ),
                    array(
                        'label'       => __( 'Room Price', ST_TEXTDOMAIN),
                        'id'          => 'room_price_tab',
                        'type'        => 'tab'
                    ),

                    array(
                        'label'       => sprintf( __( 'Price (%s)', ST_TEXTDOMAIN),TravelHelper::get_default_currency('symbol')),
                        'id'          => 'price',
                        'type'        => 'text',
                        'desc'        => __( 'Per night', ST_TEXTDOMAIN),
                    ),
                    array(
                        'label'       =>  __( 'Discount Rate', ST_TEXTDOMAIN),
                        'id'          => 'discount_rate',
                        'type'        => 'text',
                        'desc'        => __( 'Discount by %', ST_TEXTDOMAIN),
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
                        'label'    => __('Additional paid options', ST_TEXTDOMAIN),
                        'desc'     => __('Additional paid options', ST_TEXTDOMAIN),
                        'id'       => 'paid_options',
                        'type'     => 'list-item',
                        'settings' => array(
                            array(
                                'id'    => 'price',
                                'type'  => 'text',
                                'std'   => 0,
                                'label' => __('Price', ST_TEXTDOMAIN)
                            )
                        )

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
                        'label'       => __( 'Room Facility', ST_TEXTDOMAIN),
                        'id'          => 'room_detail_tab',
                        'type'        => 'tab'
                    ),

                    array(
                        'label'       => __( 'Adults Number', ST_TEXTDOMAIN),
                        'id'          => 'adult_number',
                        'type'        => 'text',
                        'desc'        => __( 'Number of Adults in room', ST_TEXTDOMAIN),
                        'std'         =>1
                    ),
                    array(
                        'label'       => __( 'Children Number', ST_TEXTDOMAIN),
                        'id'          => 'children_number',
                        'type'        => 'text',
                        'desc'        => __( 'Number of Children in room', ST_TEXTDOMAIN),
                        'std'         =>0
                    ),
                    array(
                        'label'       => __( 'Beds Number', ST_TEXTDOMAIN),
                        'id'          => 'bed_number',
                        'type'        => 'text',
                        'desc'        => __( 'Number of Beds in room', ST_TEXTDOMAIN),
                        'std'         =>0
                    ),
                    array(
                        'label'       => __( 'Room footage (square feet)', ST_TEXTDOMAIN),
                        'desc'       => __( 'Room footage (square feet)', ST_TEXTDOMAIN),
                        'id'          => 'room_footage',
                        'type'        => 'text',
                    ),
                    array(
                        'label' => __('Room external booking',ST_TEXTDOMAIN),
                        'id' => 'st_room_external_booking',
                        'type'        => 'on-off',
                        'std' => "off",
                    ),
                    array(
                        'label' => __('Room external booking',ST_TEXTDOMAIN),
                        'id' => 'st_room_external_booking_link',
                        'type'        => 'text',
                        'std' => "",
                        'condition'   =>'st_room_external_booking:is(on)',
                        'desc'=>"<em>".__('Notice: Must be http://...',ST_TEXTDOMAIN)."</em>",
                    ),
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

            parent::register_metabox($this->metabox);
        }


        /**
         *
         *
         * @since 1.0.9
         *
         */
        static function _update_avg_price($post_id=false)
        {
            if(!$post_id)
            {
                $post_id=get_the_ID();
            }
            $post_type=get_post_type($post_id);
            if($post_type=='hotel_room')
            {
                $hotel_id = get_post_meta($post_id,'room_parent',true);
                if(!empty($hotel_id)) {
                    $is_auto_caculate = get_post_meta($hotel_id,'is_auto_caculate',true);
                    if($is_auto_caculate != 'off' ){
                        $query=array(
                            'post_type' =>'hotel_room',
                            'posts_per_page'=>100,
                            'meta_key'=>'room_parent',
                            'meta_value'=>$hotel_id
                        );
                        $traver=new WP_Query($query);
                        $price=0;
                        while($traver->have_posts()){
                            $traver->the_post();
                            $price +=get_post_meta(get_the_ID(),'price',true);
                        }
                        wp_reset_query();
                        if($traver->post_count){
                            $avg_price = $price / $traver->post_count;
                        }
                        update_post_meta($hotel_id,'price_avg',$avg_price);
                    }
                }
            }
        }
        static function _alter_search_query($where)
        {
            global $wp_query;

            if(!is_admin()) return $where;

            if($wp_query->get('post_type') !='hotel_room' ) return $where;

            global $wpdb;

            if($wp_query->get('s')){

                $_GET['s']=sanitize_title_for_query($_GET['s']);
                $add_where=" OR $wpdb->posts.ID IN (SELECT post_id FROM
                     $wpdb->postmeta
                    WHERE $wpdb->postmeta.meta_key ='room_parent'
                    AND $wpdb->postmeta.meta_value IN (SELECT $wpdb->posts.ID
                        FROM $wpdb->posts WHERE  $wpdb->posts.post_title LIKE '%{$_GET['s']}%'
                    )

             )  ";

                $where.=$add_where;


            }

            return $where;
        }

        function hotel_update_min_price($meta_id, $object_id, $meta_key, $meta_value)
        {

            $post_type=get_post_type($object_id);
            if ( wp_is_post_revision( $object_id ) )
                return;
            if($post_type=='hotel_room')
            {
                //Update old room and new room
                if( $meta_key=='room_parent')
                {

                    $old=get_post_meta($object_id,$meta_key,true);


                    if($old!=$meta_value)
                    {
                        $this->_do_update_hotel_min_price($old,false,$object_id);
                        $this->_do_update_hotel_min_price($meta_value);
                    }else{

                        $this->_do_update_hotel_min_price($meta_value);
                    }
                }


            }

        }
        function meta_updated_update_min_price($meta_id, $object_id, $meta_key, $meta_value)
        {
            if($meta_key=='price')
            {
                $hotel_id=get_post_meta($object_id,'room_parent',true);
                $this->_do_update_hotel_min_price($hotel_id);

            }
        }

        function _do_update_hotel_min_price($hotel_id,$current_meta_price=false,$room_id=false){
            if(!$hotel_id) return;
            $query=array(
                'post_type' =>'hotel_room',
                'posts_per_page'=>100,
                'meta_key'=>'room_parent',
                'meta_value'=>$hotel_id
            );

            if($room_id){
                $query['posts_not_in']=array($room_id);
            }


            $q=new WP_Query($query);

            $min_price=0;
            $i=1;
            while($q->have_posts()){
                $q->the_post();
                $price=get_post_meta(get_the_ID(),'price',true);
                if($i==1){
                    $min_price=$price;
                }else{
                    if($price<$min_price){
                        $min_price=$price;
                    }
                }


                $i++;
            }

            wp_reset_query();

            if($current_meta_price!==FALSE){
                if($current_meta_price<$min_price){
                    $min_price=$current_meta_price;
                }
            }

            update_post_meta($hotel_id,'min_price',$min_price);

        }
    }

    new STAdminRoom();
}
