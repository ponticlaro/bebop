;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List || {};

	var ItemView = List.ItemView = Backbone.View.extend({	

		tagName: 'li',

		events: {
			'click [bebop-list--action]': 'doAction'
		},

		initialize: function(options) {

			this.$el.attr('bebop-list--el', 'item').addClass('bebop-list--item').html(options.template);
			this.$dataContainer = this.$el.find('[bebop-list--el="data-container"]');
			this.$dataContainer.attr('name', options.fieldName +'[]').val(JSON.stringify(this.model.attributes));

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

		updateData: function(event) {

			var self    = this,
				view    = this.model.previous('view'),
				$fields = this.$el.find('[bebop-list--view="'+ view +'"] [bebop-list--field]'),
				data    = {};

			_.each($fields, function(field, index) {

				var $field       = $(field),
					fieldString  = $field.attr('bebop-list--field'),
					fieldDetails = fieldString.split(':'),
					fieldName    = fieldDetails[0],
					fieldTarget  = fieldDetails.length > 1 ? fieldDetails[1] : null,
					value        = ""

				if (view == 'edit') {

					 value = $field.val();

				} else {

					if (fieldTarget) {

						value = $field.attr(fieldTarget);

					} else {

						value = $field.text();
					}
				}

				data[fieldName] = value;
				self.model.set(fieldName, value);
			});

			this.$dataContainer.val(JSON.stringify(data));
		},

		render: function() {

			var self           = this,
				currentViewId  = this.model.get('view'),
				previousViewId = this.model.previous('view') ? this.model.previous('view') : currentViewId,
				$currentView   = this.$el.find('[bebop-list--view="'+ currentViewId +'"]');

			if (this.model.hasChanged('view')) this.updateData();

			var $fields = this.$el.find('[bebop-list--view="'+ currentViewId +'"] [bebop-list--field]');

			_.each($fields, function(field, index) {

				var $field       = $(field),
					fieldString  = $field.attr('bebop-list--field'),
					fieldDetails = fieldString.split(':'),
					fieldName    = fieldDetails[0],
					fieldTarget  = fieldDetails.length > 1 ? fieldDetails[1] : null;

				if (currentViewId == 'edit') {

					$currentView.find('[bebop-list--field="'+ fieldName +'"]').val(self.model.get(fieldName));

				} else {

					if (fieldTarget) {

						$currentView.find('[bebop-list--field="'+ fieldString +'"]').attr(fieldTarget, self.model.get(fieldName));

					} else {

						$currentView.find('[bebop-list--field="'+ fieldName +'"]').text(self.model.get(fieldName));
					}
				}
			});

			$currentView.show().siblings().hide();

			if (currentViewId == 'edit') {

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
		},

		destroy: function() {

			var self = this;

			this.$el.slideUp(250, function() {
				$(this).remove();
			})
		}
	});

})(window, document, undefined, jQuery || $);