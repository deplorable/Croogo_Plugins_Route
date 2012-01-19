<?php 
echo $this->Html->css('/route/css/route', 'stylesheet', array("media"=>"all" ), false);
?>
<div class="route index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
            <li><?php echo $this->Html->link(__d('route','New route', true), array('action'=>'add')); ?></li>
            <li><?php echo $this->Html->link(__d('route','Enable all', true), array('action'=>'admin_enable_all'), array(), __('Enable all routes?', true)); ?></li>            
            <li><?php echo $this->Html->link(__d('route','Disable all', true), array('action'=>'admin_disable_all'), array(), __('Disable all routes?', true)); ?></li>            
            <li><?php echo $this->Html->link(__d('route','Delete all routes', true), array('action'=>'admin_delete_all'), array(), __('Delete all routes?\nTHIS CANNOT BE UNDONE!', true)); ?></li>            
            <li><?php echo $this->Html->link(__d('route','Manually Regenerate Custom Routes File', true), array('action'=>'regenerate_custom_routes_file')); ?></li>            
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
	    <?php
			$tableHeaders =  $this->Html->tableHeaders(
				array(
					$this->Paginator->sort('id'),
					__d('route','Alias', true),
					__d('route','Body', true),											
					$this->Paginator->sort('status'),                                            
					  __('Actions', true),
				)
			);
			echo $tableHeaders;
	
			$rows = array();
			foreach ($routes as $route) {
			
				$actions = ' ' . $this->Html->link(
					__('Delete', true), 
					array(
						'controller' => 'route',
						'action' => 'delete',
						$route['Route']['id'],
						'token' => $this->params['_Token']['key'],
					), 
					null, 
					__('Are you sure you want to delete this route?', true)
				);
				
				$actions .= ' ' . $this->Html->link(
					__('Edit Route', true), 
					array(
						'controller' => 'route', 
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
	
	
				$newrow = array(
					$route['Route']['id'],
				);
				
				if ($route['Route']['status'] == 1) {
					$newrow[] = $this->Html->link(
							DS . $route['Route']['alias'], 
							DS . $route['Route']['alias']
					);
				}
				else {
					$newrow[] = 
						'<span class="route_disabled_link">'.
						DS . $route['Route']['alias'].
						'</span>';
				}
					
				$newrow[] = substr(trim(strip_tags($route['Route']['body'])), 0, 150);
				$newrow[] = $this->Layout->status($route['Route']['status']);
				$newrow[] = $actions;
				
				$rows[] = $newrow;
			}
	
			echo $this->Html->tableCells($rows);
			echo $tableHeaders;
		?>
    </table>
</div>

<div class="paging"><?php 
	echo $this->Paginator->numbers(); 
?></div>

<div class="counter">
	<?php 
		echo $this->Paginator->counter(
			array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}', true)
			)
		); 
	?>
</div>