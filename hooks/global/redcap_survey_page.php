<?php

/**

Global Redcap Survey Page Hook

Any references included here will be globally applied to EVERY survey page

**/

error_log ('Global 1');

/*
	Enable hook_functions and hook_fields arrays globally 
	
	I enable this globally since so many of my hooks rely on the @FUNCTION notes field.  You could
	instead enable it on a per-project hook
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/scan_for_custom_questions.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("ERROR: In Hooks - unable to include required file $file while in " . __FILE__);
}


error_log ('Global 2');

/*
	I want to enable imagemaps for all surveys on this instance of REDCap, so I'll put
	the following include here.  Alternatively, I could only enable it for a single project
	by placing this block of code in /hooks/pidXXXX/redcap_survey_page.php.  Before this code
	can be run, you must have already defined the hook_functions array which is done by
	running the 'scan_for_custom_questions.php' script.
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/imagemap/imagemap.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
}


error_log ('Global 3');


/*
	I want to enable @MEDIAPLAYER for all surveys on this instance of REDCap, so I'll put
	the following include here.  Alternatively, I could only enable it for a single project
	by placing this block of code in /hooks/pidXXXX/redcap_survey_page.php.  Before this code
	can be run, you must have already defined the hook_functions array which is done by
	running the 'scan_for_custom_questions.php' script.
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/mediaplayer/mediaplayer.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
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


/*
	@HIDDEN hides the field
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/utility/hidden.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
}

/*
	@DISABLED hides the field
*/
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/utility/disabled.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
}




error_log ('Global Done');



