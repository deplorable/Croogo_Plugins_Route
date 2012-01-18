<?php
/**
 * Routes
 *
 * route_routes.php will be loaded in main app/config/routes.php file.
 */
    Croogo::hookRoutes('Route');
/**
 * Behavior
 *
 * This plugin's Route behavior will be attached whenever Node model is loaded.
 */
    Croogo::hookBehavior('Node', 'Route.Route', array());
/**
 * Admin menu (navigation)
 */
    CroogoNav::add('extensions.children.route', array(
        'title' => __('Route'),
        'url' => array(
			'plugin' => 'route',
			'controller' => 'route',
			'action' => 'index',
		),
        'access' => array('admin'),
        'children' => array(
		    'listroutes' => array(
				'title' => __('List Routes'),
				'url' => array(
					'plugin' => 'route',
					'controller' => 'route',
					'action' => 'index',
					),
				'weight' => 10,
			),
			'createroutes' => array(
				'title' => __('Create Route'),
				'url' => array(
					'plugin' => 'route',
					'controller' => 'route',
					'action' => 'add',
					),
				'weight' => 15,
			),
			'regenerateroutes' => array(
				'title' => __('Regenerate Routes File'),
				'url' => array(
					'plugin' => 'route',
					'controller' => 'route',
					'action' => 'regenerate_custom_routes_file',
					),
				'weight' => 15,
			),
		
		),
    ));
/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Route' will be placed under 'Actions' column.
 */
    //Croogo::hookAdminRowAction('Nodes/admin_index', 'Route', 'plugin:route/controller:route/action:index/:id');
/**
 * Admin tab
 *
 * When adding/editing Content (Nodes),
 * an extra tab with title 'Route' will be shown with markup generated from the plugin's admin_tab_node element.
 *
 * Useful for adding form extra form fields if necessary.
 */
    Croogo::hookAdminTab('Nodes/admin_add', 'Route', 'route.admin_tab_node');
    Croogo::hookAdminTab('Nodes/admin_edit', 'Route', 'route.admin_tab_node');
?>