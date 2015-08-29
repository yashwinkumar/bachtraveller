<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * User Reports
 *
 * Created by ShineTheme
 *
 */
?>
<div class="st-create">
    <h2><?php _e("Reports",ST_TEXTDOMAIN) ?></h2>
</div>
<?php
$date_fist = STUser_f::get_fist_year_reports();
$type = STInput::request( 'order_by' ,'month' );
$year_now = date("Y");
switch($type){
    case "15days":
        if(st_check_is_checkout_woocomerce(true)){
            $data_reports = STUser_f::get_info_reports('15days',false,false,false,STInput::request('date_start'),STInput::request('date_end'));
        }else{
            $data_reports = STUser_f::get_info_reports_old('15days',false,false,false,STInput::request('date_start'),STInput::request('date_end'));
        }
        $title =__("Last 30 days",ST_TEXTDOMAIN);
        break;
    case "month":
    case "quarter":
        if(STInput::request('data_year')){
            $year = STInput::request('data_year');
        }else{
            $year = $year_now;
        }
        if(st_check_is_checkout_woocomerce(true)){
            $data_reports = STUser_f::get_info_reports('month',$year);
        }else{
            $data_reports = STUser_f::get_info_reports_old('month',$year);
        }
        $title = __("Year: ",ST_TEXTDOMAIN).$year;
        break;
    case "custom_date":
        if(st_check_is_checkout_woocomerce(true)){
            $data_reports = STUser_f::get_info_reports('custom_date',false,false,false,STInput::request('date_start'),STInput::request('date_end'));
        }else{
            $data_reports = STUser_f::get_info_reports_old('custom_date',false,false,false,STInput::request('date_start'),STInput::request('date_end'));
        }
        $title = __("Date: ",ST_TEXTDOMAIN).date('d/m/Y',strtotime(STInput::request('date_start'))).' -> '.date('d/m/Y',strtotime(STInput::request('date_end')));
        break;
    case "year":
        if(st_check_is_checkout_woocomerce(true)){
            $data_reports = STUser_f::get_info_reports('year',false,STInput::request('year_start'),STInput::request('year_end'));
        }else{
            $data_reports = STUser_f::get_info_reports_old('year',false,STInput::request('year_start'),STInput::request('year_end'));
        }
        $title = __("Year: ",ST_TEXTDOMAIN).STInput::request('year_start').' -> '.STInput::request('year_end');
        break;
    default :
        if(st_check_is_checkout_woocomerce(true)){
            $data_reports = STUser_f::get_info_reports();
        }else{
            $data_reports = STUser_f::get_info_reports_old();
        }
        $title = '';
        break;
}
?>

<div class="row">
    <div class="col-md-3">
        <div id="callout-navbar-breakpoint" class="bs-callout bs-callout-info">
            <h4 id="changing-the-collapsed-mobile-navbar-breakpoint">
                <?php echo TravelHelper::format_money($data_reports['average_total']) ?>
            </h4>
            <p><?php _e("Net sale(s) in this period",ST_TEXTDOMAIN) ?> </p>
        </div>
    </div>
    <div class="col-md-3">
        <div id="callout-navbar-breakpoint" class="bs-callout bs-callout-danger">
            <h4 id="changing-the-collapsed-mobile-navbar-breakpoint">
                <?php echo esc_html($data_reports['number_orders']) ?>
            </h4>
            <p><?php _e("Order(s) placed",ST_TEXTDOMAIN) ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div id="callout-navbar-breakpoint" class="bs-callout bs-callout-success">
            <h4 id="changing-the-collapsed-mobile-navbar-breakpoint">
                <?php echo esc_html($data_reports['number_items']) ?>
            </h4>
            <p><?php _e("Item(s) purchased",ST_TEXTDOMAIN) ?></p>
        </div>
    </div>
