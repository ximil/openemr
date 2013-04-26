<?
// module/Installer/Module.php
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
