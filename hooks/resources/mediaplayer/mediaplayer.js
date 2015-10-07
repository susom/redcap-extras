// ****************************************************************************************************
// CUSTOM MEDIA PLAYER
// ****************************************************************************************************

/**
 * INSTRUCTIONS:
 *	 This question type enables the insertion of videos and audio into a REDCap survey.
 *   The parameters for the video are stored in the question's label and parsed by the script.
 *   This extension uses jPlayer and must have the necessary jPlayer additions: https://jplayer.org
 *
 *   To use, create a TEXT question and set the NOTES to contain @MEDIAPLAYER.
 *   The parameters are specified in the LABEL as:
 *   Video files can be created using free tools like http://easyhtml5video.com/
 
		{
		 "title": "This title will apear beneath the player",
		 "media": {
			 "m4v": "https://med.stanford.edu/webtools-dev/video/test1/Video_15.m4v",
			 "ogv": "https://med.stanford.edu/webtools-dev/video/test1/Video_15.ogv",
			 "poster": "https://med.stanford.edu/webtools-dev/video/test1/Video_15.jpg"
		 },
		 "supplied": "m4v, ogv",
		 "width": 750,
		 "height": 422,
		 "hideContextMenu": true,
		 "fullscreen": false,
		 "autostart": false,
		 "mustWatch": true,
		 "hideControls": true
		}


AUDIO1
Put config between squirley brackets:
{
 "title": "Mahna Mahna",
 "media": {
 "mp3": "https://med.stanford.edu/webtools-dev/video/audio/08 - Mahna Mahna.mp3",
 "poster": "https://med.stanford.edu/webtools-dev/video/audio/Mahna Mahna.jpg"
 },
 "supplied": "mp3",
 "width": 480,
 "height": 360,
 "autostart": true
} 
 *   Note that the label can contain meta-text before the {} brackets - this text will be ignored.
 *   hideContextMenu prevents right-clicking on the media to get additional options (such as save).  I do not recommend
 *   using this with hideControls.
 *   hideControls removes the play,next,fullscreen buttons along with the progress indicators.
 *   Note that mustWatch means the 'next' or 'submit' buttons are not available until the video is finished
 */


function mediaplayerStart() {
	jQuery(function(){
		// Should have mediaplayerVars available.
		console.log (mediaplayerVars);
		
		// Render each imagemap
		$.each(mediaplayerVars, function(index, value) {
			console.log ('field: ' + index);
			console.log (value);
			//renderImageMap(value);

			initializeJPlayer(value);
		});
		
		// Check if mobile for resizing
		//if (isMobileDevice) {
		//	resizeImageMaps();	// Call it once to set the initial size
		//	$(window).resize(resizeImageMaps); // Bind to window resizing in case the device is rotated
		//}
	});
}

