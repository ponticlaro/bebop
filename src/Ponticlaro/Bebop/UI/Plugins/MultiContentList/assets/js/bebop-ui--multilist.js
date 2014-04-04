;(function(window, document, undefined, $) {

	// When DOM is ready
	$(function() {

		// Generate lists
		$.each($('[bebop-multilist--el="container"]'), function(index, item) {

			new Bebop.MultiList({el: item});

		});
		
	});

})(window, document, undefined, jQuery || $);