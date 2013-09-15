<?php
namespace Games;


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
            ))

    ),

    'controllers' => array(
        'invokables' => array(
            'Games\Controller\Index' => 'Games\Controller\IndexController',
            'Games\Controller\TicTacToe' => 'Games\Controller\TicTacToeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            /*
           'games' => array(
               'type'    => 'Literal',
               'options' => array(
                   'route'    => '/games',
                   'defaults' => array(
                       '__NAMESPACE__' => 'Games\Controller',
                       'controller'    => 'Index',
                       'action'        => 'index',
                   ),
               ),
           ),

           'pending_games' => array(
               'type'    => 'Literal',
               'options' => array(
                   'route'    => '/pending',
                   'defaults' => array(
                       '__NAMESPACE__' => 'Games\Controller',
                       'controller'    => 'Index',
                       'action'        => 'pendingGames',
                   ),
               ),
               'may_terminate' => true,
               'child_routes' => array(
                   'post' => array(
                       'type' => 'segment',
                       'options' => array(
                           'route' => '/[:slug]',
                           'constraints' => array(
                               'slug' => '[a-zA-Z0-9_-]+'
                           ),
                           'defaults' => array(
                               'action' => 'view'
                           )
                       )
                   )
               )
           ),
           */

            'games' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/games[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Games\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                )
            ),
            'TicTacToe' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/tic-tac-toe[/:action][/:game_id][/:player_id]',
                    'constraints' => array(
                        'game_id' => '[0-9]+',
                        'player_id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Games\Controller',
                        'controller'    => 'TicTacToe',

                    ),
                ),
            )
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Games' => __DIR__ . '/../view',
        ),
    ),
    

);