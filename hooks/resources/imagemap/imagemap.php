<?php

/**
	This is a hook that permits the use of IMAGE MAPS in surveys (and potentially in data-entry forms)
	It is based off the imageMapster.js project by James Treworgy

	Because all of the images and JS are injected directly into the survey via PHP, it is not necessary
	for the directory where these files are hosted to be web-accessible (which is great for shibboleth users)

	In order to declare a new imageMap, you have to have the areas (as defined in the map.html file) as
	well as a corresponding image in PNG format.  Please see the example maps for reference.

	This script assumes that the hook_functions array has already been made.  This is done by including
	the scan_for_custom_questions.php script before calling this one.  Alternatively that code could be
	incorporated into this script.

	Like all things - this is a work-in-progress :-)  Please provide "constructive" feedback :-)

	Andrew Martin
	Stanford University
**/

$term = "@IMAGEMAP";
$imageMapLibrary['PAINMAP_MALE'] = array(
	'name'  => 'painmap_male',
	'alt'   => "Male Front Pain Map",
	'image' => "painmap_male.png",
	'width' => 553,
	'height'=> 580,
	'map'   => "painmap_male.html"
);

$imageMapLibrary['PAINMAP_FEMALE'] = array(
	'name'  => 'painmap_female',
	'alt'   => "Female Front Pain Map",
	'image' => "painmap_female.png",
	'width' => 518,
	'height'=> 580,
	'map'   => "painmap_female.html"
);

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


# Step 1 - inject imageMapster.js
echo "<script type='text/javascript'>";
readfile(dirname(__FILE__) . DS . "imageMapster.js");
echo "</script>";

//error_log ("Loaded imageMapster.js");
//error_log ("Hook functions: " . print_r($hook_functions[$term],true));
//error_log ("Elements: " . print_r($elements,true));

# Step 2 - for each function to be run, inject the proper images/area maps
$startup_vars = array();
foreach($hook_functions[$term] as $field => $details) {
	// Get the elements index and parameters from the details array
	$elements_index = $details['elements_index'];
	$params = $details['params'];
	if (isset($imageMapLibrary[$params])) {
		if (!isset($startup_vars[$params])) {
			// Copy the default parameters
			$js_params = $imageMapLibrary[$params];
			// Add the field name so we can find it in javascript on the client
			$js_params['field'] = $field;
			// Load the image
			$image_file = dirname(__FILE__) . DS . $js_params['image'];
			$b64 = base64_encode(file_get_contents($image_file));
			//error_log ("b64: $b64");
			$src = "data:image/png;base64,$b64";
			$js_params['src'] = $src;
			// Load the area map
			$areas = file_get_contents(dirname(__FILE__) . DS . $js_params['map']);
			$js_params['areas'] = $areas;
			// Add the question type (text or checkbox)
			$js_params['type'] = $elements[$elements_index]['rr_type'];
			$startup_vars[] = $js_params;
		}
	} else {
		error_log ("ERROR: Parameters for $term are not configured in the imagemap hook.");
		//return;
	}
}

//error_log("Startup Vars: ". print_r($startup_vars,true));

# Step 3 - inject the custom javascript and start the post-rendering
$script_path = dirname(__FILE__) . DS . "imagemap.js";
$start_function = "imageMapStart()";

echo "<script type='text/javascript'>";
echo "var imageMapLibrary = ".json_encode($startup_vars).";";
readfile($script_path);
echo "$(document).ready(function() {".$start_function."});";
echo "</script>";



# Helper function for debugging
function debugIt($var, $title = '') {
	echo "<hr>";
	if ($title) echo "<h2>$title</h2>";
	echo "<pre>".print_r($var,true)."</pre>";
}