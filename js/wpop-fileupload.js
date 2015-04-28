jQuery(document).ready(function($) {

	// PDF uploader in options page

	$('.file-uploaded .dashicons').hover(function() {
		$(this).removeClass('dashicons-yes').addClass('dashicons-no-alt');
	}, function() {
		$(this).removeClass('dashicons-no-alt').addClass('dashicons-yes');
	});

	$('.file-uploaded .dashicons').on('click', function() {
		$('.file-uploaded').fadeOut();
	});


 
 	// Cover metabox asset
 
    var custom_uploader;
 
 
    $('#upload_cover').click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#post_cover').val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
		
});



