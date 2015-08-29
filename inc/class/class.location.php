<?php
    /**
     * @package WordPress
     * @subpackage Traveler
     * @since 1.0
     *
     * Class STLocation
     *
     * Created by ShineTheme
     *
     */
    if (!class_exists('STLocation')) {
        class STLocation extends TravelerObject
        {

            function init()
            {
                parent::init();
                $this->init_metabox();


                add_action('wp_ajax_st_search_location', array($this, 'search_location'));
                add_action('wp_ajax_nopriv_st_search_location', array($this, 'search_location'));
                add_action( 'widgets_init' , array( $this , 'add_sidebar' ) );
				
            }

            function get_featured_ids($arg = array())
            {
                $default = array(
                    'posts_per_page' => 10,
                    'post_type'      => 'location'
                );

                extract(wp_parse_args($arg, $default));

                $ids = array();

                $query = array(
                    'posts_per_page' => $posts_per_page,
                    'post_type'      => $post_type,
                    'meta_key'       => 'is_featured',
                    'meta_value'     => 'on'
                );

                $q = new WP_Query($query);

                while ($q->have_posts()) {
                    $q->the_post();
                    $ids[] = get_the_ID();
                }

                //wp_reset_query();

                return $ids;
            }

            function search_location()
            {
                //Small security
                check_ajax_referer('st_search_security', 'security');

                $s = STInput::get('s');
                $arg = array(
                    'post_type'      => 'location',
                    'posts_per_page' => 10,
                    's'              => $s
                );

                if ($s) {
                }

                global $wp_query;

                query_posts($arg);
                $r = array();

                while (have_posts()) {
                    the_post();

                    $r['data'][] = array(
                        'title' => get_the_title(),
                        'id'    => get_the_ID(),
                        'type'  => __('Location', ST_TEXTDOMAIN)
                    );
                }
                wp_reset_query();
                echo json_encode($r);

                die();
            }

            function init_metabox()
            {

                $this->metabox[] = array(
                    'id'       => 'st_location',
                    'title'    => __('Location Setting', ST_TEXTDOMAIN),
                    'pages'    => array('location'),
                    'context'  => 'normal',
                    'priority' => 'high',
                    'fields'   => array(
                        array(
                            'label' => __('Location Information', ST_TEXTDOMAIN),
                            'id'    => 'detail_tab',
                            'type'  => 'tab'
                        ),
                        array(
                            'label' => __('Logo', ST_TEXTDOMAIN),
                            'id'    => 'logo',
                            'type'  => 'upload',
                            'desc'  => __('logo', ST_TEXTDOMAIN),
                        ),
                        array(
                            'label' => __('Set as Featured', ST_TEXTDOMAIN),
                            'id'    => 'is_featured',
                            'type'  => 'on-off',
                            'desc'  => __('Set this location is featured', ST_TEXTDOMAIN),
                            'std'   => 'off'
                        ),
                        array(
                            'label' => __('Zip Code', ST_TEXTDOMAIN),
                            'id'    => 'zipcode',
                            'type'  => 'text',
                            'desc'  => __('Zip code of this location', ST_TEXTDOMAIN),
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
//                        array(
//                            'label' => __('Sale setting', ST_TEXTDOMAIN),
//                            'id'    => 'sale_number_tab',
//                            'type'  => 'tab'
//                        ),
//
//                        array(
//                            'label' => __('Total Sale Number', ST_TEXTDOMAIN),
//                            'id'    => 'total_sale_number',
//                            'type'  => 'text',
//                            'desc'  => __('Total Number Booking', ST_TEXTDOMAIN),
//                        ),
//                        array(
//                            'label' => __('Rate setting', ST_TEXTDOMAIN),
//                            'id'    => 'rate_number_tab',
//                            'type'  => 'tab'
//                        ),
//
//                        array(
//                            'label' => __('Rate Review', ST_TEXTDOMAIN),
//                            'id'    => 'rate_review',
//                            'type'  => 'text',
//                        ),
                    )
                );
            }
            /**
            * @since 1.1.3
            * count post type in a location
            *
            */
            static function get_count_post_type($post_type, $location_id = null){
                global $wpdb;
                $meta_key = "id_location";
                if ($post_type =="st_rental") {
                    $meta_key = "location_id" ; 
                }
                if (!$location_id ){$location_id = get_the_ID();}
                $sql = "
                SELECT ID FROM `{$wpdb->posts}` join {$wpdb->postmeta} on {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID 
                where {$wpdb->posts}.post_type = '{$post_type}'
                and {$wpdb->posts}.post_status = 'publish'
                and {$wpdb->postmeta}.meta_key = '{$meta_key}'
                and {$wpdb->postmeta}.meta_value = '{$location_id}'
                group by {$wpdb->posts}.ID;
                ";
                $results = $wpdb->get_results( $sql , OBJECT);  
                wp_reset_query();
                return ($wpdb->num_rows); 

            }

            /**
            * @since 1.1.3
            * text in footer location element
            *
            */

            static function get_total_text_footer($post_type){
                /* Example 10 cars in paris*/

                $link = STLocation::get_link_search($post_type);
                $count_post_type = STLocation::get_count_post_type($post_type);

                if ($post_type == "st_cars"){
                    $post_type_names = __(' cars ',ST_TEXTDOMAIN);$post_type_name = __(' car ',ST_TEXTDOMAIN);
                }
                if ($post_type == "st_hotel"){
                    $post_type_names = __(' hotels ',ST_TEXTDOMAIN);$post_type_name = __(' hotel ',ST_TEXTDOMAIN);
                }
                if ($post_type == "st_rental"){
                    $post_type_names = __(' rentals ',ST_TEXTDOMAIN);$post_type_name = __(' rental ',ST_TEXTDOMAIN);
                }
                if ($post_type == "st_tours"){
                    $post_type_names = __(' tours ',ST_TEXTDOMAIN);$post_type_name = __(' tour ',ST_TEXTDOMAIN);
                }
                if ($post_type == "st_activity"){
                    $post_type_names = __(' activities ',ST_TEXTDOMAIN);$post_type_name = __(' activity ',ST_TEXTDOMAIN);
                }

                $text  ;
                if ($count_post_type>1){
                    $text .= esc_html( $count_post_type). $post_type_names;
                }
                    else {
                        $text .= esc_html( $count_post_type).$post_type_name;
                    }
                $text .=__(" in " , ST_TEXTDOMAIN).get_the_title() ." ."; 
                $return .= 
                "
                    <div class='text-right'>
                        <span>".$text."</span>
                        <a href=".esc_url($link).">".__("View all",ST_TEXTDOMAIN)."</a>
                    </div>
                ";
                return $return ; 
            }
            /**
            * @since 1.1.2
            * create new location static sidebar 
            * 
            *
            */
            function add_sidebar (){
                register_sidebar( 
                    array(
                        'name'          => __( 'Location sidebar ' , ST_TEXTDOMAIN ) ,
                        'id'            => 'location-sidebar' ,
                        'description'   => __( 'Widgets in this area will be show information in current Location' , ST_TEXTDOMAIN ) ,
                        'before_title'  => '<h4>' ,
                        'after_title'   => '</h4>' ,
                        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">' ,
                        'after_widget'  => '</div>' ,
                    ) 
                );
            }
			static function get_info_by_post_type($id, $post_type= null){
				
				if (in_array($post_type ,array("hotel", "hotels", "st_hotels"))){ $post_type = "st_hotel" ;  }
				if (in_array($post_type ,array("car", "cars", "st_car"))){ $post_type = "st_cars" ;}
				if (in_array($post_type ,array("rental", "rentals" , "st_rentals"))){ $post_type = "st_rental" ; }
				if (in_array($post_type ,array("activity", "activities", "st_activity"))){ $post_type = "st_activity" ;}
				if (in_array($post_type ,array("tour", "tours", "st_tour"))){ $post_type = "st_tours" ; }
				if (!$post_type){	return ; }
				
				$location_meta_text = 'id_location' ; 
				if ($post_type == 'st_rental' ){
					$location_meta_text = 'location_id' ; 
				}
				
				$location_id = get_the_ID() ; 
				
				global $wpdb;
				
				$sql = "SELECT ID
				FROM `{$wpdb->postmeta}` 
				JOIN `{$wpdb->posts}` 
				ON 	{$wpdb->posts}.ID = {$wpdb->postmeta}.post_id 
					and {$wpdb->postmeta}.meta_key = '{$location_meta_text}' 
					and {$wpdb->postmeta}.meta_value = '{$location_id}'
					and {$wpdb->posts}.post_status = 'publish'
					and {$wpdb->posts}.post_type = '{$post_type}'
				GROUP BY {$wpdb->posts}.ID";
				
				$results = $wpdb->get_results( $sql , OBJECT); 		
				$num_rows = $wpdb->num_rows;
				// get review = count all comment number
				$comment_num  = 0 ;
				foreach($results as $row){
					$comment_num  = $comment_num + STReview::count_all_comment($row->ID) ;
				};		
				wp_reset_query();				
				return array(
					'post_type' =>$post_type,
					'post_type_name'=>  self::get_post_type_name($post_type),
					'reviews'	=>$comment_num,
					'offers'	=>$num_rows,
					'min_max_price'=>self::get_min_max_price_location($post_type, $location_id)
				) ; 
				
			}
            /**
            * @package Wordpress
            * @subpackage traveler
            * @since 1.1.3
            *
            */
            static function get_post_type_name($post_type){
                ob_start();
                if ($post_type == "st_cars"){st_the_language("cars")  ; }
                if ($post_type == "st_tours"){st_the_language("tours")  ; }
                if ($post_type == "st_rental"){ st_the_language("rentals")  ; }
                if ($post_type == "st_activity"){ st_the_language("activities")  ; }
                if ($post_type == "st_hotel"){st_the_language("hotels")  ; }
                $return = ob_get_clean();
                return $return;
            }
            /**
            *
            * since 1.1.2
            * get single price
            *
            */
            public static function get_item_price($post_id){
                if (!$post_id) {$post_id = get_the_ID();}
                $post_type = get_post_type($post_id );
                $discount         = get_post_meta( $post_id , 'discount' , true );
                if ($post_type == "st_rental" or $post_type == "hotel_room"){
                    $discount         = get_post_meta( $post_id , 'discount_rate' , true );              
                }
                $is_sale_schedule = get_post_meta( $post_id , 'is_sale_schedule' , true );
                
                if ($post_type =="st_cars"){
                    $price     = get_post_meta( $post_id , 'cars_price' , true );
                }
                else {
                    $price     = get_post_meta( $post_id , 'price' , true );
                }
                if($is_sale_schedule == 'on') {
                    $sale_from = get_post_meta( $post_id , 'sale_price_from' , true );
                    $sale_to   = get_post_meta( $post_id , 'sale_price_to' , true );
                    if($sale_from) {

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
                return apply_filters( 'location_single_price', $new_price );

            }
			public static function get_min_max_price_location($post_type, $location_id){
				if (!in_array($post_type , array('st_cars','st_tours' , 'st_hotel'  , 'st_activity' , 'st_rental'))){return ; }
				$location_meta_text = 'id_location' ; 
				if ($post_type == 'st_rental' ){
					$location_meta_text = 'location_id' ; 
				}
				global $wpdb;
				$sql = "
				select ID from {$wpdb->posts}
				join {$wpdb->postmeta} 
				on {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID				
				and {$wpdb->postmeta}.meta_key = '{$location_meta_text}'
				and {$wpdb->postmeta}.meta_value = '{$location_id}'
				and {$wpdb->posts}.post_status = 'publish'
				where {$wpdb->posts}.post_type = '{$post_type}'
				group by {$wpdb->postmeta}.post_id" ; 
				
				$results = $wpdb->get_results($sql, OBJECT) ; 
				
				$min_price = 999999999999 ;
				$max_price = 0 ;
				$detail = array() ;
				
				if (!is_array($results) or empty($results)){return ; }
				
					
					if ($post_type =="st_hotel"){
                        foreach ($results as $post_id){
                        $post_id = $post_id->ID ; 
						// call all room of current hotel
						// coppy from hotel class and fixed . 
						$query=array(
						'post_type' =>'hotel_room',
						'meta_key'=>'room_parent',
						'meta_value'=>$post_id
						);
						$q=new WP_Query($query);
						while($q->have_posts()){
							$q->the_post();
                            $price = self::get_item_price(get_the_ID());                          
							if ($price<$min_price){
                                $min_price = $price ; 
                                $detail['item_has_min_price'] = get_the_ID();                                
							}
						}
                        
						wp_reset_query();		
                        }				
						
					}else {		
                        foreach ($results as $post_id){
                            $post_id = $post_id->ID ; 			
    						$price_text_field = "price" ; 
    						if ($post_type == 'st_cars'){$price_text_field = 'cars_price' ; }
    						$price = self::get_item_price($post_id);
                            
    						if ($price<$min_price){
    							$min_price = $price ; 
    							$detail['item_has_min_price'] = $post_id; 
    						}
    						if ($price>$max_price) {
    							$max_price = $price ; 
    							$detail['item_has_max_price'] = $post_id; 
    						}
                        
    					}
    				}
				return array('price_min'=>$min_price , 'price_max'=>$max_price, 'detail'=>$detail) ; 
			}
			static function scrop_thumb($image){
				$return = '<img src="'.esc_url($image).'" style="width: 100%" alt = "'.get_the_title().'" >';
				return apply_filters('location_item_crop_thumb',$return) ; 
			}

            /**
            * @since 1.1.3
            * get location information
            *
            */

            static function get_slider($gallery_array){
                $return ; 
                $gallery_array = explode(',',$gallery_array);
                $return .='<div class="fotorama" data-allowfullscreen="true" data-nav="thumbs">';
                if(is_array($gallery_array) and !empty($gallery_array))
                {
                    foreach($gallery_array as $key=>$value)
                    {
                        $return.= wp_get_attachment_image($value,array(800,600,'bfi_thumb'=>true));
                    }
                }
                $return .='</div>';
                return $return;
            }
            /**
            * @since 1.1.3
            * get link search location by post type 
            * 
            */
            static function get_link_search($post_type){

                if ($post_type == "st_cars"){$post_type_s = "cars" ; }
                if ($post_type == "st_hotel"){$post_type_s = "hotel" ; }
                if ($post_type == "st_rental"){$post_type_s = "rental" ; }
                if ($post_type == "st_activity"){$post_type_s = "acitivity" ; }
                if ($post_type == "st_tours"){$post_type_s = "tours" ; }

                $layout_id = st()->get_option($post_type_s.'_search_result_page' , true);
                $link=esc_url(add_query_arg(array(
                'post_type'=>$post_type,
                'location_name'=>get_the_title(),
                'location_id'=>get_the_ID(),                
                's'=>'',
                'layout'=>$post_type=="st_hotel" ? $layout_id : ''),home_url()));
                return $link ; 
            }
            /**
            * @since 1.1.3
            * static rate by location and rate
            * return array(1=>xx , 2=> xyz , 3=>sss  , 4=>ssss, 5+>ksfs)
            **/
            static function get_rate_count($star_array , $post_type_array){
                global $wpdb; 

                if (!$star_array) {$star_array = array(5,4,3,2,1) ; }
                if (!$post_type_array) {$post_type_array = array('st_cars','st_hotel','st_rental','st_tours','st_activity') ; }
                $post_type_list_sql ; 

                if (!empty($post_type_array) and is_array($post_type_array)){
                    $post_type_list_sql .=" and ( ";
                    foreach ($post_type_array as $key => $value) {
                        if ($key == 0 ){
                            $post_type_list_sql .= "{$wpdb->posts} .post_type = '{$value}' ";
                        }else {
                            $post_type_list_sql .= " or {$wpdb->posts} .post_type = '{$value}' ";
                        }
                    }
                    $post_type_list_sql .=" ) ";
                }  


                $return = array();
                $location_id = get_the_ID();

                if (is_array($star_array) and !empty($star_array)){
                    foreach ($star_array as $key => $value) {
                        $star = $value ; 
                        $sql = "
                        SELECT ID FROM {$wpdb->commentmeta}  
                        join {$wpdb->comments}  on {$wpdb->commentmeta} .comment_id = {$wpdb->comments} .comment_ID
                        join {$wpdb->posts}  on {$wpdb->comments} .comment_post_ID = {$wpdb->posts} .ID
                        where {$wpdb->commentmeta} .meta_key = 'comment_rate' and {$wpdb->commentmeta} .meta_value = {$star}
                        and {$wpdb->posts} .comment_status  = 'open'
                        and {$wpdb->posts} .post_status = 'publish'
                            ".$post_type_list_sql."
                        and {$wpdb->comments} .comment_approved = 1
                        GROUP BY {$wpdb->commentmeta} .comment_id";
                        $results = $wpdb->get_results( $sql , OBJECT);      
                        //return ($wpdb->num_rows);
                        $i = 0 ;
                        foreach ($results as $key => $value) {
                            if (get_post_type($value->ID) == "st_rental"){
                                $meta_key = "location_id" ; 
                            }else {
                                $meta_key = "id_location" ;
                            }
                            if (get_post_meta($value->ID , $meta_key , true) == get_the_ID()){
                                $i ++;
                            }
                        } 
                        $return [$star] = $i ;  
                    }
                }
                
                return $return ; 

            }
            /**
            * @package wordpress
            * @subpackage traveler 
            * @since 1.1.3
            * @descript get random post type to show widget 
            */
            public static function get_random_post_type($location_id , $post_type){
                if (!$location_id){
                    $location_id = get_the_ID();
                }
                if (!$post_type){
                    $post_type = "st_cars"; 
                }
                $query = array(
                    'posts_per_page' => 1,
                    'post_type'=>$post_type,
                    'orderby'=>'rand',
                    'post_status'=>'publish',
                    );
                query_posts( $query );
                while (have_posts()) {
                    the_post();
                    $id = get_the_ID();
                }
                wp_reset_query();
                return $id ; 
            }

        }

        $a = new STLocation();
        $a->init();
    }
