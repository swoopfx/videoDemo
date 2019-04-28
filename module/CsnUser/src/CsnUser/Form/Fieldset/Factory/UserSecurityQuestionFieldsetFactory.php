<?php
namespace CsnUser\Form\Fieldset\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CsnUser\Form\Fieldset\UserSecurityQuestionFieldset;


/**
 *
 * @author swoopfx
 *        
 */
class UserSecurityQuestionFieldsetFactory implements FactoryInterface
{

    /**
     */
    public function __construct()
    {
        
        // TODO - Insert your code here
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     *
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        
       $fieldset = new UserSecurityQuestionFieldset();
       $generalService = $serviceLocator->getServiceLocator()->get('GeneralServicer\Service\GeneralService');
       $em = $generalService->getEntityManager();
       $fieldset->setGeneralService($generalService)->setEntityManager($em);
       return $fieldset;
    }
}

