<?php
class CRouteComponent extends Component{

    protected $controller = null;
	/**
	 * Plugin name controller belongs to
	 *
	 * @var string
	 */
	var $pluginName = 'Route';
	
	/**
	 * Other components used by this component
	 *
	 * @var array
	 */	
	public $components = array('Session');
	
	/**
	 * Default filename to store custom routes in
	 *
	 * @var string
	 */		
	var $customRoutesFilenameWithoutPath = 'all.php';
	
	/**
	 * Construct Controller
	 *
	 * @param Componentcollection $collection
	 * @param array  $settings
	 */
	 
	function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
    }
	
	/**
	 * Initialize Controller - called before Controller::beforeFilter()
	 *
	 * @param object $controller
	 */
	function initialize(&$controller) {
		// saving the controller reference for later use
		$this->controller =& $controller;
		$this->Route = ClassRegistry::init('Route.Route');;		
	}

	/**
	 * Determine path of customRoutesFile
	 *
	 * @return string
	 */
	function get_custom_routes_filepath() {
		$path = APP . 'Plugin' . DS . $this->pluginName . DS . 'generated_routes' . DS . $this->customRoutesFilenameWithoutPath;
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
			$permissions = @fileperms ( $path );
			$fileowner = @fileowner($path);
			$filegroup = @filegroup($path);
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
				if ($permissions != 0) {
					$resultArray['output'] .= "<strong>File Permissions are: </strong>". substr(sprintf('%o', $permissions), -4);
				}
				else {
					$resultArray['output'] .= "<strong>File Permissions are: </strong> Unknown (permissions issue?)";
				}
				$resultArray['output'] .= "<BR>";
				if ($permissions == 0) {
					$resultArray['output'] .= "<strong>File Mask is: </strong> Unknown (permissions issue?)";
				}
				else {
					$resultArray['output'] .= "<strong>File Mask is: </strong>".$this->_resolveperms($permissions);
				}
				$resultArray['output'] .= "<BR>";
				if ($fileowner === false) {
					$resultArray['output'] .= "<strong>Owned by User: </strong> Unknown (permissions issue?)";
				}
				else {
					$resultArray['output'] .= "<strong>Owned by User: </strong>".$fileownerarray['name'];
				}
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

		return $resultArray;
	}
}
		
?>