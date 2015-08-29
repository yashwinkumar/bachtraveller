<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * User create room
 *
 * Created by ShineTheme
 *
 */
?>
<div class="st-create">
    <h2><?php st_the_language('user_create_room') ?></h2>
</div>
<div class="msg">
    <?php echo STUser_f::get_msg(); ?>
</div>
<form action="" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('user_setting','st_insert_room'); ?>
    <div class="form-group form-group-icon-left">
        <i class="fa  fa-file-text input-icon input-icon-hightlight"></i>
        <label><?php st_the_language('user_create_room_title') ?></label>
        <input id="title" name="title" type="text" placeholder="<?php st_the_language('user_create_room_title') ?>" class="form-control">
        <div class="st_msg console_msg_title"></div>
    </div>
    <div class="form-group form-group-icon-left">
        <label><?php  st_the_language('user_create_room_content') ?></label>
        <?php wp_editor('','st_content'); ?>
    </div>
    <div class="form-group form-group-icon-left">
        <label><?php st_the_language('user_create_room_description') ?></label>
        <textarea id="desc" name="desc" class="form-control"></textarea>
        <div class="st_msg console_msg_desc"></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $category = STUser_f::get_list_taxonomy('room_type');
            if(!empty($category)):
                ?>
                <div class="form-group form-group-icon-left">
                    <label> <?php st_the_language('user_create_room_type') ?></label>
                    <div class="row">
                        <?php foreach($category as $k=>$v): ?>
                            <div class="col-md-3">
                                <div class="checkbox-inline checkbox-stroke">
                                    <label>
                                        <input name="id_category[]" class="i-check" type="checkbox" value="<?php echo esc_attr($k) ?>" /><?php echo esc_html($v) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                </div>
            <?php endif ?>
        </div>
        <div class="col-md-12">
            <?php
            $taxonomy = STUser_f::get_list_value_taxonomy('hotel_room');
            if(!empty($taxonomy)):
                ?>
                <div class="form-group form-group-icon-left ">
                    <label> <?php st_the_language('user_create_room_attributes') ?></label>
                    <div class="row">
                        <?php foreach($taxonomy as $k=>$v): ?>
                            <div class="col-md-3">
                                <div class="checkbox-inline checkbox-stroke">
                                    <label>
                                        <i class="<?php echo esc_html($v['icon']) ?>"></i>
                                        <input name="taxonomy[]" class="i-check" type="checkbox" value="<?php echo esc_attr($v['value'].','.$v['taxonomy']) ?>" /><?php echo esc_html($v['label']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <label><?php st_the_language('user_create_room_select_hotel') ?></label>
                <input type="text" name="room_parent" placeholder="<?php st_the_language('user_create_room_search') ?>" id="room_parent" class="st_post_select" data-author="<?php echo esc_attr($data->ID)?>" data-post-type="st_hotel" style="width: 100%">
                <div class="st_msg console_msg_room_parent"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <label><?php st_the_language('user_create_room_featured_image') ?> </label>
                <input id="featured-image" name="featured-image" type="file"  class="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_number_room') ?></label>
                <input id="number_room" name="number_room" type="number" min="1" value="1"  class="form-control">
                <div class="st_msg console_msg_number_room"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-money input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_price_per_night') ?></label>
                <input id="price" name="price" type="text" placeholder="<?php st_the_language('user_create_room_price_per_night') ?>" class="form-control">
                <div class="st_msg console_msg_price"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php _e("Discount Rate",ST_TEXTDOMAIN) ?></label>
                <input id="discount_rate" name="discount_rate" type="text" placeholder="<?php _e("Discount Rate",ST_TEXTDOMAIN) ?>" class="form-control">
                <div class="st_msg console_msg_discount_rate"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group form-group-icon-left">
                <label><?php _e("Custom Price",ST_TEXTDOMAIN) ?></label>
            </div>
        </div>
        <div class="content_data_price">

        </div>
        <div class="col-md-12">
            <div class="form-group form-group-icon-left">
                <button id="btn_add_custom_price" class="btn btn-info" type="button"><?php _e("Add Price Custom",ST_TEXTDOMAIN) ?></button>
                <br>
            </div>
        </div>
    </div>
    <h4><?php st_the_language('user_create_room_facility') ?></h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_adults_number') ?></label>
                <input id="adult_number" name="adult_number" type="number" min="1" value="1"  class="form-control">
                <div class="st_msg console_msg_adult_number"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_children_number') ?></label>
                <input id="children_number" name="children_number" type="number" min="1" value="1"  class="form-control">
                <div class="st_msg console_msg_children_number"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_beds_number') ?></label>
                <input id="bed_number" name="bed_number" type="number" value="1" min="1" class="form-control">
                <div class="st_msg console_msg_bed_number"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                <label><?php st_the_language('user_create_room_room_footage')?></label>
                <input id="room_footage" name="room_footage" type="text" placeholder="<?php st_the_language('user_create_room_room_footage')?>" class="form-control">
                <div class="st_msg console_msg_room_footage"></div>
            </div>
        </div>
    </div>
    <input  type="button" id="btn_check_insert_post_type_room"  class="btn btn-primary" value="SUBMIT">
    <input name="btn_insert_post_type_room" id="btn_insert_post_type_room" type="submit"  class="btn btn-primary hidden" hidden="" value="SUBMIT">
</form>

<div class="data_price_html" style="display: none">
    <div class="item">
        <div class="col-md-4">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label><?php _e("Start Date",ST_TEXTDOMAIN) ?></label>
                <input id="st_start_date" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>" name="st_start_date[]" type="text" placeholder="<?php _e("Start Date",ST_TEXTDOMAIN) ?>" class="form-control date-pick">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label><?php _e("End Date",ST_TEXTDOMAIN) ?></label>
                <input id="st_end_date" data-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>" name="st_end_date[]" type="text" placeholder="<?php _e("End Date",ST_TEXTDOMAIN) ?>" class="form-control date-pick">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group form-group-icon-left">
                <i class="fa fa-money input-icon input-icon-hightlight"></i>
                <label><?php _e("Price",ST_TEXTDOMAIN) ?></label>
                <input id="st_price" name="st_price[]" type="text" placeholder="<?php _e("Price",ST_TEXTDOMAIN) ?>" class="form-control">
            </div>
        </div>
        <div class="col-md-1">
            <input name="st_priority[]" value="0" type="hidden" class="">
            <input name="st_price_type[]" value="default" type="hidden" class="">
            <input name="st_status[]" value="1" type="hidden" class="">
            <div class="btn btn-danger btn_del_price_custom" style="margin-top: 27px"><?php _e("Del",ST_TEXTDOMAIN) ?></div>
        </div>
    </div>
</div>