;(function(window, document, undefined, $) {

	var Bebop = window.Bebop = window.Bebop || {};

	Bebop = (function() {

		return {
			
		};

	})();

	$('[bebop-list--el="container"]').on('focusin', '[bebop-ui--el="datepicker"]', function() {

		$(this).datepicker({ 
			dateFormat: "M dd, yy"
		});
	});

})(window, document, undefined, jQuery || $);