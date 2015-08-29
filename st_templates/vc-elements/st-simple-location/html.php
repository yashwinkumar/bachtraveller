<?php  $c=TravelHelper::get_location_temp();
?>
<div class="loc-info text-right hidden-xs hidden-sm">
    <h3 class="loc-info-title"><?php the_title() ?></h3>
    <p class="loc-info-weather">
       <span class="loc-info-weather-num">
       <?php echo balanceTags($c['temp']) ?>
       </span>
       <?php echo balanceTags($c['icon']) ?>
    </p>
    <ul class="loc-info-list">
        <?php

        $hotel=new STHotel();
        if($hotel->is_available())
        {
            $min_price = get_post_meta(get_the_ID() , 'min_price_st_hotel' , true );
            $min_price = TravelHelper::format_money($min_price);
            $offer = get_post_meta(get_the_ID() , 'offer_st_hotel' , true );
            if(!empty($min_price) and !empty($offer)){
                $page_search = st_get_page_search_result('st_hotel');
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type=st_hotel&location_id='.get_the_ID()));
                }
                if($offer < 2){
                    $offer = $offer." ".__("Hotel from",ST_TEXTDOMAIN);
                }else{
                    $offer = $offer." ".__("Hotels from",ST_TEXTDOMAIN);
                }
                echo '<li><a href="'.$link.'"><i class="fa fa-building-o"></i> '.$offer.' '.$min_price.'/'.STLanguage::st_get_language('night').'</a></li>';
            }
        }


        $rental =new STRental();

        if($rental->is_available())
        {
            $min_price = get_post_meta(get_the_ID() , 'min_price_rental' , true );
            $min_price = TravelHelper::format_money($min_price);
            $offer = get_post_meta(get_the_ID() , 'offer_st_rental' , true );
            if(!empty($min_price) and !empty($offer)){
                $page_search = st_get_page_search_result('st_rental');
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type=st_rental&location_id='.get_the_ID()));
                }
                if($offer < 2){
                    $offer = $offer." ".__("Rental from",ST_TEXTDOMAIN);
                }else{
                    $offer = $offer." ".__("Rentals from",ST_TEXTDOMAIN);
                }
                echo '<li><a href="'.$link.'"><i class="fa fa-home"></i> '.$offer.' '.$min_price.'/'.STLanguage::st_get_language('night').'</a></li>';
            }
        }

        $car=new STCars();
        if($car->is_available())
        {
            $min_price = get_post_meta(get_the_ID() , 'min_price_st_cars' , true );
            $min_price = TravelHelper::format_money($min_price);
            $offer = get_post_meta(get_the_ID() , 'offer_st_cars' , true );
            if(!empty($min_price) and !empty($offer)){
                $page_search = st_get_page_search_result('st_cars');
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type=st_cars&location_id='.get_the_ID()));
                }
                if($offer < 2){
                    $offer = $offer." ".__("Car from",ST_TEXTDOMAIN);
                }else{
                    $offer = $offer." ".__("Cars from",ST_TEXTDOMAIN);
                }
                echo '<li><a href="'.$link.'"><i class="fa fa-car"></i> '.$offer.' '.$min_price.'/'.STLanguage::st_get_language('day').'</a></li>';
            }
        }

        $tour=new STTour();
        if($tour->is_available())
        {
            $min_price = get_post_meta(get_the_ID() , 'min_price_st_tours' , true );
            $min_price = TravelHelper::format_money($min_price);
            $offer = get_post_meta(get_the_ID() , 'offer_st_tours' , true );
            if(!empty($min_price) and !empty($offer)){
                $page_search = st_get_page_search_result('st_tours');
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type=st_tours&location_id='.get_the_ID()));
                }
                if($offer < 2){
                    $offer = $offer." ".__("Tour from",ST_TEXTDOMAIN);
                }else{
                    $offer = $offer." ".__("Tours from",ST_TEXTDOMAIN);
                }
                echo '<li><a href="'.$link.'"><i class="fa fa-bolt"></i> '.$offer.' '.$min_price.'</a></li>';
            }
        }

        $activity=new STActivity();
        if($activity->is_available())
        {
            $min_price = get_post_meta(get_the_ID() , 'min_price_st_activity' , true );
            $min_price = TravelHelper::format_money($min_price);
            $offer = get_post_meta(get_the_ID() , 'offer_st_activity' , true );
            if(!empty($offer)){
                $page_search = st_get_page_search_result('st_activity');
                if(!empty($page_search)){
                    $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
                }else{
                    $link = home_url(esc_url('?s=&post_type=st_activity&location_id='.get_the_ID()));
                }
                if($offer < 2){
                    $offer = $offer." ".__("Activity this Week",ST_TEXTDOMAIN);
                }else{
                    $offer = $offer." ".__("Activities this Week",ST_TEXTDOMAIN);
                }
                echo '<li><a href="'.$link.'"><i class="fa fa-bolt"></i> '.$offer.'</a></li>';
            }
        }

        ?>
    </ul>
    <?php
    $page_search = st_get_page_search_result($st_type);
    if(!empty($page_search)){
        $link = add_query_arg(array('location_id'=>get_the_ID()),get_the_permalink($page_search));
    }else{
        $link = home_url(esc_url('?s=&post_type='.$st_type.'&location_id='.get_the_ID()));
    }
    ?>
    <a class="btn btn-white btn-ghost mt10" href="<?php echo esc_url($link) ?>">
        <i class="fa fa-angle-right"></i>
        <?php echo STLanguage::st_get_language('explore') ?>
    </a>
</div>