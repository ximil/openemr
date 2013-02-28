<?php
// module/Lab/Module.php
namespace Lab;

use Lab\Model\Lab;
use Lab\Model\LabTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
//use Zend\EventManager\EventManager;

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
            $controller->layout('layout/layout');
        }, 100);
    }
	
    public function getServiceConfig()
	{
       return array(
           'factories' => array(
               'Lab\Model\LabTable' =>  function($sm) {
                   $tableGateway = $sm->get('LabTableGateway');
                   $table = new LabTable($tableGateway);
                   return $table;
               },
               'LabTableGateway' => function ($sm) {
                   $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                   $resultSetPrototype = new ResultSet();
                   $resultSetPrototype->setArrayObjectPrototype(new Lab());
                   return new TableGateway('procedure_order', $dbAdapter, null, $resultSetPrototype);
               },
           ),
       );
	}
}
?>
