;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List || {};

	var ItemView = List.ItemView = Backbone.View.extend({	

		tagName: 'li',

		className: 'bebop-list--item',

		events: {
			'click [bebop-list--action]': 'doAction',
			'change [bebop-list--el="content"] [bebop-ui--field]': 'updateSingle',
			'keyup [bebop-list--el="content"] [bebop-ui--field]': 'updateSingle'
		},

		initialize: function(options) {

			var self = this;

			// Set main template as $el html
			this.$el.html(options.templates.main);

			this.$content = this.$el.find('[bebop-list--el="content"]');

			this.fields = {}

			// Build views object
			this.views = {
				browse: {
					$el: this.$el.find('[bebop-list--view="browse"]'),
					template: options.templates.browse,
				},
				edit: {
					$el: this.$el.find('[bebop-list--view="edit"]'),
					template: options.templates.edit,
					cleanHTML: options.templates.edit.replace(/\{\{[^\}]*\}\}/g, '')
				}
			}

			// Collect data container input
			this.$dataContainer = this.$el.find('[bebop-list--el="data-container"]').attr('name', options.fieldName +'[]');

			this.mode = options.mode ? options.mode : null;

			// Get image widget
			if (this.mode == 'gallery') {

				new Bebop.Media({
					el: this.$el.find('[bebop-media--el="container"]'),
					id: this.model.get('id')
				});
			}

			// Insert JSON data into data container
			this.storeData();

			// Add event listeners for model events
			this.listenTo(this.model, 'change', this.storeData);
			this.listenTo(this.model, 'change:view', this.render);
			this.listenTo(this.model, 'destroy', this.destroy);
		},

		doAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-list--action');

			// Execute action if available
			if (this[action] != undefined) this[action](event);
		},

		edit: function() {
			this.model.set('view', 'edit');
		},

		browse: function() {
			this.model.set('view', 'browse');
		},

		updateSingle: function(event) {

			var name = $(event.currentTarget).attr('bebop-ui--field');

			this.model.set(name, this.getFieldValue(name));
		},

		update: function() {

			_.each(this.views.edit.fields, function(field, name) {
				
				this.model.set(name, this.getFieldValue(name));

			}, this);
		},

		storeData: function() {

			// Clone model attributes so that we can exclude 'view' from data to be saved
			var data = _.clone(this.model.attributes);

			// Remove 'view' from data to be saved
			delete data.view;

			this.$dataContainer.val(JSON.stringify(data));
		},

		remove: function() {

 			this.model.destroy();
		},

		destroy: function() {

			this.$el.slideUp(250, function() {

				$(this).remove();
			})
		},

		prepareView: function() {

			var view = this.model.get('view');

			// Collect fields and add missing ones to the model
			_.each(this.$content.find('[name]:not([name^="___bebop-ui--placeholder-"])'), function(el, index){

			 	var $el     = $(el),
			 		name    = $el.attr('name'),
			 		type    = $el.attr('type');
			 		newName = type == 'radio' || type == 'checkbox' ? '___bebop-ui--placeholder-'+ name : null;

			 	$el.attr('name', newName).attr('bebop-ui--field', name);

			 	this.fields[name] = {
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


			// Handle action buttons
			if (view == 'edit') {

				this.$el.find('[bebop-list--action="edit"]').attr('bebop-list--action', 'browse')
						.find('b').text('Save').end()
						.find('span').removeClass('bebop-ui-icon-edit').addClass('bebop-ui-icon-save');
				
				this.$el.find('[bebop-list--action="remove"]').attr('disabled', true);

			} else {

				this.$el.find('[bebop-list--action="browse"]').attr('bebop-list--action', 'edit')
						.find('b').text('Edit').end()
						.find('span').removeClass('bebop-ui-icon-save').addClass('bebop-ui-icon-edit');
			
				this.$el.find('[bebop-list--action="remove"]').attr('disabled', false);
			}
		},

		getFieldValue: function(name)
		{
			var $field = this.$content.find('[bebop-ui--field="'+ name +'"]');

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

		getPrettyValue: function(name, value) {

			if(name == 'view') return value;

			var $field = $('<div>').html(this.views.edit.cleanHTML).find('[name="'+ name +'"]');

			if ($field.length > 0 && $field.get(0).tagName == 'SELECT') {

				value = value ? $field.find('option[value="'+ value +'"]').text() : value;
			};

			return value;
		},

		getTemplateData: function() {

			var view = this.model.get('view'),
				data = _.clone(this.model.attributes);

			// Add 'is_' values for mustache templates
			_.each(data, function(value, key) {

				if (value instanceof Array) {

					_.each(value, function(singleValue, index, valuesList) {
					
						// Check for "pretty" values for browse or reorder view
						if(view != 'edit') {
							//data[key][index] = this.getPrettyValue(key, singleValue);
						}

						data[key + '_has_' + singleValue] = true;

					}, this);

				} else {

					// Check for "pretty" values for browse or reorder view
					if(view != 'edit') {
						data[key] = this.getPrettyValue(key, value);
					}

					data[key + '_is_' + value] = true;
				}

			}, this);

			return data;
		},

		render: function() {

			// Update model if we moved from the edit view
			if (this.model.hasChanged('view') && this.model.previous('view') == 'edit') this.update();

			var view     = this.model.get('view'),
				viewHtml = Mustache.render(this.views[view].template, this.getTemplateData());

			// Render current view
			this.views[view].$el.html(viewHtml);

			// Prepare current view for interaction
			this.prepareView();

			// Show current view
			this.views[view].$el.show().siblings('[bebop-list--view]').hide();

			// Show item if not already visible
			if(!this.$el.is(':visible')) this.$el.slideDown(200);

			return this;
		}
	});

})(window, document, undefined, jQuery || $);