<?php 
$star = STHotel::getStar();
if($star): ?>
    <div class="hotel-star booking-item-rating">
        <span class="booking-item-rating-number"><?php echo __('Hotel star:', ST_TEXTDOMAIN) ; ?></span>
        <ul class="icon-list icon-group booking-item-rating-stars">
            <?php
            echo  TravelHelper::rate_to_string($star);
            ?>
        </ul>
    </div>
<?php endif; ?>