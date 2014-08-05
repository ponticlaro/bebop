<div bebop-list--el="container" bebop-list--config='<?php echo preg_replace("/'/", "&#39;", json_encode($this->config->getAll())); ?>'>

	<?php $data = $this->getData();

	if ($data) {

		foreach ($data as $value) { ?>
			 
			<input type="hidden" name='<?php echo $this->getFieldName() .'[]'; ?>' value='<?php echo preg_replace("/'/", "&#39;", $value); ?>'>

		<?php }

	}

	else { ?>

		<input type="hidden" name='<?php echo $this->getFieldName() .'[]'; ?>' value=''>
	
	<?php } ?>

</div>