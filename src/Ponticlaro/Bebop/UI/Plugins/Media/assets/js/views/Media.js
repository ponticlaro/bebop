;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var Media = Bebop.Media = Backbone.View.extend({

		events: {
			'click [bebop-media--action]': 'doAction'
		},

		initialize: function(options) {

			console.log('NEW MEDIA WIDGET INITIALIZED');

			// Collect DOM elements
			this.$el            = $(options.el);
			this.$previewer     = this.$el.find('[bebop-media--el="previewer"]');
			this.$actions       = this.$el.find('[bebop-media--el="actions"]');
			this.$dataContainer = this.$el.find('input');

			// Collect templates
			var $imageTemplate    = this.$el.find('[bebop-media--template="image-view"]'),
				$nonImageTemplate = this.$el.find('[bebop-media--template="non-image-view"]'),
				$emptyTemplate    = this.$el.find('[bebop-media--template="empty-view"]'),
				$loadingTemplate    = this.$el.find('[bebop-media--template="loading-view"]');

			this.templates = {
				image: $imageTemplate.html(),
				nonImage: $nonImageTemplate.html(),
				empty: $emptyTemplate.html(),
				loading: $loadingTemplate.html()
			}

			$imageTemplate.remove();
			$nonImageTemplate.remove();
			$emptyTemplate.remove();
			$loadingTemplate.remove();

			// Set default status model
			this.status = new Backbone.Model({
				view: 'loading',
				id: this.$dataContainer.val(),
				data: null
			});

			// Get instance configuration
			var config  = this.$el.attr('bebop-media--config');
			this.config = new Backbone.Model(config ? JSON.parse(config) : {});
			this.$el.attr('bebop-media--config', null);

			// Get field name
			this.fieldName = this.config.get('field_name');

			// Instantiate WordPress media picker
			this.mediaPicker = wp.media({
				frame: 'select',
	            multiple: false,
	            title: this.config.get('title'),
	            library: {
	                type: this.config.get('mime_types')
	            },
	            button: {
	                text: this.config.get('button_text')
	            }
			});

			this.mediaPicker.on("select", function() {

				var selection = this.mediaPicker.state().get('selection').toJSON(),
					file      = selection.length > 0 ? selection[0] : null;

				console.log(file);

				if (file) {

					this.status.set('data', file);
					this.status.set('id', file.id);

					// Images
					if (file.type == 'image') {
						this.status.set('view', 'image');
					} 

					// Non-images
					else {
						this.status.set('view', 'nonImage');
					
					}

				}

				this.render();

			}, this);

			this.listenTo(this.status, 'change:view', this.render);
			this.listenTo(this.status, 'change:data', this.handleNewData);

			if (this.status.get('view') == 'loading' && this.status.get('id')) {
				this.fetchMedia();

			} else {
				this.status.set('view', 'empty');
			}

			this.render();
		},

		doAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-media--action');

			// Execute action if available
			if (this[action] != undefined) this[action](event);
		},

		storeData: function() {
			this.$dataContainer.val(this.status.get('id'));
		},

		select: function() {
			this.mediaPicker.open();
		},

		remove: function() {
			this.status.set('data', null);
		},

		fetchMedia: function() {

			var self = this,
				url  = location.protocol +'//'+ location.host +'/_bebop-api/posts/'+ this.status.get('id');

			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(data) {

					if (data && data.ID != 'undefined') {
						self.status.set('data', data);
					}
				},
				error: function() {

				}
			});
		},

		handleNewData: function() {

			var data = this.status.get('data');

			if (data) {
				id          = data.id != undefined ? data.id : data.ID,
				typeValue   = data.post_mime_type != undefined ? data.post_mime_type : data.mime,
				view        = typeValue.indexOf('image') != -1 ? 'image' : 'nonImage',
				data.url    = data.permalink != undefined ? data.permalink : data.url;
				data.title  = data.post_title != undefined ? data.post_title : data.title;

				this.status.set('id', id);
				this.status.set('view', view);

			} else {

				this.status.set('id', '');
				this.status.set('view', 'empty');
			}

			this.storeData();
		},

		render: function(){

			var view = this.status.get('view'),
				data = this.status.get('data'),
				html = Mustache.render(this.templates[view], data);

			if (data && data.url != undefined) {

				this.$actions.find('[bebop-media--action="select"] b').text('Change');

			} else {

				this.$actions.find('[bebop-media--action="select"] b').text('Select');
			}

			this.$previewer.html(html);

			return this;
		}
	});

})(window, document, undefined, jQuery || $);