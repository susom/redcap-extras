<?php

/**
	Custom REDCap Data Entry functions for this project	
**/


/*
	Enable hook_functions and hook_fields (if not already done)
*/
if (!isset($hook_functions)) {
	$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/scan_for_custom_questions.php';
	if (file_exists($file)) {
		include_once $file;
	} else {
		error_log ("ERROR: In Hooks - unable to include required file $file while in " . __FILE__);
	}
}



/*
	@IMAGEVIEW tries to display a preview of uploaded files
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/imageview/imageview.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
}



?>