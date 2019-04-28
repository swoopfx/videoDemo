<?php
/**
 * CsnUser - Coolcsn Zend Framework 2 User Module
 * 
 * @link https://github.com/coolcsn/CsnUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */
namespace CsnUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
// use Zend\Session\Config\StandardConfig;
use CsnUser\Entity\User;
use CsnUser\Options\ModuleOptions;
use CsnUser\Entity\Lastlogin;
use Users\Entity\BrokerActivation;
use Settings\Service\SettingsService;

/**
 * Index controller
 */
class IndexController extends AbstractActionController
{

    /**
     *
     * @var ModuleOptions
     */
    protected $options;

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var Zend\Mvc\I18n\Translator
     */
    protected $translatorHelper;

    /**
     *
     * @var Zend\Form\Form
     */
    protected $userFormHelper;

    /**
     * Index action
     *
     * The method show to users they are guests
     *
     * @return Zend\View\Model\ViewModelarray navigation menu
     */
    protected $user;

    private $authService;

    private $loginForm;

    private $userEntity;

    protected $errorView;

    private $mailService;

    public function indexAction()
    {
        return new ViewModel(array(
            'navMenu' => $this->options->getNavMenu()
        ));
    }

    /**
     * This persit the user s login timestamp
     * 
     * @param unknown $user            
     */
    private function lastLogin($user)
    {
        $em = $this->entityManager;
        if ($user->getLastlogin() == NULL) {
            $lastloginEntity = new Lastlogin();
            $lastloginEntity->setUser($user)->setLastlogin(new \DateTime());
            $user->setLastlogin($lastloginEntity);
        } else {
            $lastloginEntity = $user->getLastlogin();
            $lastloginEntity->setUser($user)->setLastlogin(new \DateTime());
        }
        try {
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            $this->flashmessenger()->addErrorMessage("We could not logi at this time ");
        }
    }

    private function brokerActivationCondition($userId)
    {
        $em = $this->entityManager;
        $brokerEntity = $em->getRespository("CsnUser\Entity\InsuranceBrokerRegistered")->findOneBy(array(
            "user" => $userId
        ));
        
        var_dump($brokerEntity);
        
        if ($brokerEntity->getBrokerActivation == NULL) {
            $brokerActivationEntity = new BrokerActivation();
        } else {
            $brokerActivationEntity = $brokerEntity->getBrokerActivation();
        }
        
        try {
            $brokerActivationEntity->setActivation($em->find("Settings\Entity\ActivationType", SettingsService::BROKER_ACTIVATION_COMMISION))
                ->setBroker($brokerEntity);
            $brokerEntity->setBrokerActivation($brokerActivationEntity);
            
            $em->persist($brokerEntity);
            $em->flush();
        } catch (\Exception $e) {
            $this->flashmessenger()->addErrorMessage("We could not define the activation");
        }
    }

