<?php
	
/**
	This is a hook utility function that makes HTML MATRIX tables based on label configuration

	Andrew Martin
	Stanford University
**/

$term = '@INPUTMATRIX';
error_log("Staring $term");

/*
	Enable hook_functions and hook_fields (if not already done)
*/
if (!isset($hook_functions)) {
	$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/init_hook_functions.php';
	if (file_exists($file)) {
		include_once $file;
	} else {
		error_log ("ERROR: In Hooks - unable to include required file $file while in " . __FILE__);
	}
}


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
?>

<script type='text/javascript'>
$(document).ready(function() {
	var matrixFields = <?php print json_encode($startup_vars); ?>;
	//console.log(matrixFields);
	
	// Loop through each field_name
	$(matrixFields).each(function(i, field_name) {
		//console.log('i: ' + i);console.log(field_name);
		
		// Get parent tr for table
		var tr = $('tr[sq_id="' + field_name + '"]');
		//console.log('tr');console.log(tr);
		
		// Hide the input
		$('input[name="' + field_name + '"]', tr).hide();
		
		// Replace term from note
		var note = $('div.note', tr);
		$(note).text($(note).text().replace('<?php echo $term ?>', ''));
		
		// Get table in label
		var t = $('td.label table.inputmatrix', tr);
		
		// Remove the br's that REDCap inserts before the table
		$(t).siblings('br').remove();
		
		// Iterate through each 'th' in the table
		$('th', t).each(function(j, th) {
			// Get the contents of the th cell
			var th_label = $(th).text();  // This is the text in the TH element
			//console.log('j:' + j);console.log(th);console.log(th_label);
			
			// Search for a tr element with the id from the th cell
			var real_tr = $("tr[sq_id='" + th_label + "']");
			if ($(real_tr).size()) {
				// Get the label
				var real_label = $("td.label:not(.quesnum)", $(real_tr));
				// Move the label into the table and add a 'label' class for rendering
				$(th).html($(real_label.contents()));
			}
			
			if (th_label.length > 0) {
				$(th).addClass('label');
			}
		});
	
		// Iterate through each 'td' in the table
		$('td', t).each(function(j, td) {
			// Get the contents of the td cell
			var td_label = $(td).text();  // This is the text in the TD element
		
			// Search for a tr element with the id from the td cell
			var real_tr = $("tr[sq_id='" + td_label + "']");
			if ($(real_tr).size()) {
				// Get the input
				var real_input = $("input", $(real_tr));
			
				// Resize the input
				$(real_input).css('width',50);
			
				// Move it to the td cell
				$(td).html($(real_input));
				
				// Hide the TRs.
				$(real_tr).css('display','none');
			}
		});
	});
});
</script>
