<?php
namespace CsnUser\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use CsnUser\Service\NewUserService;

/**
 *
 * @author swoopfx
 *        
 */
class UserFactory implements FactoryInterface
{

    /**
     */
    public function __construct()
    {}

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     *
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $user = new NewUserService();
        $em = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $user->setEntityManager($em);
        return $user;
    }
}