</div>
<?php
$_number_order = $data_reports['number_orders'];
$_data_post = $data_reports['post_type'];
if($_number_order > 0):
?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th style="width: 130px;"><?php _e("Type",ST_TEXTDOMAIN) ?></th>
                <th><?php _e("Total Price",ST_TEXTDOMAIN) ?></th>
                <th style="width: 140px;"><?php _e("Item(s) purchased",ST_TEXTDOMAIN) ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($_data_post as $k=>$v){?>
                    <tr>
                        <th scope="row"><strong><?php $obj = get_post_type_object( $k ); echo esc_html($obj->labels->singular_name); ?></strong></th>
                        <td><?php echo TravelHelper::format_money($v['average_total']) ?></td>
                        <td><?php echo esc_html($v['number_items']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="head_reports_h2"><?php _e("Statement",ST_TEXTDOMAIN) ?></h2>
        <div class="head_reports">
            <div class="head_control">
                <div class="head_time">
                    <?php
                    $number_year = $year_now - $date_fist['y'] ;
                    if($number_year > 1){
                        $fist_year = $year_now - 1 ;
                    }else{
                        $fist_year = $date_fist['y'] ;
                    }
                    ?>
                    <a href="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'year','year_start'=>$fist_year ,'year_end'=>$year_now ),get_the_permalink()))?>"><?php _e("All Time",ST_TEXTDOMAIN) ?></a>
                    <?php
                    for($i = $fist_year ; $i <= $year_now ; $i++){
                        echo ' / <a href="'.esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'month','data_year'=>($i) ),get_the_permalink())) .'">'.($i).'</a>';
                    }
                    ?>
                </div>
                <div class="head_btn">
                    <a class="btn btn-primary" href="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'15days','date_start'=>$date_fist['last_15days'] , 'date_end'=>$date_fist['date_now'] ),get_the_permalink()))?>"><?php _e("Last 30 days",ST_TEXTDOMAIN) ?></a>
                    <button class="btn btn-primary btn_custom_more_option"><?php _e("More Option",ST_TEXTDOMAIN) ?></button>
                    <button class="btn btn-primary btn_custom_period"><?php _e("Period",ST_TEXTDOMAIN) ?></button>
                    <div class="group_period_input div_custom_period <?php if(STInput::request('is_period')) echo "show_custom" ?>">
                        <form class="" action="<?php the_permalink(get_the_ID()) ?>">
                            <input type="hidden" name="sc" value="reports">
                            <input type="hidden" name="order_by" value="custom_date">
                            <input type="hidden" name="is_period" value="true">
                            <input class="form-control date-pick" data-date-format="yyyy-mm-dd" name="date_start" value="<?php echo STInput::request('date_start') ?>" placeholder="<?php _e("Start Date",ST_TEXTDOMAIN) ?>">
                            <i class="fa  fa-arrow-right"></i>
                            <input class="form-control date-pick" data-date-format="yyyy-mm-dd" name="date_end" value="<?php echo STInput::request('date_end') ?>" placeholder="<?php _e("End Date",ST_TEXTDOMAIN) ?>">
                            <button class="btn btn-primary"><?php _e("Search",ST_TEXTDOMAIN) ?></button>
                        </form>
                    </div>
                    <div class="group_period_input div_custom_more_option <?php if(STInput::request('more_option_type')) echo "show_custom" ?>" >
                        <select class="st_reports_more_option">
                            <option <?php if(STInput::request('more_option_type') == 'today') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['date_now'] , 'date_end'=>$date_fist['date_now'] ,'more_option_type'=>'today'),get_the_permalink()))?>"><?php _e("Today",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'yesterday') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['yesterday'] , 'date_end'=>$date_fist['date_now'] ,'more_option_type'=>'yesterday'),get_the_permalink()))?>"><?php _e("Yesterday",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'this_week') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['the_week']['this_week']['start'] , 'date_end'=>$date_fist['the_week']['this_week']['end'] ,'more_option_type'=>'this_week'),get_the_permalink()))?>"><?php _e("This Week",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_week') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['the_week']['last_week']['start'] , 'date_end'=>$date_fist['the_week']['last_week']['end'] ,'more_option_type'=>'last_week'),get_the_permalink()))?>"><?php _e("Last Week",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_7days') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['last_7days'] , 'date_end'=>$date_fist['date_now'],'more_option_type'=>'last_7days' ),get_the_permalink()))?>"><?php _e("Last 7 Days",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_30days') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['last_15days'] , 'date_end'=>$date_fist['date_now'],'more_option_type'=>'last_30days' ),get_the_permalink()))?>"><?php _e("Last 30 Days",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_60days') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['last_60days'] , 'date_end'=>$date_fist['date_now'],'more_option_type'=>'last_60days' ),get_the_permalink()))?>"><?php _e("Last 60 Days",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_90days') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'custom_date','date_start'=>$date_fist['last_90days'] , 'date_end'=>$date_fist['date_now'],'more_option_type'=>'last_90days' ),get_the_permalink()))?>"><?php _e("Last 90 Days",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'this_year') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'month','data_year'=>($year_now),'more_option_type'=>'this_year' ),get_the_permalink())) ?>"><?php _e("This Year",ST_TEXTDOMAIN) ?></option>
                            <option <?php if(STInput::request('more_option_type') == 'last_year') echo 'selected' ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'month','data_year'=>$date_fist['last_year'],'more_option_type'=>'last_year' ),get_the_permalink())) ?>"><?php _e("Last Year",ST_TEXTDOMAIN) ?></option>
                        </select>
                        <button class="btn btn-primary btn_submit_custom_more_option"><?php _e("Search",ST_TEXTDOMAIN) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <?php if($data_reports['number_orders'] < 1){ ?>
            <div id="st_data_reports hidden"></div>
            <h2> <?php _e("No data",ST_TEXTDOMAIN) ?></h2>
        <?php }else{ ?>
            <?php if(STInput::request('order_by') == 'month' or STInput::request('order_by') == 'quarter'){ ?>
                <div>
                    <?php _e("Show by",ST_TEXTDOMAIN) ?> :
                    <select class="st_reports_show_by">
                        <option <?php if(STInput::request('order_by') == 'month') echo 'selected'; ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'month','data_year'=>STInput::request('data_year') ),get_the_permalink()))?>"><?php _e("Month",ST_TEXTDOMAIN) ?></option>
                        <option <?php if(STInput::request('order_by') == 'quarter') echo 'selected'; ?> value="<?php echo esc_url(add_query_arg(array('sc'=>'reports','order_by'=>'quarter','data_year'=>STInput::request('data_year') ),get_the_permalink()))?>"><?php _e("Quarter",ST_TEXTDOMAIN) ?></option>
                    </select>
                </div>
            <?php } ?>
            <div id="st_data_reports"></div>
        <?php } ?>
    </div>
