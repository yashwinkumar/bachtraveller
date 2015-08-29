<?php 
	$list_post_type = explode(",", $st_location_header_rate_count_to) ; 
	$list_star  = explode(',', $st_location_star_list);
	$result = STLocation::get_rate_count($list_star , $list_post_type);	

?>
<ul class='icon-list text-white bgr-opacity' id='location_header_static'>
<?php 
	if (!empty($result) and is_array($result)){
		foreach ($result as $key => $value) {
			$rate_text  = __(" rate" , ST_TEXTDOMAIN);
			if ($value >1){$rate_text  = __(" rates" , ST_TEXTDOMAIN);}

			if ($key == 5){
				echo "<li> ".$value .$rate_text.' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>'."</li>";
			}
			if ($key == 4){
				echo "<li> ".$value .$rate_text.' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>'."</li>";
			}
			if ($key == 3){
				echo "<li> ".$value .$rate_text.' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>'."</li>";	
			}
			if ($key == 2){
				echo "<li> ".$value .$rate_text.' <i class="fa fa-star"></i><i class="fa fa-star"></i>'."</li>";		
			}
			if ($key == 1){
				echo "<li> ".$value .$rate_text.' <i class="fa fa-star"></i>'."</li>";			
			}
		}
	}
?>
</ul>
