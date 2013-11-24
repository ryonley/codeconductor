<?php
namespace RelyAuth\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use RelyAuth\Form\LoginForm;
use RelyAuth\Form\RegistrationForm;
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

    public function RegisterAction()
    {
        $sm = $this->getServiceLocator();
        $em = $this->getEntityManager();
        $config = new Config($sm->get('Config'));
        $success_route = $config->login_success->route_name;

        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('success');
        }

        $form       = new RegistrationForm();


        $request = $this->getRequest();
        if ($request->isPost()){
            $user = new User($sm);
            $form->setInputFilter($user->getRegistrationFilter());
            $form->setData($request->getPost());
            if ($form->isValid()){
                // This is where we Create a user record
                // Create a token and a link
                // Send an email to the user with that link

                $name = $request->getPost('name');
                $username = $request->getPost('email');
                $password = $request->getPost('password');

                //Create the token

                //Set token validity time

                //Create a role object
                $role = $em->find('RelyAuth\Entity\Role', 1);

                $user = new User();
                $user->setUserName($username)
                     ->setPassword($password)
                     ->setRealName($name)
                     ->setToken($this->generateToken($username))
                     ->setStatus('pending')
                     ->setRole($role);

                // Save the user
                $em->persist($user);
                $em->flush();

                // Create the link
                $url = $this->url()->fromRoute('confirm', array('token' => $user->getToken()));

                echo $url;

                // Send the email

                // Set the message
            }
        }

        return array(
            'form'      => $form,
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }

    public function confirmAction(){
        // This link will send the user to a page that doesn't require authorization
        // It will query for user by token and upon finding user,
        // it will change their status to active if it is pending and set the registration date
        // it will then provide a link to the login form
    }

    protected function generateToken($email){
        $token = sha1($email.time().rand(0, 1000000));
        return $token;
    }
    
    
}

?>
