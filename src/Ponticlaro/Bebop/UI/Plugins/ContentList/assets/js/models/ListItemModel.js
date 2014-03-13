;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List || {};

	var ItemModel = List.ItemModel = Backbone.Model.extend({	
		
		defaults: {
			view: 'browse'
		},

		sync: function () {
			return false; 
		}
	});

})(window, document, undefined, jQuery || $);