<?php

return array(


    'controllers' => array(
        'invokables' => array(
            'Dashboard\Controller\Index' => 'Dashboard\Controller\IndexController'

        ),
    ),
    'router' => array(
        'routes' => array(
             
            'dashboard' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
             
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Dashboard' => __DIR__ . '/../view',
        ),
    ),
    'resources' => array(
        'Games'
    )
    

);