function initializeJPlayer(params) {
	// Get TR Element
	var tr = $('tr[sq_id='+params.field+']');
	//console.log('tr');console.log($(tr));
	
	// Get note
	var note = $('div.note', tr);
	
	// Get Label
	var label = $('td.label:last', tr);
	//console.log('label');console.log($(label));

	// Get Data (not always present - depends on rendering options)
	var data = $('td.data', tr);
	//console.log('data');console.log($(data));

	// Get result input tag
	var result = $('input[name="' + params.field + '"]', tr);

	// Hide the note (except on online-designer)
	if (page == "DataEntry/index.php" || page == "surveys/index.php") {
		$(note).css('display','none');
	} else {
		$(note).append('<br><em>This note will not be visible on the survey or data entry form</em>');
	}
	
	// Read the configuration
	var playerConfig = params;
	//console.log('PlayerConfig: ' + JSON.stringify(playerConfig));	//console.log(playerConfig);
	
	// Each time the parent tr is hidden/displated we need to hide/show our jPlayer container
	// This allows our videos to work with branching logic, etc...
	if (page == "surveys/index.php" || page == "DataEntry/index.php") {
		try {
			// Switching to observer method as DOMSubtreeModified was discontinued
			var observer = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					//console.log('Mutation: ' + mutation.type); console.log(mutation);
					var target = mutation.target;
					var sq_id = $(target).attr('sq_id');
					var video_tr = $('tr[contains="video"][field_name="'+sq_id+'"]');
					matchVisibility(target, video_tr[0]);
				})
			});
			var observer_config = { attributes: true, attributeOldValue: true, attributeFilter: ['style'] };
			observer.observe($(tr)[0], observer_config);
		} catch(err) {
			try {
				$(tr).bind('DOMSubtreeModified',function(e){
					var isVisible = $(this).is(":visible");
					var sq_id = $(this).attr('sq_id');
					var video_tr = $('tr[contains="video"][field_name="'+sq_id+'"]');
					//console.log('video_tr');console.log(video_tr);
					if (isVisible) {
						$(video_tr).show();
					} else {
						$(video_tr).hide();
						$(this).jPlayer("play",0);
					}
					//console.log('video_tr');console.log(video_tr);
				});
			} catch (err2) {
				// Error 2
			}
		}
	}
	
	// Check if library is defined in label and add to global config for page
	if (typeof playerConfig === 'undefined') {
		alert ('invalid config');
		return;
	}
	
	// Get player HTML to insert into page
	var div_class = playerConfig.type == 'audio' ? 'jp-audio' : 'jp-video';
	
	var videoHtml = '<tr field_name="'+params.field+'" contains="video" width="100%" ' + ($(tr).is(":visible") ? '' : 'style="display:none;"') + '>' +
		'<td colspan=' + (page=='surveys/index.php' ? 3 : 2 ) + ' width="100%" style="text-align:center;background-color:#F3F3F3;">' +
			'<div class="hideUntilReady" style="width:100%;">' +
				params.html +
			'</div>' +
		'</td>' +
	'</tr>';
	//console.log('videoHtml');console.log(videoHtml);
	
	
	// Insert video in a new table row
	var video_tr = $(tr).last().after(videoHtml);
	
	// Container DIV for element
	var container = $('#jp_container_' + params.field);
	//console.log('container');console.log(container);
	
	// Convert DIV into jPlayer
	var jp = $("#jquery_jplayer_" + params.field).jPlayer({
		ready: function () {
		
			// Set Media Object
			if(!playerConfig.media) {
				alert('Invalid player configuration');
				return false;
			} else {
				//console.log('media');console.log(playerConfig.media);
				$(this).jPlayer("setMedia", playerConfig.media);
			}
			//console.log('playerConfig');console.log(playerConfig);

			// Set title
			if (playerConfig.title) { $('.jp-title ul li', container).text(playerConfig.title); } else { $('.jp-title').hide(); }
			//console.log('playerConfig.title');console.log(playerConfig.title);
		
			// Set size
			if (playerConfig.width || playerConfig.height) {
				$(this).jPlayer("option", "size", {width: playerConfig.width, height: playerConfig.height, cssClass: "jp-video"});
				//$(this).jPlayer("option", "size", {width: playerConfig.width, height: playerConfig.height});
			}

			// Fullscreen
			if (page == "surveys/index.php") {
				if (playerConfig.fullscreen) { $(this).jPlayer("option", "fullScreen", true); }
			}

			// Disable video context menu to reduce ability to save
			if (page == "surveys/index.php" && playerConfig.hideContextMenu) {
				$(this).bind('contextmenu',function() { return false; });				
			}


			// hideControls if specified - removes play buttons, etc..
			if ((page == "DataEntry/index.php" || page == "surveys/index.php") && playerConfig.hideControls) {
				//console.log('Hiding Controls');
				$(".jp-interface", $(this).closest(".jp-video")).hide();
			}

			// Autostart
			if (page == "surveys/index.php" || page == "DataEntry/index.php") {
				if (playerConfig.autostart) {
					//console.log('autostart');
					$(this).jPlayer("play");
				}				
			}
		
			// Show resume buttons if video complete
			if (playerConfig.mustWatch) {
				// Get next/submit button
				var mustWatchText = 'Watch video to continue...';
				var btn = $('button:last', 'tr.surveysubmit');
				var btnSpan = $('span',btn);
				if ($(btnSpan).text() !== mustWatchText) {
					$(btn).attr('oldText', $('span', btn).text()).css('width','auto').css('cursor','wait');
					$('span',btn).text('Watch video to continue...');
					$(btn).attr('disabled','disabled');
					$('span',btn).attr('disabled','disabled');
					//console.log('Hiding btn');console.log(btn);
				}

				// Cleanup when video is done
				$(this).bind($.jPlayer.event.ended, function(event) {
					// Unhide button (if it was hidden)
					var btn = $('button:last', 'tr.surveysubmit');
					//console.log('Restoring btn');console.log(btn);
					$('span', btn).text($(btn).attr('oldText'));
					$(btn).removeAttr('disabled').css('cursor','auto');
					$('span',btn).removeAttr('disabled');
				});
			}
		
		
			if (playerConfig.type == 'audio') {
				//console.log ('Hiding jp-toggles')
				$('ul.jp-toggles').hide();
				$('div.jp-video-play').hide();
			}
		
		
		
			$(this).bind($.jPlayer.event.timeupdate, function (event) {
				var playerTime = Math.round(event.jPlayer.status.currentPercentAbsolute * 10) / 10;
				//var currentTime = event.jPlayer.status.currentTime;
				//console.log('timeupdate: ' + playerTime);
				var curVal = $(result).val() ? $(result).val() : 0;
				if (playerTime > curVal) $(result).val(playerTime);
				//console.log(rq.result.val());
			});
		
			// Additional end-of-play cleanup
			$(this).bind($.jPlayer.event.ended, function(event) {
				//console.log('ended1');
				
				// Set value to 100% as video was completed
				$(result).val(100);

				// If autoSubmit is set, then submit
				if (playerConfig.autoSubmit) {
					var f = $(this).parentsUntil($('form')).parent().submit();
				}
				
				// Turning off fullscreen at end of movie isn't working quite right...
				$(this).jPlayer("option", {"fullScreen": false});
				//$(this).jPlayer("option", "restoreScreen", true);
				
				// Enable replay
				$(".jp-video-play", $(this).closest(".jp-video")).show();
				
				// Rehide the play div since it only does it automatically the first time for some reason
				$(this).bind($.jPlayer.event.play, function(event) {
					$(".jp-video-play", $(this).closest(".jp-video")).hide();							
				});
			});
		},
		//preload: "auto",
		swfPath: params.customMediaPath,
		supplied: params.supplied, 	//"m4v, ogv",
		cssSelectorAncestor: '#jp_container_' + params.field
	});

	//console.log('jplayer configured');
	//$('div.hideUntilReady').show();

	// HIDE Question (instead of hiding tr, we hide all three tds so as not to interfere with detecting branching logic
	if (page == "surveys/index.php" || page == "DataEntry/index.php") {				
		$('td', tr).css('display','none');
		//$('td.label', rq.tr).css('display','none');
	}
}


