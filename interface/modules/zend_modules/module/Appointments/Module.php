<?php
// module/Appointments/Module.php
namespace Appointments;

use Appointments\Model\Appointments;
use Appointments\Model\AppointmentsTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\View\Helper\Openemr\Emr;

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
		$controller->layout('appointments/layout/layout');
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
		    'Appointments\Model\AppointmentsTable' =>  function($sm) {
			$tableGateway = $sm->get('AppointmentsTableGateway');
			$table = new AppointmentsTable($tableGateway);
			return $table;
		    },
		    'AppointmentsTableGateway' => function ($sm) {
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
			$resultSetPrototype = new ResultSet();
			$resultSetPrototype->setArrayObjectPrototype(new Appointments());
			return new TableGateway('openemr_postcalendar_events', $dbAdapter, null, $resultSetPrototype);
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
