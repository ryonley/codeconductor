<?php

return array(
  'controller_plugins' => array(
     'invokables' =>  array(
         'IsAllowed' => 'RelyAuthorize\Controller\Plugin\IsAllowed'
     )  
  ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'RelyAuthorize' => __DIR__ . '/../view',
        ),
    ),
);
