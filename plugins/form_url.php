<!--
# Contributors:
#    
#    Andrei Sura: github.com/indera
#    Sanath Kumar Pasumarthy: github.com/sanathp
#	 Radha Krishna Murthy Kandula : <radhakrishna.nani@gmail.com>
#
# Copyright (c) 2015, University of Florida
# All rights reserved.
#
# Distributed under the BSD 3-Clause License
# For full text of the BSD 3-Clause License see http://opensource.org/licenses/BSD-3-Clause
-->
<html>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<div class="container">
		<div class="jumbotron">
			<h1>Form URL Plugin</h1>      
			<p>This plugin will help generate a direct URL to the required form, given the Project Name, Study ID and Page name.</p>      
		</div>
	<body>
	
	<div id="login" class="container" style="width:50%;">
		<form role="form" action="show_url.php" method="post" width="5%">
			<div class="form-group">
			  <label for="project_name">Project Name</label>
			  <input type="text" name="project_name" class="form-control" id="project_name"
				value="HCV-TARGET 2.0 DEVELOPMENT"
				placeholder="Enter Project Name" />
			</div>
			<div class="form-group">
			  <label for="study_id">Study ID</label>
			  <input type="text" class="form-control" name="study_id" id="study_id"
				value="1"
				placeholder="Enter Study ID" />
			</div>
			<div class="form-group">
			  <label for="page_name">Page name</label>
			  <input type="text" class="form-control" name="page_name" id="page_name"
				value="demographics"
				placeholder="Enter page name (eg., demographics, cbc)" />
			</div>
			<div class="form-group">
			  <label for="event_name">Event name</label>
			  <input type="text" class="form-control" name="event_name" id="event_name"
				value="1"
				placeholder="Enter event name (exact name)" />
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
	      </form>
	</div>
	</body>
	</div>
</html>
