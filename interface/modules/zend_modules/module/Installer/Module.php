<?
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
namespace Installer;

// Add these import statements:
use Installer\Model\InstModule; 
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Installer\Model\InstModuleTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Installer\Model\InstModuleTable' =>  function($sm) {
                    $tableGateway = $sm->get('InstModuleTableGateway');
                    $table = new InstModuleTable($tableGateway);
                    return $table;
                },
                'InstModuleTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new InstModule());
                    return new TableGateway('InstModule', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
  
    
    public function onBootstrap(\Zend\EventManager\EventInterface $e)
    {
    	$config = $e->getApplication()->getServiceManager()->get('Configuration');   
    	$sessionConfig = new SessionConfig();
    	$sessionConfig->setOptions($config['session']);
    	$sessionManager = new SessionManager($sessionConfig, null, null);
    	Container::setDefaultManager($sessionManager);    
    	$sessionManager->start();    
    }
}?>
