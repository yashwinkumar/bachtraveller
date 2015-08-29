<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class TravelerObject
 *
 * Created by ShineTheme
 * @update 1.1.1
 *
 */
class TravelerObject
{
    public $min_price;
	
    protected $post_type='st_hotel';

    protected $metabox=array();

    protected $orderby=array();

	
    function init()
    {   
        //Add Stats display for posted review
		add_action( 'admin_init', array($this,'do_init_metabox') );
        add_action('st_review_stats_'.$this->post_type.'_content',array($this,'display_posted_review_stats'));
    }

    function is_available()
    {
        return st_check_service_available($this->post_type);
    }

    /**
     *
     *
     * @update 1.1.3
     * */
	function  _class_init()
	{
	

        add_action('save_post', array($this,'update_avg_rate'));

        add_filter('post_class',array($this,'change_post_class'));

        add_filter('pre_get_posts', array($this,'_admin_posts_for_current_author'));

        add_action('init',array($this,'_top_ajax_search'));

        add_action('st_single_breadcrumb', array($this, 'add_breadcrumb'));
		
	}

    /**
     *
     *
     * @since 1.1.3
     * */
    function add_breadcrumb($sep)
    {
        $bc_show_location_url=st()->get_option('bc_show_location_url','on');
        $location_id = get_post_meta(get_the_ID(), 'id_location', TRUE);

        if (!$location_id) {
            $location_id = get_post_meta(get_the_ID(), 'location_id', TRUE);
        }

        $array = array();
        $parents = get_post_ancestors($location_id);
        if (!empty($parents) and is_array($parents)) {
            foreach ($parents as $key => $value) {
                $link = get_home_url('/');
                if($bc_show_location_url=='on'){

                    $post_type = get_post_type();
                    $page_search = st_get_page_search_result($post_type);
                    if(!empty($page_search)){
                        $link = esc_url(add_query_arg(array('page_id'   => $page_search,
                                                            'location_id' => $value, 'location_name' => get_the_title($value)), $link));
                    }else{
                        $link = esc_url(add_query_arg(array('post_type'   => get_post_type(),'s'=>'',
                                                            'location_id' => $value, 'location_name' => get_the_title($value)), $link));
                    }

                }else
                {
                    $link=get_permalink($value);
                }
                echo '<li><a href="' . $link . '">' . get_the_title($value) . '</a></li>';
            }
        }

        //var_dump($location_id);
        if ($location_id) {

            $link = get_home_url('/');

            if($bc_show_location_url=='on'){
                $post_type = get_post_type();
                $page_search = st_get_page_search_result($post_type);
                if(!empty($page_search)){
                    $link = esc_url(add_query_arg(array('page_id'   => $page_search,
                                                        'location_id' => $location_id, 'location_name' => get_the_title($location_id)), $link));
                }else{
                    $link = esc_url(add_query_arg(array('post_type'   => get_post_type(),'s'=>'',
                                                        'location_id' => $location_id, 'location_name' => get_the_title($location_id)), $link));
                }

            }else
            {
                $link=get_permalink($value);
            }
            echo '<li><a href="' . $link . '">' . get_the_title($location_id) . '</a></li>';
        }


    }



    function _admin_posts_for_current_author($query)
    {
        if($query->is_admin) {
            $post_type=$query->get('post_type');

            if(!current_user_can('manage_options') and (!is_string($post_type) or $post_type!='location'))
            {
                global $user_ID;
                $query->set('author',  $user_ID);
            }
        }
        return $query;
    }

    function _top_ajax_search(){

        if(STInput::request('action')!='st_top_ajax_search')  return;

        //Small security
        check_ajax_referer( 'st_search_security', 'security' );
        //$search_header_onof = st()->get_option('search_header_onoff', 'on');
        $search_header_orderby = st()->get_option('search_header_orderby', 'none');
        $search_header_list = st()->get_option('search_header_list', 'post');
        $search_header_order = st()->get_option('search_header_order', 'ASC');
        $s=STInput::get('s');
        $arg=array(
            //'post_type'=>array('post','st_hotel','st_rental','location','st_tours','st_cars','st_activity'),
            'post_type' => $search_header_list,
            'posts_per_page'=>10,
            's'=>$s,
            'suppress_filters'=>false,
            'order_by' => $search_header_orderby,
            'order' => $search_header_order
        );

        global $sitepress;

        if(class_exists('SitePress') and STInput::get('lang'))
        {
            $sitepress->switch_lang(STInput::get('lang'));
        }

        $query=new WP_Query();
        $query->is_admin=false;
        $query->query($arg);
        $r=array();

        while($query->have_posts()){
            $query->the_post();
            $post_type=get_post_type(get_the_ID());
            $obj=get_post_type_object($post_type);

            $item=array(
                'title'=> get_the_title(),
                'id'=>get_the_ID(),
                'type'=>$obj->labels->singular_name,
                'url'=>get_permalink(),
                'obj'=>$obj
            );

            if($post_type=='location'){
                $item['url']=home_url(esc_url_raw('?s=&post_type=st_hotel&location_id='.get_the_ID()));
            }

            $r['data'][]=$item;
        }

        wp_reset_query();
        echo json_encode($r);

        die();
    }

