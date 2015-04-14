<span title="Select all items" bebop-list--el="bulk-actions-select-all-items" class="bebop-ui-checkbox bebop-ui-icon-checkbox-unchecked"></span>
<div bebop-list--el="bulk-actions-container" class="bulk-actions-container" style="display:none">
	<select name="bulk_action">
		<option value="">Select action...</option>
		<option value="edit">Edit</option>
		<option value="delete">Delete</option>
	</select>
	<button bebop-list--formAction="bulkAction" class="button button-primary">
		<?php echo $instance->getLabel('bulk_action_button'); ?></span>
	</button>
</div>