</div>
<input type="hidden" class="st_reports_order_by" value="<?php echo STInput::request( 'order_by' ,'month' ) ?>">
<?php $data_js = STUser_f::get_js_reports($data_reports,$type,STInput::request('date_start'),STInput::request('date_end'))?>
<script class="code" type="text/javascript">
    <?php
        echo $data_js['data_key'];
        echo $data_js['data_lable'];
        echo $data_js['data_value'];
        echo $data_js['data_ticks'];
    ?>
    jQuery(document).ready(function($){
        if(data_value.length >0) {
            var data_reports = $.jqplot('st_data_reports', data_value, {
                title : '<?php  echo esc_html($title) ?>',
                animate: true,
                seriesDefaults: {
                    pointLabels: {show: true},
                    <?php if($type == 'quarter'){ ?>
                    renderer: $.jqplot.BarRenderer,
                    <?php }elseif($type=='year' and STInput::request('year_end') - STInput::request('year_start') < 4){ ?>
                    renderer: $.jqplot.BarRenderer,
                    <?php }elseif($type=='custom_date' and $number_days = STDate::date_diff(strtotime(STInput::request('date_start')),strtotime(STInput::request('date_end')))  <= 7){ ?>
                    renderer: $.jqplot.BarRenderer,
                    <?php } ?>
                    rendererOptions: {fillToZero: true}
                },
                legend: {
                    show: true,
                    placement: 'insideGrid'
                },
                series: data_lable,
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        ticks: data_ticks
                    },
                    yaxis: {
                        pad: 1.05,
                        tickOptions: {formatString: '$%d'}
                    }
                }
            });
        }

        $('.st_reports_show_by').change(function(event){
            var url=$(this).val();
            if(url){
                window.location.href=url;
            }
        });

        $(".btn_submit_custom_more_option").click(function(){
            var url= $('.st_reports_more_option').val();
            if(url){
                window.location.href=url;
            }
        });

        $('.btn_custom_period').click(function(){
            $('.div_custom_more_option').css('display','none');
            $('.div_custom_period').css('display','inline-block');
        });
        $('.btn_custom_more_option').click(function(){
            $('.div_custom_period').css('display','none');
            $('.div_custom_more_option').css('display','inline-block');

        });

        var st_report_date_custom = $(".st_report_date_custom").find(".jqplot-xaxis-tick");
        var st_reports_order_by = $('.st_reports_order_by').val();
        if(st_reports_order_by != 'month'){
            if(st_report_date_custom.length > 10){
                st_report_date_custom.addClass('hidden');
                $k = Math.ceil( st_report_date_custom.length / 10 );
                for(var i =0 ; i < st_report_date_custom.length ; i++ ){
                    if(i==0){
                        $(st_report_date_custom[0]).removeClass('hidden')
                        $number_next = $k + i;
                    }
                    if($number_next == i){
                        $(st_report_date_custom[i]).removeClass('hidden')
                        $number_next = $k + i;
                    }
                    if(st_report_date_custom.length == i+1){
                        $(st_report_date_custom[i]).removeClass('hidden')
                    }
                }
            }
        }
    });

</script>