    function change_post_class($class)
    {
        return $class;
    }

    function update_avg_rate($post_id){
        $avg=STReview::get_avg_rate($post_id);
        update_post_meta($post_id,'rate_review',$avg);
    }





    /**
     *
     * $range in kilometer
     *
     *
     * */
    function get_near_by($post_id=false,$range=20,$limit=5)
    {
        if(!$post_id) $post_id=get_the_ID();


        //if ( false !== ( $value = get_transient( 'st_items_nearby_'.$post_id ) ) )
          //  return $value;

        $map_lat=(float)get_post_meta($post_id,'map_lat',true);
        $map_lng=(float)get_post_meta($post_id,'map_lng',true);
        $post_type=get_post_type($post_id);

        $location_key='location_id';
        if ($post_type == 'st_rental' ){
            $location_key = 'location_id' ;
        }
        $location_key=apply_filters('st_'.$post_type.'_location_id_metakey',$location_key);

        $location_id=get_post_meta($post_id,$location_key,true);

        //Search by Kilometer :6371
        //Miles: 3959
        global $wpdb;
            $querystr = "
            SELECT $wpdb->posts.*,( 6371 * acos( cos( radians({$map_lat}) ) * cos( radians( mt1.meta_value ) ) *
cos( radians( mt2.meta_value ) - radians({$map_lng}) ) + sin( radians({$map_lat}) ) *
sin( radians( mt1.meta_value ) ) ) ) AS distance
            FROM $wpdb->posts, $wpdb->postmeta as mt1,$wpdb->postmeta as mt2
            WHERE $wpdb->posts.ID = mt1.post_id
            and $wpdb->posts.ID=mt2.post_id
            AND mt1.meta_key = 'map_lat'
            and mt2.meta_key = 'map_lng'
            and $wpdb->posts.ID !=$post_id
            AND $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = '{$this->post_type}'
            AND $wpdb->posts.post_date < NOW()
            GROUP BY $wpdb->posts.ID
            ORDER BY distance ASC
            LIMIT 0,{$limit}
         ";


          $pageposts = $wpdb->get_results($querystr, OBJECT);

          set_transient('st_items_nearby_'.$post_id,$pageposts,5*HOUR_IN_SECONDS);
          return $pageposts;

    }
    function get_review_stats()
    {
        return array();
    }

    function display_posted_review_stats($comment_id)
    {

        if(get_post_type()==$this->post_type) {
            $data=$this->get_review_stats();

            $output[]='<ul class="list booking-item-raiting-summary-list mt20">';

            if(!empty($data) and is_array($data))
            {
                foreach($data as $value)
                {
                    $key=$value['title'];

                    $stat_value=get_comment_meta($comment_id,'st_stat_'.sanitize_title($value['title']),true);

                    $output[]='
                    <li>
                        <div class="booking-item-raiting-list-title">'.$key.'</div>
                        <ul class="icon-group booking-item-rating-stars">';
                    for($i=1;$i<=5;$i++)
                    {
                        $class='';
                        if($i>$stat_value) $class='text-gray';
                        $output[]='<li><i class="fa fa-smile-o '.$class.'"></i>';
                    }

                    $output[]='
                        </ul>
                    </li>';
                }
            }

            $output[]='</ul>';


            echo implode("\n",$output);
        }
    }
    function getOrderby()
    {
        $this->orderby=array(
            'price_asc'=>array(
                'key'=>'price_asc',
                'name'=>__('Price (low to high)',ST_TEXTDOMAIN)
            ),
            'price_desc'=>array(
                'key'=>'price_desc',
                'name'=>__('Price (hight to low)',ST_TEXTDOMAIN)
            ),
            'avg_rate'=>array(
                'key'=>'avg_rate',
                'name'=>__('Review',ST_TEXTDOMAIN)
            )
        );

        return $this->orderby;
    }


    public  function do_init_metabox()
    {
        $custom_metabox=$this->metabox;
        /**
         * Register our meta boxes using the
         * ot_register_meta_box() function.
         */
        if ( function_exists( 'ot_register_meta_box' ) )
        {
            if(!empty($custom_metabox))
            {
                foreach ($custom_metabox as $value)
                {
                    ot_register_meta_box( $value );
                }
            }
        }
    }

