<div class="route form">
	<h2><?php echo $title_for_layout; ?>&nbsp;</h2>
        
	<?php echo $this->Form->create('Route', array('url' => array('controller'=>'routes', 'action'=>'edit'))); ?>
    <fieldset>
		<?php
			echo $this->Form->input('alias', array('label' => __('Alias', true)));
			echo $this->Form->input('body', array('label' => __('Body', true), 'class' => 'content'));
			echo $this->Form->input('status', array('label' => __('Status', true)));
        ?>
    </fieldset>

    <div class="buttons">
		<?php
			echo $this->Form->end(__('Save', true));
			echo $this->Html->link(
				__('Cancel', true), 
				array(
					'controller' => 'routes',
					'action' => 'index',
				), 
				array(
					'class' => 'cancel',
				)
			);
        ?>
	</div>
</div>