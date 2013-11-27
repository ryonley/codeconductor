<?php
namespace Testing;


return array(
    'controllers' => array(
        'invokables' => array(
            'Testing\Controller\Index' => 'Testing\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(


            'testing' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/testing[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Testing\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                )
            ),

        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'testing/testing' => __DIR__.'/../view/testing/index/special.phtml',
        ),
        'template_path_stack' => array(
            'Testing' => __DIR__ . '/../view',
        ),
    ),


);