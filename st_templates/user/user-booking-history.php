<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * User booking history
 *
 * Created by ShineTheme
 *
 */
$class_user = new STUser_f();
$html_all = $class_user->get_book_history('');
$html_pending = $class_user->get_book_history('pending');
$html_complete = $class_user->get_book_history('complete');
$html_canceled = $class_user->get_book_history('canceled');
?>
<div class="st-create">
    <h2><?php STUser_f::get_title_account_setting() ?></h2>
</div>
    <div class="tabbable">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#tab-all" data-toggle="tab"><?php _e("All",ST_TEXTDOMAIN) ?></a></li>
            <li><a href="#tab-pending" data-toggle="tab"><?php _e("Pending",ST_TEXTDOMAIN) ?></a></li>
            <li><a href="#tab-complete" data-toggle="tab"><?php _e("Complete",ST_TEXTDOMAIN) ?></a></li>
            <li><a href="#tab-canceled" data-toggle="tab"><?php _e("Canceled",ST_TEXTDOMAIN) ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-all">
                <?php
                if(!empty($html_all)){?>
                    <table class="table table-bordered table-striped table-booking-history">
                        <thead>
                        <tr>
                            <th><?php st_the_language('user_type')?></th>
                            <th><?php st_the_language('user_title')?></th>
                            <th><?php st_the_language('user_location') ?></th>
                            <th><?php st_the_language('user_order_date')?></th>
                            <th><?php st_the_language('user_execution_date') ?></th>
                            <th><?php st_the_language('user_cost') ?></th>
                            <th><?php _e("Status",ST_TEXTDOMAIN) ?></th>
                            <th><?php st_the_language('action') ?></th>
                        </tr>
                        </thead>
                        <tbody id="data_history_book">
                        <?php
                        echo balanceTags($html_all);
                        ?>
                        </tbody>
                    </table>
                    <span class="btn btn-primary btn_load_his_book" data-per="2" data-type=""><?php st_the_language('user_load_more') ?></span>
                <?php
                }else{
                    echo '<h5>'.st_the_language('user_no_booking_history').'</h5>';
                } ?>
            </div>
            <div class="tab-pane fade" id="tab-pending">
                <?php
                if(!empty($html_pending)){?>
                    <table class="table table-bordered table-striped table-booking-history">
                        <thead>
                        <tr>
                            <th><?php st_the_language('user_type')?></th>
                            <th><?php st_the_language('user_title')?></th>
                            <th><?php st_the_language('user_location') ?></th>
                            <th><?php st_the_language('user_order_date')?></th>
                            <th><?php st_the_language('user_execution_date') ?></th>
                            <th><?php st_the_language('user_cost') ?></th>
                            <th><?php _e("Status",ST_TEXTDOMAIN) ?></th>
                            <th><?php st_the_language('action') ?></th>
                        </tr>
                        </thead>
                        <tbody id="data_history_book">
                        <?php
                        echo balanceTags($html_pending);
                        ?>
                        </tbody>
                    </table>
                    <span class="btn btn-primary btn_load_his_book" data-per="2" data-type="pending" ><?php st_the_language('user_load_more') ?></span>
                <?php
                }else{
                    echo '<h5>'.st_the_language('user_no_booking_history').'</h5>';
                } ?>
            </div>
            <div class="tab-pane fade" id="tab-complete">
                <?php
                if(!empty($html_complete)){?>
                    <table class="table table-bordered table-striped table-booking-history">
                        <thead>
                        <tr>
                            <th><?php st_the_language('user_type')?></th>
                            <th><?php st_the_language('user_title')?></th>
                            <th><?php st_the_language('user_location') ?></th>
                            <th><?php st_the_language('user_order_date')?></th>
                            <th><?php st_the_language('user_execution_date') ?></th>
                            <th><?php st_the_language('user_cost') ?></th>
                            <th><?php _e("Status",ST_TEXTDOMAIN) ?></th>
                            <th><?php st_the_language('action') ?></th>
                        </tr>
                        </thead>
                        <tbody id="data_history_book">
                        <?php
                        echo balanceTags($html_complete);
                        ?>
                        </tbody>
                    </table>
                    <span class="btn btn-primary btn_load_his_book" data-per="2" data-type="complete"><?php st_the_language('user_load_more') ?></span>
                <?php
                }else{
                    echo '<h5>'.st_the_language('user_no_booking_history').'</h5>';
                } ?>
            </div>
            <div class="tab-pane fade" id="tab-canceled">
                <?php
                if(!empty($html_canceled)){?>
                    <table class="table table-bordered table-striped table-booking-history">
                        <thead>
                        <tr>
                            <th><?php st_the_language('user_type')?></th>
                            <th><?php st_the_language('user_title')?></th>
                            <th><?php st_the_language('user_location') ?></th>
                            <th><?php st_the_language('user_order_date')?></th>
                            <th><?php st_the_language('user_execution_date') ?></th>
                            <th><?php st_the_language('user_cost') ?></th>
                            <th><?php _e("Status",ST_TEXTDOMAIN) ?></th>
                            <th><?php st_the_language('action') ?></th>
                        </tr>
                        </thead>
                        <tbody id="data_history_book">
                        <?php
                        echo balanceTags($html_canceled);
                        ?>
                        </tbody>
                    </table>
                    <span class="btn btn-primary btn_load_his_book" data-per="2"  data-type="canceled"><?php st_the_language('user_load_more') ?></span>
                <?php
                }else{
                    echo '<h5>'.st_the_language('user_no_booking_history').'</h5>';
                } ?>
            </div>
        </div>
    </div>
