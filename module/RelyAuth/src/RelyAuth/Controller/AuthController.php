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
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


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
        $em = $this->getEntityManager();
        $config = new Config($sm->get('Config'));
        $success_route = $config->login_success->route_name;
        $messages = array();
        
        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('success');
        }
                 
        $form       = new LoginForm();


        $request = $this->getRequest();
        if ($request->isPost()){
            $post = $request->getPost();
            $users = $em->getRepository('RelyAuth\Entity\User')->findBy(array('username' => $post['email']));
            $user = $users[0];
            $form->setInputFilter($user->getInputFilter());

            $form->setData($post);
            if ($form->isValid()){
                    // ONLY Authenticate if the user is valid
                    if('active' == $user->getStatus()){
                                $authService = $sm->get('Zend\Authentication\AuthenticationService');
                                $adapter = $authService->getAdapter();
                                $adapter->setIdentityValue($request->getPost('email'));
                                $adapter->setCredentialValue($request->getPost('password'));
                                $authResult = $authService->authenticate();


                                foreach($authResult->getMessages() as $message)
                                {

                                    $messages[] = $message;
                                }

                                if($authResult->isValid()){
                                    return $this->redirect()->toRoute($success_route);
                                }
                    } else {
                        $messages[] = 'Your account has not been activated';
                    }
            }
        }
        
        return array(
            'form'      => $form,
            'messages'  => $messages
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
        $messages = array();

        //if already login, redirect to success page
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('dashboard');
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

                //Set token validity time

                //Create a role object
                $role = $em->find('RelyAuth\Entity\Role', 1);

                //Create new user
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

                // Create the activation link
                $url = $this->url()->fromRoute('confirm', array('token' => $user->getToken()));
                $full_url = "http://".$_SERVER['SERVER_NAME'].$url;

                // Prepare the activation email
                $htmlBody = "<h2>Please click the link below to complete your registration.</h2>
                             <p><a href='".$full_url."'>Complete Registration</a></p>";

                $subject = "Welcome to codeConductor.com";

                // Send the email
                $this->sendMail($htmlBody,  $subject,  $username);

                // Set the message
                $messages[] = "You should receive an activation email soon.";
            }
        }

        return array(
            'form'      => $form,
            'messages'  => $messages
        );
    }

    public function confirmAction(){
        // It will query for user by token and upon finding user,
        // it will change their status to active if it is pending and set the registration date
        // it will then provide a link to the login form
        $em = $this->getEntityManager();
        $token = $this->params()->fromRoute('token');

        // Find the user who's token id matches
        $users = $em->getRepository('RelyAuth\Entity\User')->findBy(array('token' => $token));
        $user = $users[0];
        $user->setStatus('active');
        $em->persist($user);
        $em->flush();
    }

    protected function generateToken($email){
        $token = sha1($email.time().rand(0, 1000000));
        return $token;
    }

    protected function sendMail($htmlBody,  $subject, $to, $from='info@codeconductor.com')
    {
        $html = new MimePart($htmlBody);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $mail = new Mail\Message();
        $mail->setBody($body)
            ->setFrom($from, 'codeConductor')
            ->addTo($to)
            ->setSubject($subject);

        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
    }


    
    
}

?>
