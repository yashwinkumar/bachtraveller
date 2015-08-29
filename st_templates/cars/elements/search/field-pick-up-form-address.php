<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars element search field pick up form
 *
 * Created by ShineTheme
 *
 */
$default=array(
    'title'=>'',
    'is_required'=>'on',
    'placeholder'=>''
);
if(isset($data)){
    extract(wp_parse_args($data,$default));
}else{
    extract($default);
}
if($is_required == 'on'){
    $is_required = 'required';
}
?>
<div class="form-group form-group-lg form-group-icon-left">
    <i class="fa fa-map-marker input-icon input-icon-highlight"></i>
    <label><?php echo esc_html($title)?></label>
    <input name="pick-up" data-country="" <?php echo esc_attr($is_required) ?> value="<?php echo STInput::get('pick-up') ?>" class="typeahead_pick_up_address form-control <?php echo esc_attr($is_required) ?>" placeholder="<?php echo ($placeholder)?$placeholder: st_get_language('car_city_airport_or_us_zip_code')?>" type="text" />
</div>