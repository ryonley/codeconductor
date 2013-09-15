<?php
/*
 * 10.3  If you access the login action and you are already logged in, it redirects you to the success controller.. when it should redirect you to the dash... 
 * actually it should redirecty you based on your role.  This could be set in the config also... but I don't think it needs a success controller,
or maybe it does.. maybe instead of redirecting to the dash after successful authentication.. it should redirect to the success controller and the success controller 
 * should redirect based on the role and config..?
 */
namespace RelyAuth\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
 
class SuccessController extends AbstractActionController
{
    public function indexAction()
    {
        if (! $this->getServiceLocator()
                 ->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('login');
        }
         
        return $this->redirect()->toRoute('dash');
        //return new ViewModel();
    }
}
?>
