jQuery(document).ready(function($) {

	$('a#delete-custom-font').click(function(event) {
		$confirm = confirm("You are about to permanently delete the selected items. 'Cancel' to stop, 'OK' to delete.");
		if($confirm == true)
			return true;
		else
			return false;
	});
});