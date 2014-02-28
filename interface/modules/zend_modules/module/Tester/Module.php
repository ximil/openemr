<?php
// module/Tester/Module.php
namespace Tester;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Tester\Model\TesterTable;

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
	
    public function init(ModuleManager $moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controller->layout('tester/layout/layout');
                $route = $controller->getEvent()->getRouteMatch();
                $controller->getEvent()->getViewModel()->setVariables(array(
                    'current_controller' => $route->getParam('controller'),
                    'current_action' => $route->getParam('action'),
                )); 
        }, 100);
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Tester\Model\TesterTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new TesterTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
   
}
?>