//playerLibrary = new Object();	// This is an object to store all videos found on the page


//var customMediaPath = app_path_webroot + 'Resources/js/custom_media_player';

// Initial function called by loading ajax call
//function mediaPlayerStart() {

	//console.log('Hello');

//	loadJplayerCss();
//	loadJplayerJs();
//	console.log('Goodbye');
//}

/*
// Step 1: Load CSS
function loadJplayerCss() {
	var cssPrefix = customMediaPath + '/blue.monday/';
	$.ajax({
		url: cssPrefix + 'jplayer.blue.monday.css',
		dataType: "text",
		success: function(cssText) {
			//Since we are injecting css, the relative path for the url() function is not correct
			correctedText = cssText.replace(/\"jplayer/g, '"' + cssPrefix + 'jplayer');
			$('body').prepend('<style type="text/css">' + correctedText + '</style>');
			loadJplayerJs();
		}
	});
}
*/

/*

// Step 2: Load Javascript library (or append to this script)
function loadJplayerJs() {
	$.ajax({dataType: "Script", url: customMediaPath + '/jquery.jplayer.min.js', cache: true}).done(checkForJplayer);
}
*/

/*
// Returns the html requires for each instance of the jplayer
function getJplayerHtml(field_name, div_class) {
	// Setting cssSelectorAncestor as 'jp_container_' + field_name
	// http://jplayer.org/latest/developer-guide/#jPlayer-predefined-css-selectors
	var html = '' +
		'<div id="jp_container_'+field_name+'" class="'+div_class+'" style="margin:3px auto 3px auto;">' +
			'<div class="jp-type-single">'+
				'<div id="jquery_jplayer_'+field_name+'" class="jp-jplayer"></div>' +
				'<div class="jp-gui">' +
					'<div class="jp-video-play">' +
						'<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>'+
					'</div>' +
					'<div class="jp-interface">' +
						'<div class="jp-progress">' +
							'<div class="jp-seek-bar">' +
								'<div class="jp-play-bar"></div>' +
							'</div>'+
						'</div>' +
						'<div class="jp-current-time"></div>' +
						'<div class="jp-duration"></div>' + 
						'<div class="jp-controls-holder">' +
							'<ul class="jp-controls">' +
								'<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>' +
								'<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>' +
								'<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>' +
								'<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>' +
								'<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>' +
								'<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>' +
							'</ul>' +
							'<div class="jp-volume-bar">' +
								'<div class="jp-volume-bar-value"></div>' +
							'</div>' +
							'<ul class="jp-toggles">' +
								'<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>' +
								'<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>' +
								'<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>' +
								'<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>' +
							'</ul>' +
						'</div>' +
						'<div class="jp-title">' +
							'<ul>' +
								'<li></li>' +
							'</ul>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="jp-no-solution">'
					'<span>Update Required</span>To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.' +
				'</div>' +
			'</div>' +
		'</div>';
  return html;
}

*/

