<?php
/**
 * Route Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.4
 * @author   Damian Grant <codebogan@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RouteController extends RouteAppController {

	/**
	 * Default pagination options
	 *
	 * @var array
	 * @access public
	 */
	public $paginate = array(
        'limit' => 25,
		'order' => array (
			'Route.alias' => 'asc',
		),
	);
	
	/**
	 * Plugin name
	 *
	 * @var string
	 */
	var $pluginName = 'Route';
	
	/**
	 * Controller name
	 *
	 * @var string
	 * @access public
	 */
    public $name = 'Route';
	
	/**
	 * Components used by the Controller
	 *
	 * @var array
	 * @access public
	 */	
	public $components = array('Security', 'Session', 'Route.CRoute');

	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
    public $uses = array('Route.Route');

	/**
	 * Route List/Index
	 */
    function admin_index() {
		$this->set('title_for_layout', __('Route', true));
		/*$this->paginate = array(
			'order' => 'Route.alias ASC'
		);*/
		$this->set('routes', $this->paginate('Route.Route'));				
    }
		
	/**
 	 * Add route
	 */
	function admin_add() {
		$this->set('title_for_layout', __('Create route', true));				
		if (!empty($this->data)) {
			$this->Route->create();
			if ($this->Route->save($this->data)) {
				$this->Session->setFlash(__('Route has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
			} else {
				$this->Session->setFlash(__('Error saving route', true), 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Edit route
	 *
	 * @param integer $id
	 */
	function admin_edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Cannot Edit - Missing Route ID', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		
		if (!empty($this->request->data)) {
			$this->request->data['Route']['id'] = $id;					  
		
			if ($this->Route->save($this->request->data)) {
				$this->Session->setFlash(__('Route has been saved', true), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
			} else {
				$this->Session->setFlash(__('Route could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
				$this->set('title_for_layout', __('Edit a route', true));				
			}
		}
		
		if (empty($this->request->data)) {
			$this->set('title_for_layout', __('Edit route', true));				
			$this->request->data = $this->Route->read(null, $id);
			//if node id is not empty, load in node information
			$linkednode = null;
			if ($this->request->data['Route']['node_id'] != null) {
				$linkednode = $this->Node->read(null, $this->request->data['Route']['node_id']);
			}
			$this->set('linkednode', $linkednode);
		}
	}
		
	/**
	 * Delete route
	 *
	 * @param integer $id (route id)
	 */
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Missing Route ID', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if($this->Route->delete($id)) {
			$this->Session->setFlash(__('Route successfully deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
		}
	}

	/**
	 * Route plugin index (does nothing)
	 */
    function index() {
    	$this->set('title_for_layout', __('Route', true));
    }
		
	/**
	 * Generate custom routes file
	 */		
	function admin_regenerate_custom_routes_file() {
		$this->set('title_for_layout', __('Regenerating Custom Routes File...', true));
		$result = $this->CRoute->write_custom_routes_file();
		$this->set('output_for_layout', $result['output']);
		if ($result['code'] != '') {
			$result['code'] = '<textarea wrap="off" style="margin-top: 10px; font-size: 11px;" readonly="readonly">'.$result['code'].'</textarea>';
		}
		$this->set('code_for_layout', $result['code']);			 
	}
	
	/**
	 * Enable all routes
	 */	
	function admin_enable_all() {
		$this->Route->updateAll(
			array('Route.status' => 1),
			array('Route.status' => 0)
		);
		$this->Session->setFlash(__('All routes have been enabled.', true), 'default', array('class' => 'success'));
		$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
	}

	/**
	 * Disable all routes
	 */		
	function admin_disable_all() {
		$this->Route->updateAll(
			array('Route.status' => 0),
			array('Route.status' => 1)
		);
		$this->Session->setFlash(__('All routes have been disabled.', true), 'default', array('class' => 'success'));
		$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
	}
	
	/**
	 * Delete all routes
	 */		
	function admin_delete_all() {
		$this->Route->deleteAll('1');
		$this->Session->setFlash(__('All routes have been deleted!', true), 'default', array('class' => 'success'));
		$this->redirect(array('action'=>'admin_regenerate_custom_routes_file'));				
	}		
}

?>