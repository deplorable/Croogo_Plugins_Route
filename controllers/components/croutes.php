<?php
class CRoutesComponent extends Object{

	/**
	 * Plugin name controller belongs to
	 *
	 * @var string
	 */
	var $pluginName = 'route';
	
	/**
	 * Other components used by this component
	 *
	 * @var array
	 */	
	var $components = array('Session');
	
	/**
	 * Default filename to store custom routes in
	 *
	 * @var string
	 */		
	var $customRoutesFilenameWithoutPath = 'all.php';
	
	/**
	 * Initialize Controller - called before Controller::beforeFilter()
	 *
	 * @param object $controller
	 * @param array  $settings
	 */
	function initialize(&$controller, $settings = array()) {
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
		$path = APP . 'plugins' . DS . $this->pluginName . DS . 'generated_routes' . DS . $this->customRoutesFilenameWithoutPath;
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
			
        $platformraw = PHP_OS;
        if (stripos($platformraw, 'win') !== false) {
            $platform = 'win';
        }
        else { //assume linux/freebsd/*nix
            $platform = 'unix';
        }
            
		try {
            if ($platform == 'unix') {
                $permissions = fileperms ( $path );
                $fileowner = fileowner($path);
                $filegroup = filegroup($path);
                $fileownerarray = posix_getpwuid($fileowner);
                $filegrouparray = posix_getgrgid($filegroup);
                $webserver_process_user_array = posix_getpwuid(posix_geteuid());
                $webserver_process_group_array = posix_getgrgid($filegroup);
            }
				
            if ($this->check_customroutesfilename_is_being_included() == false) {
                $this->Session->setFlash('The croogo_router.php file does not have include_once(\'../plugins/route/generated_routes/all.php\'); Generated routes will NOT work until you fix this!');
                //echo "NOT INCLUDED";
                //die();
            }
            
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
				if ($platform == 'unix') {
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
		}
		catch (Exception $e) {
			//do nothing
		}

		return $resultArray;
	}
    
    function check_customroutesfilename_is_being_included() {
        //look for include_once('../plugins/route/generated_routes/all.php') inside the croogo_routes.php file
        $crFile = APP.'config'.DS.'croogo_routes.php';
        $crContents = file_get_contents($crFile);
        $crSearchFor1 = "include_once('../plugins/route/generated_routes/all.php');";
        $crSearchFor2 = 'include_once("../plugins/route/generated_routes/all.php");';
        $crSearchForBad1 = "//include_once('../plugins/route/generated_routes/all.php');";
        $crSearchForBad2 = "/*include_once('../plugins/route/generated_routes/all.php');*/";
        
        $isEnabled = false;
        
        /*if (preg_match($crSearchForBad1,$crContents) === 0) {
        }*/
        
        if (strpos($crContents,$crSearchFor1) !== false)
        {
            $isEnabled = true;
        }
        
        if (strpos($crContents,$crSearchFor2) !== false)
        {
            $isEnabled = true;
        }
        
        return $isEnabled;
    }
}
		
?>