<div class="route index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
            <li><?php echo $html->link(__d('route','New route', true), array('action'=>'add')); ?></li>
            <li><?php echo $html->link(__d('route','Enable all', true), array('action'=>'admin_enable_all'), array(), __('Enable all routes?', true)); ?></li>            
            <li><?php echo $html->link(__d('route','Disable all', true), array('action'=>'admin_disable_all'), array(), __('Disable all routes?', true)); ?></li>            
            <li><?php echo $html->link(__d('route','Delete all routes', true), array('action'=>'admin_delete_all'), array(), __('Delete all routes?\nTHIS CANNOT BE UNDONE!', true)); ?></li>            
            <li><?php echo $html->link(__d('route','Manually Regenerate Custom Routes File', true), array('action'=>'regenerate_custom_routes_file')); ?></li>            
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
	    <?php
			$tableHeaders =  $html->tableHeaders(
				array(
					$paginator->sort('id'),
					__d('route','Alias', true),
					__d('route','Body', true),											
					$paginator->sort('status'),                                            
					  __('Actions', true),
				)
			);
			echo $tableHeaders;
	
			$rows = array();
			foreach ($routes as $route) {
				$actions = ' ' . $this->Html->link(
					__('Delete', true), 
					array(
						'controller' => 'routes',
						'action' => 'delete',
						$route['Route']['id'],
						'token' => $this->params['_Token']['key'],
					), 
					null, 
					__('Are you sure?', true)
				);
	
				$actions .= ' ' . $this->Html->link(
					__('Edit Route', true), 
					array(
						'controller' => 'routes', 
						'action' => 'edit', 
						$route['Route']['id']
					)
				);
	
				if ($route['Route']['node_id'] != 0) {
					$actions .= ' ' . $this->Html->link(
						__('Edit Node', true), 
						array(
							'plugin' => '', 
							'controller' => 'nodes', 
							'action' => 'edit', 
							$route['Route']['node_id']
						)
					);
				}
	
				$rows[] = array(
					$route['Route']['id'],
					$this->Html->link(
						'/' . $route['Route']['alias'], 
						'/' . $route['Route']['alias']
					),
					substr(trim(strip_tags($route['Route']['body'])), 0, 150),
					$layout->status($route['Route']['status']),
					$actions,
				);
			}
	
			echo $this->Html->tableCells($rows);
			echo $tableHeaders;
		?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter">
	<?php 
		echo $paginator->counter(
			array(
				'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
			)
		); 
	?>
</div>