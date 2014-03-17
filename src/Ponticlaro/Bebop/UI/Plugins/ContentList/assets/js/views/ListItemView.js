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

			this.$template = $(options.template);

			this.$el.html(options.template);

			this.views = options.views;
			this.views.edit.fields = {};

			// Get content container and empty it
			this.$content = this.$el.find('[bebop-list--el="content"]');

			// Collect data container input
			this.$dataContainer = this.$el.find('[bebop-list--el="data-container"]').attr('name', options.fieldName +'[]');

			// Collect fields and add missing ones to the model
			_.each($('<div>').html(this.views.edit.$template.html().replace(/\<%=?.*%\>/g, '')).find('[bebop-list--field]'), function(el, index){

			 	var $el  = $(el),
			 		name = $el.attr('bebop-list--field');

			 	this.views.edit.fields[name] = {
			 		$el: $el,
			 		tagName: $el.get(0).tagName,
			 		type: $el.attr('type')
			 	}

			 	if (!this.model.has(name)) {

			 		var value = '';

			 		// Set value to empty array in case of a select with multiple values
			 		if ($el.get(0).tagName == 'SELECT' && $el.attr('multiple')) {
			 			value = [];
			 		}

			 		this.model.set(name, value);
			 	}

			}, this);

			// Insert JSON data into data container
			this.storeData();

			// Add event listeners for model events
			//this.listenTo(this.$el, 'keyup change', this.update);
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

		browse: function(event) {
			this.model.set('view', 'browse');
		},

		update: function() {

			_.each(this.views.edit.fields, function(field, name) {
				
				this.model.set(name, this.getFieldValue(name));

			}, this);

			this.storeData();
		},

		storeData: function() {

			// Clone model attributes so that we can exclude 'view' from data to be saved
			var data = _.clone(this.model.attributes);

			// Remove 'view' from data to be saved
			delete data.view;

			this.$dataContainer.val(JSON.stringify(data));
		},

		remove: function(event) {

 			this.model.destroy();
		},

		destroy: function() {

			this.$el.slideUp(250, function() {

				$(this).remove();
			})
		},

		getFieldValue: function(name)
		{
			var $field = this.$el.find('[bebop-list--field="'+ name +'"]');
				
			switch($field.get(0).tagName) {

				case 'INPUT':
				
					if ($field.attr('type') == 'checkbox') {

						value = $field.is(':checked') ? $field.val() : '';
					}

					else if ($field.attr('type') == 'radio') {
						
						if ($field.length > 1) {

							value = '';

							_.each($field, function(el, index) {

								var $el = $(el);

								if($el.is(':checked')) value = $el.val();
							});

						} else {

							value = $field.is(':checked') ? $field.val() : '';
						}
					}

					else {

						value = $field.val();
					}

					break;

				case 'SELECT':

					if ($field.attr('multiple')) {

						value = [];

						_.each($field.find('option:selected'), function(option, index) {

						   	value[index] = $(option).val();
						});

					} else {

						value = $field.find('option:selected').val();
					}

					break;

				default: 

					value = $field.val();
					break;
			}

			return value;
		},

		render: function() {

			var view = this.model.get('view');

			// Update model if we moved from the edit view
			if (this.model.hasChanged('view') && this.model.previous('view') == 'edit') this.update();
			
			// Render target view with current model data
			var viewHtml = _.template(this.views[view].$template.html(), this.model.toJSON());

			// Render current view
			this.$content.html(viewHtml);

			// Handle action buttons
			if (view == 'edit') {

				this.$el.find('[bebop-list--action="edit"]').attr('bebop-list--action', 'browse')
						.find('b').text('Save').end()
						.find('span').removeClass('bebopools-ui-icon-edit').addClass('bebopools-ui-icon-save');
				
				this.$el.find('[bebop-list--action="remove"]').attr('disabled', true);

			} else {

				this.$el.find('[bebop-list--action="browse"]').attr('bebop-list--action', 'edit')
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