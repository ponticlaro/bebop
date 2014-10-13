;(function(window, document, undefined, $) {

	window.Bebop = window.Bebop || {};

	var Media = Bebop.Media = Backbone.View.extend({

		events: {
			'click [bebop-media--action]': 'doAction'
		},

		initialize: function(options) {

			// Collect main DOM element
			this.$el = $(options.el);

			if (this.$el.prop('bebop-media--initialized') === undefined) {

				// Mark element as initialized
				this.$el.prop('bebop-media--initialized', true);

				// Collect templates
				var $body = $(document.body);

				this.templates = {
					main: $body.find('[bebop-media--template="main"]').html().trim(),
					image: $body.find('[bebop-media--template="image-view"]').html().trim(),
					nonImage: $body.find('[bebop-media--template="non-image-view"]').html().trim(),
					empty: $body.find('[bebop-media--template="empty-view"]').html().trim(),
					error: $body.find('[bebop-media--template="error-view"]').html().trim(),
					loading: $body.find('[bebop-media--template="loading-view"]').html().trim()
				}

				// Insert main template
				this.$el.append(this.templates.main);

				// Collect other DOM elements
				this.$previewer     = this.$el.find('[bebop-media--el="previewer"]');
				this.$actions       = this.$el.find('[bebop-media--el="actions"]');
				this.$dataContainer = this.$el.find('input');

				// Set default status model
				this.status = new Backbone.Model({
					view: 'loading',
					id: options.id != undefined ? options.id : this.$dataContainer.val(),
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
			};
		},

		doAction: function(event) {

			event.preventDefault();

			var action = $(event.currentTarget).attr('bebop-media--action');

			// Execute action if available
			if (this[action] != undefined) this[action](event);
		},

		storeData: function() {
			this.$dataContainer.val(this.status.get('id')).trigger('change');
		},

		select: function() {
			this.mediaPicker.open();
		},

		remove: function() {
			this.status.set('data', null);
		},

		fetchMedia: function() {

			var self = this,
				url  = location.protocol +'//'+ location.host +'/_bebop/api/posts/'+ this.status.get('id');

			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(data) {

					if (data) {

						self.status.set('data', data);

					} else {

						self.status.set('data', null);
					}
				},
				error: function(xhr) {

					self.status.set('data', {
						error: true,
						status: xhr.status,
						message: xhr.statusText
					});
				}
			});
		},

		handleNewData: function() {

			var data = this.status.get('data');

			if (data) {

				if(data.error != undefined && data.error) {

					this.status.set('view', 'error');

				} else if(data.ID != undefined || data.id != undefined) {

					id          = data.id != undefined ? data.id : data.ID,
					typeValue   = data.post_mime_type != undefined ? data.post_mime_type : data.mime,
					view        = typeValue.indexOf('image') != -1 ? 'image' : 'nonImage',
					data.title  = data.post_title != undefined ? data.post_title : data.title;

					if (data.type == 'image') {

						data.url = data.sizes.thumbnail != undefined ? data.sizes.thumbnail.url : data.url;
					} 

					else {

						data.url = data.permalink != undefined ? data.permalink : data.url;
					}

					this.status.set('id', id);
					this.status.set('view', view);
				}

			} else {

				this.status.set('id', '');
				this.status.set('view', 'empty');
			}

			this.storeData();
		},

		render: function(){

			var prevView    = this.status.previous('view'),
				currentView = this.status.get('view'),
				data        = this.status.get('data'),
				html        = Mustache.render(this.templates[currentView], data);

			this.$el.removeClass('view--' + prevView).addClass('view--' + currentView);

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