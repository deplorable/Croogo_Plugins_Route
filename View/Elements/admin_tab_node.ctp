<?php
	$route_alias = '';

	if ($this->action != 'admin_add') { //we are in edit mode
		$validationProbs = $this->validationErrors;
		$haveRouteValidationIssue = false;
		if (is_array($validationProbs)) {
			if (sizeof($validationProbs['Node']) > 0) {
				if (isset($validationProbs['Node']['route_alias'])) {
					$haveRouteValidationIssue = true;
				}
			}
		}
		
		$node_id = $this->data['Node']['id'];
		
		if ($haveRouteValidationIssue == true) {
			$route_alias = $this->request->data['Node']['route_alias'];
		}
		else {
			
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
	}

    echo $this->Form->input('route_alias', array('value'=>$route_alias));
?>