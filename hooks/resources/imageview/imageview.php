<?php
	
/**
	This is a hook that tries to preview an image after upload

	Andrew Martin
	Stanford University
**/

$term = '@IMAGEVIEW';

error_log('Staring @IMAGEVIEW');

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


# Step 1 - Inject javascript library
//echo "<script type='text/javascript'>";
//readfile(dirname(__FILE__) . DS . "imageview.js");
//echo "</script>";


# Step 2 - for each field to be montiored, inject necessary code
$startup_vars = array();
foreach($hook_functions[$term] as $field => $details) {
	$startup_vars[] = $field;
}


# Step 3 - inject the custom javascript and start the post-rendering
$script_path = dirname(__FILE__) . DS . "imageview.js";
$start_function = "imageviewStart()";

echo "<script type='text/javascript'>";
echo "var imageviewFields = ".json_encode($startup_vars).";";
readfile($script_path);
echo "$(document).ready(function() {".$start_function."});";
echo "</script>";




error_log ("Hook functions: " . print_r($hook_functions[$term],true));
error_log ("Elements: " . print_r($elements,true));


	
	
	
	
?>