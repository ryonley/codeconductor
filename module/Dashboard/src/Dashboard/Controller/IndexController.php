<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /*
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $user = new User();
        $user->setRealName('John')->setUserName('jj48@gmail.com')->setPassword('tacoma');
        $objectManager->persist($user);
        $objectManager->flush();
        */
        //$sm = $this->getServiceLocator();
      // $authService = $sm->get('Zend\Authentication\AuthenticationService');

        // EACH OPTION WILL BE MATCHED TO A MODULE'S INDEX CONTROLLER INDEX ACTION
       $resources = array(
         'Games' => array(
             'route' => 'games',
             'pretty_name' => 'Games'
          )
       );



       $layout = $this->layout();
       $layout->setTemplate('layout/dashboard');

        return array('resources' => $resources);
    }


}
