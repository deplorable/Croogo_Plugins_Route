<?php
/**
 * Route Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Croogo
 * @author   Damian Grant <codebogan@optusnet.com.au>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RouteBehavior extends ModelBehavior {

	public $components = array('RouteComponent');
	public $customRoutesFilenameWithoutPath = 'all.php';

	/**
	 * Setup
	 *
	 * @param object $model
	 * @param array  $config
	 * @return void
	 */
    public function setup(&$model, $config = array()) {
		if (is_string($config)) {
			$config = array($config);
		}
		$this->settings[$model->alias] = $config;
    }

	/**
	 * afterFind callback
	 *
	 * @param object  $model
	 * @param array   $results
	 * @param boolean $primary
	 * @return array
	 */
	public function afterFind(&$model, $results = array(), $primary = false) {
		if ($model->alias == 'Node') {
			foreach ($results AS $i => $result) {
				$results[$i]['Route'] = array();
			}
		}
		return $results;
	}
		
	/**
	 * beforeValidate callback
	 *
	 * @param object  $model
	 */
	public function beforeValidate(&$model) {
		//add validate fields 
		$model->validate['route_alias'] = array(
			'aliasDoesNotExist' => array(
				'rule' => array('doesAliasExist'),
				'message' => 'This alias is already in use by another route',
			),
			'aliasValid' => array(
				'rule' => array('isAliasValid'),
				'message' => 'The alias must not begin with a slash or backslash character. Only alphanumeric characters, underscores, hyphens and slashes or backslashes are acceptable.',
			),				
		);
	}
	
	/**
	 * afterSave callback
	 *
	 * @param object  $model
	 * @param boolean   $created
	 */		
	public function afterSave(&$model, $created) {
		if ($model->alias == 'Node') {
			$data = $model->data['Node'];
			$route_alias = $data['route_alias'];
	
			if (isset($data['id'])) {
				$node_id = $data['id'];
			}
			else {
				$node_id = $model->id;
			}

			//lets look for the node_id in the Routes table
			$params = array('conditions' => array('Route.node_id' => $node_id));
			$this->Route = ClassRegistry::init('Route.Route');;		
			$matchingRoute = $this->Route->find('first', $params);
			if ($matchingRoute != null) { //let's update our route with the new path 
				if ((trim($route_alias)) == '') {
					//empty alias - delete the route id
					$route_id = $matchingRoute['Route']['id'];
					$this->Route->delete($route_id, false);
				}
				else { 
					//non-empty alias
					$route_id = $matchingRoute['Route']['id'];
					$this->Route->id = $route_id;
					$this->Route->saveField('alias', $route_alias);
					$this->Route->saveField('body', "array('controller' => 'nodes', 'action' => 'view', 'type' => '".$data['type']."', ".$node_id.")");
				}
			}
			else {
				//create a new route that points to the node 
				$this->Route->create();
				$this->data = array();
				$this->data['Route'] = array();
				$this->data['Route']['alias'] = $route_alias;
				$this->data['Route']['node_id'] = $node_id;
				$this->data['Route']['body'] = "array('controller' => 'nodes', 'action' => 'view', 'type' => '".$data['type']."', ".$node_id.")";
				$this->data['Route']['status'] = 1;
				if ($this->Route->save($this->data)) {
					//Saved					
				}
				else {
					//Not Saved
				}
			}
	
			$this->write_custom_routes_file();
		}
	}
		
	/**
	 * Determine path of customRoutesFile
	 *
	 * @return string
	 */
	function get_custom_routes_filepath() {
		$path = APP . 'plugins' . DS . 'route' . DS . 'generated_routes' . DS . $this->customRoutesFilenameWithoutPath;
		return $path;
	}
	
	/**
	 * Retrieve Routes from Database
	 *
	 * @return array
	 */
	function get_custom_routes_from_db() {
		$params = array('conditions' => array('Route.status' => 1));
		$routes = $this->Route->find('all', $params);
		return $routes;
	}
	
	/**
	 * Generate CroogoRouter::connect PHP code to save in the customRoutesFile (e.g. all.php)
	 *
	 * @return string
	 */
	function get_custom_routes_code() {
		$routes = $this->get_custom_routes_from_db();
		$newline = "\n";
		$code = '';
		$code .= '<?php' . $newline;
		foreach($routes as $key=>$route) {
			$testing = eval('return '.$route['Route']['body'].';');
			if (is_array($testing)) {
				$code .= 'CroogoRouter::connect(\'/'.$route['Route']['alias'].'\', '.$route['Route']['body'].');' . $newline;
			}
		}
		$code .= '?>';
		return $code;
	}
	
	/**
	 * Convert UNIX File Permissions Umask into human-readable string
	 *
	 * @param integer $perms
	 * @return string
	 */
	function _resolveperms($perms) {
		$oct = str_split( strrev( decoct( $perms ) ), 1 );
		//               0      1      2      3      4      5      6      7
		$masks = array( '---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx' );
		return(
			sprintf( 
				'%s %s %s',
				array_key_exists( $oct[ 2 ], $masks ) ? $masks[ $oct[ 2 ] ] : '###',
				array_key_exists( $oct[ 1 ], $masks ) ? $masks[ $oct[ 1 ] ] : '###',
				array_key_exists( $oct[ 0 ], $masks ) ? $masks[ $oct[ 0 ] ] : '###'
			)
		);
	}
	
	/**
	 * Write Custom Routes to the custom route file that is included_once by the croogo_router.php file	
	 *
	 * @param array $check
	 * @return boolean
	 */
	function write_custom_routes_file() {
		$path = $this->get_custom_routes_filepath();
		$resultArray = array();
		$resultArray['output'] = '';
		$resultArray['code'] = '';			
	
		try {
			$permissions = fileperms ( $path );
			$fileowner = fileowner($path);
			$filegroup = filegroup($path);
			$fileownerarray = posix_getpwuid($fileowner);
			$filegrouparray = posix_getgrgid($filegroup);
			$webserver_process_user_array = posix_getpwuid(posix_geteuid());
			$webserver_process_group_array = posix_getgrgid($filegroup);
		
			if (is_writable($path))
			{
				$fp = @fopen($path, 'w');
				if ($fp !== false) {
					$code = $this->get_custom_routes_code();
					$resultArray['code'] = $code;
					fwrite($fp, $code);
					fclose($fp);
					$resultArray['output'] .=  "File has been written to:<BR />";
					$resultArray['output'] .= $path."<BR>";
				}
			}
			else {
				$this->Session->setFlash('Route Plugin: Cannot overwrite '.$path);
				$resultArray['output'] .= "<h3 style='color: red;'>Cannot overwrite ".basename($path)."!</h3>";
				$resultArray['output'] .= "<strong style='color: red;'>Please ensure file is writable by the webserver process.</strong>";					
				$resultArray['output'] .= "<BR><BR>";
				$resultArray['output'] .= "<strong>File Location: </strong>".$path;					
				$resultArray['output'] .= "<BR>";					
				$resultArray['output'] .= "<strong>File Permissions are: </strong>". substr(sprintf('%o', $permissions), -4);
				$resultArray['output'] .= "<BR>";
				$resultArray['output'] .= "<strong>File Mask is: </strong>".$this->_resolveperms($permissions);
				$resultArray['output'] .= "<BR>";
				$resultArray['output'] .= "<strong>Owned by User: </strong>".$fileownerarray['name'];
				$resultArray['output'] .= "<BR>";					
				$resultArray['output'] .= "<strong>Owned by Group: </strong>".$filegrouparray['name'];					
				$resultArray['output'] .= "<BR>";					
				$resultArray['output'] .= "<strong>Webserver running as User: </strong>".$webserver_process_user_array['name'];
				$resultArray['output'] .= "<BR>";					
				$resultArray['output'] .= "<strong>Webserver running in Group: </strong>".$webserver_process_group_array['name'];					
			}
		}
		catch (Exception $e) {
			//do nothing
		}
	}
	
	/**
	 * Validation: Check if alias exists already for another Route
	 *
	 * @param array $check
	 * @return boolean
	 */
	function doesAliasExist($check) {
		$check = $check->data['Node'];
		$route_alias = $check['route_alias'];
	
		if (!isset($check['id'])) {
			$node_id = -1;
		}
		else {
			$node_id = $check['id'];
		}
	
		if ($route_alias == '')	{
			return true;
		}
		else {
			if ($node_id == -1) { //we are adding a route
				$params = array('conditions' => array('Route.alias' => $route_alias));
			}
			else { //we are editing a route
				$params = array('conditions' => array('Route.alias' => $route_alias, 'Route.node_id !=' => $node_id));
			}
	
			$this->Route = ClassRegistry::init('Route.Route');;		
			$numMatches = 0;
			$numMatches = $this->Route->find('count', $params);

			if ($numMatches > 0) {
				return false;
			}
			else {
				return true;
			}
		}
	}
		
	/**
	 * Validation: Check if alias entered contains any bad characters
	 *
	 * @param array $check
	 * @return boolean
	 */			
	function isAliasValid($check) {
		$check = $check->data['Node'];
		$thealias = $check['route_alias'];
		$firstchar = substr($thealias, 0, 1);
		App::import('Sanitize');
		$thealiassanitized = Sanitize::paranoid($thealias, array('/', '\\', '_', '-'));

		if (($firstchar == "/") || ($firstchar == "\\")) {
			return false;				
		}
		else if ($thealiassanitized == $thealias) {
			return true;
		}
		else {
			return false;
		}
	}	
}