/*
// Helper class for dealing with REDCap question HTML
function redcapQuestion(noteDiv) {
	this.noteDiv = $(noteDiv);
	this.tr = $(noteDiv).parentsUntil($("tr[sq_id]")).parent();
	this.labelDiv = $('td.label:last', this.tr);
	this.labelConfig = $(this.labelDiv).text().replace(/\n/g,'').match(/({.*})/)[1];
	this.dataDiv = $('td.data:last', this.tr);
	this.field_name = $(this.tr).attr('sq_id');
	this.result = $('input[name="' + this.field_name + '"]', this.tr);
	this.visible = $(this.tr).is(":visible");
	//DEBUG
	//console.log('redcapQuestion: '+this.field_name);console.log(this);
}
*/

/*
// Return the javascript object from the label
redcapQuestion.prototype.getConfig = function() {
	var rawText = $(this.labelDiv).text();
	var cleanText = rawText.replace(/\n/g,'');	// Clean up carriage returns
	var jsonText = cleanText.match(/({.*})/)[1];	// Take everything between the {}
	return JSON.parse(jsonText);	
}
*/

function matchVisibility(sourceElement, destinationElement) {
	if ( $(sourceElement).is(":visible") ) {
		$(destinationElement).show();
	} else {
		$(destinationElement).hide();
	}
}

