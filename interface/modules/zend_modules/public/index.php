<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Jacob T.Paul <jacob@zhservices.com>
//           Shalini Balakrishnan  <shalini@zhservices.com>
//
// +------------------------------------------------------------------------------+
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
require_once(dirname(__FILE__)."/../../../globals.php");
require_once(dirname(__FILE__)."/../../../../library/forms.inc");
require_once(dirname(__FILE__)."/../../../../library/options.inc.php");
require_once(dirname(__FILE__)."/../../../../library/acl.inc");
//require_once(dirname(__FILE__)."/../../../../library/lists.inc");
if (isset ($phpgacl_location)) {
	include_once("$phpgacl_location/gacl_api.class.php");
	$gacl = new gacl_api();
    }
    else {
	    die("You must first set up library/acl.inc to use phpGACL!");
    }
require_once("$srcdir/acl_upgrade_fx.php");

chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

