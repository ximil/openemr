<?php
// module/Acl/Module.php
namespace Acl;

use Acl\Model\Acl;
use Acl\Model\AclTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\View\Helper\Openemr\Emr;
use Zend\Mvc\MvcEvent;

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
	
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Acl\Model\AclTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new AclTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }   

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function init(ModuleManager $moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controller->layout('acl/layout/layout');
            $route = $controller->getEvent()->getRouteMatch();
            $controller->getEvent()->getViewModel()->setVariables(array(
                'current_controller' => $route->getParam('controller'),
                'current_action' => $route->getParam('action'),
            )); 
        }, 100);
    }
    

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'emr_helper' => function($sm) {
                    $locator = $sm->getServiceLocator();
                    return new Emr($locator->get('Request'));
                },
            ),
        );
    }
}
?>
