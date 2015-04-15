;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List = Backbone.View.extend({

		events: {
			'click > [bebop-list--el="form"] [bebop-list--formAction]': 'doFormAction'
		},

		initialize: function(options) {

			// Store reference to current instance
			var self = this,	
				data;

			// Collect container DOM element
			this.$el = $(options.el);

			// Get parent list & model
			this.parentList  = options.parentList;
			this.parentModel = options.parentModel;

			//////////////////////////
			// Handle configuration //
			//////////////////////////
			var config = this.$el.attr('bebop-list--config');

			// Remove config attribute
			this.$el.attr('bebop-list--config', null);

			// This is a child list
			if (config == 'inherit') {

				this.config = this.parentList.config;
			}

			// This is a parent list
			else {

				this.config = new Backbone.Model(config ? JSON.parse(config) : {});
			}

			// Remove config attribute from DOM
			this.$el.attr('bebop-media--config', null);

			// Add mode to container as attribute
			this.$el.attr('bebop-list--mode', this.config.get('mode'));

			// Get field name
			this.fieldName = this.config.get('field_name');

			// Build status object
			this.status = new Backbone.Model({
				isChildList: config == 'inherit' ? true : false,
				dataContext: null,
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

 			//////////////////
			// Collect data //
			//////////////////
			
			// This is a child list
			if (this.status.get('isChildList') && this.parentList) {

				data = this.$el.attr('bebop-list--data');

				var dataContext = data.replace('context:', '');

				this.status.set('dataContext', dataContext);

				var contextData = this.parentModel.get(dataContext);

				this.collection = new List.Collection(contextData);

				this.fieldName = dataContext;

				this.$el.find('[bebop-list--el="data-container"]').attr('name', dataContext);
			} 

			// This is a parent list
			else {

				this.collection  = new List.Collection();
				this.$dataInputs = this.$el.find('input');

				_.each(this.$dataInputs, function(item) {

					var $el   = $(item),
						value = $el.val();

					// Add value to collection
					if (value) {

						this.collection.add(JSON.parse(value));
					}

					// Remove input with empty value from DOM
					else {

						$el.remove();
					}

				}, this);
			}

			//////////////////////
			// END Collect data //
			//////////////////////

			//////////////////////////////////
			// Build default HTML structure //
			//////////////////////////////////
			
			// Title
			this.$title = $('<div>').attr('bebop-list--el', 'title');

			if (!this.status.get('isChildList') && this.config.get('title')) 
				this.$el.append(this.$title.text(this.config.get('title')));

			// Description
			this.$description = $('<div>').attr('bebop-list--el', 'description');

			if (!this.status.get('isChildList') && this.config.get('description')) 
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

			// This is a child list
			if (this.status.get('isChildList') && this.parentList) {

				// Set sort function for child lists
				this.collection.comparator = function(modelA, modelB) {

					var indexOfA = self.$list.find('li[bebop-list--model-id="'+ modelA.cid +'"]').index(),
						indexOfB = self.$list.find('li[bebop-list--model-id="'+ modelB.cid +'"]').index();
					
					if (indexOfA < indexOfB) {

						return -1;
					}

					else if (indexOfA > indexOfB) {

						return 1;
					}

					else {

						return 0;
					}
				};

				this.itemTemplates = this.parentList.itemTemplates;
				this.formTemplates = this.parentList.formTemplates;

				var context = this.status.get('dataContext');

				// Fallback to default form if the context one is empty
				this.formTemplates.main = this.formTemplates[context] !== undefined ? this.formTemplates[context] : this.formTemplates['default'];

				// Set child list field name
				this.itemTemplates.main = $('<div>').html(this.itemTemplates.main).find('[bebop-list--el="data-container"]').attr('name', this.fieldName).end().html();
			} 

			// This is a parent list
			else {

				// Find template container for this list
				var $tplContainer = $('#bebop-list--'+ this.config.get('field_name') +'-templates-container');

				this.formTemplates = {};

				///////////////////////////
				// Handle Form Templates //
				///////////////////////////
				$formTemplates     = $tplContainer.find('[bebop-list--formTemplate]');
				this.formTemplates = {};

				_.each($formTemplates, function(el, index) {

					var $el        = $(el),
						templateId = $el.attr('bebop-list--formTemplate');

					// If we have a template ID, store it in the templates object
					if (templateId) this.formTemplates[templateId] = $el.html().trim();

					// Remove element from DOM
					$el.remove();
					
				}, this);

			
				// Fallback to default form if the main one is empty
				if (this.formTemplates.main === "")
					this.formTemplates.main = this.formTemplates['default'];

				///////////////////////////
				// Handle Item Templates //
				///////////////////////////
				$itemTemplates     = $tplContainer.find('[bebop-list--itemTemplate]');
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

						this.itemTemplates[templateId] = html.trim();
					}

					// Remove element from DOM
					$el.remove();
					
				}, this);

				$tplContainer.remove();
			}

			// Add forms HTML to DOM
			this.$topForm.html(this.formTemplates.main);
			this.$bottomForm.html(this.formTemplates.main);

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

			this.handleEmptyIndicator = function() {

				if (self.status.hasChanged('empty')) {

					if (self.status.get('empty')) {

						self.$emptyStateIndicator.find('input').attr('name', self.fieldName).end().slideDown(200);

					} else {

						self.$emptyStateIndicator.find('input').attr('name', '').end().slideUp(200);
					}
				}
			};

			this.listenTo(this.status, 'change:empty', this.handleEmptyIndicator);

			if (this.collection.length === 0) this.status.set('empty', true);

			// Remove empty state item
			this.collection.on('add', function(model) {

				var insertPosition = this.status.get('insertPosition');

				if (insertPosition == 'append') {

					this.appendItem(model);

				} else if(insertPosition == 'prepend') {

					this.prependItem(model);
				}

				if(this.collection.length == 1) this.status.set('empty', false);

				if (this.status.get('isChildList')) {

					this.collection.sort();
					this.collection.trigger('updateParentCollection');
				}

			}, this);

			// Add empty state item
			this.collection.on('remove', function(model) {

				if (this.collection.length === 0) this.status.set('empty', true);

			}, this);


			if (this.parentList && this.parentModel) {

				// Update parent collection
				this.collection.on('updateParentCollection', function() {

					this.parentModel.set(this.status.get('dataContext'), this.collection.toJSON());
					
				}, this);
			}

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

			// This is a child list
			if (this.status.get('isChildList')) {

				// Handle items order
				this.$list.on("sortstop", function(event, ui) {

					self.collection.sort();
					self.collection.trigger('updateParentCollection');
				});
			}

			this.render();
		},

		doFormAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-list--formAction');

			// Save current event
			this.status.set('currentEvent', event);

			// Execute action if available
			if (this['formAction_' + action] !== undefined) this['formAction_' + action](event);
		},

		isMode: function(mode) {

			return this.status.get('mode') == mode ? true : false;
		},

		formAction_toggleReorder: function(event) {

			if (this.status.get('view') != 'reorder') {

				this.status.set('view', 'reorder');

				_.each(this.buttons.editAll, function(item) {
					item.$el.removeClass('is-enabled')
						.find('b').text('Edit All').end()
					    .find('span').removeClass('bebop-ui-icon-save').addClass('bebop-ui-icon-edit');
				});

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

		formAction_toggleEditAll: function(event) {

			if (this.status.get('view') != 'edit') {

				this.status.set('view', 'edit');

				_.each(this.buttons.sort, function(item) {
					item.$el.removeClass('is-enabled');
				});

				_.each(this.buttons.editAll, function(item) {
					item.$el.addClass('is-enabled')
						.find('b').text('Save All').end()
					    .find('span').removeClass('bebop-ui-icon-edit').addClass('bebop-ui-icon-save');
				});

			} else {

				this.status.set('view', 'browse');

				_.each(this.buttons.editAll, function(item) {
					item.$el.removeClass('is-enabled')
						.find('b').text('Edit All').end()
					    .find('span').removeClass('bebop-ui-icon-save').addClass('bebop-ui-icon-edit');
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
				mode: this.status.get('mode'),
				list: this,
				dataContext: this.status.get('dataContext')
			});
		},

		addNewModel: function(data) {

			if (!data) data = {};

			if (data.view === undefined) data.view = 'edit';

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
			this.collection.each(function(model, index) {	

				// Append new model
				this.appendItem(model);
 
				// Remove source data input from DOM
				if (this.$dataInputs !== undefined && this.$dataInputs.length >= 1)
					this.$dataInputs.get(index).remove();

			}, this);

			return this;
		}
	});

	List.addFormAction = function(name, fn) {

		var actionFn = 'formAction_' + name;

		this.prototype[actionFn] = fn;
	};

})(window, document, undefined, jQuery || $);