<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel field location
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
    <i class="fa fa-map-marker input-icon"></i>
    <label><?php echo esc_html( $title)?></label>
    <input <?php echo esc_attr($is_required) ?> name="<?php echo esc_attr($location_name)?>" value="<?php if($old_location and $title=get_the_title($old_location)) echo esc_html( $title); else echo esc_html($value_search) ?>" class="typeahead_location form-control <?php echo esc_attr($is_required) ?>" placeholder="<?php echo ($placeholder)?$placeholder: st_get_language('city_name_or_zip_code')?>" type="text" />
    <input  type="hidden" name="location_id" value="<?php echo STInput::get('location_id') ?>" class="location_id">
</div>