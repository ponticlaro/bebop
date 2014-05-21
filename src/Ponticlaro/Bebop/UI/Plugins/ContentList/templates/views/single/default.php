<?php 

$data         = $config->get('data');
$browse_view  = $config->get('browse_view');
$reorder_view = $config->get('reorder_view');
$edit_view    = $config->get('edit_view');

$config->remove('data');
$config->remove('browse_view');
$config->remove('reorder_view');
$config->remove('edit_view');

?>

<div bebop-list--el="container" bebop-list--config='<?php echo json_encode($config->get()); ?>' class="bebop-list--container">

	<div bebop-list--el="title">
		<?php echo $config->get('title'); ?>
	</div>
	
	<?php if ($config->get('description')) { ?>

		<div bebop-list--el="description">
			<?php echo $config->get('description'); ?>
		</div>

	<?php } ?>

	<div bebop-list--el="top-form" class="bebop-list--form bebop-ui-clrfix"></div>

	<ul bebop-list--el="list" bebop-list--is-sortable="true" class="bebop-list--list">
		<?php if ($data) {
			foreach ($data as $item) {

				// Escape single quotes
				$item = preg_replace("/([^\"]*)'([^\"]*)/", "$1&#39;$2", $item); ?>
								
				<input bebop-list--el="data-placeholder" type="hidden" name="<?php echo $config->get('field_name'); ?>[]" value='<?php echo $item; ?>'>

			<?php }
		} ?>
	</ul>

	<div bebop-list--el="empty-state-indicator" class="bebop-list--empty-state-indicator" style="display:none">
		<input type="hidden">
		<span class="bebop-list--item-name">No items added until now</span>
	</div>

	<div bebop-list--el="bottom-form" class="bebop-list--form bebop-ui-clrfix"></div>

	<script bebop-list--template="item" class="bebop-list--item" type="text/template" style="display:none">
		
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

	<script bebop-list--template="top-form" type="text/template" style="display:none">
		<?php if ($config->get('show_top_form')) include $config->get('top_form'); ?>
	</script>

	<script bebop-list--template="bottom-form" type="text/template" style="display:none">
		<?php if ($config->get('show_bottom_form')) include $config->get('bottom_form'); ?>
	</script>

	<script bebop-list--template="browse-view" type="text/template" style="display:none">
		<?php echo $browse_view; ?>
	</script>

	<script bebop-list--template="browse-view" type="text/template" style="display:none">
		<?php echo $browse_view; ?>
	</script>

	<script bebop-list--template="reorder-view" type="text/template" style="display:none">
		<?php echo $reorder_view; ?>
	</script>

	<script bebop-list--template="edit-view" type="text/template" style="display:none">
		<?php echo $edit_view; ?>
	</script>
	
</div>