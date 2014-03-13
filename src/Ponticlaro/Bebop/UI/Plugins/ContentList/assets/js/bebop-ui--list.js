;(function(window, document, undefined, $) {

	// When DOM is ready
	$(function() {

		// Generate lists
		$.each($('[bebop-list--el="container"]'), function(index, item) {

			new Bebop.List({el: item});

		});
		
	});

})(window, document, undefined, jQuery || $);