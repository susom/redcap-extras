<?php
	
/**

	REDCap custom verify username
	
	This hook ensures that usernames are valid via an LDAP lookup before
	adding a new user to a project.

	A few of the parameters for this function are currently stored in the redcap_config table...  This probably isn't ideal and we should look into declaring a .properties file or something like that for storing per-installation specific values.

	Andy Martin
	Stanford University

**/

// MAKE SURE redcap_confg CONTIANS stanford_ldap_url AND stanford_ldap_token;
// Note: success just means the query returned - not that the result is a single valid user
// Do a 'soft' match looking for anything that resembles the searched username
// (such as email addresses or aliases in LDAP)
// 	Our ldap_lookup returns an array like:
// 		[	"count" => 2,
// 			"1" => ["uid" => "andy123", "attribute1..n" => "value1..n"],
// 			"2" => [...]
// 		]

list($success, $ldap_result) = ldap_lookup($username, false);
$msg = array();	// Array to hold messages

// If the LDAP server is down or another error occurs, notify the user
if (!$success) {
	// Deliver error message and exit
	$msg[] = "There was an error validating the specified SUNet ID (<b>$username</b>).<br/><br/>
			This usually means the Stanford LDAP resource is not responding or another network error
			has occurred.  Please back up and try again.  If this error recurs, please send an email to
			<a href='mailto:redcap-help@lists.stanford.edu'>redcap-help@lists.stanford.edu</a> 
			describing the problem.
			<hr><br>We apologize for the inconvenience and will do our best to resolve it promptly.";
	// Potentially notify a REDCap admin! with $ldap_result as a message
	return array('status'=>FALSE, 'message'=>implode('<br><br>',$msg));
}

// Get number of results from LDAP search (included as first paramter from response)
$count = $ldap_result['count'];

// No match found
if ($count === 0) {
	$msg[] = "The specified SUNet ID (<b>$username</b>) does not appear to be valid.<br/><br/>
		Some users have aliases so the prefix to their email address isn't necessarily the same as their
		SUNet ID.  Try searching <a href='http://stanfordwho.stanford.edu/SWApp/authSearch.do?search=$username' 
		target='_BLANK'><b>Stanford Who</b></a> if you are unsure.<br><br>
		If you are unable to locate your collaborator in Stanford Who, please contact them and request 
		their correct SUNet ID.";
	return array('status'=>FALSE, 'message'=>implode('<br><br>',$msg));
}

// Look for an exact match in the remaining 1 or more ldap results
$match = 0;
for ($i = 1; $i <= $count; $i++) {
	if ($ldap_result[$i]['uid'] == $username) {
		$match = $i;
		break;
	}
}

if ($match) {
	// An exact match was found
	$displayName = isset($ldap_result[$match]['displayname']) ? ($ldap_result[$match]['displayname']) : "-- NO NAME LISTED IN DIRECTORY --";
	
	// See if the matching user is already in REDCap
	if (check_existing_user($ldap_result[$match])) {
		// User exists - display a message that shows the full username as additional confirmation (instead of just their id)
		$msg[] = "Adding <b>$displayName ($username)</b> to this project";
	} else {
		// User added - give additonal instructions
		$msg[] = "<b>$displayName ($username)</b> is a new user to REDCap.  We suggest you introduce them to REDCap and recommend they watch the videos under the <b>Training Resources</b> tab to become familiar with REDCap and its features.";
	}
	
	// If there were additional 'partial' matches, return them beneath the exact match so user is posititve
	// they are selecting the right person
	if ($count > 1) {
		$other_results = $ldap_result;
		unset($other_results[$match]);	// Remove the match so it doesn't appear in the table
		$msg[] = "In addition to the exact match, the following accounts were also returned from your search.  If you were
				trying to add one of these user(s) to your project, please cancel and try again using the exact SUNet ID 
				for the user.<br><br>" . get_table_from_ldap_results($other_results);
	 }
	 
	// Additional message about user rights, encouraging people to use roles
	$msg[] = "<span style='font-size:smaller;font-style:italic;'>Please be sure to configure appropriate user rights for this user.  We strongly recommend using <u>User Roles</u> to manage rights.<br>If you have questions about permissions or user roles, please contact <a style='font-size:smaller;' href='mailto:redcap-help@lists.stanford.edu'>redcap-help@lists.stanford.edu</a>.</span>";
	
	return array('status'=>TRUE, 'message'=>implode('<br><br>',$msg));
} else {
	// No exact match was found, but partial match(es) were.  Display a list of those matches
	$msg[] = "The username you specified (<b>$username</b>) is a potential match for the following accounts:<br><br>" .
			get_table_from_ldap_results($ldap_result) . "<br><br>Please use the exact <u>SUNetID</u> for the user you are 
			looking for and try again.<br><br>If none of these are the user you are looking for, please use 
			<a href='http://stanfordwho.stanford.edu/SWApp/authSearch.do?search=$username' target='_BLANK'>Stanford Who</a> 
			to locate your collaborator.";
	return array('status'=>FALSE, 'message'=>implode('<br><br>',$msg));
}





