<?php

/**

	Custom REDCap Hooks File

	This file should be referenced in the REDCap Control Center under General Configuration : REDCap Hooks

	In this example, this file is placed inside the /hooks/ folder off the base redcap directory.  So:
	- redcap_v5.x.x
    - plugins
    - hooks
       - this file (and it is referenced in the control center, in my case as /var/redcap/dev/webroot/hooks/redcap_hooks.php)

	Andrew Martin
	Stanford University

**/

###################
# Verifies usernames added to projects against our LDAP directory before creating users in REDCap
# This is a custom Stanford hook but could be extended for other LDAP-based users
function redcap_custom_verify_username($username) {
	// Check for a global script that applies to all projects
	$global_handler_script = dirname(__FILE__) . "/global/redcap_custom_verify_username.php";
	if (file_exists($global_handler_script)) {
		$result = include_once $global_handler_script;
		return $result;
	}
}



###################
# Allow custom code in each REDCap survey page
# This is a generic hook that enables survey code customization.  It does so in two ways: global and per-project
# Please note that since they are enabled sequentially, any functions defined in the first will be available in
# subsequent scripts so you have to be careful about redefining helper functions, etc.. as they could throw an error
# if they already exist
function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id)
{    
	// Check for a global script that applies to all projects
	$global_handler_script = dirname(__FILE__) . "/global/redcap_survey_page.php";
	if (file_exists($global_handler_script)) include $global_handler_script;

	// Check for a project-specific script
	$project_handler_script = dirname(__FILE__) . "/pid{$project_id}/redcap_survey_page.php";
	if (file_exists($project_handler_script)) include $project_handler_script;	
}



###################
# Allow custom code in each REDCap Data Entry page
# This is a generic hook that enables data entry code customization.  It does so in two ways: global and per-project
# Please note that since they are enabled sequentially, any functions defined in the first will be available in
# subsequent scripts so you have to be careful about redefining helper functions, etc.. as they could throw an error
# if they already exist
function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id)
{    
	// Check for a global script that applies to all projects
	$global_handler_script = dirname(__FILE__) . "/global/redcap_data_entry_form.php";
	if (file_exists($global_handler_script)) include $global_handler_script;

	// Check for a project-specific script
	$project_handler_script = dirname(__FILE__) . "/pid{$project_id}/redcap_data_entry_form.php";
	if (file_exists($project_handler_script)) include $project_handler_script;	
}



?>
