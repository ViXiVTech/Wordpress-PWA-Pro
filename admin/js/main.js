jQuery(document).ready(function($){
    $('.pwapro-colorpicker').wpColorPicker();	// Color picker
	$('.pwapro-icon-upload').click(function(e) {	// Application Icon upload
		e.preventDefault();
		var pwapro_meda_uploader = wp.media({
			title: 'Application Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = pwapro_meda_uploader.state().get('selection').first().toJSON();
			$('.pwapro-icon').val(attachment.url);
		})
		.open();
	});
	$('.pwapro-splash-icon-upload').click(function(e) {	// Splash Screen Icon upload
		e.preventDefault();
		var pwapro_meda_uploader = wp.media({
			title: 'Splash Screen Icon',
			button: {
				text: 'Select Icon'
			},
			multiple: false  // Set this to true to allow multiple files to be selected
		})
		.on('select', function() {
			var attachment = pwapro_meda_uploader.state().get('selection').first().toJSON();
			$('.pwapro-splash-icon').val(attachment.url);
		})
		.open();
	});
});
