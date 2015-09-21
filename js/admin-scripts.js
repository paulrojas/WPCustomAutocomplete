(function($) {
	// $('.searchautocomplete-settings .sortable').sortable();
	var $settings = $('.wpcustomautocomplete-settings');
	$settings.on('click.wpcustom-autocomplete', '.revert', function(e) {
		e.preventDefault();
		for(var key in WPCustomAutocompleteAdmin.defaults) {
			$('#' + key, $settings).val(WPCustomAutocompleteAdmin.defaults[key]);
		}
	});
})(jQuery);