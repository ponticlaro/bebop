<?php

$container_id         = $data->get('container_id');
$field_name           = $data->get('field_name');
$item_id              = $data->get('data'); 
$item                 = $item_id ? get_post($item_id) : null;
$no_selection_message = $data->get('no_selection_message');

$modal_title       = $data->get('modal_title');
$modal_button_text = $data->get('modal_button_text');
$mime_types        = $data->get('mime_types');
$multiple          = $data->get('multiple');

?>

<div id="<?php echo $container_id; ?>" class="bebop-media__container"
	
	bebop-media--modal-title='<?php echo $modal_title; ?>'
	bebop-media--modal-button-text='<?php echo $modal_button_text ?>'

	<?php if ($multiple) { ?>
		bebop-media--modal-multiple='true'
	<?php } ?>

	<?php if ($mime_types) { ?>
		bebop-media--modal-mime-types='<?php echo json_encode($mime_types); ?>'
	<?php } ?>
>

	<?php if ($data->get('display_label')) { ?>
		<label>
			<?php echo $data->get('label'); ?>
		</label>	
	<?php } ?>

	<div class="bebop-media--previewer">
		<?php if ($item) { ?>

			<?php if (preg_match('/image\//', $item->post_mime_type)) { ?>
				
				<div class="bebop-media--preview-image">
					<?php echo wp_get_attachment_image($item->ID, 'thumbnail'); ?>
				</div>

			<?php } else { ?>
				
				<div class="bebop-media--preview-doc">
					<a target="_blank" class="bebop-media--file-title" href="<?php echo wp_get_attachment_url($item->ID); ?>">
						View selected file in new tab
					</a>
				</div>
			
			<?php } ?>

		<?php } else { ?>

			<div class="bebop-media--preview-no-selected-item">
				<span><?php echo $no_selection_message; ?></span>
			</div>

		<?php } ?>
	</div>
	
	<div class="bebop-media__single_buttons">
		<button class="button button-small bebop-button bebop-select">
			<?php echo $data->get('select_button_text'); ?>
		</button>
		<button class="button button-small bebop-button bebop-remove">
			<?php echo $data->get('remove_button_text'); ?>
		</button>
	</div>

	<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $item_id; ?>">
	
	<div class="bebop-media--template bebop-media--preview-doc" style="display:none">	
		<a target="_blank"  class="bebop-media--file-title" href="">
			View selected file in new tab
		</a>
	</div>

	<div class="bebop-media--template bebop-media--preview-no-selected-item" style="display:none">
		<span class="bebop-media--file-title"><?php echo $no_selection_message; ?></span>
	</div>

</div>

