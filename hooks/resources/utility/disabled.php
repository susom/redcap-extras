<?php
	
/**
	This is a hook utility function that disables all fields with @DISABLED in the notes area.

	Andrew Martin
	Stanford University
**/

$term = '@DISABLED';

error_log("Staring $term");

// Assumes we have populated the hook_functions array
if (!isset($hook_functions)) {
	echo "ERROR: Missing check for hook_functions array in " . __FILE__ . ".  Check your global hook for redcap_survey_page.";
	return;
}

if (!isset($hook_functions[$term])) {
	// Skip this page - term not called
	error_log ("Skipping - no $term functions called.");
	return;
} 


# Step 1 - Create array of fields to hide and inject
$startup_vars = array();
foreach($hook_functions[$term] as $field => $details) {
	$startup_vars[] = $field;
}
echo "<script type='text/javascript'>
$(document).ready(function() {
	var disabledFields = ".json_encode($startup_vars).";
	$(disabledFields).each(function() {
		var tr = $('tr[sq_id='+this+']');
		
		// Replace term from note
		var note = $('div.note', tr);
		$(note).text($(note).text().replace('".$term."', ''));
		
		// Disable inputs
		$('input', tr).prop('disabled',true);
		
		//console.log('Disabled '+this);
	});
});
</script>";

?>
