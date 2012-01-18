<?php
/**
 * Route
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Damian Grant <codebogan@optusnet.com.au>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Route extends RouteAppModel {

	/**
	 * Model name
	 *
	 * @var string
	 */
	public $name = 'Route';
	
	/**
	 * Model table
	 *
	 * @var string
	 */
	public $useTable = 'routes';
		
	/**
	 * Validation
	 *
	 * @var array
	 */
	 
    var $validate = array(
		'alias' => array(
			'alphaNumeric' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'message' => 'This field cannot be left blank.',
			),
			'aliasDoesNotExist' => array(
				'rule' => array('doesAliasExist'),
				'message' => 'This alias is already in use by another route',
			),
			'aliasValid' => array(
				'rule' => array('isAliasValid'),
				'message' => 'The alias must not begin with a slash or backslash character. Only alphanumeric characters, underscores, hyphens and slashes or backslashes are acceptable.',
			),
		),
		'body' => array(
			'lengthcheck' => array(
				'rule' => array('minLength', 1),
				'message' => 'This field cannot be left blank.',
			),
			'codecheck' => array(
				'rule' => array('isBodyValid'),
				'message' => 'The body must contain a valid PHP array to be passed to the Router in the form: array()', 
			),
		),
	);
		
	/**
	 * Validation: Check if alias exists already for another Route
	 *
	 * @param array $check
	 * @return boolean
	 */
	function doesAliasExist($check) {
		$id = -1;
		if (isset($this->data['Route']['id'])) {
			$id = $this->data['Route']['id'];
		}
	
		$params = array('conditions' => 
			array(
				'Route.alias' => $check, 
				'Route.id !=' => $id
			)
		);

		$numMatches = 0;
		$numMatches = $this->find('count', $params);

		if ($numMatches > 0) { //another Route exists with the same alias
			return false;
		}
		else { //no other Routes exist with the same alias
			return true;
		}
	}

	/**
	 * Validation: Check if alias entered contains any bad characters
	 *
	 * @param array $check
	 * @return boolean
	 */
	function isAliasValid($check) {
		$thealias = $check['alias'];
		$firstchar = substr($thealias, 0, 1);
		//App::import('Sanitize'); old Cake 1.3 code
		App::uses('Sanitize', 'Utility');
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
		 
	/**
	 * Validation: Check if body entered as a string is an array
	 * e.g. array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 'slug' => 'about')
	 *
	 * @param array $check
	 * @return boolean
	 */	 
		
	function isBodyValid($check) {
		$thebody = $check['body'];
		$testing = @eval('return '.$thebody.';');
		if (is_array($testing)) {
			return true;
		}
		else {
			return false;
		}
	}
}