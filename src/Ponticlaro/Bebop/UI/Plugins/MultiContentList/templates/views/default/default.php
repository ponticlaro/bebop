<div bebop-multilist--el="container">	

	<div bebop-multilist--el="tabs">
		<ul class="bebop-ui-clrfix">
			<?php foreach ($lists->getAll() as $key => $list) { ?>
				
				<li bebop-multilist--el="tab" bebop-multilist--tabID="<?php echo $key; ?>">
					<?php echo $list->getTitle(); ?>
				</li>

			<?php } ?>
		</ul>
	</div>
	
	<div bebop-multilist--el="panes">
		<?php foreach ($lists->getAll() as $key => $list) { ?>
				
			<div bebop-multilist--el="pane" bebop-multilist--paneID="<?php echo $key; ?>">
				<?php $list->render(); ?>
			</div>

		<?php } ?>
	</div>

</div>