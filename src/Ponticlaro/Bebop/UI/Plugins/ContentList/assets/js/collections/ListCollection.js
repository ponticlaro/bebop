;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List || {};

	var Collection = List.Collection = Backbone.Collection.extend({
		
		model: List.ItemModel,
		
		sync: function () {
			return false; 
		}
	});

})(window, document, undefined, jQuery || $);