    /**
     * Log in action
     *
     * The method uses Doctrine Entity Manager to authenticate the input data
     *
     * @return Zend\View\Model\ViewModel|array login form|array messages|array navigation menu
     */
    public function loginAction()
    {
        $user = $this->identity();
        if ($user) {
            return $this->redirect()->toRoute($this->options->getLoginRedirectRoute());
        }
        
        // use the generated controllerr plugin for the redirection
        
        $form = $this->loginForm->createUserForm($this->userEntity, 'login');
        $messages = null;
        if ($this->getRequest()->isPost()) {
            $form->setValidationGroup('usernameOrEmail', 'password', 'rememberme', 'csrf');
            $form->setData($this->getRequest()
                ->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $authService = $this->authService;
                $adapter = $authService->getAdapter();
                $usernameOrEmail = $this->params()->fromPost('usernameOrEmail');
                
                try {
                    // $user = $this->entityManager
                    // ->createQuery("SELECT u FROM CsnUser\Entity\User u WHERE u.email = '$usernameOrEmail' OR u.username = '$usernameOrEmail'")
                    // ->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
                    
                    // $user = $user[0];
                    
                    $user = $this->user->selectUserDQL($usernameOrEmail);
                    if (count($user) > 0) {
                        $user = $user[0];
                    }
                    // var_dump($user);
                    if (count($user) == 0) {
                        
                        $messages = 'The username or email is not valid!';
                        return new ViewModel(array(
                            'error' => $this->translatorHelper->translate('Your authentication credentials are not valid'),
                            'form' => $form,
                            'messages' => $messages,
                            'navMenu' => $this->options->getNavMenu()
                        ));
                    }
                    if (! $user->getEmailConfirmed() == 1) {
                        $messages = $this->translatorHelper->translate('You are yet to confirm your account, please go to the registered email to confirm your account');
                        return new ViewModel(array(
                            'error' => $this->translatorHelper->translate('Unconfirmed account'),
                            'form' => $form,
                            'messages' => $messages,
                            'navMenu' => $this->options->getNavMenu()
                        ));
                    }
                    if ($user->getState()->getId() < 2) {
                        $messages = $this->translatorHelper->translate('Your username is disabled. Please contact an administrator.');
                        return new ViewModel(array(
                            'error' => $this->translatorHelper->translate('Your authentication credentials are not valid'),
                            'form' => $form,
                            'messages' => $messages,
                            'navMenu' => $this->options->getNavMenu()
                        ));
                    }
                    
                    $adapter->setIdentity($user->getUsername());
                    $adapter->setCredential($this->params()
                        ->fromPost('password'));
                    
                    $authResult = $authService->authenticate();
                    // $class_methods = get_class_methods($adapter);
                    // echo "<pre>";print_r($class_methods);exit;
                    
                    if ($authResult->isValid()) {
                        $identity = $authResult->getIdentity();
                        $authService->getStorage()->write($identity);
                        // echo session_id();
                        // var_dump(session_id());
                        // var_dump($authService->getStorage()->read($identity));
                        
                        // Last Login Date
                        $this->lastLogin($this->identity());
                       
                        if ($this->params()->fromPost('rememberme')) {
                            $time = 1209600; // 14 days (1209600/3600 = 336 hours => 336/24 = 14 days)
                            $sessionManager = new SessionManager();
                            $sessionManager->rememberMe($time);
                        }
                        
                        /**
                         * At this region check if the user varible isProfiled is true
                         * If it is true make sure continue with the login
                         * If it is false branch into the condition get the user role mand seed it to
                         * the userProfile Sertvice
                         * to display the required form to fill the profile
                         * if required redirect to the copletinfg profile Page
                         */
                        
                        return $this->redirect()->toRoute($this->options->getLoginRedirectRoute());
                    }
                    
                    foreach ($authResult->getMessages() as $message) {
                        $messages .= "$message\n";
                    }
                } catch (\Exception $e) {
                    // echo "Something went wrong";
                    return $this->errorView->createErrorView($this->translatorHelper->translate('Something went wrong during login! Please, try again later.'), $e, $this->options->getDisplayExceptions(), $this->options);
                    // ->getNavMenu();
                }
            }
        }
        
        return new ViewModel(array(
            'error' => $this->translatorHelper->translate('Your authentication credentials are not valid'),
            'form' => $form,
            'messages' => $messages
        ));
        // 'navMenu' => $this->options->getNavMenu()
    }

    /**
     * Log out action
     *
     * The method destroys session for a logged user
     *
     * @return redirect to specific action
     */
    public function logoutAction()
    {
        $auth = $this->authService;
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }
        
        return $this->redirect()->toRoute($this->options->getLogoutRedirectRoute());
    }

    /**
     * get options
     *
     * @return ModuleOptions
     */
    public function setOptions($opt)
    {
        $this->options = $opt;
    }

    public function setAuth($as)
    {
        $this->authService = $as;
    }

    public function setLoginForm($form)
    {
        $this->loginForm = $form;
    }

    public function setUserEntity($ue)
    {
        $this->userEntity = $ue;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function setTransLator($tr)
    {
        $this->translatorHelper = $tr;
    }

    public function selectUserService($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setErrorView($errorView)
    {
        $this->errorView = $errorView;
        return $this;
    }
}
