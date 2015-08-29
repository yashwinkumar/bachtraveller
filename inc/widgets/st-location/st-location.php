<?php

/**
* @package  Wordpress 
* @subpackage shinetheme
* @since 1.1.3
*/
class st_location_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'st_location_widget', 
			__('ST Statistical Location', ST_TEXTDOMAIN), 
			array( 'description' => __( 'Statisical car, tour, rental, cruise, hotel , activity in current Location and price, review, rate  , ... of them ', ST_TEXTDOMAIN ), ) 
		);
		add_action('admin_enqueue_scripts',array($this,'add_scripts'));
	 	add_action ( 'admin_enqueue_scripts', function () {
	        if (is_admin ())
	            wp_enqueue_media ();
	    });
	    add_action( 'wp_ajax_update_background', array($this,'update_background') );
	}
	public function update_background(){
		/*if ((STInput::request('background_location_item'))){
			$id  = STInput::request('background_location_item') ; 
			echo wp_get_attachment_image($bgr , array(240,240) ,false , array('class'=>"bgr_location"));
			die();
		}
		die();*/
	}
	public function add_scripts(){
		$screen=get_current_screen();

        if($screen->base=='widgets'){
        	wp_enqueue_style('jquery-ui',get_template_directory_uri().'/css/admin/jquery-ui.min.css');
            wp_enqueue_script('location_widget',get_template_directory_uri().'/js/admin/widgets/location_widget.js',array('jquery','jquery-ui-sortable'),null,true);            
        }
	}

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );	
		$args['after_title'] = apply_filters('after_title_widget_location' , $args['after_title'] );	
		$args['before_title'] = apply_filters('before_title_widget_location' , $args['before_title'] );	
		$args['before_widget'] = apply_filters('before_widget_location' , $args['before_widget'] ); 
		$args['after_widget'] = apply_filters('after_widget_location' , $args['after_widget'] ); 

		$post_type  = $instance['post_type'] ?$instance['post_type'] : "st_cars" ; 
		echo balancetags($args['before_widget']);

		if ( ! empty( $title ) )
		echo balancetags($args['before_title'] . $title . $args['after_title']);

		$post_type = $instance['post_type']? $instance['post_type']: "st_cars";
		$array = STLocation::get_info_by_post_type(get_the_ID(), $post_type);		
		$array['title']= $instance['title'] ?   $instance['title'] : "";
        $array['post_type']= $instance['post_type'];
        if(!isset($instance['use_feature'] ) || $instance['use_feature'] =="feature"){
        	$post_id = STLocation::get_random_post_type(get_the_ID() , $instance['post_type']);
        	$array['thumb'] = get_post_thumbnail_id($post_id);
        }else {
        	$array['thumb']= $instance['background'] ?   $instance['background'] : "";
        };
		echo st()->load_template('location/location-content-item' , null, $array ) ;


		echo balancetags($args['after_widget']);
	}
		
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Title', ST_TEXTDOMAIN );
		}
		if ( isset( $instance[ 'post_type' ] ) ) {
			$post_type = $instance[ 'post_type' ];
		}
		else {
			$post_type = __( 'Post type', ST_TEXTDOMAIN );
		}
		if ( isset( $instance[ 'background' ] ) ) {
			$bgr = $instance[ 'background' ];
		}
		else {
			
		}
		if (isset($instance['use_feature'] )){
			$use_feature = $instance['use_feature'] ; 
		}else {
			$use_feature = "feature"; 
		}
		$imgid 		=		(isset( $instance[ 'imgid' ] )) ? $instance[ 'imgid' ] : "";
		$img    	= 		wp_get_attachment_image_src($imgid, 'thumbnail');
		?>
		<div class='location_widget_item'>
			<p>
				<label for="<?php echo balancetags($this->get_field_id( 'title' )); ?>"><?php _e( 'Title:' ); ?></label> 
				<br>
				<input class="widefat" id="<?php echo balancetags( $this->get_field_id( 'title' )); ?>" name="<?php echo balancetags($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<!-- select post type -->
			<p>
				<label for="<?php echo balancetags($this->get_field_id( 'post_type' )); ?>"><?php _e( 'Select post type:' ); ?></label> 
				<br>
				<select name="<?php echo balancetags($this->get_field_name( 'post_type' )); ?>" >
					<option <?php if ($post_type =="st_cars") echo balancetags("selected") ; ?>  value='st_cars'>Cars</option>
					<option <?php if ($post_type =="st_hotel") echo balancetags("selected") ; ?>  value='st_hotel'>Hotels</option>
					<option <?php if ($post_type =="st_rental") echo balancetags("selected") ; ?>  value='st_rental'>Rentals</option>
					<option <?php if ($post_type =="st_tours") echo balancetags("selected") ; ?>  value='st_tours'>Tours</option>
					<option <?php if ($post_type =="st_activity") echo balancetags("selected") ; ?>  value='st_activity'>Activities</option>
				</select>
			</p>

			<input <?php if ($use_feature =="feature") echo "checked";   ?> type="radio" id ="<?php echo balancetags($this->get_field_id('use_feature'));?>" name="<?php echo balancetags($this->get_field_name('use_feature'));?>" id="<?php echo balancetags($this->get_field_id('use_feature'));?>" value="feature" />
			<label class="checked_label_location">Use random feature image post type</label><br><br>
			<input <?php if ($use_feature =="static") echo "checked";   ?> type="radio" id ="<?php echo balancetags($this->get_field_id('use_feature'));?>" name="<?php echo balancetags($this->get_field_name('use_feature'));?>" id="<?php echo balancetags($this->get_field_id('use_feature'));?>" value="static" />
			<label class ="checked_label_location">Use static background</label>

			<!-- image select  -->
			<p>
			
			<?php 
	        	if($bgr != "") {
	        		echo wp_get_attachment_image($bgr , array(240,240) ,false , array('class'=>"bgr_location"));        		
				}
	        ?>
	        <br>
			<input type="text" value="<?php echo esc_attr( $bgr ); ?>" class="widefat bgr_info_hidden" id="<?php echo balancetags($this->get_field_id( 'background' )); ?>" name="<?php echo balancetags($this->get_field_name( 'background' )); ?>" max="" min="1" step="1">
	            
	        <button class="set_custom_images button">Set background</button>	        
			</p>
		</div>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {  
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';		
		$instance['background'] = ( ! empty( $new_instance['background'] ) ) ? strip_tags( $new_instance['background'] ) : '';
		$instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : '';
		$instance['use_feature'] = ( ! empty( $new_instance['use_feature'] ) ) ? strip_tags( $new_instance['use_feature'] ) : '';

		if ($instance['use_feature'] == 'feature'){
			unset($instance['background']);
		}
		return $instance;
	}
} // Class st_location_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'st_location_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );