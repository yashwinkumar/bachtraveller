<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Activity element search address
 *
 * Created by ShineTheme
 *
 */
$default=array(
    'title'=>'',
    'is_required'=>'on',

);

if(isset($data)){
    extract(wp_parse_args($data,$default));
}else{
    extract($default);
}
if(!isset($field_size)) $field_size='lg';

$old_location=STInput::get('location_id');

if(empty($location_name)){
    $location_name = 's';
    $value_search = get_search_query();
}else{
    $value_search = STInput::request($location_name);
}
if($is_required == 'on'){
    $is_required = 'required';
}
?>
<div class="form-group form-group-<?php echo esc_attr($field_size)?> form-group-icon-left">
    <i class="fa fa-map-marker input-icon input-icon-highlight"></i>
    <label><?php echo balanceTags( $title)?></label>
    <input name="<?php echo esc_attr($location_name)?>" <?php echo esc_attr($is_required) ?> value="<?php if($old_location and $title=get_the_title($old_location)) echo esc_html( $title); else echo esc_html($value_search) ?>" class="typeahead_location form-control <?php echo esc_attr($is_required) ?>" placeholder="<?php echo isset($placeholder)? $placeholder: st_get_language('activity_or_us_zip_code')?>" type="text" />
    <input type="hidden"  name="location_id" value="<?php echo STInput::get('location_id') ?>" class="location_id">
</div>