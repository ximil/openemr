<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Tester'        => 'Tester\Controller\TesterController',
            'Tester-Setup'     => 'Tester\Controller\SetupController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'tester' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/tester[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Tester',
                        'action'     => 'index',
                    ),
                ),
            ),
            'tester-setup' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/tester/setup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Tester-Setup',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'tester' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'tester/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'tester/layout/setup' => __DIR__ . '/../view/layout/setup.phtml'
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
