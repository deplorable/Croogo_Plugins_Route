Description

This Route plugin for Croogo lets you create pretty URLs for nodes or other routes from the Admin Interface.

Version: 1.0

Instalation

	1. Upload plugin
	2. Activate plugin
	3. Ensure plugin's generated_routes/all.php file is writable by the webserver process.
	   You can test this by going to the Extensions>Route>List Routes page and clicking on
	   "Manually Regenerate Custom Routes File". If the file is not writable, an error will display.
	   Keep trying to Manually Regenerate the Custom Routes File after changing the file permissions or
	   owner (user/group) of the file. Eventually you will get it working!
	4. Manually edit your app/config/croogo_routes.php file, adding the following line to the bottom:
	   include_once('../plugins/route/generated_routes/all.php'); 
	5. Go to Content>List, and edit a node you have created earlier. (or create one if there are no
	   nodes already). You should see a "Route" tab. Enter the name of the alias you wish to use
	   for the node. Aliases must not begin with a slash or backslash, and can only include
	   alphanumeric characters, underscores and slashes or backslashes. 
	   Here are a few examples for acceptable aliases:
	     about-us
		 about_us
		 aboutus
		 88milesperhour
		 mystuff/rocks
		 yourstuff/really-really-really/sucks
	   NOTE: Take care not to conflict with any of the default routes present in the croogo_routes file, 
	   or in the config/[pluginname]_routes.php file of other plugins!	 
	6. Save the Node and the custom routes file will update, as long as it is writable by the webserver.
	7. Go to Extensions>Route>List Routes and you should see the alias you entered with a slash at the 
	   beginning as a hyperlink. Clicking on it should take	you to view the node.

NOTE: On the "List Routes" page, routes linked to nodes will have an extra option named "Edit Node" for
convenience. Routes which do not have a 2-way link to nodes will not have this option. Likewise, routes
which do not relate to nodes at all will not have this option.
	
You may also try creating your own custom routes using the "New Route" button on the "List Routes" page, 
or from the "Extensions>Route>Create Route" menu item. The body of the route must be contained inside
an array() for the time being, in this version of the plugin. This will let you map routes to certain
controllers, but not to specific paths. 

Author: Damian Grant
Email: <codebogan@gmail.com>

