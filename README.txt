Description

This Route plugin for Croogo lets you create pretty URLs for nodes or other routes from the Admin Interface.

Version: 1.4 (for Croogo 1.4/CakePHP 2.x)

IMPORTANT NOTE: 
This README.txt file should be located at app/Plugin/Route/README.txt if you have installed the Route plugin correctly into your Croogo 1.4/CakePHP 2.x app folder.

Instalation

1. Upload plugin

2. Activate plugin

3. Ensure plugin's generated_routes/all.php file is writable by the webserver process. You can test this by going to the Extensions>Route>List Routes page and clicking on "Manually Regenerate Custom Routes File". If the file is not writable, an error will display. Keep trying to Manually Regenerate the Custom Routes File after changing the file permissions or owner (user/group) of the file. Eventually you will get it working!

4. Manually edit your app/Config/croogo_routes.php file, adding the following line to the bottom:
   include_once('../Plugin/Route/generated_routes/all.php'); 

5. Go to Content>List, and edit a node you have created earlier. (or create one if there are no nodes already). You should see a "Route" tab. Enter the name of the alias you wish to use for the node. Aliases must not begin with a slash or backslash, and can only include alphanumeric characters, underscores and slashes or backslashes. 
   Here are a few examples for acceptable aliases:
   about-us
   about_us
   aboutus
   88milesperhour
   mystuff/rocks
   yourstuff/really-really-really/sucks
   NOTE: Take care not to conflict with any of the default routes present!

6. Save the Node and the custom routes file will update, as long as it is writable.

7. Go to Extensions>Route>List Routes and you should see the alias you entered with a slash at the beginning as a hyperlink. Clicking on it should take	you to view the node.

NOTE #1: Take care not to conflict with any of the default routes present in croogo_routes.php file, or in the Config/[pluginname]_routes.php file of other plugins!	 

NOTE #2: On the "List Routes" page, routes linked to nodes will have an extra option named "Edit Node" for convenience. Routes which do not have a 2-way link to nodes will not have this option. Likewise, routes which do not relate to nodes at all will not have this option.
	
You may also try creating your own custom routes using the "New Route" button on the "List Routes" page, or from the "Extensions>Route>Create Route" menu item. The body of the route must be contained inside an array() for the time being, in this version of the plugin. This will let you map routes to certain controllers, but not to specific paths. 

Example of Route Body:
array('controller' => 'nodes', 'action' => 'view', 'type' => 'page', 20)
where 20 is the ID of the node, and the node's type is a 'page'

Author: Damian Grant
Email: <codebogan@gmail.com>