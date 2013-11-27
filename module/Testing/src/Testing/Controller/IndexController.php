<?php
namespace Testing\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        $data = array('message' => 'hi');
        $view = new ViewModel($data);
        return $view;
    }

    public function getdataAction(){
        $response = $this->getResponse();

        $data = array('message' => 'Hello World');
        $view = new ViewModel();
        $view->setTemplate('testing/testing')
            ->setVariable('message', 'testing 123')
            ->setTerminal(true);

        $output = $this->getServiceLocator()
            ->get('viewrenderer')
            ->render($view);

        $response->setContent(\Zend\Json\Json::encode(array( 'output' => $output, 'other_data' => 'here is some other data')));
        return $response;
    }
}