<?php

/**

	Project-specific redcap_survey_page hooks configuration

**/

/*
	This is an example how you could include the imagemap hook only for a specific project (instead of having this code in the global section)


# INCLUE THE IMAGEMAP QUESTION TYPE
$file = dirname(APP_PATH_DOCROOT).DS.'hooks/resources/imagemap/imagemap.php';
if (file_exists($file)) {
	include_once $file;
} else {
	error_log ("Unable to include required file $file while in " . __FILE__);
}
*/