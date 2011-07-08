<a href="#"><?php __('Route'); ?></a>
<ul>
    <li><?php echo $this->Html->link(__('List Routes', true), array('plugin' => 'route', 'controller' => 'routes', 'action' => 'index'));?></li>
    <li><?php echo $this->Html->link(__('Create Route', true), array('plugin' => 'route', 'controller' => 'routes', 'action' => 'add'));?></li>
</ul>