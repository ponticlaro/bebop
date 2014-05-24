;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List = Backbone.View.extend({

		events: {
			'click [bebop-list--el="form"] [bebop-list--formAction]': 'doFormAction'
		},

		initialize: function(options) {

			// Store reference to current instance
			var self = this;

			//////////////////////////
			// Handle configuration //
			//////////////////////////
			var config  = this.$el.attr('bebop-list--config');
			this.config = new Backbone.Model(config ? JSON.parse(config) : {});
			this.$el.attr('bebop-media--config', null);

			// Build status object
			this.status = new Backbone.Model({
				mode: this.config.get('mode'),
				view: 'browse',
				insertPosition: null,
				empty: false,
				isSortable: true,
				templateEngine: 'mustache',
				currentEvent: null
			});

			//////////////////////////////
			// END Handle configuration //
			//////////////////////////////

			//////////////////////////////////
			// Build default HTML structure //
			//////////////////////////////////
			
			// Collect container DOM element
			this.$el = $(options.el);

			// Title
			this.$title = $('<div>').attr('bebop-list--el', 'title');

			if (this.config.get('title')) 
				this.$el.append(this.$title.text(this.config.get('title')));

			// Description
			this.$description = $('<div>').attr('bebop-list--el', 'description');

			if (this.config.get('description')) 
				this.$el.append(this.$description.text(this.config.get('description')));

			// Top Form
			this.$topForm = $('<div>').attr('bebop-list--el', 'form').attr('bebop-list--formId', 'top').addClass('bebop-ui-clrfix');
			this.$el.append(this.$topForm);

			// List
			this.$list = $('<ul>').attr('bebop-list--el', 'list');
			this.$el.append(this.$list);

			// Empty state indicatior
			this.$emptyStateIndicator = $('<div>').attr('bebop-list--el', 'empty-state-indicator').css('display', 'none')
												  .append('<input type="hidden"><span class="bebop-list--item-name">No items added until now</span>');
			this.$el.append(this.$emptyStateIndicator);

			// Bottom Form
			this.$bottomForm = $('<div>').attr('bebop-list--el', 'form').attr('bebop-list--formId', 'bottom').addClass('bebop-ui-clrfix');
			this.$el.append(this.$bottomForm);

			//////////////////////////////////////
			// END Build default HTML structure //
			//////////////////////////////////////

			//////////////////
			// Collect data //
			//////////////////
			this.collection = new List.Collection;

			var dataJSON = this.$el.attr('bebop-list--data'),
				data     = dataJSON ? JSON.parse(dataJSON) : [];

			_.each(data, function(item) {

				this.collection.add(JSON.parse(item));
			}, this);

			//////////////////////
			// END Collect data //
			//////////////////////

			$topFormTemplate    = this.$el.find('[bebop-list--template="top-form"]');
			$bottomFormTemplate = this.$el.find('[bebop-list--template="bottom-form"]');
			topFormHtml         = $topFormTemplate.html();
			bottomFormHtml      = $bottomFormTemplate.html();

			if (topFormHtml) this.$el.find('[bebop-list--formId="top"]').html(topFormHtml);
			if (bottomFormHtml) this.$el.find('[bebop-list--formId="bottom"]').html(bottomFormHtml);

			$topFormTemplate.remove();
			$bottomFormTemplate.remove();

			// Collect form DOM element and action buttons
			this.$form   = this.$el.find('[bebop-list--el="form"]');
			this.buttons = {};

			// handle forms & buttons
			_.each(this.$form, function(el, index) {

				var $form    = $(el),
					formId   = $form.attr('bebop-list--formId'),
					$buttons = $form.find('[bebop-list--formAction]');

				_.each($buttons, function(el, index) {

					var $button  = $(el);
						buttonId = $button.attr('bebop-list--formElId');

					if (!$.isArray(this.buttons[buttonId])) this.buttons[buttonId] = [];

					this.buttons[buttonId].push({
						$el: $button,
						formId: formId
					});

				}, this);

			}, this);

			// Get field name
			this.fieldName = this.config.get('field_name');

			//////////////////////
			// Handle templates //
			//////////////////////
			$itemTemplates     = this.$el.find('[bebop-list--itemTemplate]');
			this.itemTemplates = {};

			_.each($itemTemplates, function(el, index) {

				var $el        = $(el),
					templateId = $el.attr('bebop-list--itemTemplate');

				// If we have a template ID, store it in the templates object
				if (templateId) {

					var html;

					if (templateId == 'main') {

						html = $el.clone().find('[bebop-list--el="data-container"]').attr('name', this.fieldName).end().html();
					}

					else {

						html = $el.html();
					}

					this.itemTemplates[templateId] = html;
				}

				// Remove element from DOM
				$el.remove();
				
			}, this);

			this.handleEmptyIndicator = function() {

				if (self.status.hasChanged('empty')) {

					if (self.status.get('empty')) {

						self.$emptyStateIndicator.attr('name', self.fieldName).slideDown(200);

					} else {

						self.$emptyStateIndicator.attr('name', '').slideUp(200);
					}
				}
			}

			this.listenTo(this.status, 'change:empty', this.handleEmptyIndicator);

			if (this.collection.length == 0) this.status.set('empty', true);

			// Remove empty state item
			this.collection.on('add', function(model) {

				var insertPosition = this.status.get('insertPosition');

				if (insertPosition == 'append') {

					this.appendItem(model);

				} else if(insertPosition == 'prepend') {

					this.prependItem(model);
				}

				if(this.collection.length == 1) this.status.set('empty', false);

			}, this);

			// Add empty state item
			this.collection.on('remove', function(model) {

				if(this.collection.length == 0) this.status.set('empty', true);

			}, this);

			// Check sortable configuration attribute
			if (this.$list.attr('bebop-list--is-sortable') == 'true')
				this.status.set('isSortable', true);

			if (this.isMode('gallery')) {

				// Instantiate WordPress media picker
				this.mediaPicker = wp.media({
					frame: 'select',
		            multiple: true,
		            title: 'Upload or select existing resources',
		            library: {
		                type: 'image'
		            },
		            button: {
		                text: 'Add images'
		            }
				});

				this.mediaPicker.on("select", function() {

					var selection = this.mediaPicker.state().get('selection').toJSON();

					_.each(selection, function(file, index, selection) {

						if (file.type == 'image') {

							this.collection.add(new List.ItemModel({
								id: file.id,
								view: this.status.get('view'),
								mode: this.status.get('mode')
							}));
						} 

					}, this);

				}, this);
			}

			this.status.on('change:view', function() {

				this.refresh();

			}, this);

			this.$list.sortable({
				handle: ".bebop-list--drag-handle",
				placeholder: "bebop-list--item-placeholder bebop-ui-icon-target"
			});

			this.render();
		},

		doFormAction: function(event) {

			var action = $(event.currentTarget).attr('bebop-list--formAction');

			// Save current event
			this.status.set('currentEvent', event);

			// Execute action if available
			if (this['formAction_' + action] != undefined) this['formAction_' + action](event);
		},

		isMode: function(mode) {

			return this.status.get('mode') == mode ? true : false;
		},

		formAction_toggleReorder: function(event) {

			if (this.status.get('view') != 'reorder') {

				this.status.set('view', 'reorder');

				_.each(this.buttons.sort, function(item) {
					item.$el.addClass('is-enabled');
				});

			} else {

				this.status.set('view', 'browse');

				_.each(this.buttons.sort, function(item) {
					item.$el.removeClass('is-enabled');
				});
			}
		},

		formAction_insertItem: function(event) {

			this.addNewitem();
		},

		addNewitem: function(data) {

			this.setInsertPosition();

			if (!data) data = {};

			if (this.isMode('gallery')) {

				this.mediaPicker.open();

			} else {

				this.addNewModel(data);
			}
		},

		setInsertPosition: function() {

			var event          = this.status.get('currentEvent'),
				$form          = $(event.currentTarget).parents('[bebop-list--el="form"]'),
				insertPosition = $form.attr('bebop-list--formId') == 'top' ? 'prepend' : 'append';

			this.status.set('insertPosition', insertPosition);
		},

		getNewItemView: function(model) {

			return new List.ItemView({
				model: model,
				templates: this.itemTemplates,
				fieldName: this.fieldName,
				mode: this.status.get('mode')
			});
		},

		addNewModel: function(data) {

			if (!data) data = {};

			if (data.view == undefined) data.view = 'edit'

			this.collection.add(new List.ItemModel(data));
		},

		prependItem: function(model) {

			var itemView = this.getNewItemView(model);

			this.$list.prepend(itemView.render().el);
		},

		appendItem: function(model) {

			var itemView = this.getNewItemView(model);

			this.$list.append(itemView.render().el);
		},

		refresh: function() {

			var previousView = this.status.previous('view'),
				currentView  = this.status.get('view');

			// Re-render all 
			this.collection.each(function(model) {	

				model.set('view', currentView);	

			}, this);
		},

		render: function(){

			// Re-render all 
			this.collection.each(function(model) {	

				this.appendItem(model);

			}, this);

			return this;
		}
	});

	List.addFormAction = function(name, fn) {

		var actionFn = 'formAction_' + name;

		this.prototype[actionFn] = fn;
	}

})(window, document, undefined, jQuery || $);