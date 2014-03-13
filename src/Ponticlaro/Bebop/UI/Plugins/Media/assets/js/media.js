;(function(window, document, undefined, $){

	var Bebop = window.Bebop = window.Bebop || {};

	Bebop.Media = (function(){

		return {




		}

	})();

	$(function(){

		$('.bebop-media--gallery-list').sortable({
			handle: ".bebop-media--drag-handle",
			placeholder: "bebop-media--gallery-item-placeholder",
			forcePlaceholderSize: true
		});

		$('.bebop-media--gallery-list').on('click', 'button', function(e){

			e.preventDefault();

			var $button    = $(this),
				$list      = $button.parents('.bebop-media--gallery-list'),
				fieldname  = $list.attr('bebop-media--gallery-fieldname'),
				$item      = $button.parents('.bebop-media--gallery-item'),
				$input     = $item.find('input'),
				data       = JSON.parse($input.val());

			if ($button.hasClass('edit-button')) {

				$button.removeClass('edit-button').addClass('save-button').attr('title', 'Save')
					   .find('b').text('Save').end()
					   .find('span').removeClass('bebop-ui-icon-edit').addClass('bebop-ui-icon-save');

				var $caption            = $item.find('.caption'),
					$photo_credit       = $item.find('.photo_credit'),
					caption_value       = $caption.text(),
					photo_credit_value  = $photo_credit.text(),
					$caption_input      = $('<textarea>').hide().addClass('caption').val(caption_value),
					$photo_credit_input = $('<input>').hide().attr('type', 'text').addClass('photo_credit').val(photo_credit_value);

				$caption.after($caption_input).remove();
				$photo_credit.after($photo_credit_input).remove();
				$caption_input.show();
				$photo_credit_input.show();

				$item.find('.remove-button').attr('disabled', true);
			
			} else if ($button.hasClass('save-button')) {

				$button.removeClass('save-button').addClass('edit-button').attr('title', 'Edit')
					   .find('b').text('Edit').end()
					   .find('span').removeClass('bebop-ui-icon-save').addClass('bebop-ui-icon-edit');

				var $caption                = $item.find('.caption'),
					$photo_credit           = $item.find('.photo_credit'),
					caption_value           = $caption.val(),
					photo_credit_value      = $photo_credit.val(),
					$caption_container      = $('<span>').hide().addClass('caption').text(caption_value),
					$photo_credit_container = $('<span>').hide().addClass('photo_credit').text(photo_credit_value);

				data.caption      = caption_value;
				data.photo_credit = photo_credit_value;

				$caption.after($caption_container).remove();
				$photo_credit.after($photo_credit_container).remove();
				$caption_container.show();
				$photo_credit_container.show();

				$input.val(JSON.stringify(data));

				$item.find('.remove-button').attr('disabled', false);

			} else if ($button.hasClass('remove-button')) {

				$item.slideUp(300, function(){
					$(this).remove();

					$items = $list.find('.bebop-media--gallery-item');

					if ($items.length == 0) {

						var $input = $('<input>').attr('type', 'hidden')
												 .attr('name', fieldname)
												 .attr('bebop-media--list-is-empty', true)
												 .val("");
						
						$list.append($input);
					}
				});
			}
		})

		$('.bebop-button').on('click', function(e) {

			e.preventDefault();

			var $button    = $(this),
				$parent    = $button.parents('.bebop-media__container'),
				$previewer = $parent.find('.bebop-media--previewer'),
				$input     = $parent.find('input');

			// Select media
			if ($button.hasClass('bebop-select')) {

				var config = {

					frame: 'select',
		            multiple: false,
		            title: 'Select image',
		            library: {
		                type: ''
		            },
		            button: {
		                text: 'Select Image'
		            }
				}

				var mime_types  = $parent.attr('bebop-media--modal-mime-types'),
					title       = $parent.attr('bebop-media--modal-title'),
					button_text = $parent.attr('bebop-media--modal-button-text'),
					multiple    = $parent.attr('bebop-media--modal-multiple');
				
				if (mime_types) config.library.type = JSON.parse(mime_types);
				if (multiple) config.multiple = true;
				if (title) config.title = title;
				if (button_text) config.button.text = button_text;

				var media = wp.media(config);

				media.open();

				media.on("select", function() {

					var selection = media.state().get('selection').toJSON();

					if (media.options.multiple) {

						var $list     = $parent.find('.bebop-media--gallery-list'),
							fieldname = $list.attr('bebop-media--gallery-fieldname'),
							$items    = $list.find('.bebop-media--gallery-item'),
							$template = $parent.find('.bebop-media--template.bebop-media--gallery-item').clone().removeClass('bebop-media--template').show();

						if ($items.length == 0) {

							$list.find('[bebop-media--list-is-empty]').remove();
						};

						$.each(selection, function(key, file){

							var image_url  = typeof file.sizes.thumbnail == 'undefined' ? file.url : file.sizes.thumbnail.url,
								$img       = $('<img/>').attr('width', 150).attr('src', image_url);

							$previewer.html($img);

							var $newItem = $template.clone(),
								$input   = $newItem.find('input');

							$newItem.find('.bebop-media--gallery-item-image').append($img);
							$input.attr('name', fieldname + '[]').val(JSON.stringify({
								"id": file.id,
								"caption": "",
								"photo_credit": ""
							}));

							$list.append($newItem);
						});

					} else {

						var file = selection[0];

						$input.val(file.id);

						if (file.type == 'image') {

							var image_url  = typeof file.sizes.thumbnail == 'undefined' ? file.url : file.sizes.thumbnail.url,
								$img       = $('<img/>').attr('width', 150).attr('src', image_url);

							$previewer.html($img);

						} else {

							var $template = $parent.find('.bebop-media--template.bebop-media--preview-doc').clone().removeClass('bebop-media--template').show();

							$template.find('a').attr('href', file.url);

							$previewer.html($template);
						}
					}
				})
			};

			// Remove media
			if ($button.hasClass('bebop-remove')) {

				// Remove value
				$input.val("");

				// Empty media container
				var $template = $parent.find('.bebop-media--template.bebop-media--preview-no-selected-item').clone().removeClass('bebop-media--template').show();

				$previewer.html($template);
			};
		});
	});


})(window, document, undefined, jQuery || $);