    //Helper class
    static function get_last_booking_string($post_id=false)
    {
       if(!$post_id and !is_singular()) return false;
        global $wpdb;

        $post_id=get_the_ID();
        $where='';
        $join='';

        $post_type=get_post_type($post_id);

        switch($post_type) {


            default:
                $where.="and meta_value in (
                    SELECT ID from {$wpdb->posts}
                    where post_type='{$post_type}'
                )";
                break;
        }




        $query="SELECT * from ".$wpdb->postmeta."
                {$join}
                where meta_key='item_id'
                {$where}

                order by meta_id desc
                limit 0,1";

        $data= $wpdb->get_results($query,OBJECT);

        if(!empty($data)){
            foreach($data as $key=>$value)
            {
                return human_time_diff(get_the_time('U',$value->post_id), current_time('timestamp')).__(' ago',ST_TEXTDOMAIN);
            }
        }




    }

    static function get_card($card_name)
    {
        $options=st()->get_option('booking_card_accepted',array());


        if(!empty($options)){
            foreach($options as $key){
                if(sanitize_title_with_dashes($key['title'])==$card_name) return $key;
            }
        }
    }

    static function get_orgin_booking_id($item_id)
    {
        if(get_post_type($item_id)=='hotel_room'){
            if($hotel_id=get_post_meta($item_id,'room_parent',true)){
                $item_id=$hotel_id;
            }
        }
        return apply_filters('st_orgin_booking_item_id',$item_id);
    }

    static function get_min_max_price($post_type){
        if(empty($post_type)){
            return array('price_min'=>0,'price_max'=>500);
        }
        $arg = array(
            'post_type'=>$post_type,
            'posts_per_page'=>'1',
            'order'=>'ASC',
            'meta_key'=>'sale_price',
            'orderby'=>'meta_value_num',
        );
        $query=new WP_Query($arg);
        if($query->have_posts()) {
            $query->the_post();
            $price_min = get_post_meta(get_the_ID(),'sale_price',true);
        }
        wp_reset_postdata();
        $arg = array(
            'post_type'=>$post_type,
            'posts_per_page'=>'1',
            'order'=>'DESC',
            'meta_key'=>'sale_price',
            'orderby'=>'meta_value_num',
        );
        $query=new WP_Query($arg);
        if($query->have_posts()) {
            $query->the_post();
            $price_max = get_post_meta(get_the_ID(),'sale_price',true);
        }
        wp_reset_postdata();
        if(empty($price_min))$price_min=0;
        if(empty($price_max))$price_max=500;
        return array('price_min'=>ceil($price_min),'price_max'=>ceil($price_max));
    }

    static function get_list_location(){
        $arg = array(
            'post_type'=>'location',
            'posts_per_page'=>'-1',
            'order'=>'ASC',
            'orderby'=>'title',
            'post_parent'=>0,
        );
        $array_list=array();
        query_posts($arg);
        while(have_posts()){
            the_post();
            $array_list[]=array(
                'id'=>get_the_ID(),
                'title'=>get_the_title()
            );
            $children_array = self::get_child_location(get_the_ID(),'-');
            if(!empty($children_array)){
               foreach($children_array as $k=>$v){
                   $array_list[]=array(
                       'id'=>$v['id'],
                       'title'=>$v['title']
                   );
                   $children_array2 = self::get_child_location($v['id'],'--');
                   if(!empty($children_array2)) {
                       foreach ($children_array2 as $k2 => $v2) {
                           $array_list[]=array(
                               'id'=>$v2['id'],
                               'title'=>$v2['title']
                           );
                           $children_array3 = self::get_child_location($v2['id'],'---');
                           if(!empty($children_array3)) {
                               foreach ($children_array3 as $k3 => $v3) {
                                   $array_list[]=array(
                                       'id'=>$v3['id'],
                                       'title'=>$v3['title']
                                   );
                               }
                           }
                       }
                   }
               }
            }
        }
        wp_reset_query();
        return $array_list;
    }
	
	static function get_search_result_link($post_type=false)
	{
		$url= home_url('/');
        return apply_filters('st_'.$post_type.'_search_result_link',$url);
	}
    static function get_child_location($id , $prent){
        $args = array(
            'post_parent' => $id,
            'post_type'   => 'location',
            'posts_per_page' => -1,
        );
        $children_array = get_children( $args );
        $array_list = array();
        if(!empty($children_array)){
            foreach($children_array as $k=>$v){
                $array_list[]=array(
                    'id'=>$v->ID,
                    'title'=>$prent.$v->post_title
                );
            }
        }
        return $array_list;
    }


    static function st_get_custom_price_by_date($post_id,$start_date=null,$price_type='default'){
        global $wpdb;
        if($post_id)$post_id=get_the_ID();
        if(empty($start_date))$start_date = date("Y-m-d");
        $rs = $wpdb->get_results( "SELECT * FROM " . $wpdb->base_prefix . "st_price WHERE post_id=" . $post_id . " AND price_type='" . $price_type . "'  AND start_date <='" . $start_date . "' AND end_date >='" . $start_date . "' AND status=1 ORDER BY priority DESC LIMIT 1"  );
        if(!empty($rs)){
            return $rs[0]->price;
        }else{
            return false;
        }
    }
    static function st_conver_info_price($info_price){
        $list_info_price = '';
        if(!empty($info_price)){
            $start = '';
            $end = '';
            $price = "";
            foreach($info_price as $k=>$v){
                if(empty($price)){
                    $start = $v['start'];
                    $end = $v['end'];
                    $price = $v['price'];
                    $list_info_price[$start]= array(
                        'start'=>$start,
                        'end'=>$end,
                        'price'=>$price,
                    );
                }
                if($price == $v['price']){
                    $end = $v['end'];
                    $list_info_price[$start]= array(
                        'start'=>$start,
                        'end'=>$end,
                        'price'=>$price,
                    );
                }
                if($price != $v['price']){
                    $start = $v['end'];
                    $end = $v['end'];
                    $price = $v['price'];

                    $list_info_price[$start]= array(
                        'start'=>$start,
                        'end'=>$end,
                        'price'=>$price,
                    );

                }
            }
        }
        return $list_info_price;
    }
    /**
    * @since 1.1.1
    * 
    *
    */
    static function get_book_btn(){
        $class_room_btn = "";
        if (get_post_type(get_the_ID()) =="hotel_room"){
            $class_room_btn = "btn_hotel_booking" ; 
        };
        ob_start();
?>
        <input type="submit"  class=" btn btn-primary <?php echo esc_attr($class_room_btn) ; ?>" value="<?php st_the_language('book_now') ?>">
<?php
        $book_now_btn = ob_get_clean();
        return $book_now_btn;
    }

    /**
     *
     *
     * @since 1.1.1
     * @param int $room_id of booking item
     * @return string type of reposit of booking item
     * */
    public function get_deposit_type($booking_id=NULL)
    {
        if(!$booking_id)
        {
            $booking_id=get_the_ID();
        }

        return get_post_meta($booking_id,'deposit_payment_status',true);

    }

    /**
     *
     *
     * @since 1.1.1
     * */
    public function get_deposit_amount($booking_id=NULL)
    {
        if(!$booking_id)
        {
            $booking_id=get_the_ID();
        }

        return get_post_meta($booking_id,'deposit_payment_amount',true);

    }

    /**
     *
     *
     * @since 1.1.1
     * @update 1.1.2
     * */
    public function get_deposit_money_amount($room_money,$booking_id=false)
    {
        if($deposit_type=$this->get_deposit_type($booking_id) and $room_money)
        {
            $deposit_amount=$this->get_deposit_amount($booking_id);

            if($deposit_amount){
                switch($deposit_type)
                {
                    case "percent":
                        $room_money=($room_money/100)*$deposit_amount;
                        break;

                    case 'amount':
                        $room_money=$deposit_amount;
                        break;

                }
            }

        }

        return $room_money;

    }
    /**
     *
     *
     * @since 1.1.1
     * */
    function _deposit_calculator($cart_data,$item_id)
    {
        if($this->get_deposit_type($item_id) and $this->get_deposit_amount($item_id))
        {
            $old_price=$cart_data['price'];
            $cart_data['price']=$this->get_deposit_money_amount($old_price,$item_id);

            $cart_data['data']['deposit_money']=array(
                'type'=>$this->get_deposit_type($item_id),
                'old_price'=>$old_price,
                'amount'=>$this->get_deposit_amount($item_id)
            );
        }

        return $cart_data;
    }
    /**
     *
     *
     * @since 1.1.5
     * */
    function _get_location_by_name( $location_name )
    {
        if(empty( $location_name ))
            return $location_name;

        $ids   = array();
        global $wpdb;
        $query = "SELECT SQL_CALC_FOUND_ROWS  ".$wpdb->posts.".ID
                    FROM ".$wpdb->posts."
                    WHERE 1=1
                    AND (((".$wpdb->posts.".post_title LIKE '%".$location_name."%') OR (".$wpdb->posts.".post_content LIKE '%".$location_name."%')))
                    AND ".$wpdb->posts.".post_type = 'location'
                    AND ((".$wpdb->posts.".post_status = 'publish' OR ".$wpdb->posts.".post_status = 'pending'))
                    ORDER BY ".$wpdb->posts.".post_title LIKE '%".$location_name."%' DESC, ".$wpdb->posts.".post_date DESC LIMIT 0, 10";
        $data = $wpdb->get_results($query, OBJECT);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $ids[] = $v->ID ;
            }
        }
        return $ids;
    }


}

$a=new TravelerObject();
$a->_class_init();
