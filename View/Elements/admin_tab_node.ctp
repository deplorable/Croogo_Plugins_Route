<?php
	$route_alias = '';
	$route_status = 0;
	$route_status_editable = false;

	if ($this->action == 'admin_edit') { //we are in edit mode
		$route_status_editable = true;
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
			$route_status = $this->request->data['Node']['route_status'];
		}
		else {
			
			$this->Route = ClassRegistry::init('Route.Route');;		
			$route = $this->Route->find('first', array('conditions' => array('Route.node_id' => $node_id)));
			
			if ($route === false) { //no route for this node
				$route_alias = '';
				$route_status = 1;
				$route_status_editable = false;
			}
			else {
				$this->request->data['Route'] = $route['Route'];
				$route_alias = $this->request->data['Route']['alias'];
				$route_status = $this->request->data['Route']['status'];
			}
		}
	}
	else {
		$route_status = 1; //creating a route
	}

    echo $this->Form->input('route_alias', array('label' => __('Alias'), 'value'=>$route_alias));
	if ($route_status_editable == true) {
		if ($route_status == 1) {
			echo $this->Form->input('route_status', array('label' => __('Status'), 'value'=>0, 'type'=>'checkbox', 'checked'=>true));
		}
		else {
			echo $this->Form->input('route_status', array('label' => __('Status'), 'value'=>0, 'type'=>'checkbox', 'checked'=> false ));
		}
	}
	else {
		if ($route_status == 1) {
			echo $this->Form->input('route_status', array('label' => __('Status'), 'value'=>1, 'type'=>'hidden'));
		}
		else {
			echo $this->Form->input('route_status', array('label' => __('Status'), 'value'=>0, 'type'=>'hidden'));
		}
	}
	
?>