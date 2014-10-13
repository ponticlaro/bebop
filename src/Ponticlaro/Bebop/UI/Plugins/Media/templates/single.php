<div bebop-media--el="container" bebop-media--config='<?php echo json_encode($data->getAll()); ?>'>
	<input type="hidden" name="<?php echo $data->get('field_name'); ?>" value="<?php echo $data->get('data'); ?>">
</div>