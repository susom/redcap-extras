
<h2 style="color:#800000;">
	Form URL Plugin
</h2>

<?php

// Call the REDCap Connect file in the main "redcap" directory
require_once "../redcap_connect.php";

$project_name = $_POST["project_name"];
$study_id = $_POST["study_id"];
$page_name = $_POST["page_name"];
$event_name = $_POST["event_name"];
$sql = "select project_id from redcap_projects where app_title = '$project_name'";
$q = db_query($sql);
$arr = db_fetch_assoc($q);
$project_id = $arr["project_id"];
$event_id = "";
$sql = "select rd.event_id from redcap_data rd join redcap_events_metadata rm ON (rm.event_id = rd.event_id) where project_id = '$project_id' and record = '$study_id' and field_name = 'study_id' and descrip = '$event_name'";
$q = db_query($sql);
$arr = db_fetch_assoc($q);
$event_id = $arr["event_id"];
$actual_link = "http://$_SERVER[HTTP_HOST]/redcap/redcap_v6.0.5/DataEntry/index.php?pid=$project_id&id=$study_id&page=$page_name&event_id=$event_id";

?>

<html>
	<body>
		<?php
		echo "Event is ".$event_id;
			echo '<a href="' . $actual_link . '" target="_blank">Link to form</a>';
		?>
	</body>
</html>