<?php

/**

	Custom REDCap Hooks File

	This file should be referenced in the REDCap Control Center under General Configuration : REDCap Hooks

	Andrew Martin
	Stanford University

**/


###################
# Allow custom code in each REDCap survey page
function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id)
{    
	// Check for a global script that applies to all projects
	$global_handler_script = dirname(__FILE__) . "/hooks/global/redcap_survey_page.php";
	if (file_exists($global_handler_script)) include $global_handler_script;

	// Check for a project-specific script
	$project_handler_script = dirname(__FILE__) . "/hooks/pid{$project_id}/redcap_survey_page.php";
	if (file_exists($project_handler_script)) include $project_handler_script;	
}



###################
# Verifies usernames added to projects against our LDAP directory before creating users in REDCap
# This is a custom-stanford hook but could be extended for other LDAP-based users
function redcap_custom_verify_username($username) {
	// Check for a global script that applies to all projects
	$global_handler_script = dirname(__FILE__) . "/hooks/global/redcap_custom_verify_username.php";
	if (file_exists($global_handler_script)) {
		$result = include_once $global_handler_script;
		return $result;
	}
}



?>
