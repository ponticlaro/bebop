;(function(window, document, undefined, $) {

	// When DOM is ready
	$(function() {

		// Generate Media widgets
		$.each($('[bebop-media--el="container"]'), function(index, item) {

			new Bebop.Media({el: item});

		});
	});

})(window, document, undefined, jQuery || $);