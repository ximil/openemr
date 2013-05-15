<?php
// module/Encounter/Module.php
namespace Encounter;

use Encounter\Model\Encounter;
use Encounter\Model\EncounterTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\ModuleManager;
use Zend\View\Helper\Openemr\Emr;
//require_once ("D:/Web/Apache2/htdocs/openemr/gacl/gacl_api.class.php");
//require_once (__DIR__ . '../../../../gacl/gacl_api.class.php');


class Module
{
	public function __construct()
	{
		//$arr = explode('interface', __DIR__) ;
		//require_once ($arr[0] . '/gacl/gacl_api.class.php');
		//require_once ($arr[0] . '/library/acl.inc');
		//require_once ($arr[0] . '/library/lists.inc');
		//include 'D:/Web/ZendFramework/library/Zend/Loader/AutoloaderFactory.php';
		/*Zend\Loader\AutoloaderFactory::factory(array(
				'Zend\Loader\StandardAutoloader' => array(
						'autoregister_zf' => true,
				)
		));*/
		//$loader = new Zend\Loader\StandardAutoloader();
		//$loader->registerNamespace('GAAPI', $arr[0] . '/gacl/gacl_api.class.php');
		//$loader->register();
		
	}
	
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
            	'prefixes' => array(
            		//'ACL_INC' 	=> __DIR__ . '../../../../library/acl.inc',
            		//'LISTS_INC' => __DIR__ . '../../../../library/lists.inc',
            		//'GACL_API'	=> __DIR__ . '../../../../gacl/gacl_api.class.php',
            		//'GACL_API'	=> 'D:/Web/Apache2/htdocs/openemr/gacl/gacl_api.php',
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
            $controller->layout('encounter/layout/layout');
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
                'Encounter\Model\EncounterTable' =>  function($sm) {
                    $tableGateway = $sm->get('EncounterTableGateway');
                    $table = new EncounterTable($tableGateway);
                    return $table;
                },
                'EncounterTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Encounter());
                    return new TableGateway('form_encounter', $dbAdapter, null, $resultSetPrototype);
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
