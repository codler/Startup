# Route

To use route, remember to set both route.base.host and route.base.path setting in configuration file and edit RewriteBase in .htaccess

#### Example - your project is in http://localhost:81/Startup/

	# .htaccess
	RewriteBase /Startup/

	# Startup.php
	c::set('route.base.host', 'localhost:81'); // Hostname
	c::set('route.base.path', '/Startup/'); // URL Application path
	
# Subdomain

If you want to share same session across all subdomain you need to add this in .htaccess file

	php_value session.cookie_domain .localhost