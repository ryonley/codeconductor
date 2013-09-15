<?php
namespace Dashboard;

use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
//use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use RelyAuth\Model\MyDbTable as AuthAdapter;
use RelyAuth\Model\User;
use RelyAuth\Model\UsersTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;

class Module 
{
    
    public function getAutoloaderConfig()
    {
        return array(

            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }


    public function getServiceConfig()
    {
        return array(
            'factories'=>array(
                  

                        'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                            // If you are using DoctrineORMModule:
                            return $serviceManager->get('doctrine.authenticationservice.orm_default');
                        },
                        
            ),
        );
    }
 
}

?>
