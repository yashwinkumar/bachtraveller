<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Cars change search form
 *
 * Created by ShineTheme
 *
 */
$cars=new STCars();
$fields=$cars->get_search_fields_box();
?>
<h3><?php st_the_language('change_location_and_date') ?></h3>
<form method="get" action="" class="clearfix">
    <?php
        if(!get_option('permalink_structure'))
        {
            echo '<input type="hidden" name="st_cars"  value="'.st_get_the_slug().'">';
        }
    ?>
    <div class="row">
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

    <input type="submit" class="btn btn-primary btn-lg" value="<?php st_the_language('change_location_and_date') ?>">
 <!--   <button class="btn btn-primary btn-lg" type="submit"><?php /*st_the_language('search_for_cars') */?></button>-->
</form>