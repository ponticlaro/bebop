;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var List = Bebop.List = Backbone.View.extend({

		events: {
			'click [bebop-list--el="form"] [bebop-list--action]': 'doAction'
		},

		initialize: function(options) {

			// Store reference to current instance
			var self = this;

			this.status = new Backbone.Model({
				mode: 'browse',
				empty: false,
				isSortable: true,
				templateEngine: 'mustache'
			});

			this.collection = new List.Collection(),

			// Collect container DOM element
			this.$el = $(options.el);

			// Collect form DOM element
			this.$form = this.$el.find('[bebop-list--el="form"]');

			// Collect list DOM element
			this.$list = this.$el.find('[bebop-list--el="list"]');

			// Collect item DOM elements
			this.$dataPlaceholders = this.$list.find('[bebop-list--el="data-placeholder"]');

			// Add each item to collection
			_.each(this.$dataPlaceholders, function(el, index) {

				var jsonData = $(el).val();

				if (jsonData) this.collection.add(JSON.parse(jsonData));

			}, this);

			// Get field name
			this.fieldName = this.$el.attr('bebop-list--fieldName');

			//////////////////////
			// Handle templates //
			//////////////////////
			this.templates = {};
			
			$rawItemTemplate = this.$el.find('[bebop-list--template="item"]');
			$browseTemplate  = this.$el.find('[bebop-list--template="browse-view"]');
			$editTemplate    = this.$el.find('[bebop-list--template="edit-view"]');

			this.itemTemplates = {
				main: $rawItemTemplate.clone().find('[bebop-list--el="data-container"]').attr('name', this.fieldName).end().html(),
				browse: $browseTemplate.html(),
				edit: $editTemplate.html()
			}

			$rawItemTemplate.remove();
			$browseTemplate.remove();
			$editTemplate.remove();

			// Collect empty state indicator DOM element
			this.$emptyStateIndicator = this.$el.find('[bebop-list--el="empty-state-indicator"]');

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

				if (model.get('view') == 'edit') {

					this.prependOne(model);

				} else {

					this.appendOne(model);
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

			// Make list sortable if isSortable is true
			if (this.status.get('isSortable')) {

				this.$list.sortable({
					handle: ".bebop-list--drag-handle",
					placeholder: "bebop-list--gallery-item-placeholder",
					forcePlaceholderSize: true
				});
			};

			this.render();
		},

		doAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-list--action');

			// Execute action if available
			if (this[action] != undefined) this[action](event);
		},

		addOne: function(event) {

			this.collection.add(new List.ItemModel({view: 'edit'}));
		},

		getNewItemView: function(model) {

			return new List.ItemView({
				model: model,
				templates: this.itemTemplates,
				fieldName: this.fieldName
			});
		},

		prependOne: function(model) {

			var itemView = this.getNewItemView(model);

			this.$list.prepend(itemView.render().el);
		},

		appendOne: function(model) {

			var itemView = this.getNewItemView(model);

			this.$list.append(itemView.render().el);
		},

		render: function(){

			// Render all 
			this.collection.each(function(model) {	

				this.appendOne(model);

			}, this);		

			// Remove all data placeholders if we still have them
			if (this.$dataPlaceholders.length > 0)
				this.$dataPlaceholders.remove();

			return this;
		}
	});

})(window, document, undefined, jQuery || $);