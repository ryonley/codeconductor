<?php

namespace RelyAuthorize\Controller\Plugin;

 
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

use Zend\Authentication\AuthenticationService;

class IsAllowed extends AbstractPlugin
{
    protected $extra;
    protected $sesscontainer ;
    
    public function __construct() {
        $this->extra = "a little something extra";
    }
    
       private function getSessContainer()
    {
        if (!$this->sesscontainer) {
            $this->sesscontainer = new SessionContainer();
        }
        return $this->sesscontainer;
    }
    
    
    public function doAuth($e) {
            //setting ACL...
        $acl = new Acl();

        $acl->addRole(new Role('anonymous'));
        $acl->addRole(new Role('user'),  'anonymous');

        $resources = array(
           'Application',
           'RelyAuth',
           'Dashboard',
           'DoctrineORMModule',
           'Games'
        );

        foreach($resources as $resource){
            $acl->addResource(new Resource($resource));
        }




        foreach($resources as $resource){
            // THE "USER" ROLE SHOULD BE ABLE TO ACCESS EVERYTHING FOR NOW
            $acl->allow('user', $resource);
            // ANONYMOUS USERS SHOULD HAVE ACCESS TO EVERYTHING EXCEPT THE DASHBOARD
            if($resource != 'Dashboard') $acl->allow('anonymous', $resource);
        }




    
        

        $action = $e->getRouteMatch()->getParam('action');
        $controllerClass = $e->getRouteMatch()->getParam('controller');
        $namespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        
       // $role = (! $this->getSessContainer()->role) ? 'anonymous' : $this->getSessContainer()->role;
        // GET THE ROLE OF THE USER
        // GET AN INSTANCE OF THE LOGGED USER FIRST
        $serviceLocator = $this->getController()->getServiceLocator();
        $authenticationService = $serviceLocator->get('Zend\Authentication\AuthenticationService');

        if($loggedUser = $authenticationService->getIdentity()){
           // $role = (!isset( $loggedUser->getRole()->first()->name)) ? 'anonymous' : $loggedUser->getRole()->first()->getRoleName();
            $role_name = (isset($loggedUser))? $loggedUser->getRole()->getName(): "";
            $role = (!isset($role_name) || "" == $role_name)? 'anonymous' : $loggedUser->getRole()->getName();
        } else {
            $role = 'anonymous';
        }




       if ( ! $acl->isAllowed($role, $namespace, $action)){

            $router = $e->getRouter();
            $url    = $router->assemble(array(), array('name' => 'login'));
            $response = $e->getResponse();

            //redirect to login route...
            // change with header('location: '.$url); if code below not working
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            $e->stopPropagation();
        }
    }
    
    

}
?>
