<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as Config;
use RelyAuth\Entity\User as User;
use RelyAuth\Entity\Role as Role;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /*
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $sm = $this->getServiceLocator();
        $authenticationService = $sm->get('Zend\Authentication\AuthenticationService');
        $loggedUser = $authenticationService->getIdentity();
        $real_name = $loggedUser->getRealName();
        $username = $loggedUser->getUserName();
        $role = $loggedUser->getRole()->first()->getRoleName();
        echo "<p>Name: $real_name </p>";
        echo "<p>Username: $username </p>";
        echo "<p>Role: $role </p>"; */


        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');


        $user = new User();
        $user->setRealName('Rodger')->setUserName('ryonley@gmail.com')->setPassword('tacoma');
        $objectManager->persist($user);

       // $role = $objectManager->getRepository('RelyAuth\Entity\Role')->findBy(array('id' => 1));
        $role = $objectManager->find('RelyAuth\Entity\Role', 1);

        $user->setRole($role);

        //$myrole = $user->getRole()->getName();
        //echo $myrole;

       //$objectManager->flush();



        return new ViewModel();
    }

    public function aboutAction(){
        echo "<p>testing</p>";

    }
}
