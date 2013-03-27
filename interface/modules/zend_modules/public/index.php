<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
require_once(dirname(__FILE__)."/../../../globals.php");
require_once(dirname(__FILE__)."/../../../../library/forms.inc");
require_once(dirname(__FILE__)."/../../../../library/options.inc.php");
require_once(dirname(__FILE__)."/../../../../library/acl.inc");
$_SESSION['phpgacl'] = $phpgacl_location;
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

