<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Activity success payment item row
 *
 * Created by ShineTheme
 *
 */
$object_id=$key;
$order_token_code=STInput::get('order_token_code');

if($order_token_code)
{
    $order_code=STOrder::get_order_id_by_token($order_token_code);

}
$total=0;
$check_in=$data['data']['check_in'];
$check_out=$data['data']['check_out'];
$number=$data['number'];
$price=$data['price'];
if(!$number) $number=1;
$total+= $price*$number;
$link='';
if(isset($object_id) and $object_id){
    $link=get_permalink($object_id);
}
?>
<?php $type_price = get_post_meta($object_id,'type_price',true); ?>
<tr>
    <td><?php echo esc_html($i) ?></td>
    <td>
        <a href="<?php echo esc_url($link)?>" target="_blank">
        <?php echo get_the_post_thumbnail($key,array(360,270,'bfi_thumb'=>true),array('style'=>'max-width:100%;height:auto'))?>
        </a>
    </td>
    <td>
        <p><strong><?php st_the_language('booking_address') ?></strong> <?php echo get_post_meta($object_id,'address',true)?> </p>
        <p><strong><?php st_the_language('booking_web') ?></strong> <?php echo get_post_meta($object_id,'contact_web',true)?> </p>
        <p><strong><?php st_the_language('booking_email') ?></strong> <?php echo get_post_meta($object_id,'contact_email',true)?> </p>
        <p><strong><?php st_the_language('booking_phone') ?></strong> <?php echo get_post_meta($object_id,'contact_phone',true)?> </p>
        <p><strong><?php st_the_language('booking_activity') ?></strong> <?php  echo get_the_title($object_id)?></p>

        <?php if($type_price == 'people_price'){ ?>
            <p><strong><?php _e('Adult Price',ST_TEXTDOMAIN) ?>: </strong>
                <?php echo get_post_meta($order_id,'adult_number',true);?> x
                <?php echo TravelHelper::format_money(get_post_meta($object_id,'adult_price',true));?>
            </p>
            <p><strong><?php _e('Child Price',ST_TEXTDOMAIN) ?>: </strong>
                <?php echo get_post_meta($order_id,'child_number',true);?> x
                <?php echo TravelHelper::format_money(get_post_meta($object_id,'child_price',true));?>
            </p>
        <?php }else{ ?>
            <p><strong><?php st_the_language('booking_amount') ?></strong> <?php echo esc_html($number)?></p>
            <p><strong><?php st_the_language('booking_price') ?></strong> <?php
                echo TravelHelper::format_money($price);
                ?></p>
        <?php } ?>
        <p><strong><?php st_the_language('booking_check_in') ?></strong> <?php echo @date(get_option('date_format'),strtotime($check_in)) ?></p>
        <p><strong><?php st_the_language('booking_check_out') ?></strong> <?php echo @date(get_option('date_format'),strtotime($check_out)) ?></p>
    </td>
</tr>