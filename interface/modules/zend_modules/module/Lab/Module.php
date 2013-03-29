<?php
// module/Lab/Module.php
namespace Lab;

use Lab\Model\Lab;
use Lab\Model\LabTable;
use Lab\Model\Result;
use Lab\Model\ResultTable;
use Lab\Model\Pull;//ADDED VIPIN
use Lab\Model\PullTable;//ADDED VIPIN
use Lab\Model\Specimen;
use Lab\Model\SpecimenTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\View\Helper\Openemr\Emr;
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
                'Lab\Model\LabTable' =>  function($sm) {
                    $tableGateway = $sm->get('LabTableGateway');
                    $table = new LabTable($tableGateway);
                    return $table;
                },
								'Lab\Model\PullTable' =>  function($sm) {
                    $tableGateway = $sm->get('LabTableGateway');
                    $table = new PullTable($tableGateway);
                    return $table;
                },
                'LabTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lab());
                    return new TableGateway('procedure_order', $dbAdapter, null, $resultSetPrototype);
                },
								'Lab\Model\ResultTable' =>  function($sm) {
                    $tableGateway = $sm->get('ResultTableGateway');
                    $table = new ResultTable($tableGateway);
                    return $table;
                },
                'ResultTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Result());
                    return new TableGateway('procedure_result', $dbAdapter, null, $resultSetPrototype);
                },
								'SpecimenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Specimen());
                    return new TableGateway('procedure_order', $dbAdapter, null, $resultSetPrototype);
                },
								'Lab\Model\SpecimenTable' =>  function($sm) {
                    $tableGateway = $sm->get('SpecimenTableGateway');
                    $table = new SpecimenTable($tableGateway);
                    return $table;
                },
            ),
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                // the array key here is the name you will call the view helper by in your view scripts
                'emr_helper' => function($sm) {
                    $locator = $sm->getServiceLocator(); // $sm is the view helper manager, so we need to fetch the main service manager
                    return new Emr($locator->get('Request'));
                },
            ),
        );
    }
}
?>
