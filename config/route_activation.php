<?php
/**
 * Route Activation
 *
 * Activation class for Route plugin.
 * This is optional, and is required only if you want to perform tasks when your plugin is activated/deactivated.
 *
 * @package  Croogo
 * @author   Damian Grant <codebogan@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RouteActivation {

    /**
     * Schema directory
     *
     * @var string
     */
    private $SchemaDir;

    /**
     * DB connection
     *
     * @var object
     */
    private $db;
    
    /**
     * DB connection
     *
     * @var array
     */    
	var $uses = array('Session');

    /**
     * Constructor
     *
     * @return 
     */
     public function  __construct() {

         $this->SchemaDir = APP.'plugins'.DS.'route'.DS.'config'.DS.'schemas';
         $this->db =& ConnectionManager::getDataSource('default');

    }
    
	/**
	 * onActivate will be called if this returns true
	 *
	 * @param  object $controller Controller
	 * @return boolean
	 */
    public function beforeActivation(&$controller) {

        App::Import('CakeSchema');
        $CakeSchema = new CakeSchema();

        // list schema files from config/schema dir
        if (!$cake_schema_files = $this->_listSchemas($this->SchemaDir))
            return false;        
            
        // create table for each schema (if not exists)
        foreach ($cake_schema_files as $schema_file) {
            $schema_name = substr($schema_file, 0, -4);
            $schema_class_name = Inflector::camelize($schema_name).'Schema';
            $table_name = $schema_name;

            if (!in_array($table_name, $this->db->_sources)) {
                 include_once($this->SchemaDir.DS.$schema_file);
                 $ActiveSchema = new $schema_class_name;
                 if(!$this->db->execute($this->db->createSchema($ActiveSchema, $table_name))) {
                    return false;
                 }
            }
        }
            
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
    }

	/**
	 * onDeactivate will be called if this returns true
	 *
	 * @param  object $controller Controller
	 * @return boolean
	 */
    public function beforeDeactivation(&$controller) {

        // list schema files from config/schema dir
        if (!$cake_schema_files = $this->_listSchemas($this->SchemaDir))
            return false;

        // delete tables for each schema
        foreach ($cake_schema_files as $schema_file) {
            $schema_name = substr($schema_file, 0, -4);
            $table_name = $schema_name;
            /*if(!$this->db->execute('DROP TABLE '.$table_name)) {
                    return false;
            }*/
        }
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
    }
    
    /**
     * List schemas
     *
     * @return array
     */
    private function _listSchemas($dir = false) {

        if (!$dir) return false;

        $cake_schema_files = array();
        if ($h = opendir($dir)) {
            while (false !== ($file = readdir($h))) {
                if (($file != ".") && ($file != "..")) {
                    $cake_schema_files[] = $file;
                }
            }
        } else {
            return false;
        }

        return $cake_schema_files;
    }    
}
?>