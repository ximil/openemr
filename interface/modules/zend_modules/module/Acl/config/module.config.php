<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Acl' => 'Acl\Controller\AclController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'acl' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/acl[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Acl',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

	'view_manager' => array(
        'template_path_stack' => array(
            'acl' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'acl/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);