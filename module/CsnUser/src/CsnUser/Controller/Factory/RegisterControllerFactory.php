<?php
namespace CsnUser\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CsnUser\Controller\RegistrationController;

/**
 *
 * @author swoopfx
 *        
 */
class RegisterControllerFactory implements FactoryInterface
{

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     *
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $ctr = new RegistrationController();
       
        $trans = $serviceLocator->getServiceLocator()->get('MvcTranslator');
        $em = $serviceLocator->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $generalService = $serviceLocator->getServiceLocator()->get('General\Service\GeneralService');
//         $em = $generalService->getEntityManager();
        $er = $serviceLocator->getServiceLocator()->get('csnuser_error_view');
        //$et = $generalService->getAuth();
//         $mailService = $generalService->getMailService();
        $mailService = $serviceLocator->getServiceLocator()->get('acmailer.mailservice.default');
        
        
        
        $registerForm = $serviceLocator->getServiceLocator()->get('csnuser_user_form');
        $op = $serviceLocator->getServiceLocator()->get('csnuser_module_options');
        
        $ctr->setTranslator($trans)
            ->setErroView($er)
            ->setEntityManager($em)
           
//             ->setAuthService($et)
            ->setMailService($mailService)
            ->setRegisterForm($registerForm)
            ->setOptions($op)
            ->setGeneralService($generalService);
            
        return $ctr;
    }
}

?>