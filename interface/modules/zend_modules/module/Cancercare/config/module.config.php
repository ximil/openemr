<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Cancercare'            => 'Cancercare\Controller\CancercareController',
            'Cancercaredispatch'    => 'Cancercare\Controller\CancercaredispatchController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'cancercare' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cancercare[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cancercare',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cancercaredispatch' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cancercaredispatch[/:action][/:id][/:val][/:id][/:val]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z_]*',
                        'val'    => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Cancercaredispatch',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'cancercare' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'cancercare/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
