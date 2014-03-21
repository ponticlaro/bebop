<div bebop-media--el="container" class="bebop-media--container" bebop-media--config='<?php echo json_encode($data->get()); ?>'>

	<div bebop-media--el="previewer" class="bebop-media--previewer">
		
	</div>
	
	<div bebop-media--el="actions">
		<button bebop-media--action="select" class="button button-small">
			<b>Select</b> <span class="bebop-ui-icon-file-upload"></span>
		</button>
		<button bebop-media--action="remove" class="button button-small">
			<span class="bebop-ui-icon-remove"></span>
		</button>
	</div>

	<input type="hidden" name="<?php echo $data->get('field_name'); ?>" value="<?php echo $data->get('data'); ?>">
	
	<script bebop-media--template="image-view" type="text/template" style="display:none">
		<div class="bebop-media--previewer-image">
			<div class="bebop-media--previewer-image-inner">
				<img src="{{sizes.thumbnail.url}}">
			</div>
		</div>
	</script>

	<script bebop-media--template="non-image-view" type="text/template" style="display:none">
		<div class="bebop-media--previewer-inner">
			<div class="bebop-media--previewer-icon bebop-ui-icon-file"></div>
			<div class="bebop-media--previewer-file-title">{{title}}</div>
			<div class="bebop-media--previewer-info">
				<a href="{{url}}" target="_blank">Open file in new tab</a> <span class="bebop-ui-icon-share"></span>
			</div>
		</div>
	</script>

	<script bebop-media--template="empty-view" type="text/template" style="display:none">
		<div class="bebop-media--previewer-inner">
			<div class="bebop-media--previewer-icon bebop-ui-icon-file-remove"></div>
			<div class="bebop-media--previewer-file-title">No file selected</div>
		</div>
	</script>

	<script bebop-media--template="loading-view" type="text/template" style="display:none">
		<div class="bebop-media--previewer-inner">
			<div class="bebop-media--previewer-icon bebop-ui-icon-busy"></div>
			<div class="bebop-media--previewer-file-title">Loading...</div>
		</div>
	</script>
</div>