<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars search form
 *
 * Created by ShineTheme
 *
 */
$cars=new STCars();
$fields=$cars->get_search_fields_box();
?>
<h3><?php st_the_language('search_for_cars') ?></h3>
<form method="get" action="<?php the_permalink() ?>">
    <div class="row">
        <?php
            if(!get_option('permalink_structure'))
            {
                echo '<input type="hidden" name="page_id"  value="'.STInput::request('page_id').'">';
            }
        ?>
        <input type="hidden" name="layout" value="<?php echo STInput::get('layout') ?>">
        <input type="hidden" name="style" value="<?php echo STInput::get('style') ?>">
        <?php
        if(!empty($fields)){
            foreach($fields as $key=>$value){
                $name=$value['title'];
                $size=$value['layout_col_box'];
                ?>
                <div class="col-md-<?php echo esc_attr($size); ?>">
                    <?php echo st()->load_template('cars/elements/search/field-'.$value['field_atrribute'],false,array('data'=>$value)) ?>
                </div>
        <?php
            }
        }
        ?>
    </div>
    <input type="submit" class="btn btn-primary btn-lg" value="<?php st_the_language('search_for_cars') ?>">
 <!--   <button class="btn btn-primary btn-lg" type="submit"><?php /*st_the_language('search_for_cars') */?></button>-->
</form>