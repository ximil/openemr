<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Lab' => 'Lab\Controller\LabController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'lab' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/lab[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Lab',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
             'lab' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
                'ViewJsonStrategy',
                'ViewFeedStrategy',
        ),
    ),
);

?>
