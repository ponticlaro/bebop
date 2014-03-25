<div bebop-list--el="container" bebop-list--mode="<?php echo $config->get('mode'); ?>" bebop-list--config='<?php echo json_encode($config->get()); ?>' class="bebop-list--container">

	<?php if ($config->get('form_before_list')) include dirname(__FILE__) .'/../partials/form-before.php'; ?>

	<ul bebop-list--el="list" bebop-list--is-sortable="true" class="bebop-list--list">
		<?php if ($config->get('data')) {
			foreach ($config->get('data') as $item) { ?>
				
				<input bebop-list--el="data-placeholder" type="hidden" name="<?php echo $config->get('field_name'); ?>[]" value='<?php echo $item; ?>'>

			<?php }
		} ?>
	</ul>

	<?php if ($config->get('form_after_list')) include dirname(__FILE__) .'/../partials/form-after.php'; ?>
	
	<script bebop-list--template="item" class="bebop-list--item" type="text/template" style="display:none">
		
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

	<script bebop-list--template="browse-view" type="text/template" style="display:none">
		<?php echo $config->get('browse_view'); ?>
	</script>

	<script bebop-list--template="edit-view" type="text/template" style="display:none">
		<?php echo $config->get('edit_view'); ?>
	</script>

	<div bebop-list--el="empty-state-indicator" class="bebop-list--empty-state-indicator" style="display:none">
		<input type="hidden">
		<span class="bebop-list--item-name">No items added until now</span>
	</div>
	
</div>