<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Class STAdmin
 *
 * Created by ShineTheme
 *
 */

if(!class_exists('STAdmin'))
{

    class STAdmin
    {
        private $template_dir = 'inc/admin/views';
        static private $message = "";
        static private $message_type = "";
        public $metabox;

        function __construct()
        {

        }

        /**************** Price *****************/


        function st_create_custom_price()
        {
            $data=apply_filters('st_data_custom_price',array());
            if(!empty($data) and is_array($data)){
                add_meta_box( 'st_custom_price' , $data['title'] , array( $this , 'st_custom_price_func' ) , $data['post_type'] , 'normal' , 'high' );
            }
        }

        function st_custom_price_func( $object , $box )
        {
            echo self::load_view('admin/html','price',array('post_id'=>$object->ID ,'st_custom_price_nonce'=>wp_create_nonce( plugin_basename( __FILE__ ) )));
        }

        function st_save_custom_price( $post_id , $post )
        {
            if(!empty($_POST[ 'st_custom_price_nonce' ])){
                if(!wp_verify_nonce( $_POST[ 'st_custom_price_nonce' ] , plugin_basename( __FILE__ ) )) return $post_id;

                if(!current_user_can( 'edit_post' , $post_id )) return $post_id;

                $price_new = $_REQUEST[ 'st_price' ];
                $price_type = $_REQUEST[ 'st_price_type' ];
                $start_date = $_REQUEST[ 'st_start_date' ];
                $end_date = $_REQUEST[ 'st_end_date' ];
                $status = $_REQUEST[ 'st_status' ];
                $priority = $_REQUEST[ 'st_priority' ];

                self::st_delete_price($post_id);

                if($price_new and $start_date and $end_date){
                    foreach($price_new as $k=>$v){
                        if(!empty($v)){
                            self::st_add_price( $post_id , $price_type[$k] , $v , $start_date[$k] , $end_date[$k] , $status[$k] , $priority[$k] );
                        }
                    }
                }
            }
        }

        static function st_get_all_price($post_id)
        {
            global $wpdb;
            $rs = $wpdb->get_results( "SELECT * FROM " . $wpdb->base_prefix . "st_price WHERE post_id=" . $post_id );
            return $rs;
        }

        static function st_add_price( $post_id , $type_price = 'default' , $price , $start_date , $end_date , $status = 1 , $priority = 0 )
        {
            global $wpdb;
            $start_date = date("Y-m-d",strtotime($start_date));
            $end_date = date("Y-m-d",strtotime($end_date));
            if($the_post = wp_is_post_revision( $post_id )) $post_id = $the_post;
            $check = $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->base_prefix . "st_price WHERE post_id=" . $post_id . " AND price_type='" . $type_price . "' AND price=" . $price . " AND start_date='" . $start_date . "' AND end_date='" . $end_date . "' AND status=" . $status . " AND priority=" . $priority );
            if(empty($check)) {
                $wpdb->insert( $wpdb->base_prefix . 'st_price' , array( 'post_id' => $post_id , 'price_type' => $type_price , 'price' => $price , 'start_date' => $start_date , 'end_date' => $end_date , 'status' => $status , 'priority' => $priority ) , array( '%d' , '%s' , '%d' , '%s' , '%s' , '%d' , '%d' ) );
                $insert_id = (int)$wpdb->insert_id;
                if($insert_id) {
                    return $insert_id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        function st_update_price( $post_id , $type_price = 'default' , $price , $start_date , $end_date , $status = 1 , $priority = 0 )
        {

        }

        static function st_delete_price( $post_id )
        {
            global $wpdb;
            $wpdb->delete( $wpdb->base_prefix . 'st_price' , array( 'post_id' => $post_id ) );
        }



        /**************** End Price *****************/

        function admin_enqueue_scripts()
        {
            wp_enqueue_style( 'st-admin' , get_template_directory_uri() . '/css/admin/admin.css' );
            wp_enqueue_script('st-edit-booking',get_template_directory_uri().'/js/admin/edit-booking.js',array('jquery'),null,true);
            wp_enqueue_script('st-custom-price',get_template_directory_uri().'/js/admin/custom-price.js',array('jquery'),null,true);
        }

        function update_location_info( $post_id )
        {
            if(wp_is_post_revision( $post_id )) return;
            $post_type = get_post_type( $post_id );

            if($post_type == 'st_cars' or $post_type == 'st_activity' or $post_type == 'st_tours' or $post_type == 'st_rental' or $post_type=='st_hotel') {
                if($post_type == 'st_rental' /*or $post_type=='hotel'*/) {
                    $location = 'location_id';
                    $location_id = get_post_meta( $post_id , $location , true );
                } else {
                    $location = 'id_location';
                    $location_id = get_post_meta( $post_id , $location , true );
                }
                $ids_in = array();
                $parents = get_posts( array( 'numberposts' => -1 , 'post_status' => 'publish' , 'post_type' => 'location' , 'post_parent' => $location_id ) );

                $ids_in[ ] = $location_id;

                foreach( $parents as $child ) {
                    $ids_in[ ] = $child->ID;
                }
                $arg = array( 'post_type' => $post_type , 'posts_per_page' => '-1' , 'meta_query' => array( array( 'key' => $location , 'value' => $ids_in , 'compare' => 'IN' , ) , ) , );
                $query = new WP_Query( $arg );
                $offer = $query->post_count;

                // get total review
                $arg = array( 'post_type' => $post_type , 'posts_per_page' => '-1' , 'meta_query' => array( array( 'key' => $location , 'value' => $ids_in , 'compare' => 'IN' , ) , ) , );
                $query = new WP_Query( $arg );
                $total = 0;
                if($query->have_posts()) {
                    while( $query->have_posts() ) {
                        $query->the_post();
                        $total += get_comments_number();
                    }
                }
                // get car min price
                $meta_key= 'sale_price';
                if($post_type == 'st_hotel'){
                    $meta_key= 'price_avg';
                }
                $arg = array( 'post_type' => $post_type , 'posts_per_page' => '1' , 'order' => 'ASC' , 'meta_key' => $meta_key , 'orderby' => 'meta_value_num' , 'meta_query' => array( array( 'key' => $location , 'value' => $ids_in , 'compare' => 'IN' , ) , ) , );
                $query = new WP_Query( $arg );
                if($query->have_posts()) {
                    $query->the_post();
                    $price_min = get_post_meta( get_the_ID() , 'sale_price' , true );
                    if($post_type == 'st_hotel'){
                        $price_min = get_post_meta( get_the_ID() , 'price_avg' , true );
                    }
                }
                wp_reset_postdata();
                update_post_meta( $location_id , 'review_' . $post_type , $total );
                if(isset( $price_min )) update_post_meta( $location_id , 'min_price_' . $post_type , $price_min );
                update_post_meta( $location_id , 'offer_' . $post_type , $offer );
            }
        }

        function array_splice_assoc( &$input , $offset , $length = 0 , $replacement = array() )
        {
            $tail = array_splice( $input , $offset );
            $extracted = array_splice( $tail , 0 , $length );
            $input += $replacement + $tail;
            return $extracted;
        }

        static function get_history_bookings( $type = "st_hotel" , $offset , $limit ,$author=false )
        {
            global $wpdb;
            $where = '';
            $join = '';
            $select = '';

            if(isset( $_GET[ 'st_date_start' ] ) and $_GET[ 'st_date_start' ]) {
                $date = date( 'Y-m-d' , strtotime( $_GET[ 'st_date_start' ] ) );
                $join .= " INNER JOIN $wpdb->postmeta as mt1 on mt1.post_id=$wpdb->posts.ID";
                $where .= ' AND  mt1.meta_key=\'check_in\'
                    AND CAST(mt1.meta_value AS DATE) >=\'' . esc_sql( $date ) . '\'
             ';
                //$select.=", STR_TO_DATE(mt1.meta_value,'%m-%d-%y') as date_picker";
            }

            if(isset( $_GET[ 'st_date_end' ] ) and $_GET[ 'st_date_end' ]) {
                $date = date( 'Y-m-d' , strtotime( $_GET[ 'st_date_end' ] ) );
                $join .= " INNER JOIN $wpdb->postmeta as mt2 on mt2.post_id=$wpdb->posts.ID";
                $where .= ' AND  mt2.meta_key=\'check_out\'
                    AND CAST(mt2.meta_value AS DATE) <=\'' . esc_sql( $date ) . '\'
             ';
                //$select.=", STR_TO_DATE(mt1.meta_value,'%m-%d-%y') as date_picker";
            }

            if($c_name = STInput::get( 'st_custommer_name' )) {
                $join .= " INNER JOIN $wpdb->postmeta as mt3 on mt3.post_id=$wpdb->posts.ID";
                $where .= ' AND  mt3.meta_key=\'st_first_name\'
             ';
                $where .= ' AND mt3.meta_value like \'%' . esc_sql( $c_name ) . '%\'';
            }

            if($author){
                $author = " AND ".$wpdb->posts.".post_author=".$author;
            }

            $querystr = " SELECT SQL_CALC_FOUND_ROWS  $wpdb->posts.* {$select}";
            $querystr .= "    FROM $wpdb->posts
                        INNER JOIN $wpdb->postmeta ON $wpdb->posts.ID=$wpdb->postmeta.post_id
                        {$join}
                        WHERE 1=1
                        AND $wpdb->posts.post_type = 'st_order'
                        AND $wpdb->postmeta.meta_key = 'item_id'
                        AND $wpdb->postmeta.meta_value in (SELECT {$wpdb->posts}.ID FROM $wpdb->posts WHERE {$wpdb->posts}.post_type='" . sanitize_title_for_query( $type ) . "' ".$author." )
                        " . $where . "
            ORDER BY $wpdb->posts.post_date DESC
            LIMIT {$offset},{$limit}
         ";

            $pageposts = $wpdb->get_results( $querystr , OBJECT );

            return array( 'total' => $wpdb->get_var( "SELECT FOUND_ROWS();" ) , 'rows' => $pageposts );
        }

        static function set_message( $message , $type = '' )
        {
            self::$message = $message;
            self::$message_type = $type;
        }

        static function message()
        {
            if(self::$message):
                ?>
                <div id="message" class="<?php echo self::$message_type?> below-h2">
                    <p><?php echo self::$message ?>
                    </p>
                </div>
            <?php endif;
        }


        function load_view( $slug , $name = false , $data = array() )
        {

            extract( $data );

            if($name) {
                $slug = $slug . '-' . $name;
            }

            //Find template in folder inc/admin/views/
            $template = locate_template( $this->template_dir . '/' . $slug . '.php' );


            //If file not found
            if(is_file( $template )) {
                ob_start();

                include $template;

                $data = @ob_get_clean();

                return $data;
            }
        }

        function register_metabox($custom_metabox)
        {
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

        function init()
        {

            $files = array(
                'admin/class.user' ,
                'admin/class.admin.menus' ,
                'admin/class.attributes' ,
                'admin/class.admin.hotel' ,
                'admin/class.admin.room' ,
                //'admin/class.admin.food' ,
                'admin/class.admin.restaurent' ,
                'admin/class.admin.rental' ,
                'admin/class.admin.cars' ,
                'admin/class.admin.tours' ,
                'admin/class.admin.activity' ,
                'admin/class.admin.location' ,
                'admin/class.admin.order' ,
                'admin/class.admin.permalink' ,
                'admin/class.admin.uploadfonticon',
                'admin/class.admin.update.content',
                //'admin/class.admin.membership',
                'admin/class.admin.rental.room',
                );


            st()->load_libs( $files );

            add_action( 'admin_enqueue_scripts' , array( $this , 'admin_enqueue_scripts' ) );
            add_action( 'save_post' , array( $this , 'update_location_info' ) );
            add_action( 'deleted_post' , array( $this , 'update_location_info' ) );
            add_action( 'admin_menu' , array( $this , 'st_create_custom_price' ) );
            add_action( 'save_post' , array( $this , 'st_save_custom_price' ) , 10 , 2 );
        }
    }

    $Admin=new STAdmin();

    $Admin->init();
}