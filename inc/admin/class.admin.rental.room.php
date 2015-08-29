<?php 
/**
*@since 1.1.3
**/
if(!class_exists('STReltalRoom')){

	class STReltalRoom extends STAdmin{

		function __construct(){

            if (!st_check_service_available('st_rental')) return;

			parent::__construct();
            //add colum for rooms
            add_filter('manage_rental_room_posts_columns', array($this, 'add_col_header'), 10);
            add_action('manage_rental_room_posts_custom_column', array($this, 'add_col_content'), 10, 2);

			add_action('init', array($this, 'init_metabox') );

            add_filter('st_rental_room_layout', array($this, 'custom_rental_room_layout'));

		}

        function add_col_header($defaults)
        {

            $this->array_splice_assoc($defaults,2,0,array('rental_parent'=>__('Rental Name',ST_TEXTDOMAIN)));

            return $defaults;
        }

        function add_col_content($column_name, $post_ID)
        {

            if ($column_name == 'rental_parent') {
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

		/**
		*@since 1.1.3
		**/
		public function init_metabox(){

			$this->metabox[] = array(
                'id'          => 'rental_room_metabox',
                'title'       => __( 'Room Setting', ST_TEXTDOMAIN),
                'desc'        => '',
                'pages'       => array( 'rental_room' ),
                'context'     => 'normal',
                'priority'    => 'high',
                'fields'      => array(
                	array(
                        'label'       => __( 'General', ST_TEXTDOMAIN),
                        'id'          => 'room_reneral_tab',
                        'type'        => 'tab'
                    ),

                    array(
                        'label'       => __( 'Rental', ST_TEXTDOMAIN),
                        'id'          => 'room_parent',
                        'type'        => 'post_select_ajax',
                        'desc'        => __( 'Choose the rental that the room belong', ST_TEXTDOMAIN),
                        'post_type'   =>'st_rental',
                        'placeholder' =>__('Search for a Rental',ST_TEXTDOMAIN)
                    ),
/*
                    array(
                        'label'       => __( 'Number of Room', ST_TEXTDOMAIN),
                        'id'          => 'number_room',
                        'type'        => 'text',
                        'desc'        => __( 'Number of rooms available for book', ST_TEXTDOMAIN),
                        'std'         =>1
                    ),*/
                    array(
                        'label' => __('Gallery',ST_TEXTDOMAIN),
                        'id' => 'gallery',
                        'type' => 'gallery'
                    ),
                    array(
                        'label'     => __('Rental Room Layout', ST_TEXTDOMAIN),
                        'id'        => 'st_custom_layout',
                        'post_type' => 'st_layouts',
                        'desc'      => __('Rental Room Layout', ST_TEXTDOMAIN),
                        'type'      => 'select',
                        'choices'   => st_get_layout('rental_room')
                    ),
                )	
            );  

            parent::register_metabox($this->metabox);
		}

        /**
        *@since 1.1.3
        **/
        public function custom_rental_room_layout($old_layout_id=false){

            if(is_singular('rental_room')){

                $meta=get_post_meta(get_the_ID(),'st_custom_layout',true);
                if($meta)
                {
                    return $meta;
                }
            }
            return $old_layout_id;
        }
	}
	new STReltalRoom();
}
?>