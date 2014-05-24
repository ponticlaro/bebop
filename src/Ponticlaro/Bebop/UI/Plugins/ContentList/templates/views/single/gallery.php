<div bebop-list--el="container" 
	 bebop-list--config='<?php echo json_encode($this->config->get()); ?>'
	 bebop-list--data='<?php echo json_encode(preg_replace("/'/", "&#39;", $this->data->get())); ?>'
	 >

	<script bebop-list--itemTemplate="main" class="bebop-list--item" type="text/template" style="display:none">
		
		<input bebop-list--el="data-container" type="hidden">
		
		<div class="bebop-list--drag-handle">
			<span class="bebop-ui-icon-move"></span>
		</div>

		<div bebop-list--el="content" class="bebop-list--item-content bebop-ui-clrfix">
			<?php Ponticlaro\Bebop::UI()->Media('Image', '', array(
				'field_name' => 'id',
				'mime_types' => array(
					'image'
				)
			))->render(); ?>
			<div bebop-list--view="browse"></div>
			<div bebop-list--view="reorder"></div>
			<div bebop-list--view="edit"></div>
		</div>

		<div bebop-list--el="item-actions">
			<button bebop-list--action="edit" class="button button-small">
				<b>Edit</b>
				<span class="bebop-ui-icon-edit"></span>
			</button>
			<button bebop-list--action="remove" class="button button-small">
				<span class="bebop-ui-icon-remove"></span>
			</button>
		</div>
	</script>
	
	<?php $views = $this->getAllItemViews();

	if ($views) {

		foreach ($views as $key => $template) { ?>
			 
			<script bebop-list--itemTemplate="<?php echo $key; ?>" type="text/template" style="display:none">
				<?php echo $template ?>
			</script>

		<?php }

	} ?>

	<script bebop-list--formTemplate="top" type="text/template" style="display:none">
		<?php if ($this->config->get('show_top_form')) echo $this->getForm(); ?>
	</script>

	<script bebop-list--formTemplate="bottom" type="text/template" style="display:none">
		<?php if ($this->config->get('show_bottom_form')) echo $this->getForm(); ?>
	</script>

</div>