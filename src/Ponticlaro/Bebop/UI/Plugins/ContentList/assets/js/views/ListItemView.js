;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List || {};

	var ItemView = List.ItemView = Backbone.View.extend({	

		tagName: 'li',

		className: 'bebop-list--item',

		events: {
			'click [bebop-list--action]': 'doAction'
		},

		initialize: function(options) {

			// Insert template html into item element
			this.$el.html(options.template);

			// Collect data container and pass data into it
			this.$dataContainer = this.$el.find('[bebop-list--el="data-container"]');
			this.$dataContainer.attr('name', options.fieldName +'[]').val(JSON.stringify(this.model.attributes));

			// Collect views
			this.views = {
				browse: {
					$el: this.$el.find('[bebop-list--view="browse"]'),
					fields: {}
				},
				edit: {
					$el: this.$el.find('[bebop-list--view="edit"]'),
					fields: {}
				},
				reorder: {
					$el: this.$el.find('[bebop-list--view="edit"]'),
					fields: {}
				}
			}

			// Collect fields from each view
			_.each(this.views, function(view, key) {

				_.each(view.$el.find('[bebop-list--field]'), function(field){

					var $field     = $(field),
						fullValue  = $field.attr('bebop-list--field'),
						details    = fullValue.split(':'),
						name       = details[0],
						targetAttr = details.length > 1 ? details[1] : null;

					this.views[key].fields[name] = {
						$el: $field,
						targetAttr: targetAttr
					}

				}, this);

			}, this);

			// Add event listeners for model events
			this.listenTo(this.model, 'change:view', this.render);
			this.listenTo(this.model, 'destroy', this.destroy);
		},

		doAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-list--action');

			// Execute action if available
			if (this[action] != undefined) this[action](event);
		},

		edit: function(event) {

			this.model.set('view', 'edit');
		},

		save: function(event) {

			this.model.set('view', 'browse');
		},

		remove: function(event) {

 			this.model.destroy();
		},

		destroy: function() {

			this.$el.slideUp(250, function() {

				$(this).remove();
			})
		},

		updateData: function(event) {

			var view = this.model.previous('view');

			_.each(this.views[view].fields, function(field, name) {

				if (view == 'edit') {

					 value = field.$el.val();

				} else {

					if (field.targetAttr) {

						value = field.$el.attr(field.targetAttr);

					} else {

						value = field.$el.text();
					}
				}

				this.model.set(name, value);

			}, this);

			this.$dataContainer.val(JSON.stringify(this.model.attributes));
		},

		render: function() {

			// Get current view
			var view = this.model.get('view');

			// Update data if view has changed
			if(this.model.hasChanged('view')) this.updateData();

			// Loop through all fields and update them with current data from previous view
			_.each(this.views[view].fields, function(field, name) {

				if (view == 'edit') {

					field.$el.val(this.model.get(name));

				} else {

					if (field.targetAttr) {

						field.$el.attr(field.targetAttr, this.model.get(name));

					} else {

						field.$el.text(this.model.get(name));
					}
				}

			}, this);

			// Show current view and hide all other
			this.views[view].$el.show().siblings().hide();

			// Handle action buttons
			if (view == 'edit') {

				this.$el.find('[bebop-list--action="edit"]').attr('bebop-list--action', 'save')
						.find('b').text('Save').end()
						.find('span').removeClass('bebopools-ui-icon-edit').addClass('bebopools-ui-icon-save');
				
				this.$el.find('[bebop-list--action="remove"]').attr('disabled', true);

			} else {

				this.$el.find('[bebop-list--action="save"]').attr('bebop-list--action', 'edit')
						.find('b').text('Edit').end()
						.find('span').removeClass('bebopools-ui-icon-save').addClass('bebopools-ui-icon-edit');
			
				this.$el.find('[bebop-list--action="remove"]').attr('disabled', false);
			}

			// Show item if not already visible
			if(!this.$el.is(':visible')) this.$el.slideDown(200);

			return this;
		}
	});

})(window, document, undefined, jQuery || $);