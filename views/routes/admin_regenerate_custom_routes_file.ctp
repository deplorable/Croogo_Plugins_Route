<div class="route form">
	<h2><?php echo $title_for_layout; ?>&nbsp;</h2>
    
	<?php echo $this->Form->create('Route', array('url' => array('controller'=>'routes', 'action'=>'index'))); ?>
    <?php echo $output_for_layout; ?>&nbsp;
    <?php echo $code_for_layout; ?>&nbsp;
    <div class="buttons">
		<?php echo $this->Form->end(__('Okay', true)); ?>
    </div>				
</div>				