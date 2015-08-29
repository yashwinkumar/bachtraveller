<?php
/**
* @since 1.1.3
**/
get_header();
get_template_part('breadcrumb');
?>
<div class="booking-item-details">
	<?php
        $detail_tour_layout = apply_filters('st_rental_room_layout','');
        if($detail_tour_layout && !empty($detail_tour_layout))
        {
            echo STTemplate::get_vc_pagecontent($detail_tour_layout);
        }else{
            echo do_shortcode('
                [vc_row][vc_column width="2/3"][st_rental_room_header][st_rental_room_content][st_rental_room_gallery style="slide"][/vc_column][vc_column width="1/3"][st_related_rental_room header_title="Related Rental Room" number_of_room="5" show_excerpt="yes"][/vc_column][/vc_row]
            ');
        }
    ?>
</div>
<?php get_footer( ) ?>
