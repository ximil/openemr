<?php
/**
 * Global Configuration Override
 *
 */

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname='.$GLOBALS['dbase'].';host='.$GLOBALS['host'],
        'username'       => $GLOBALS['login'],
        'password'       => $GLOBALS['pass'],
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($serviceManager) {
				$adapterFactory = new Zend\Db\Adapter\AdapterServiceFactory();
				$adapter = $adapterFactory->createService($serviceManager);
               \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);
               return $adapter;
         }
        ),     
    ),
);
