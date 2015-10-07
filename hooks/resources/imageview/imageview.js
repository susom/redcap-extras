
function imageviewStart() {
	console.log ('Running ImageView');
	
	// Find images
	$(imageviewFields).each(function() {
		// Render each upload
		imageView(this);
		
		// Monitor newly uploaded files so we can add the images immediately afterward
		var watch = $('input[name='+this+']');
		var watch2 = $('a#' + this + '-link');
		
		try {
			// Switching to observer method as DOMSubtreeModified was discontinued
			var observer = new MutationObserver(function(mutations) {
				mutations.forEach(function(mutation) {
					//console.log('Mutation: ' + mutation.type); console.log(mutation);
					var target = mutation.target;
					//console.log ( $(target) );
					//var field = $(target).attr('name');
					//console.log ( field );
					var url = $(target).attr('href');
					//console.log ( 'url: ' + url);
					
					//var timeoutID = window.setTimeout(function() {
						var field = $(target).attr('id').replace('-link','');
					//	console.log('Field is ' + field);
						imageView(field);
						//}, 1000);

					//imageView(field);
					//var sq_id = $(target).attr('sq_id');
					//var video_tr = $('tr[contains="video"][field_name="'+sq_id+'"]');
					//matchVisibility(target, video_tr[0]);
				})
			});
			//var observer_config = { attributes: true, attributeOldValue: true, attributeFilter: ['style'] };
			var observer_config = { attributes: true, attributeOldValue: true, attributeFilter: ['value', 'href'] };
			//observer.observe(watch[0], observer_config);
			observer.observe(watch2[0], observer_config);
			console.log ('watching...'); console.log(watch2[0]);
			// console.log(img_link[0]);
		} catch(err) {
			throw(err);
			try {
				$(watch).bind('DOMSubtreeModified',function(e){
					console.log ('DOMSubtreeModified event');
					// This code might work for IE but I haven't tested it and it isn't doing anything right now...
				});
			} catch (err2) {
				console.log('err2');
				// Error 2
			}
		}
		
	});
}


function imageView(field) {
	var term = '@IMAGEVIEW';
	// Get question TR
	var tr = $('tr[sq_id='+field+']');
	
	// Get note field
	var note = $('div.note', tr);
	
	// Remove term
	$(note).text( $(note).text().replace(term, ''));
	
	// Get the image link element
	var img_link = $('a#'+field+'-link');
	
	// Get the image url if it exists
	var link = $(img_link).attr('href');

	// If no file has been uploaded - stop processing
	if (link == '') return true;
	
	// Try to idenfity file type from the link text
	/*	Commenting this out because when the filename is long, it doesn't work...
	
	var link_text = $(img_link).text();
	//link_text = link_text.replace(new RegExp("\(.*\)"),"");
	//console.log('Link-text: ' + link_text);
	var patt1=/\.([0-9a-z]{1,5})(?:[\s\(]|$)/i;
	var ext = link_text.match(patt1);
	console.log(ext);
	var supported_image_types = ['png','jpg','jpeg','gif','tiff'];

	if(!ext || typeof ext[1] == "undefined" || supported_image_types.indexOf(ext[1]) == -1 ) {
		console.log ('Skipping');
		return true;
	} 
	*/
	
	// Get the td where the image will go (changes depending on Custom Alignment choice)
	var img_container = $(img_link).closest('td');
	var container_width = $(img_container).width();	// Set image to width of container
	
	// Get the url for the image and add on the response hash
	var response_hash = $('#form input[name=__response_hash__]').val();
	var url = link + '&__response_hash__=' + response_hash;
	console.log('URL: ' + url);
	
	// Delete old image if it exists
	$('img#'+field+'-preview', tr).remove();
	
	// Insert the image
	var img = $('<img/>').prop('id', field+'-preview').prop('src',url).prop('width',container_width);
	//$(img_container).prepend(img);
	$(img_link).prepend(img);
}