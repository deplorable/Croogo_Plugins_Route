<?php
	$route_alias = '';

	if ($this->action != 'admin_add') { //we are in edit mode
		$node_id = $this->data['Node']['id'];
		$this->Route = ClassRegistry::init('Route.Route');;		
		$route = $this->Route->find('first', array('conditions' => array('Route.node_id' => $node_id)));
		
		if ($route === false) { //no route for this node
			$route_alias = '';
		}
		else {
			$this->request->data['Route'] = $route['Route'];
			$route_alias = $this->request->data['Route']['alias'];
		}
	}

    echo $this->Form->input('route_alias', array('value'=>$route_alias));
?>