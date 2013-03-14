<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Lab'       => 'Lab\Controller\LabController',
            'Result'    => 'Lab\Controller\ResultController',
            'Pull' => 'Lab\Controller\PullController',
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
            
            'result' => array(
            'type' => 'segment',
                'options' => array(
                    //'route'    => '/lab/:controller[[/:action][/:id]]',
                    'route'    => '/lab/result[/:action][/:id]',
                    'constraints' => array(
                        //'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller'    => 'Result',
                        'action'        => 'index',
                    ),
                ),
            ),
            'pull' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/lab/pull[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Pull',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'lab' => __DIR__ . '/../view/',
             'pull' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
