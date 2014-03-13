<?php

$container_id         = $data->get('container_id');
$field_name           = $data->get('field_name');
$items                = $data->get('data'); 
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

	<div class="bebop-media__gallery_buttons">
		<button class="button button-primary bebop-button bebop-select">
			<?php echo $data->get('select_button_text'); ?>
		</button>
	</div>

	<ul class="bebop-media--gallery-list" bebop-media--gallery-fieldname="<?php echo $field_name; ?>">
		
		<?php if ($items) { ?>

			<?php foreach ($items as $item) { 

				$json_item = $item;
				$item      = json_decode($item); 

				?>
				
				<li class="bebop-media--gallery-item bebop-ui-clrfix">

					<div class="bebop-media--drag-handle">
						<span class="bebop-ui-icon-move"></span>
					</div>

					<input type="hidden" name="<?php echo $field_name; ?>[]" value='<?php echo $json_item; ?>'>
					
					<div class="bebop-media--gallery-item-image">
						<?php echo wp_get_attachment_image($item->id, 'thumbnail'); ?>
					</div>
					
					<div class="bebop-media--gallery-item-content">

						<strong>Caption:</strong><br>
						<span class="caption"><?php echo $item->caption; ?></span><br><br>

						<strong>Photo Credit:</strong><br>
						<span class="photo_credit"><?php echo $item->photo_credit; ?></span><br><br>
						
						<div class="bebop-media--gallery-item-actions">
							<button title="Edit" class="button edit-button">
								<b>Edit</b>
								<span class="bebop-ui-icon-edit"></span>
							</button>

							<button title="Remove" class="button remove-button">
								<span class="bebop-ui-icon-remove"></span>
							</button>
						</div>
					</div>
	
				</li>

			<?php } ?>

		<?php } else { ?>
			
			<input type="hidden" name="<?php echo $field_name; ?>" bebop-media--list-is-empty="1" value="">

		<?php } ?>

	</ul>

	<li class="bebop-media--template bebop-media--gallery-item wpt-ui-clrfix" style="display:none">
		
		<div class="bebop-media--drag-handle">
			<span class="bebop-ui-icon-move"></span>
		</div>

		<input type="hidden" name="" value=''>	

		<div class="bebop-media--gallery-item-image"></div>
		
		<div class="bebop-media--gallery-item-content">

			<strong>Caption:</strong><br>
			<span class="caption"></span><br><br>

			<strong>Photo Credit:</strong><br>
			<span class="photo_credit"></span><br><br>

			<div class="bebop-media--gallery-item-actions">		
				<button title="Edit" class="button edit-button">
					<b>Edit</b>
					<span class="bebop-ui-icon-edit"></span>
				</button>

				<button title="Remove" class="button remove-button">
					<span class="bebop-ui-icon-remove"></span>
				</button>
			</div>
			
		</div>

	</li>

</div>