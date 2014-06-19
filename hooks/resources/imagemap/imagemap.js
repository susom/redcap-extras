// ****************************************************************************************************
// IMAGE MAP FUNCTIONS TO ADD IMAGE MAP CAPABILITIES TO REDCAP QUESTIONS
// ****************************************************************************************************

/*
 INSTRUCTIONS:
 THIS FEATURE HAS BEEN MODIFIED TO WORK AS A HOOK SO CAN BE SUPPORTED WITHOUT MODIFICATION TO THE REDCAP SOURCE CODE.

 YOU MUST ALSO COPY THIS FILE (custom_image_map.js) INTO THE /Resoruces/js/ DIRECTORY

 CONFIGURATION:
 TO USE THIS YOU MUST FIRST HAVE A DEFINED IMAGE AND THE AREAS FOR IT WITH THE data-key ATTRIBUTE SET FOR
 THE VALUE YOU WANT TO USE TO RECORD THE SELECTION.

 CREATE A NEW TEXT QUESTION IN YOUR DATA-DICTIONARY AND SET THE NOTES FIELD TO BE: @IMAGEMAP=LIBRARY_NAME (ALL CAPS)

 NOTES:
 EACH TIME YOU UPGRADE REDCAP YOU WILL HAVE TO:
  - RE-ADD THE FUNCTION TO THE END OF BASE.JS
  - COPY THE IMAGES TO THE IMAGE_SOURCE_DIR (APP_PATH_IMAGES IS DEFAULT HERE)
  - COPY THIS FILE TO THE NEW VERSIONS RESOURCES/JS/ DIRECTORY
  - DO A SANITY TEST
*/

// Define your available image maps here, the key to the array should be specified in the notes field of the question,
// as in @IMAGEMAP=PAINMAP_MALE

/**
 * IMAGEMAP FUNCTIONS
 */

function imageMapStart() {
	jQuery(function(){
		//console.log (imageMapLibrary);
		
		// Render each imagemap
		$.each(imageMapLibrary, function(index, value) {
			//console.log ('index: ' + index);
			//console.log (value);
			renderImageMap(value);
		});
		
		// Check if mobile for resizing
		if (isMobileDevice) {
			resizeImageMaps();	// Call it once to set the initial size
			$(window).resize(resizeImageMaps); // Bind to window resizing in case the device is rotated
		}
	});
}

