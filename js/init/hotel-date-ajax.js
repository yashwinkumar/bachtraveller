jQuery(document).ready(function($) {
    var listDate = '';
    $('.input-daterange input[name="start"], .input-daterange input[name="end"]').each(function() {
        $(this).datepicker({
            format: $('[data-date-format]').data('date-format')
        });
        date_start = $(this).datepicker('getDate');
        $(this).datepicker('addNewClass','booked');
        var $this = $(this);
        if(date_start == null)
            date_start = new Date();

        year_start = date_start.getFullYear();

        ajaxGetRentalOrder(year_start, $this);
    });
    

    $('.input-daterange input[name="start"]').on('changeYear', function(e) {
        var $this = $(this);
        year =  new Date(e.date).getFullYear();

        ajaxGetRentalOrder(year, $this);
    });

    $('.input-daterange input[name="end"]').on('changeYear', function(e) {
        var $this = $(this);
        year =  new Date(e.date).getFullYear();

        ajaxGetRentalOrder(year, $this);
    });

    function ajaxGetRentalOrder(year, me){
        var data = {
            item_post_type: 'hotel_room',
            _st_st_booking_post_type: 'st_hotel',
            year: year,
            security:st_params.st_search_nonce,
            action:'st_getOrderByYear',
        };

        $.post(st_params.ajax_url, data, function(respon) {
            if(respon != ''){
                listDate = respon;
                me.datepicker('setRefresh',true);
                me.datepicker('setDatesDisabled',respon);
                
            }    
        },'json');
    }
});