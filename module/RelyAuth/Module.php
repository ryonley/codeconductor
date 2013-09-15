<?php
namespace RelyAuth;

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
                  
                        'RelyAuth\Model\MyAuthStorage' => function($sm){
                            return new \RelyAuth\Model\MyAuthStorage('zf_tutorial'); 
                        },
                        'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                            // If you are using DoctrineORMModule:
                            return $serviceManager->get('doctrine.authenticationservice.orm_default');
                        },

                        'auth' => function($serviceManager) {
                            return $serviceManager->get('doctrine.authenticationservice.orm_default');
                        },

                        'AuthServ' => function($sm){
                            $authAdapter =   $sm->get('doctrine.authenticationadapter.odm_default');

                            $cryptStrategy = $sm->get('cryptStrategy');

                            $authAdapter->setCryptStrategy($cryptStrategy);



                            $authService = new AuthenticationService();
                            $authService->setAdapter($authAdapter);

                            return $authService;
                        },



                        'AuthService' => function($sm) {
                                    //My assumption, you've alredy set dbAdapter
                                    //and has users table with columns : user_name and pass_word
                                    //that password hashed with md5
                            $dbAdapter           = $sm->get('Zend\Db\Adapter\Adapter');

                           
                            $authAdapter = new AuthAdapter($dbAdapter);
                            
                             $cryptStrategy = $sm->get('cryptStrategy');
                             $authAdapter->setCryptStrategy($cryptStrategy);

                              $authAdapter
                                    ->setTableName('users')
                                    ->setIdentityColumn('username')
                                    ->setCredentialColumn('password')
                                ;

                            $authService = new AuthenticationService();
                            //$authService = new MyAuthService();

                            $authService->setAdapter($authAdapter);
                            //$authService->setStorage($sm->get('RelyAuth\Model\MyAuthStorage'));

                            return $authService;
                        },
                         'dbAdapter' => function($sm){
                            return $sm->get('Zend\Db\Adapter\Adapter');
                         },
                          'RelyAuth\Model\UsersTable' => function($sm) {
                                $tableGateway = $sm->get('UsersTableGateway');
                                $table = new UsersTable($tableGateway, $sm);
                                return $table;
                         },
                         'UsersTableGateway' => function($sm){
                             $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                             $resultSetPrototype = new ResultSet();
                             $resultSetPrototype->setArrayObjectPrototype(new User());
                             return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                         },
                         // TO DO ...  ADD A WAY TO TAKE THIS FROM THE MODULES CONFIG
                        'cryptStrategy' => function($sm){
                               $bcrypt = new Bcrypt(array(
                                'salt' => '1234567890123456',
                                'cost' => 14
                                ));
                               return $bcrypt;
                        }
                        
            ),
        );
    }
 
}

?>
