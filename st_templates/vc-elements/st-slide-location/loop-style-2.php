<?php
$thumbnail=get_post_thumbnail_id();
$img=wp_get_attachment_url($thumbnail);
if(empty($img)){
    $img = get_template_directory_uri().'/img/no-image.png';
}
$class_bg_img = Assets::build_css(" background: url(".$img.") ");
$logo = get_post_meta( get_the_ID() , 'st_logo_location' , true);

$c=TravelHelper::get_location_temp();
?>
<div class="bg-holder full">
    <div class="bg-mask"></div>
    <div class="bg-blur <?php echo esc_attr($class_bg_img) ?>" ></div>
    <div class="bg-content">
        <div class="container">
            <div class="loc-info text-right hidden-xs hidden-sm">
                <h3 class="loc-info-title">
                    <?php if(!empty($logo)){ ?>
                       <img src="<?php echo balanceTags($logo) ?>" alt="Logo" title="Image Title" />
                    <?php } ?>
                    <?php the_title() ?>
                </h3>
                <?php if($st_weather == 'yes'){ ?>
                <p class="loc-info-weather">
                    <span class="loc-info-weather-num"><?php echo esc_html($c['temp']); ?></span>
                    <?php echo balanceTags($c['icon']) ?>
                </p>
                <?php } ?>
                <ul class="loc-info-list">
                    <?php
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
                    <?php STLanguage::st_the_language('explore'); ?>
                </a>
            </div>
        </div>
    </div>
</div>