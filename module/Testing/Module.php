<?php
namespace Testing;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;



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
                'myViewRenderer' => function($sm){
                    $renderer = new PhpRenderer();

                    $map = new Resolver\TemplateMapResolver(array(
                        'layout'      => __DIR__ . '/view/layout.phtml',
                        'testing/index' => __DIR__ . '/view/testing/index/index.phtml',
                    ));
                    $renderer->setResolver($map);

                    return $renderer;
                }
            ),
            'myNewRenderer' => function(){
                $renderer = new PhpRenderer();
                $resolver = new Resolver\TemplatePathStack();
                $resolver->setPaths(array(
                    'script_paths' => __DIR__ . '/view'
                ));

                $renderer->setResolver($resolver);
                return $renderer;
            }
        );
    }

}

?>
