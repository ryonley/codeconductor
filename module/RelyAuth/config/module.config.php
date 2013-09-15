<?php
namespace RelyAuth;

return array(

    'doctrine' => array(

        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )),
            'authentication' => array(
                'orm_default' => array(
                    'object_manager' => 'Doctrine\ORM\EntityManager',
                    'identity_class' => 'RelyAuth\Entity\User',
                    'identity_property' => 'username',
                    'credential_property' => 'password',
                    'credentialCallable' => 'RelyAuth\Entity\User::hashPassword'
                ),
            ),
    ),

    'controllers' => array(
        'invokables' => array(
            'RelyAuth\Controller\Auth' => 'RelyAuth\Controller\AuthController',
            'RelyAuth\Controller\Success' => 'RelyAuth\Controller\SuccessController'
        ),
    ),
    'router' => array(
        'routes' => array(
             
            'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/auth',
                    'defaults' => array(
                        '__NAMESPACE__' => 'RelyAuth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'logout' => array(
                 'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                          '__NAMESPACE__' => 'RelyAuth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'logout',
                    )
                )
            ),
             
            'success' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'RelyAuth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'success',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
             
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'RelyAuth' => __DIR__ . '/../view',
        ),
    ),
    
    'login_success' => array(
        'route_name' => 'dashboard'
    )
);