/*
// Parse the page for video questions
function checkForJplayer() {
	//console.log('Checking for videos...');
	var term = '@MEDIA2PLAYER';

	// Three are three page states: "Design/online_designer.php", "surveys/index.php", "DataEntry/index.php"
	if (page == "surveys/index.php" || page == "DataEntry/index.php" || page == "Design/online_designer.php") {
		var matches = $("div.note:contains("+term+")");
		$(matches).each( function() {
			// Get the question
			var rq = new redcapQuestion(this);
			// Read the configuration
			var playerConfig = rq.getConfig();
			//console.log('PlayerConfig: ' + JSON.stringify(playerConfig));	//console.log(playerConfig);
			
			// Each time the parent tr is hidden/displated we need to hide/show our jPlayer container
			// This allows our videos to work with branching logic, etc...
			if (page == "surveys/index.php" || page == "DataEntry/index.php") {
				
			try {
				// Switching to observer method as DOMSubtreeModified was discontinued
				var observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mutation) {
						//console.log('Mutation: ' + mutation.type); console.log(mutation);
						var target = mutation.target;
						var sq_id = $(target).attr('sq_id');
						var video_tr = $('tr[contains="video"][field_name="'+sq_id+'"]');
						matchVisibility(target, video_tr[0]);
					})
				});
				var observer_config = { attributes: true, attributeOldValue: true, attributeFilter: ['style'] };
				observer.observe(rq.tr[0], observer_config);
			} catch(err) {
				try {
					$(rq.tr).bind('DOMSubtreeModified',function(e){
						var isVisible = $(this).is(":visible");
						var sq_id = $(this).attr('sq_id');
						var video_tr = $('tr[contains="video"][field_name="'+sq_id+'"]');
						//console.log('video_tr');console.log(video_tr);
						if (isVisible) {
							$(video_tr).show();
						} else {
							$(video_tr).hide();
							$(this).jPlayer("play",0);
						}
						//console.log('video_tr');console.log(video_tr);
					});
				} catch (err2) {
					// Error 2
				}
			}

			}
			
			// Check if library is defined in label and add to global config for page
//			if (typeof playerConfig === 'undefined') {
//				alert ('invalid config');
//				return;
//			} else {
//				playerLibrary[rq.field_name] = playerConfig;
//			}
			//console.log(playerLibrary);
			
			// Get player HTML to insert into page
			var div_class = playerConfig.type == 'audio' ? 'jp-audio' : 'jp-video';
			
			var videoHtml = '<tr field_name="'+rq.field_name+'" contains="video" width="100%" ' + (rq.visible ? '' : 'style="display:none;"') + '>' +
				'<td colspan=' + (page=='surveys/index.php' ? 3 : 2 ) + ' width="100%" style="text-align:center;background-color:#F3F3F3;">' +
					'<div class="hideUntilReady" style="width:100%;">' +
						getJplayerHtml(rq.field_name, div_class) +
					'</div>' +
				'</td>' +
			'</tr>';
			//console.log('videoHtml');console.log(videoHtml);
			
			
			// Insert video in a new table row
			var video_tr = $(tr).last().after(videoHtml);
			
			// Container DIV for element
			var container = $('#jp_container_' + params.field_name);
			//console.log('container');console.log(container);
			
			// Hide the note (except on online-designer)
			if (page == "DataEntry/index.php" || page == "surveys/index.php") {
				$(this).css('display','none');
			} else {
				$(this).append('<br><em>This note will not be visible on the survey or data entry form</em>');
			}
			
			// Convert DIV into jPlayer
			var jp = $("#jquery_jplayer_" + rq.field_name).jPlayer({
				ready: function () {
				
					// Set Media Object
					if(!playerConfig.media) {
						alert('Invalid player configuration');
						return false;
					} else {
						//console.log('media');console.log(playerConfig.media);
						$(this).jPlayer("setMedia", playerConfig.media);
					}
					//console.log('playerConfig');console.log(playerConfig);

					// Set title
					if (playerConfig.title) { $('.jp-title ul li', container).text(playerConfig.title); } else { $('.jp-title').hide(); }
					//console.log('playerConfig.title');console.log(playerConfig.title);
				
					// Set size
					if (playerConfig.width || playerConfig.height) {
						//$(this).jPlayer("option", "size", {width: playerConfig.width, height: playerConfig.height, cssClass: "jp-video"});
					}

					// Fullscreen
					if (page == "surveys/index.php") {
						if (playerConfig.fullscreen) { $(this).jPlayer("option", "fullScreen", true); }
					}
		
					// Disable video context menu to reduce ability to save
					if (page == "surveys/index.php" && playerConfig.hideContextMenu) {
						$(this).bind('contextmenu',function() { return false; });				
					}


					// hideControls if specified - removes play buttons, etc..
					if ((page == "DataEntry/index.php" || page == "surveys/index.php") && playerConfig.hideControls) {
						//console.log('Hiding Controls');
						$(".jp-interface", $(this).closest(".jp-video")).hide();
					}

					// Autostart
					if (page == "surveys/index.php" || page == "DataEntry/index.php") {
						if (playerConfig.autostart) {
							//console.log('autostart');
							$(this).jPlayer("play");
						}				
					}
				
					// Show resume buttons if video complete
					if (playerConfig.mustWatch) {
						// Get next/submit button
						var mustWatchText = 'Watch video to continue...';
						var btn = $('button:last', 'tr.surveysubmit');
						var btnSpan = $('span',btn);
						if ($(btnSpan).text() !== mustWatchText) {
							$(btn).attr('oldText', $('span', btn).text()).css('width','auto').css('cursor','wait');
							$('span',btn).text('Watch video to continue...');
							$(btn).attr('disabled','disabled');
							$('span',btn).attr('disabled','disabled');
							//console.log('Hiding btn');console.log(btn);
						}

						// Cleanup when video is done
						$(this).bind($.jPlayer.event.ended, function(event) {
							// Unhide button (if it was hidden)
							var btn = $('button:last', 'tr.surveysubmit');
							//console.log('Restoring btn');console.log(btn);
							$('span', btn).text($(btn).attr('oldText'));
							$(btn).removeAttr('disabled').css('cursor','auto');
							$('span',btn).removeAttr('disabled');
						});
					}
				
				
					if (playerConfig.type == 'audio') {
						//console.log ('Hiding jp-toggles')
						$('ul.jp-toggles').hide();
						$('div.jp-video-play').hide();
					}
				
				
				
					$(this).bind($.jPlayer.event.timeupdate, function (event) {
						var playerTime = Math.round(event.jPlayer.status.currentPercentAbsolute * 10) / 10;
						//var currentTime = event.jPlayer.status.currentTime;
						//console.log('timeupdate: ' + playerTime);
						var curVal = (rq.result.val() ? rq.result.val() : 0);
						if (playerTime > curVal) rq.result.val(playerTime);
						//console.log(rq.result.val());
						$()
				
					});
				
//					$(this).bind($.jPlayer.event.play, function(event) { } );
 
					// Additional end-of-play cleanup
					$(this).bind($.jPlayer.event.ended, function(event) {
						//console.log('ended1');
						
						// Set value to 100% as video was completed
						rq.result.val(100);

						// If autoSubmit is set, then submit
						if (playerConfig.autoSubmit) {
							var f = $(this).parentsUntil($('form')).parent().submit();
						}
						
						// Turning off fullscreen at end of movie isn't working quite right...
						$(this).jPlayer("option", {"fullScreen": false});
						//$(this).jPlayer("option", "restoreScreen", true);
						
						// Enable replay
						$(".jp-video-play", $(this).closest(".jp-video")).show();
						// Rehide the play div since it only does it automatically the first time for some reason
						$(this).bind($.jPlayer.event.play, function(event) {
							$(".jp-video-play", $(this).closest(".jp-video")).hide();							
						});
					});
				
				
				},
				//preload: "auto",
				swfPath: customMediaPath,
				supplied: playerConfig.supplied, 	//"m4v, ogv",
				cssSelectorAncestor: '#jp_container_' + rq.field_name
			});

			//console.log('jplayer configured');
			//$('div.hideUntilReady').show();

			// HIDE Question (instead of hiding tr, we hide all three tds so as not to interfere with detecting branching logic
			if (page == "surveys/index.php" || page == "DataEntry/index.php") {				
				$('td', rq.tr).css('display','none');
				//$('td.label', rq.tr).css('display','none');
			}
			
		});
		
		//console.log('matches iterated.');
	}	// Ignore if not rendering

}
*/
