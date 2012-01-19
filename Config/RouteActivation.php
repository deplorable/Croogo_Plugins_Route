<?php
/**
 * Route Activation
 *
 * Activation class for Route plugin.
 * This is optional, and is required only if you want to perform tasks when your plugin is activated/deactivated.
 *
 * @package  Croogo
 * @version  1.4
 * @author   Damian Grant <codebogan@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RouteActivation {

	var $uses = array('Session');

	/**
	 * onActivate will be called if this returns true
	 *
	 * @param  object $controller Controller
	 * @return boolean
	 */
    public function beforeActivation(&$controller) {
        return true;
    }

	/**
	 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
	 *
	 * @param object $controller Controller
	 * @return void
	 */
    public function onActivation(&$controller) {
        // ACL: set ACOs with permissions
        $controller->Croogo->addAco('Route'); // RouteController
        $controller->Croogo->addAco('Route/admin_index'); // RouteController::admin_index()
        $controller->Croogo->addAco('Route/admin_add'); // RouteController::admin_index()				
        $controller->Croogo->addAco('Route/admin_edit'); // RouteController::admin_index()								
        $controller->Croogo->addAco('Route/index', array('registered', 'public')); // RouteController::index()
		
		$controller->Croogo->addAco('route'); // RouteController
        $controller->Croogo->addAco('route/admin_index'); // RouteController::admin_index()
        $controller->Croogo->addAco('route/admin_add'); // RouteController::admin_index()				
        $controller->Croogo->addAco('route/admin_edit'); // RouteController::admin_index()								
        $controller->Croogo->addAco('route/index', array('registered', 'public')); // RouteController::index()

        // Main menu: add an Route link
        $mainMenu = $controller->Link->Menu->findByAlias('main');
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
            ),
        ));
		
        $controller->Link->save(array(
            'menu_id' => $mainMenu['Menu']['id'],
            'title' => 'Route',
            'link' => 'plugin:route/controller:route/action:index',
            'status' => 1,
        ));
		
		// Import the Schema into Database
		App::uses('File', 'Utility');
		App::import('Model', 'CakeSchema', false);
		App::import('Model', 'ConnectionManager');
		$db = ConnectionManager::getDataSource('default');
		
		if(!$db->isConnected()) {
			$this->Session->setFlash(__('Could not connect to database.'), 'default', array('class' => 'error'));
		} else {
			CakePlugin::load('Route'); //is there a better way to do this?
			$schema =& new CakeSchema(array('name'=>'route', 'plugin'=>'Route'));
			$schema = $schema->load();
			
			foreach($schema->tables as $table => $fields) {
				$create = $db->createSchema($schema, $table);
				$db->execute($create);
			}
		}
    }

	/**
	 * onDeactivate will be called if this returns true
	 *
	 * @param  object $controller Controller
	 * @return boolean
	 */
    public function beforeDeactivation(&$controller) {
        return true;
    }

	/**
	 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
	 *
	 * @param object $controller Controller
	 * @return void
	 */
    public function onDeactivation(&$controller) {
        // ACL: remove ACOs with permissions
        $controller->Croogo->removeAco('Route'); // RouteController ACO and it's actions will be removed

        // Main menu: delete Route link
        $link = $controller->Link->find('first', array(
            'conditions' => array(
                'Menu.alias' => 'main',
                'Link.link' => 'plugin:route/controller:route/action:index',
            ),
        ));
		
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $link['Link']['menu_id'],
            ),
        ));
		
        if (isset($link['Link']['id'])) {
            $controller->Link->delete($link['Link']['id']);
        }
		
		// Remove the tables from Database
		App::uses('File', 'Utility');
		App::import('Model', 'CakeSchema', false);
		App::import('Model', 'ConnectionManager');
		$db = ConnectionManager::getDataSource('default');
		
		if(!$db->isConnected()) {
			$this->Session->setFlash(__('Could not connect to database.'), 'default', array('class' => 'error'));
		} else {
			CakePlugin::load('Route'); //is there a better way to do this?
			$schema =& new CakeSchema(array('name'=>'route', 'plugin'=>'Route'));
			$schema = $schema->load();
			
			foreach($schema->tables as $table => $fields) {
				$drop = $db->dropSchema($schema, $table);
				$db->execute($drop);
			}
		}
    }
}
?>