function renderImageMap(params) {
	
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

	// Get result input tag (it is assumed that image maps for now are concatenated, comma-delimited strings in an input field)
	var result = $('input[name="' + params.field + '"]', tr);

	// Hide the note (except on online-designer)
	if (page == "DataEntry/index.php" || page == "surveys/index.php") {
		$(note).css('display','none');
	} else {
		$(note).append('<br><em>This note will not be visible on the survey or data entry form</em>');
	}

	// Hide the input on surveys only
	if (page == "surveys/index.php") {
		// Hide Checkbox Fields
		$('.frmrdh',tr).hide();
		$('.frmrd',tr).hide();
		// Hide text input
		$('input[type=text][name="'+params.field+'"]').hide();
		//$(result).css('display','none');
	}
	
	// Get the image map (with IDs based on the question so you can have multiple of the same image maps on a single page)
	var id = params.field + '_' + params.name;
	var imgTag = $('<img/>', {
		name: params.name,
		field: params.field,
		src: params.src,
		width: params.width,
		id: params.field + '_' + params.name,
		usemap: '#map_' + id,
		alt: params.alt,
		border: 0
	});
	//console.log('imgTag');console.log($(imgTag));
	var mapTag = $('<map/>', {
		order: 1,
		name: 'map_' + id
	}).html(params.areas);
	
	//In this case, the data-set attribute in the image map 
	$('area:not([data-key=""])',mapTag).each(function() {
		$(this).attr('title', $(this).attr('data-key'));
	});

	//console.log('mapTag');console.log($(mapTag));
	var imageMap = $('<div/>').addClass('imagemap').append(imgTag).append(mapTag);
	//console.log('imageMap');console.log($(imageMap));

	// Insert image map after label
	$(label).append(imageMap);

	// Determine if imagemap is selectable
	var selectable = true;
	if (page == "DataEntry/index.php") {
		var regex = new RegExp('editresp\=1$');
		if (!regex.test(document.baseURI)) {
			selectable = false;
		}
	}

	// Apply Mapster to image tag
	var img = $('#'+id, label).mapster({
		fillColor: 'ff0000',
		mapKey:'data-key',
		fillOpacity: 0.4,
		isSelectable: selectable,
		render_highlight: {
			fillColor: '333333',
			fillOpacity: 0.2
		},
		onStateChange: function (data) {
			// Update input when changed
			if (data.state == 'select') {
				updateAreaList(this, data);
			}
		}
	});
	
	// On mobile devices where the viewport is fixed in redcap it may be necessary to resize width
	if (isMobileDevice) {
		$(img).attr('resize_check', 'true');
	}

	// Load saved values
	loadAreaList(params.field);

	// If bound to checkbox, capture checkbox clicks and reflect them in the imageMap
	$('input[type=checkbox]', tr).bind('change', function() {
		var tr = $(this).closest('tr');
		//console.log(tr);
		var field_name = $(tr).attr('sq_id');
		//console.log(field_name);
		var img = $('img[field="'+field_name+'"]', tr).not(".mapster_el");
		//console.log(img);
		var code = $(this).attr('code');
		//console.log(code);
		var checked = $(this).is(":checked");
		//console.log(checked);
		$(img).mapster('set',checked,code);
		//console.log ('is checked: ' + checked);
	});
}

function loadAreaList(field_name) {
	// Get TR for question
	var tr = $('tr[sq_id='+field_name+']');
	//console.log ('tr');console.log(tr);

	img = $('img[field="'+field_name+'"]', tr).not(".mapster_el");
	//console.log ('img');console.log(img);

	// If checkboxes are used, then update imagemap from values
	$('input[type=checkbox]:checked', tr).each(function() {
		// (this) is redcap checkbox field.
		var code = $(this).attr('code');
		//console.log('Code: ' + code);
		$(img).mapster('set',true,code);
		//console.log('Set image to checked at code ' + code);
	});

	// If text - then process from list
	$('input[type=text][name="'+field_name+'"]', tr).each(function() {
		$(img).mapster('set',true,$(this).val());
	})
}

// Takes the values from the image map and saves them to the redcap form
function updateAreaList(image, data) {
	var field_name = $(image).attr('field');
	var tr = $('tr[sq_id='+field_name+']');

	// If checkbox exists - make sure they are in-sync
	$('input[type=checkbox][code="'+data.key+'"]', tr).each(function() {
		//console.log ('Found checkbox ' + data.key);
		//console.log (cb);
		var checked = $(this).is(":checked");
		//console.log ('is checked: ' + checked);
		var selected = data.selected;
		//console.log ('is selected: ' + selected);
		if (checked != selected) {
			$(this).click().trigger('onclick');
			//$(this).blur();
		}
	});

	// If input field is used to hold list, then update list
	$('input[type=text][name="'+field_name+'"]', tr).each(function() {
		// Update input with value from mapster image
		var sel = $(image).mapster('get');
		if (sel) {
			var selSort = sel.split(',').sort().join(',');
			$(this).val(selSort);
		} else {
			$(this).val('');
		}
		$(this).blur();
	});
}

function resizeImageMaps() {
	// find all resize-check images and set width based on viewport width
	var window_width = $( window ).width();	// Get viewport width
	$('img[resize_check="true"]').each( function() {
		var image_width = this.getAttribute('width'); // Get original image width
		var max_width = Math.min(image_width,window_width); // Determine max
		$(this).mapster('resize',max_width,null);
	});
}