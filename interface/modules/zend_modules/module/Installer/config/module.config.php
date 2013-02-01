<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Installer\Controller\Installer' => 'Installer\Controller\InstallerController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'Installer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Installer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Installer\Controller\Installer',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
    	'template_map' => array(
    		 'site/layout'           => __DIR__ . '/../view/layout/layout.phtml',
    	
    	),
        'template_path_stack' => array(
            'installer' => __DIR__ . '/../view',
        ),
    	'layout' => 'site/layout',
    ),
	'session' => array(
			'remember_me_seconds' => 2419200,
			'use_cookies' => true,
			'cookie_httponly' => true,
	),
);