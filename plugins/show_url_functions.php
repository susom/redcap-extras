<!--
# Contributors:
#    
#    Andrei Sura: github.com/indera
#    Sanath Kumar Pasumarthy: github.com/sanathp
#   Radha Krishna Murthy Kandula : <radhakrishna.nani@gmail.com>
#
# Copyright (c) 2015, University of Florida
# All rights reserved.
#
# Distributed under the BSD 3-Clause License
# For full text of the BSD 3-Clause License see http://opensource.org/licenses/BSD-3-Clause
-->
<?php

/*
function getProjectIdFromName($project_name) {
	echo "getProjectIdFromName";
   $query = <<<SQL
SELECT
   project_id
FROM
   redcap.redcap_projects
WHERE
   app_title = '$project_name'
SQL;
   $result = db_query($query);
   $row = db_fetch_assoc($result);
   return $row['project_id'];
}
*/

function getEventDetails($project_name, $study_id, $event_name) {
   $query = <<<SQL
SELECT
   p.project_id, em.event_id
FROM
   redcap.redcap_projects AS p
   JOIN redcap.redcap_events_arms ea USING (project_id)
   JOIN redcap.redcap_events_metadata em USING (arm_id)
   JOIN redcap.redcap_data d USING (project_id, event_id)
WHERE
   app_title = '$project_name'
   AND record = '$study_id'
   AND descrip = '$event_name'
   LIMIT 1
SQL;
   echo "<!-- getEventId: \n $query \n-->";
   $result = db_query($query);
   $row = db_fetch_assoc($result);
   return $row;
}

/**
 * Translates the four parameters into a event ID url
 *
 * @param project_name  : string corresponding to `redcap_projects.app_title` column
 * @param study_id      : string corresponding to `redcap_data.record` column
 * @param event_name    : string corresponding to `redcap_events_metadata.descrip` column
 * @param page_name     : string representing the form name `redcap_events_forms.form_name`
 *
 * @return string
 */
function getUrlForEvent($project_name, $study_id, $event_name, $page_name) {
   $event_details = getEventDetails($project_name, $study_id, $event_name);
   $url_data = array(
	'pid'      => $event_details['project_id'],
	'id'       => $study_id,
        'page'     => $page_name,
        'event_id' => $event_details['event_id']
   );

   $url = "http://" . $_SERVER['HTTP_HOST'] . "/redcap/redcap_v5.7.4/DataEntry/index.php?"
	. http_build_query($url_data);
   return $url;
}
