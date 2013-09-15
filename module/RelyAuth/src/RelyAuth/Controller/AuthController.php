<?php
namespace RelyAuth\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use RelyAuth\Form\LoginForm;
//use Dashboard\Model\User;
use Zend\Session\Container as SessionContainer;
use Zend\Config\Config as Config;
use Doctrine\ORM\EntityManager;
use RelyAuth\Entity\User;


class AuthController extends AbstractActionController
{
    protected $form;
    protected $storage;
    protected $authservice;
    protected $usersTable;
    protected $em;



    public function getAuthService(){
        if(!$this->authservice){
            $this->authservice = $this->getServiceLocator()->get('Auth');
        }
        return $this->authservice;
    }



    public function setEntityManager(EntityManager $em){
        $this->em = $em;
    }

    public function getEntityManager(){
        if(null === $this->em){
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    
    
    
    public function loginAction()
    {
        $sm = $this->getServiceLocator();
        $config = new Config($sm->get('Config'));
        $success_route = $config->login_success->route_name;
        
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('success');
        }
                 
        $form       = new LoginForm();
         
        
        $request = $this->getRequest();
        if ($request->isPost()){
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()){

                $authService = $sm->get('Zend\Authentication\AuthenticationService');

                $adapter = $authService->getAdapter();
                $adapter->setIdentityValue($request->getPost('email'));
                $adapter->setCredentialValue($request->getPost('password'));
                $authResult = $authService->authenticate();

                
                foreach($authResult->getMessages() as $message)
                {
                    $this->flashmessenger()->addMessage($message);
                } 
                       
                if($authResult->isValid()){
                           // $identity = $authResult->getIdentity();
                             return $this->redirect()->toRoute($success_route);
                }
            }
        }
        
        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }
  
    
     public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        $authenticationService = $sm->get('Zend\Authentication\AuthenticationService');
        $authenticationService->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }
    
    public function successAction(){
        $sm = $this->getServiceLocator();
        $authenticationService = $sm->get('Zend\Authentication\AuthenticationService');
        $loggedUser = $authenticationService->getIdentity();
        $real_name = $loggedUser->getRealName();
        $username = $loggedUser->getUserName();
        //$role = $loggedUser->getRole()->first()->getRoleName();
        $role = $loggedUser->getRole()->getName();
        echo "<p>Name: $real_name </p>";
        echo "<p>Username: $username </p>";
        echo "<p>Role: $role </p>";


    }
    
    
}

?>
