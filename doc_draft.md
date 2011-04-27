# Getting started

1. Download Startup and place it where you want.
2. Copy startup.php to the root of your project.
3. Configure settings in startup.php (db, route, facebook, authentication etc). Also make sure it includes right path to bootstrap.php in Startup if you have moved startup.php
4. Simply include this line of code <code>require_once(dirname(__file__) . '/startup.php');</code> and you are ready to use.

# Route

To use route, remember to set both <code>route.base.host</code> and <code>route.base.path</code> setting in configuration file and edit <code>RewriteBase</code> in <code>.htaccess</code>

#### Example - your project is in http://localhost:81/Startup/

	# .htaccess
	RewriteBase /Startup/

	# Startup.php
	c::set('route.base.host', 'localhost:81'); // Hostname ( and port if it isn't 80 or 443 )
	c::set('route.base.path', '/Startup/'); // URL Application path
	
If you **don't** use <code>.htaccess</code>
	
#### Example without .htaccess - your project is in http://localhost:81/Startup/index.php/

	# Startup.php
	c::set('route.base.host', 'localhost:81'); // Hostname ( and port if it isn't 80 or 443 )
	c::set('route.base.path', '/Startup/index.php/'); // URL Application path
	
# Subdomain

If you want to share same session across all subdomain you need to add this in .htaccess file

	php_value session.cookie_domain .localhost