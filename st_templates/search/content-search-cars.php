<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Content search cars
 *
 * Created by ShineTheme
 *
 */
$cars=new STCars();
if($st_style_search=="style_1"){
    $fields=$cars->get_search_fields();
}else{
    $fields=$cars->get_search_fields_box();
}

?>

    <h2><?php echo esc_html($st_title_search) ?></h2>
    <?php $id_page = st()->get_option('cars_search_result_page');
    if(!empty($id_page)){
        $link_action = get_the_permalink($id_page);
    }else{
        $link_action = home_url( '/' );
    }
    ?>
    <form method="get" action="<?php echo esc_url($link_action) ?>">
        <?php if(empty($id_page)): ?>
        <input type="hidden" name="post_type" value="st_cars">
        <input type="hidden" name="s" value="">
        <?php endif ?>
        <div class="<?php  if($st_direction=='horizontal') echo 'row';?>">
            <?php
            if(!empty($fields)){
                foreach($fields as $key=>$value){
                    $default=array(
                        'placeholder'=>''
                    );
                    $value=wp_parse_args($value,$default);
                    $name=$value['title'];
                    $size='4';
                    if($st_style_search=="style_1")
                    {
                        $size=$value['layout_col_normal'];
                    }else
                    {
                        $size=$value['layout_col_box'];
                    }

                    if($st_direction!='horizontal'){
                        $size='x';
                    }
                    ?>
                    <div class="col-md-<?php echo esc_attr($size); ?>">
                        <?php echo st()->load_template('cars/elements/search/field-'.$value['field_atrribute'],false,array('data'=>$value,'field_size'=>$size,'st_direction'=>$st_direction,'location_name'=>'location_name','placeholder'=>$value['placeholder'])) ?>
                    </div>
                <?php
                }
            }
            ?>
        </div>
        <button class="btn btn-primary btn-lg" type="submit"><?php st_the_language('search_for_cars') ?></button>
    </form>
