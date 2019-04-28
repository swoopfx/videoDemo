<?php
namespace CsnUser\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CsnUser\Controller\IndexController;
use CsnUser\Entity\User;

/**
 *
 * @author swoopfx
 *        
 */
class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $ctr = new IndexController();
        $trans = $serviceLocator->getServiceLocator()->get('MvcTranslator');
        $ctr->setTransLator($trans);
        $form = $serviceLocator->getServiceLocator()->get('csnuser_user_form');
        $ctr->setLoginForm($form);
        $em = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $ctr->setEntityManager($em);
        $at = $serviceLocator->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $op = $serviceLocator->getServiceLocator()->get('csnuser_module_options');
        $errorView = $serviceLocator->getServiceLocator()->get('csnuser_error_view');
        $ctr->setOptions($op);
        $ctr->setAuth($at);
        $userSelectDql = $serviceLocator->getServiceLocator()->get('CsnUser\Service\NewUserService');
        $ctr->selectUserService($userSelectDql);
        $ctr->setErrorView($errorView);
        
        $ue = new User();
        $ctr->setUserEntity($ue);
        return $ctr;
    }
}

?>