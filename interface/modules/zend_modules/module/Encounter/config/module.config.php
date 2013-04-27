<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Encounter'           => 'Encounter\Controller\EncounterController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'encounter' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/encounter[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Encounter',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'encounter' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'encounter/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
