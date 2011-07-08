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
 *
 * This plugin's admin_menu element will be rendered in admin panel under Extensions menu.
 */
    Croogo::hookAdminMenu('Route');
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