<?php

/**
	This is a hook that permits the use of embedded video and audio in surveys (and potentially in data-entry forms)
	It is based off the jplayer.org project by Happyworm






	This script assumes that the hook_functions array has already been made.  This is done by including
	the scan_for_custom_questions.php script before calling this one.  Alternatively that code could be
	incorporated into this script.

	Like all things - this is a work-in-progress :-)  Please provide "constructive" feedback :-)

	Andrew Martin
	Stanford University
**/

$term = "@MEDIAPLAYER";

error_log('Starting @Mediaplayer');

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

error_log ("Hook functions: " . print_r($hook_functions[$term],true));
//error_log ("Elements: " . print_r($elements,true));

# Step 1 - inject css - this is ugly as I'm repeating the base64 for background images, but good enough for now
echo "<style type='text/css'>";
$css = file_get_contents(dirname(__FILE__) . DS . "blue.monday" . DS . "jplayer.blue.monday.css");
$url_search_terms = array(
	'"jplayer.blue.monday.jpg"' 			=> 'image/jpg',
	'"jplayer.blue.monday.seeking.gif"' 	=> 'image/gif',
	'"jplayer.blue.monday.video.play.png"' 	=> 'image/png'
);
foreach($url_search_terms as $search_term => $type) {
	$url_replace_terms[] = "data:$type;base64," . base64_encode(file_get_contents(dirname(__FILE__) . DS . "blue.monday" . DS . str_replace('"','',$search_term)));
}
$css = str_replace(array_keys($url_search_terms),$url_replace_terms, $css);
echo $css;
// readfile(dirname(__FILE__) . DS . "blue.monday" . DS . "jplayer.blue.monday.css");
echo "</style>";

# Step 2 - Inject javascript library
echo "<script type='text/javascript'>";
readfile(dirname(__FILE__) . DS . "jquery.jplayer.min.js");
echo "</script>";


# Step 3 - for each media to be run, inject the proper html container
$css = array();
$mediaplayer_vars = array();
foreach($hook_functions[$term] as $field => $details) {
	$elements_index = $details['elements_index'];
	
	// The parameters for this function are expected to be a valid json object.  Lets decode it in php to add attributes.
	$params = json_decode($details['params'],true);
	
	$params['field'] = $field;
	// Add the html attribute which controls how it will render
	$params['html'] = getJplayerHtml($field, isset($params['mediaType']) ? $params['mediaType'] : 'jp-video');
	$params['customMediaPath'] = APP_PATH_WEBROOT_FULL . "/hooks/resources/mediaplayer/Jplayer.swf";
	$css[] = "#jp_container_" . $field . " div.jp-video-play { margin-top: -" . $params['height'] . "px; height: " . $params['height'] . "px;}";
	$mediaplayer_vars[$field] = $params;
}
//error_log ('mediaplayer_vars ' . print_r($mediaplayer_vars,true));	
//error_log ('css ' . print_r($css,true));	
if (count($css)) echo "<style type='text/css'>".implode("\n",$css)."</style>";


# Step 3 - inject the custom javascript and start the post-rendering
$script_path = dirname(__FILE__) . DS . "mediaplayer.js";
$start_function = "mediaplayerStart()";
echo "<script type='text/javascript'>";
echo "var mediaplayerVars = ".json_encode($mediaplayer_vars).";";
readfile($script_path);
echo "$(document).ready(function() {".$start_function."});";
echo "</script>";



error_log (__FILE__ . " done.");



/*
'
		<div id="jp_container_1" class="jp-video jp-video-360p">
			<div class="jp-type-single">
				<div id="jquery_jplayer_1" class="jp-jplayer"></div>
				<div class="jp-gui">
					<div class="jp-video-play">
						<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
					</div>
					<div class="jp-interface">
						<div class="jp-progress">
							<div class="jp-seek-bar">
								<div class="jp-play-bar"></div>
							</div>
						</div>
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>
						<div class="jp-controls-holder">
							<ul class="jp-controls">
								<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
								<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
								<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
							</ul>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
							<ul class="jp-toggles">
								<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
								<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
								<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
								<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
							</ul>
						</div>
						<div class="jp-details">
							<ul>
								<li><span class="jp-title"></span></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
'
*/


function getJplayerHtml($field_name, $div_class) {
	// Setting cssSelectorAncestor as 'jp_container_' + field_name
	// http://jplayer.org/latest/developer-guide/#jPlayer-predefined-css-selectors
	$html = '<div id="jp_container_' . $field_name . '" class="' . $div_class . '" style="margin:3px auto 3px auto;">
		<div class="jp-type-single">
			<div id="jquery_jplayer_' . $field_name . '" class="jp-jplayer"></div>
			<div class="jp-gui">
				<div class="jp-video-play">
					<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
				</div>
				<div class="jp-interface">
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-current-time"></div>
					<div class="jp-duration"></div> 
					<div class="jp-controls-holder">
						<ul class="jp-controls">
							<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
							<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
							<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
							<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
							<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
							<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
						</ul>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
							<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
					<div class="jp-title">
						<ul>
							<li></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="jp-no-solution">
				<span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>
		</div>
	</div>';
	return str_replace("\n\t", "\n", $html);
}