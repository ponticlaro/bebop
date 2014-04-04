;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var MultiList = Bebop.MultiList = Backbone.View.extend({

		events: {
			'click [bebop-multilist--el="tab"]' : 'handleInteractiveNavigation'
		},

		initialize: function(options) {

			// Store reference to current instance
			var self = this;

			// Collect container DOM element
			this.$el = $(options.el);

			// Get tabs container
			this.$tabsContainer = this.$el.find('[bebop-multilist--el="tabs"]');

			// Get panes container
			this.$panesContainer = this.$el.find('[bebop-multilist--el="panes"]');

			// Get tabs
			this.$tabs  = this.$el.find('[bebop-multilist--el="tab"]');

			// Get panes
			this.$panes = this.$el.find('[bebop-multilist--el="pane"]');

			// Start content variable
			this.content = {}

			// Mix tabs and panes
			_.each(this.$tabs, function(tab, index, tabs) {

				this.content[index] = {
					$tab: $(tab),
					$pane: $(this.$panes.get(index))
				}

			}, this)

			// Display first tab
			this.navigateTo(0);
		},

		handleInteractiveNavigation: function(event) {

			event.preventDefault();

			var id = $(event.currentTarget).attr('bebop-multilist--tabID');

			this.navigateTo(id);
		},

		navigateTo: function(id) {

			if (this.content[id] != undefined) {
				this.content[id].$tab.addClass('is-active').siblings().removeClass('is-active');
				this.content[id].$pane.addClass('is-active').siblings().removeClass('is-active');
			}
		},

		render: function(){

			return this;
		}
	});

})(window, document, undefined, jQuery || $);