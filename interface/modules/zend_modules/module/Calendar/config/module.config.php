<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Calendar' => 'Calendar\Controller\CalendarController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'calendar' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/calendar[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Calendar',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'calendar' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'calendar/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