### SUPPORT FUNCTIONS

// Performs a call to an LDAP service where
//  - stanford_ldap_url is defined in redcap_config
//  - stanford_ldap_token is defined in redcap_config
function ldap_lookup($userid, $exact = "true") {
	global $stanford_ldap_url, $stanford_ldap_token;
	
	$redcap_lookup = file_get_contents($stanford_ldap_url."?token=" . $stanford_ldap_token . "&exact=$exact&userid=$userid");
	//DEBUG		$redcap_lookup = '{"count":1,"1":{"uid":"andy123","displayname":"Andrew B Martin PhD","sudisplaynamefirst":"Andrew","sudisplaynamelast":"Martin","mail":"andy123@stanford.edu"}}';
	$ldapResult = json_decode($redcap_lookup,true);

	//check for ldap errors
	$errorMsg = "NONE";	
	if (!$ldapResult) {
		//TBD: This is the error if the LDAP service is down - we might want to bypass in this event...
		$errorMsg = "Invalid JSON response for $userid ($redcap_lookup)";
	} else if (isset($ldapResult['error'])) { 
		$errorMsg = "There was an error looking up $userid ({$ldapResult['error']})";
	} else if (!isset($ldapResult['count'])) { 
		$errorMsg = "There was no valid count in the LDAP result for $userid.";
	}

	If ($errorMsg != "NONE") {
		return(array(false, $errorMsg));
	} else {
		return array(true, $ldapResult);		
	}
}	


// Looks to see if the user exists in REDCap.  If not, it adds them (so they can be emailed).
// Returns whether they were present or not.
function check_existing_user ($ldap_user) {
	if (User::getUserInfo($ldap_user['uid']) == false && !empty($ldap_user['mail'])) {
		global $allow_create_db_default;
		$sql = "
			insert into redcap_user_information 
				(username, user_email, user_firstname, user_lastname, user_creation, allow_create_db) 
			values ('".prep($ldap_user['uid'])."', 
				'".prep($ldap_user['mail'])."', 
				'".prep($ldap_user['sudisplaynamefirst'])."', 
				'".prep($ldap_user['sudisplaynamelast'])." (added via userrights)', 
				NOW(), 
				$allow_create_db_default)";
		$q = db_query($sql);
		if ($q) {
			log_event($sql,"redcap_user_information","MANAGE",$ldap_user['uid'],"username = '{$ldap_user['uid']}'","Update user info");
		}
		return false;
	} else {
		return true;
	}
}

// Formulates a nice html table from the ldap results
function get_table_from_ldap_results ($ldap_result) {
	$c = "<table style='margin:0px 20px;'><tr>
		<th style='padding: 0px 10px;'><b>SUNetID</b></th>
		<th style='padding: 0px 10px;'><b>Name</b></th>
		<th style='padding: 0px 10px;'><b>Email</b></th></tr>";
		for ($i=1;$i <= $ldap_result['count'];$i++) {
			$c .= "<tr>";
			$c .= "<td style='padding: 3px 10px;'><b>" . $ldap_result[$i]['uid'] . "</b></td>";
			$c .= "<td style='padding: 3px 10px;'>" . $ldap_result[$i]['sudisplaynamefirst'] . 
					" " . $ldap_result[$i]['sudisplaynamelast'] . "</td>";
			$c .= "<td style='padding: 3px 10px;'>" . $ldap_result[$i]['mail'] . "</td>";
			$c .= "</tr>";
		}
		$c .= "</table>";
		return $c;
}
