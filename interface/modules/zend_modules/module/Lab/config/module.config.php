<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Lab'           => 'Lab\Controller\LabController',
            'Result'        => 'Lab\Controller\ResultController',
            'Pull'          => 'Lab\Controller\PullController',
            'Configuration' => 'Lab\Controller\ConfigurationController',
            'Specimen'      => 'Lab\Controller\SpecimenController',
            'Provider'      => 'Lab\Controller\ProviderController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'lab' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/lab[/:action][/:id]',
                    'constraints' => array(
                        //'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'lab',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'result' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/lab/result[/:action][/:id][/:saved]',
                    'constraints' => array(
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
            
            'configuration' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/lab/configuration[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Configuration',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'specimen' => array(
                'type'    => 'segment',
                'options' => array(
                     'route'    => '/specimen[/:action][/:id][/:saved]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Specimen',
                        //'action'     => 'index',
                    ),
                ),
            ),
            
            'provider' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/provider[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Provider',
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
            'specimen' => __DIR__ . '/../view/',
            'provider'  => __DIR__ . '/../view/',
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
