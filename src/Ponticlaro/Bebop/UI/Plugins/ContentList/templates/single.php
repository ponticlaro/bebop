<?php

$items      = $config->get('data');
$field_name = $config->get('field_name');

?>

<div bebop-list--el="container" bebop-list--fieldName="<?php echo $field_name; ?>" class="bebop-list--container">

	<div bebop-list--el="form" class="bebop-list--form bebop-ui-clrfix">
		<div class="bebop-list--formField">
			<button bebop-list--action="addOne" class="button button-primary"><?php echo $config->get('label__add_button'); ?></button>
		</div>
	</div>	

	<ul bebop-list--el="list" bebop-list--is-sortable="true" class="bebop-list--list">
		<?php if ($items) {
			foreach ($items as $item) { ?>
				
				<input bebop-list--el="data-placeholder" type="hidden" name="<?php echo $field_name; ?>[]" value='<?php echo $item; ?>'>

			<?php }
		} ?>
	</ul>
	
	<script bebop-list--template="item" class="bebop-list--item" type="text/template" style="display:none">
		
		<input bebop-list--el="data-container" type="hidden">
		
		<div class="bebop-list--drag-handle">
			<span class="bebop-ui-icon-move"></span>
		</div>
		
		<div bebop-list--el="content" class="bebop-list--item-content">
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