<?php 
echo $this->Html->css('/route/css/route', 'stylesheet', array("media"=>"all" ), false);
?>
<div class="route form">
	<h2><?php echo $title_for_layout; ?>&nbsp;</h2>
        
	<?php echo $this->Form->create('Route', array('url' => array('controller'=>'Route', 'action'=>'edit'))); ?>
    <fieldset>
		<?php
			echo $this->Form->input('alias', array('label' => __('Alias', true)));
		
			if ((isset($linkednode)) && ($linkednode != null)) {
				echo $this->Form->input('node_id', array('type'=>'hidden'));
		?>
				<div class="input textarea"><label for="RouteBody">Linked to Node:</label>
				<span class="linkednode"><?php echo $this->Html->link($linkednode['Node']['title'], array('plugin' => null, 'controller' => 'nodes', 'action' => 'edit', $linkednode['Node']['id'], '#' => 'node-route'), array('target' => '_blank')); ?> (<?= $linkednode['Node']['type']; ?> | ID: <?= $linkednode['Node']['id']; ?>)</span>
				</div>
		<?php 
				echo $this->Form->input('body', array('label' => __('Body', true), 'class' => 'content', 'readonly' => true));
			}
			else {
				echo $this->Form->input('body', array('label' => __('Body', true), 'class' => 'content'));
			}
			echo $this->Form->input('status', array('label' => __('Status', true), 'type'=>'checkbox'));
        ?>
    </fieldset>

    <div class="buttons">
		<?php
			echo $this->Form->end(__('Save', true));
			echo $this->Html->link(
				__('Cancel', true), 
				array(
					'controller' => 'route',
					'action' => 'index',
				), 
				array(
					'class' => 'cancel',
				)
			);
        ?>
	</div>
</div>