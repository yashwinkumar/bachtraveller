<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Single room
 *
 * Created by ShineTheme
 *
 */
get_header();
get_template_part('breadcrumb');
?>
<div class="booking-item-details">
	<?php
        $detail_tour_layout = apply_filters('st_hotel_room_layout','');
        if($detail_tour_layout && !empty($detail_tour_layout))
        {
            echo STTemplate::get_vc_pagecontent($detail_tour_layout);
        }else{
            echo do_shortcode('
                [vc_row][vc_column width="2/3"][st_hotel_room_header][st_hotel_room_facility facility_des=""][st_hotel_room_gallery style="slide"][/vc_column][vc_column width="1/3"][st_hotel_room_price][st_hotel_room_form][/vc_column][/vc_row]
            ');
        }
    ?>
</div>
<?php get_footer( ) ?>
