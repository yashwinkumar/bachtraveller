<?php 
    $room_id = get_the_ID();
    $item_id = get_post_meta(get_the_ID(), 'room_parent', true);
    $start = (STInput::request('start')) ? STInput::request('start') : date(TravelHelper::getDateFormat(), strtotime("now"));
    $end = (STInput::request('end')) ? STInput::request('end') : date(TravelHelper::getDateFormat(), strtotime("+1 day"));
    $check_in = TravelHelper::convertDateFormat($start);
    $check_out = TravelHelper::convertDateFormat($end);

    $room_num_search = STInput::request('room_num_search');
    if(!$room_num_search){
        $room_num_search = 1;
    }

    $data_price=STRoom::get_room_price(get_the_ID(),$check_in,$check_out);
    $html_price = $data_price['price'] * STInput::request('room_num_search');
?>
<div class="booking-item-dates-change">
    <?php echo STTemplate::message()?>
	<form class="single-room-form" method="get">
		<?php wp_nonce_field('room_search','room_search')?>
		<div class="input-daterange" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>">
            <div class="form-group form-group-icon-left"><i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('check_in')?></label>
                <input placeholder="<?php echo TravelHelper::getDateFormatJs(); ?>" class="form-control" value="<?php echo $start; ?>" name="start" type="text">
            </div>
            <div class="form-group form-group-icon-left"><i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('check_out')?></label>
                <input placeholder="<?php echo TravelHelper::getDateFormatJs(); ?>" class="form-control" value="<?php echo $end; ?>" name="end" type="text">
            </div>
        </div>
        <div class="form-group form-group-select-plus">
            <label><?php st_the_language('rooms')?></label>
            <?php $num_room = intval(get_post_meta($room_id, 'number_room', true)); 
            ?>
            <select name="room_num_search" class="form-control">
                <?php

                if(!$num_room || $num_room < 0)
                    $num_room = 9;
                for($i=1;$i<=$num_room;$i++){
                    $selected = selected( $i , $room_num_search,1);
                    echo "<option {$selected} value='".$i."'>".$i."</option>";
                }
                ?>
            </select>

        </div>
        <div class="form-group form-group-select-plus">
            <label><?php st_the_language('adults')?></label>
            <select name="adult_num" class="form-control">
                <?php
                $max=st()->get_option('hotel_max_adult',14);
                for($i=1;$i<=$max;$i++){
                    $select = selected( $i , STInput::get('adult_num',1));
                    echo "<option {$select} value='{$i}'>{$i}</option>";
                }?>
            </select>
        </div>
        <div class="form-group form-group-select-plus">
            <label><?php st_the_language('children')?></label>
            
            <select name="child_num" class="form-control">
                <?php

                $max=st()->get_option('hotel_max_child',14);
                for($i=0;$i<=$max;$i++){

                    $select=selected($i,STInput::get('child_num',0));
                    echo "<option {$select} value='{$i}'>{$i}</option>";
                }?>
            </select>
        </div>
        <div class="text-right">
            <input class=" btn btn-primary btn_hotel_booking" value="Book now" type="submit">
        </div>
        <input name="action" value="hotel_add_to_cart" type="hidden">
        <input name="item_id" value="<?php echo $item_id; ?>" type="hidden">
        <input name="room_id" value="<?php echo $room_id; ?>" type="hidden">
        <input type="hidden" name="data_price" value='<?php echo serialize($data_price) ?>'>
        <input name="price" value="<?php echo esc_attr($data_price['price']) ?>" type="hidden">
        <input type="hidden" name="room_num_search" value="<?php echo $room_num_search; ?>">
        <input type="hidden" name="update_price" value="update_price">
	</form>
</div>