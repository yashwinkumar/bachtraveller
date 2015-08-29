<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Class STTour
     *
     * Created by ShineTheme
     *
     */
    if(!class_exists('STTour'))
    {
        class STTour extends TravelerObject
        {
            protected $post_type="st_tours";
            protected $orderby;
            function __construct($tours_id=false)
            {
                $this->orderby=array(
                    'new'=>array(
                        'key'=>'new',
                        'name'=>__('New',ST_TEXTDOMAIN)
                    ),
                    'price_asc'=>array(
                        'key'=>'price_asc',
                        'name'=>__('Price (low to high)',ST_TEXTDOMAIN)
                    ),
                    'price_desc'=>array(
                        'key'=>'price_desc',
                        'name'=>__('Price (hight to low)',ST_TEXTDOMAIN)
                    ),
                    'name_a_z'=>array(
                        'key'=>'name_a_z',
                        'name'=>__('Tours Name (A-Z)',ST_TEXTDOMAIN)
                    ),
                    'name_z_a'=>array(
                        'key'=>'name_z_a',
                        'name'=>__('Tours Name (Z-A)',ST_TEXTDOMAIN)
                    )
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
               if(!$this->is_available()) return;
                parent::init();

                add_filter('st_tours_detail_layout',array($this,'custom_tour_layout'));

                // add to cart
                add_action('wp_loaded',array($this,'tours_add_to_cart'),20);

                //custom search cars template
                add_filter('template_include', array($this,'choose_search_template'));

                //Filter the search hotel
               // add_action('pre_get_posts',array($this,'change_search_tour_arg'));

                //add Widget Area
                add_action('widgets_init',array($this,'add_sidebar'));
                add_filter('st_search_preload_page',array($this,'_change_preload_search_title'));

                add_filter('st_tour_add_cart_validate',array($this,'_check_overdate_tour'),10,2);

                //add_filter('st_data_custom_price',array($this,'_st_data_custom_price'));


                // Woocommerce cart item information
                add_action('st_wc_cart_item_information_st_tours',array($this,'_show_wc_cart_item_information'));
                add_action('st_before_cart_item_st_tours',array($this,'_show_wc_cart_post_type_icon'));


                add_filter('st_add_to_cart_item_st_tours', array($this, '_deposit_calculator'), 10, 2);
            }
            /**
             *
             *
             * @since 1.1.1
             * */
            function _show_wc_cart_item_information($st_booking_data=array())
            {
                echo st()->load_template('tours/wc_cart_item_information',false,array('st_booking_data'=>$st_booking_data));
            }


            /**
             *
             *
             * @since 1.1.1
             * */
            function _show_wc_cart_post_type_icon()
            {
                echo '<span class="booking-item-wishlist-title"><i class="fa fa-flag-o"></i> '.__('tour',ST_TEXTDOMAIN).' <span></span></span>';
            }


            function _st_data_custom_price(){
                return array('title'=>'Price Custom Settings','post_type'=>'st_tours');
            }
            function _check_overdate_tour($pass,$data)
            {
                if($data['type_tour'] == "specific_date"){
                    $date_now = new DateTime( );
                    $date_now = $date_now->format('d-m-Y');
                    $date_now = strtotime($date_now);

                    $date_tour = new DateTime( $data['check_in'] );
                    $date_tour = $date_tour->format('d-m-Y');
                    $date_tour = strtotime($date_tour);

                    if($date_now > $date_tour){
                        STTemplate::set_message(__('expired to book Tour online',ST_TEXTDOMAIN),'warning');
                        return false;
                    }else{
                        return true;
                    }
                }
                return true;
            }
            /**
             *
             *
             * @update 1.1.1
             * */
            static function get_search_fields_name()
            {
                return array(
                    'address'=>array(
                        'value'=>'address',
                        'label'=>__('Address',ST_TEXTDOMAIN)
                    ),
                    'address-2'=>array(
                        'value'=>'address-2',
                        'label'=>__('Address (geobytes.com)',ST_TEXTDOMAIN)
                    ),
                    'people'=>array(
                        'value'=>'people',
                        'label'=>__('People',ST_TEXTDOMAIN)
                    ),
                    'check_in'=>array(
                        'value'=>'check_in',
                        'label'=>__('Departure date',ST_TEXTDOMAIN)
                    ),
                    'check_out'=>array(
                        'value'=>'check_out',
                        'label'=>__('Arrival Date',ST_TEXTDOMAIN)
                    ),
                    'taxonomy'=>array(
                        'value'=>'taxonomy',
                        'label'=>__('Taxonomy',ST_TEXTDOMAIN)
                    ),
                    'list_location'=>array(
                        'value'=>'list_location',
                        'label'=>__('Location List',ST_TEXTDOMAIN)
                    ),
                    'duration'=>array(
                        'value'=>'duration',
                        'label'=>__('Duration',ST_TEXTDOMAIN)
                    ),
                    'duration-dropdown'=>array(
                        'value'=>'duration-dropdown',
                        'label'=>__('Duration Dropdown',ST_TEXTDOMAIN)
                    ),
                    'item_name'=>array(
                        'value'=>'item_name',
                        'label'=>__('Tour Name',ST_TEXTDOMAIN)
                    )
                );
            }
            function _change_preload_search_title($return)
            {
                if( get_query_var('post_type')=='st_tours')
                {
                    $return=__(" Tours in %s",ST_TEXTDOMAIN);

                    if(STInput::get('location_id'))
                    {
                        $return=sprintf($return,get_the_title(STInput::get('location_id')));
                    }elseif(STInput::get('location_name')){
                        $return=sprintf($return,STInput::get('location_name'));
                    }elseif(STInput::get('address')){
                        $return=sprintf($return,STInput::get('address'));
                    }else {
                        $return=__(" Tours",ST_TEXTDOMAIN);
                    }

                    $return.='...';
                }





                return $return;
            }

            function add_sidebar()
            {
                register_sidebar( array(
                    'name' => __( 'Tours Search Sidebar 1', ST_TEXTDOMAIN ),
                    'id' => 'tours-sidebar',
                    'description' => __( 'Widgets in this area will be shown on Tours', ST_TEXTDOMAIN),
                    'before_title' => '<h4>',
                    'after_title' => '</h4>',
                    'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
                    'after_widget'  => '</div>',
                ) );


                register_sidebar( array(
                    'name' => __( 'Tour Single Sidebar', ST_TEXTDOMAIN ),
                    'id' => 'tour-single-sidebar',
                    'description' => __( 'Widgets in this area will be shown on all tour.', ST_TEXTDOMAIN),
                    'before_title' => '<h4>',
                    'after_title' => '</h4>',
                    'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
                    'after_widget'  => '</div>',
                ) );
            }

            /**
             *
             *
             * @since 1.1.3
             *
             * */
            function _alter_search_query($where)
            {
                global $wpdb;


                $where.=" OR {$wpdb->posts}.ID in
                            (SELECT post_id from $wpdb->postmeta where meta_key='type_tour'
                            and meta_value='daily_tour')";

                return $where;
            }
            /**
             *
             *
             * @update 1.1.3
             * */
            function change_search_tour_arg($query)
            {

                $post_type = get_query_var('post_type');

                if($query->is_search && $post_type == 'st_tours')
                {
                    //add_filter('posts_where', array($this, '_alter_search_query'));
                    $tax=STInput::get('taxonomy');

                    if(!empty($tax) and is_array($tax))
                    {
                        $tax_query=array();
                        foreach($tax as $key=>$value)
                        {
                            if($value)
                            {
                                $tax_query[]=array(
                                    'taxonomy'=>$key,
                                    'terms'=>explode(',',$value),
                                    'COMPARE'=>"IN"
                                );
                            }
                        }

                        $query->set('tax_query',$tax_query);
                    }

                    if($location_id=STInput::get('location_id'))
                    {

                        $ids_in=array();
                        $parents = get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'post_type' => 'location', 'post_parent' => $location_id ));

                        $ids_in[]=$location_id;

                        foreach( $parents as $child ){
                            $ids_in[]=$child->ID;
                        }

                        $meta_query[]=array(
                            'key'=>'id_location',
                            'value'=>$ids_in,
                            'compare'=>'IN'
                        );
                        $query->set('meta_query',$meta_query);
//                        $query->set('s','');
                    }else{
                        if(STInput::get('location_name')){
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
                        }elseif(STInput::request('address')){
                            $value = STInput::request('address');
                            $value = explode(",",$value);
                            if(!empty($value[0]) and !empty($value[2])){
                                $meta_query[]=array(
                                    array(
                                        'key'=>'address',
                                        'value'=>$value[0],
                                        'compare'=>'like',
                                    ),
                                    array(
                                        'key'=>'address',
                                        'value'=>$value[2],
                                        'compare'=>'like',
                                    ),
                                    "relation"=>'OR'
                                );
                            }else{
                                $meta_query[]=array(
                                    'key'=>'address',
                                    'value'=> STInput::request('address'),
                                    'compare'=>"like",
                                );
                            }
                        }
                    }
                    if($orderby=STInput::get('people'))
                    {
                        $meta_query[]=array(
                            'key'=>'max_people',
                            'value'=>STInput::get('people'),
                            'compare'=>'=',
                        );
                    }

                    if($orderby=STInput::get('duration'))
                    {
                        $meta_query[]=array(
                            'key'=>'duration_day',
                            'value'=>STInput::get('duration'),
                            'compare'=>'=',
                        );
                        /*$meta_query[]=array(
                            'key'=>'type_tour',
                            'value'=>'daily_tour',
                            'compare'=>'=',
                        );*/
                    }

                    $is_featured = st()->get_option('is_featured_search_tour','off');
                    if(!empty($is_featured) and $is_featured =='on'){
                        $query->set('meta_key','is_featured');
                        $query->set('orderby','meta_value');
                        $query->set('order','DESC');
                    }

                    if($orderby=STInput::get('orderby'))
                    {
                        switch($orderby){
                            case "price_asc":
                                $query->set('meta_key','sale_price');
                                $query->set('orderby','meta_value_num');
                                $query->set('order','ASC');
                                break;
                            case "price_desc":
                                $query->set('meta_key','sale_price');
                                $query->set('orderby','meta_value_num');
                                $query->set('order','DESC');
                                break;
                            case "name_a_z":
                                $query->set('orderby','name');
                                $query->set('order','asc');
                                break;
                            case "name_z_a":
                                $query->set('orderby','name');
                                $query->set('order','desc');
                                break;
                        }
                    }
                    if($price=STInput::get('price_range')){
                        $priceobj=explode(';',$price);
                        $meta_query[]=array(
                            'key'=>'price',
                            'value'=>$priceobj[0],
                            'compare'=>'>=',
                            'type'=>"NUMERIC"
                        );
                        if(isset($priceobj[1])){
                            $meta_query[]=array(
                                'key'=>'price',
                                'value'=>$priceobj[1],
                                'compare'=>'<=',
                                'type'=>"NUMERIC"
                            );
                        }
                        $meta_query['relation']='and';
                    }

                    if($price=STInput::get('start') or $price=STInput::get('end') ){

                        $meta_query['relation']='AND';

                        if(STInput::get('start') and  STInput::get('end')){
                            $meta_query[]['relation']='AND';
                            $meta_query[]=array(
                                array(
                                    'key'=>'check_in',
                                    'value'=>date('Y-m-d',strtotime(TravelHelper::convertDateFormat(STInput::get('start')))),
                                    'compare'=>'>=',
                                    'type'=>'DATE'
                                ),
                                array(
                                    'key'=>'check_in',
                                    'value'=>date('Y-m-d',strtotime(TravelHelper::convertDateFormat(STInput::get('end')))),
                                    'compare'=>'<=',
                                    'type'=>'DATE'
                                ),
                            );
                        }else{
                            if(STInput::get('start'))
                            {
                                $meta_query[]=array(
                                    'key'=>'check_in',
                                    'value'=>date('Y-m-d',strtotime(TravelHelper::convertDateFormat(STInput::get('start')))),
                                    'compare'=>'>=',
                                    'type'=>'DATE'
                                );
                            }
                            if(STInput::get('end'))
                            {
                                $meta_query[]=array(
                                    'key'=>'check_in',
                                    'value'=>date('Y-m-d',strtotime(TravelHelper::convertDateFormat(STInput::get('end')))),
                                    'compare'=>'<=',
                                    'type'=>'DATE'
                                );
                            }
                        }
                    }

                    if($star=STInput::get('star_rate')){
                        $meta_query[]=array(
                            'key'=>'rate_review',
                            'value'=>explode(',',$star),
                            'compare'=>"IN"
                        );
                    }

                    if(!empty($meta_query)){
                        $query->set('meta_query',$meta_query);
                    }
                }else{
                    remove_filter('posts_where', array($this, '_alter_search_query'));
                }
            }
            function choose_search_template($template)
            {
                global $wp_query;
                $post_type = get_query_var('post_type');
                if( $wp_query->is_search && $post_type == 'st_tours' )
                {
                    return locate_template('search-tour.php');  //  redirect to archive-search.php
                }
                return $template;
            }

            function get_result_string()
            {
                global $wp_query,$st_search_query;
                if($st_search_query){
                    $query=$st_search_query;
                }else $query=$wp_query;

                $result_string='';
                if($query->found_posts > 1){
                    $result_string.=esc_html( $query->found_posts).__(' tours ',ST_TEXTDOMAIN);
                }else{
                    $result_string.=esc_html( $query->found_posts).__(' tour ',ST_TEXTDOMAIN);
                }

                $location_id=STInput::get('location_id');
                if($location_id and $location=get_post($location_id))
                {
                    $result_string.=sprintf(__(' in %s',ST_TEXTDOMAIN),get_the_title($location_id));                    
                }elseif(STInput::request('location_name')){
                    $result_string.=sprintf(__(' in %s',ST_TEXTDOMAIN), STInput::request('location_name'));
                }elseif(STInput::request('address')){
                    $result_string.=sprintf(__(' in %s',ST_TEXTDOMAIN), STInput::request('address'));
                }

                $start=TravelHelper::convertDateFormat(STInput::get('start'));
                $end=TravelHelper::convertDateFormat(STInput::get('end'));

                $start=strtotime($start);

                $end=strtotime($end);

                if($start and $end)
                {
                    $result_string.=__(' on ',ST_TEXTDOMAIN).date_i18n('M d',$start).' - '.date_i18n('M d',$end);
                }

                if($adult_num=STInput::get('adult_num')){
                    if($adult_num>1){
                        $result_string.=sprintf(__(' for %s adults',ST_TEXTDOMAIN),$adult_num);
                    }else{

                        $result_string.=sprintf(__(' for %s adult',ST_TEXTDOMAIN),$adult_num);
                    }

                }

                return $result_string;

            }
            static function get_count_book($post_id=null){
                if(!$post_id) $post_id=get_the_ID();
                //  $post_type = get_post_type($id_post);
                $query = array(
                    'post_type'=>'st_order',
                    'post_per_page'=>'-1',
                    'meta_query'=>array(
                        array(
                            'key'=>'item_id',
                            'value'=>$post_id,
                            'compare'=>"="
                        )
                    ),
                );

                $query = new WP_Query( $query );
                wp_reset_postdata();
                return $query->post_count;
            }
            function tours_add_to_cart()
            {
                if(STInput::request('action')=='tours_add_to_cart')
                {

                    $rs=self::do_add_to_cart();

                    if($rs){

                        $link=STCart::get_cart_link();
                        wp_safe_redirect($link);
                        die;
                    }

                }

            }
            function do_add_to_cart()
            {
                $pass_validate=true;
                $item_id= STInput::request('item_id');
                $number = STInput::request('number');;
                $discount = STInput::request('discount');
                $price = STInput::request('price');
                if(!empty($discount)){
                    $price_sale =  $price - $price * ( $discount / 100 ) ;
                    $data = array(
                        'discount'=> $discount,
                        'price_sale'=>$price_sale,
                    );
                }
                $data['check_in'] = STInput::request('check_in');
                $data['check_out'] = STInput::request('check_out');

                $data['type_tour'] = STInput::request('type_tour');
                $data['type_price'] = STInput::request('type_price');

                if($data['type_price']=='people_price')
                {
                    $prices=self::get_price_person($item_id);
                    $data['adult_price']=$prices['adult'];
                    $data['child_price']=$prices['child'];
                    $data['discount']=$prices['discount'];
                    $data['adult_number']=STInput::request('adult_number',1);
                    $data['child_number']=STInput::request('children_number',0);
                }
                $data['duration']=STInput::request('duration');

                $type_tour = STInput::request('type_tour');

                $today = strtotime(date('m/d/Y', time()));

                if($type_tour == 'daily_tour'){

                    $check_in = strtotime(TravelHelper::convertDateFormat(STInput::request('check_in')));

                }else{

                    $check_in = strtotime(get_post_meta($item_id, 'check_in', true ));

                }

                $booking_period = intval(get_post_meta($item_id, 'tours_booking_period', true ));

                $period  = STDate::date_diff($today,$check_in);

                $expired = $check_in - $today;
                
                if($type_tour == 'daily_tour'){

                    if($booking_period && $period < $booking_period){

                        STTemplate::set_message(sprintf(__('Booking is only accepted %d day(s) before today.',ST_TEXTDOMAIN), $booking_period),'danger');
                        return;
                    }
                }else{

                    if($expired < 0){

                        STTemplate::set_message(__('This tour has expired',ST_TEXTDOMAIN),'danger');

                        $pass_validate = false;

                         return;
                    }

                }
               

                if($pass_validate)
                    $pass_validate=apply_filters('st_tour_add_cart_validate',$pass_validate,$data);


                if($pass_validate)
                    STCart::add_cart($item_id,$number,$price,$data);


                return $pass_validate;
            }
            function get_cart_item_html($item_id=false)
            {
                return st()->load_template('tours/cart_item_html',null,array('item_id'=>$item_id));
            }

            function custom_tour_layout($old_layout_id)
            {
                if(is_singular('st_tours'))
                {
                    $meta=get_post_meta(get_the_ID(),'st_custom_layout',true);

                    if($meta)
                    {
                        return $meta;
                    }
                }
                return $old_layout_id;
            }

            function get_search_fields()
            {
                $fields=st()->get_option('activity_tour_search_fields');
                return $fields;
            }

            static function get_info_price($post_id=null){

                if(!$post_id) $post_id=get_the_ID();
                $price=get_post_meta($post_id,'price',true);
                $new_price=0;

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
                    if($discount>100) $discount=100;

                    $new_price=$price-($price/100)*$discount;
                    $data = array(
                        'price'=>apply_filters('st_apply_tax_amount',$new_price),
                        'price_old'=>apply_filters('st_apply_tax_amount',$price),
                        'discount'=>$discount,

                    );
                }else{
                    $new_price=$price;
                    $data = array(
                        'price'=>apply_filters('st_apply_tax_amount',$new_price),
                        'discount'=>$discount,
                    );
                }

                return $data;
            }

            static function get_price_person($post_id=null)
            {
                if(!$post_id) $post_id=get_the_ID();
                $adult_price=get_post_meta($post_id,'adult_price',true);
                $child_price=get_post_meta($post_id,'child_price',true);
				
				$adult_price = apply_filters('st_apply_tax_amount',$adult_price);
				$child_price = apply_filters('st_apply_tax_amount',$child_price);
				
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
                    if($discount>100) $discount=100;

                    $adult_price_new=$adult_price-($adult_price/100)*$discount;
                    $child_price_new=$child_price-($child_price/100)*$discount;
                    $data = array(
                        'adult'=>$adult_price,
                        'adult_new'=>$adult_price_new,
                        'child'=>$child_price,
                        'child_new'=>$child_price_new,
                        'discount'=>$discount,

                    );
                }else{
                    $data = array(
                        'adult_new'=>$adult_price,
                        'adult'    =>$adult_price,
                        'child'     =>$child_price,
                        'child_new'=>$child_price,
                        'discount'=>$discount,
                    );
                }

                return $data;
            }

            static function get_price_html($post_id=false,$get=false,$st_mid='',$class='')
            {
                if(!$post_id) $post_id=get_the_ID();

                $html='';


                $type_price = get_post_meta($post_id,'type_price',true);
                if($type_price=='people_price')
                {
                    $prices=self::get_price_person($post_id);

                    $adult_html='';

                    $adult_new_html='<span class="text-lg lh1em  ">'.TravelHelper::format_money($prices['adult_new']).'</span>';

                    // Check on sale
                    if(isset($prices['adult']) and $prices['adult'] and $prices['discount'])
                    {
                        $adult_html='<span class="text-small lh1em  onsale">'.TravelHelper::format_money($prices['adult']).'</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                        $html.=sprintf(__('Adult: %s %s',ST_TEXTDOMAIN),$adult_html,$adult_new_html);
                    }else{
                        $html.=sprintf(__('Adult: %s',ST_TEXTDOMAIN),$adult_new_html);
                    }

                    $child_new_html='<span class="text-lg lh1em  ">'.TravelHelper::format_money($prices['child_new']).'</span>';


                    // Price for child
                    if($prices['child_new'])
                    {
                        $html.=' '.$st_mid.' ';

                        // Check on sale
                        if(isset($prices['child']) and $prices['child'] and $prices['discount'])
                        {
                            $child_html='<span class="text-small lh1em  onsale">'.TravelHelper::format_money($prices['child']).'</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                            $html.=sprintf(__('Children: %s %s',ST_TEXTDOMAIN),$child_html,$child_new_html);
                        }else{
                            $html.=sprintf(__('Children: %s',ST_TEXTDOMAIN),$child_new_html);
                        }

                    }

                }
                else
                {
                    $prices=self::get_info_price($post_id);

                    if(isset($prices['price_old']) and $prices['price_old'] and $prices['discount'])
                    {
                        $html.='<span class="text-small lh1em  onsale">'.TravelHelper::format_money($prices['price_old']).'</span>&nbsp;&nbsp;<i class="fa fa-long-arrow-right"></i>';

                        $html.='<span class="text-lg lh1em  '.$class.'">'.TravelHelper::format_money($prices['price']).'</span>';

                    }else {
                        $html.='<span class="text-lg lh1em  '.$class.'">'.TravelHelper::format_money($prices['price']).'</span>';

                    }
                }

                return apply_filters('st_get_tour_price_html',$html);
            }
            static function get_array_discount_by_person_num($item_id = false){           
                /* @since 1.1.1 */
                $return = array();

                $discount_by_adult = get_post_meta($item_id, 'discount_by_adult' , true) ; 
                $discount_by_child = get_post_meta($item_id, 'discount_by_child' , true) ; 

                if (!$discount_by_adult and !$discount_by_child) { return false; }
                if (is_array($discount_by_adult) and !empty($discount_by_adult)){
                    foreach ($discount_by_adult as $row) {
                        $key = (int)$row['key']  ; 
                        $value = (int)$row['value']/100;
                        $return['adult'][$key]= $value;
                    }
                }
                if (is_array($discount_by_child) and !empty($discount_by_child)){
                    foreach ($discount_by_child as $row) {
                        $key = (int)$row['key']  ; 
                        $value = (int)$row['value']/100;                        
                        $return['child'][$key]= $value;
                    }        
                }
                
                return $return ; 
            }
            static function get_cart_item_total($item_id,$item)
            {
                $count_sale=0;
                $price_sale = $item['price'];
                if(!empty($item['data']['discount'])){
                    $count_sale = $item['data']['discount'];
                    $price_sale = $item['data']['price_sale'] * $item['number'];
                }

                $type_price=$item['data']['type_price'];

                if($type_price=='people_price')
                {
                    $adult_num=$item['data']['adult_number']; 
                    $child_num=$item['data']['child_number'];
                    $adult_price=$item['data']['adult_price'];
                    $child_price=$item['data']['child_price'];

                    if ($get_array_discount_by_person_num = self::get_array_discount_by_person_num($item_id)){
                        if ($array_adult = $get_array_discount_by_person_num['adult']){
                            if (is_array($array_adult) and  !empty($array_adult)){
                                foreach ($array_adult as $key => $value) {
                                    if ($adult_num>=(int)$key ){
                                        $adult_price2 = $adult_price*$value;
                                    }
                                }
                                $adult_price -=$adult_price2;                       
                            }
                        };
                        if ($array_child = $get_array_discount_by_person_num['child']){
                            if (is_array($array_child) and  !empty($array_child)){
                                foreach ($array_child as $key => $value) {
                                    if ($child_num>=(int)$key ){
                                        $child_price2 = $child_price*$value;
                                    }
                                }
                                $child_price -=$child_price2;                        
                            }
                        };
                    }

                    $adult_price = round($adult_price);
                    $child_price = round($child_price);
                    $total_price=$adult_num*st_get_discount_value($adult_price,$count_sale,false);
                    $total_price+=$child_num*st_get_discount_value($child_price,$count_sale,false);

                    return $total_price;
                }else
                {
                    $price = $price_sale * $item['number'];
                    return $price;
                }

            }


            function get_near_by($post_id=false,$range=20, $limit = 5)
            {
                $this->post_type='st_tours';
                $limit = st()->get_option('tours_similar_tour',5);
                return parent::get_near_by($post_id,$range, $limit);
            }

            static function get_owner_email($item_id)
            {
                return get_post_meta($item_id,'contact_email',true);
            }

            public static function tour_external_booking_submit(){
                /*
                 * since 1.1.1 
                 * filter hook tour_external_booking_submit
                */
                $post_id = get_the_ID();
                if (STInput::request('post_id')) {$post_id = STInput::request('post_id') ; }

                $tour_external_booking = get_post_meta($post_id, 'st_tour_external_booking' , "off");
                $tour_external_booking_link = get_post_meta($post_id , 'st_tour_external_booking_link' ,true) ; 
                if ($tour_external_booking =="on" and $tour_external_booking_link!==""){
                    if (get_post_meta($post_id , 'st_tour_external_booking_link' , true)){
                        ob_start();
                        ?>
                            <a class='btn btn-primary' href='<?php echo get_post_meta($post_id , 'st_tour_external_booking_link' , true) ?>'> <?php st_the_language('book_now')  ?></a>
                        <?php 
                    $return  =  ob_get_clean();
                    }
                }
                    else 
                {
                    $return  =  TravelerObject::get_book_btn();
                }
                return apply_filters('tour_external_booking_submit' , $return ) ; 
            }

            /* @since 1.1.3 */
            static function get_taxonomy_and_id_term_tour()
            {
                $list_taxonomy = st_list_taxonomy( 'st_tours' );
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
        }
        $a=new STTour();
        $a->init();
    }
