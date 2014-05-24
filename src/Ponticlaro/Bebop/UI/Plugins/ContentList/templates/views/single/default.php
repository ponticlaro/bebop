<?php $data = $this->data->get(); ?>

<div bebop-list--el="container" bebop-list--config='<?php echo json_encode($this->config->get()); ?>' class="bebop-list--container">

	<div bebop-list--el="title">
		<?php echo $this->config->get('title'); ?>
	</div>
	
	<?php if ($this->config->get('description')) { ?>

		<div bebop-list--el="description">
			<?php echo $this->config->get('description'); ?>
		</div>

	<?php } ?>

	<div bebop-list--el="form" bebop-list--formId="top" class="bebop-list--form bebop-ui-clrfix"></div>

	<ul bebop-list--el="list" bebop-list--is-sortable="true" class="bebop-list--list">
		<?php if ($data) {
			foreach ($data as $item) {

				// Escape single quotes
				$item = preg_replace("/([^\"]*)'([^\"]*)/", "$1&#39;$2", $item); ?>
								
				<input bebop-list--el="data-placeholder" type="hidden" name="<?php echo $this->config->get('field_name'); ?>[]" value='<?php echo $item; ?>'>

			<?php }
		} ?>
	</ul>

	<div bebop-list--el="empty-state-indicator" class="bebop-list--empty-state-indicator" style="display:none">
		<input type="hidden">
		<span class="bebop-list--item-name">No items added until now</span>
	</div>

	<div bebop-list--el="form" bebop-list--formId="bottom" class="bebop-list--form bebop-ui-clrfix"></div>

	<script bebop-list--itemTemplate="main" class="bebop-list--item" type="text/template" style="display:none">
		
		<input bebop-list--el="data-container" type="hidden">
		
		<div class="bebop-list--drag-handle">
			<span class="bebop-ui-icon-move"></span>
		</div>
		
		<div bebop-list--el="content" class="bebop-list--item-content">
			<div bebop-list--view="browse"></div>
			<div bebop-list--view="reorder"></div>
			<div bebop-list--view="edit"></div>
		</div>

		<div class="bebop-list--item-actions">
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

	<script bebop-list--template="top-form" type="text/template" style="display:none">
		<?php if ($this->config->get('show_top_form')) echo $this->getForm(); ?>
	</script>

	<script bebop-list--template="bottom-form" type="text/template" style="display:none">
		<?php if ($this->config->get('show_bottom_form')) echo $this->getForm(); ?>
	</script>
	
</div>