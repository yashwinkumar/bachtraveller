<?php 
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.1.3
**/
$paged=STInput::get('room_page',1);
$item_id = get_the_ID();
extract($attr);
/**
* Extract $attr to:
*@param post_per_page
*@param number_in_row
*@param order_by
*@param order
**/
$query = array(
	'post_type' => 'rental_room',
	'posts_per_page' => $post_per_page,
	'order_by' => $order_by,
	'paged' => $paged,
	'order' => $order,
	'meta_query' => array(
		array(
			'key' => 'room_parent',
			'value' => $item_id ,
			'compare' => '='
			)
		)
	);
query_posts( $query );
if(have_posts()):
echo '<div class="row">';
echo '
	<div class="col-xs-12">
		<h4>'.$header_title.'</h4>
	</div>
';
echo '<div class="st_list_rental_room owl-carousel clearfix" style="padding: 0 30px;">';
while(have_posts()): the_post();

?>
<div class="item">
	<div class="thumb">
		<header class="thumb-header">
            <a class="hover-img" href="<?php the_permalink(); ?>">
                <?php
                    $img = get_the_post_thumbnail( get_the_ID() , array(800,600,'bfi_thumb'=>true)) ;
                    if(!empty($img)){
                        echo balanceTags($img);
                    }else{
                        echo '<img width="800" height="600" alt="no-image" class="wp-post-image" src="'.bfi_thumb(get_template_directory_uri().'/img/no-image.png',array('width'=>800,'height'=>600)) .'">';
                    }
                ?>                       
                <h5 class="hover-title-center"><?php echo __('View Detail',ST_TEXTDOMAIN); ?></h5>
            </a>
        </header>
        <div class="thumb-caption">
            <h5 class="thumb-title">
            	<a class="text-darken" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h5>         
         </div>
	</div>
</div>
<?php
endwhile; endif; wp_reset_postdata(); wp_reset_query();
?>
</div></div>