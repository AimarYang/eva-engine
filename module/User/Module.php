<?php
namespace User;

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


    public function authority($event)
    {
        $router = $event->getRouteMatch();
        $moduleNamespace = $router->getParam('moduleNamespace');
        
        if($moduleNamespace != 'admin'){
            return true;
        }

        $controller = $router->getParam('controllerName');
        $action = $router->getParam('action');
        if($controller == 'core' && $action == 'index' || 
            $controller == 'logout' && $action == 'index' || 
            $controller == 'login' && $action = 'index' || 
            $controller == 'reset' && $action = 'index'){
            return true;
        }

        return true;
    }

}
