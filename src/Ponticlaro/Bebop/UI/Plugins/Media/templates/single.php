<div bebop-media--el="container" bebop-media--config='<?php echo json_encode($data->get()); ?>'>

	<div bebop-media--el="previewer"></div>
	
	<div bebop-media--el="actions">
		<button bebop-media--action="select" class="button button-small">
			<b>Select</b> <span class="bebop-ui-icon-file-upload"></span>
		</button>
		<button bebop-media--action="remove" class="button button-small">
			<span class="bebop-ui-icon-remove"></span>
		</button>
	</div>

	<input type="hidden" name="<?php echo $data->get('field_name'); ?>" value="<?php echo $data->get('data'); ?>">
</div>