<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Appointments'           => 'Appointments\Controller\AppointmentsController',
        ),
    ),

	'router' => array(
        'routes' => array(
            'appointments' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/appointments[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Appointments',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'appointments' => __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'appointments/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
);

?>
