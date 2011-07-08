<?php

class RoutesSchema extends CakeSchema {

        /**
         * Schema name
         *
         * @var string
         */
        public $name = 'Routes';

        /**
         * CakePHP schema
         *
         * @var array
         */
        public $routes = array(
            'id' => array('type' => 'integer', 'null' => false, 'length' => 20, 'key' => 'primary'),
            'alias' => array('type' => 'string', 'null' => false, 'length' => 255),
            'body' => array('type' => 'text', 'null' => false),
            'status' => array('type' => 'integer', 'null' => false, 'length' => 1, 'default' => 0),
            'node_id' => array('type' => 'integer', 'null' => false, 'length' => 11),
            'updated' => array('type' => 'datetime', 'null' => false, 'length' => NULL),
            'created' => array('type' => 'datetime', 'null' => false, 'length' => NULL),
            'tableParameters' => array('charset' => 'utf8', 'engine' => 'MyISAM')
        );

        /**
         * Before callback
         *
         * @param array $event
         * @return void
         */
        public function before($event = array()) {

        }

        /**
         * After callback
         *
         * @param array $event
         * @return void
         */
        public function after($event = array()) {

        }
}

?>