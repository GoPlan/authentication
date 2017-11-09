<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CreativeDelta\User\Application\Controller;

use CreativeDelta\User\Core\Impl\Service\UserSessionService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const ROUTE_HOME_NAME        = "home";
    const ROUTE_APPLICATION_NAME = "application";

    /**
     * @var AuthenticationService;
     */
    protected $authService;

    /**
     * IndexController constructor.
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService = null)
    {
        $this->authService = $authService;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function signInAction()
    {
        $return  = $this->params()->fromQuery(UserSessionService::QUERY_RETURN_URL_NAME);
        $session = $this->params()->fromQuery(UserSessionService::QUERY_SESSION_NAME);
        return ['return' => $return, 'session' => $session];
    }

    public function registerAction()
    {
        $return  = $this->params()->fromQuery(UserSessionService::QUERY_RETURN_URL_NAME);
        $session = $this->params()->fromQuery(UserSessionService::QUERY_SESSION_NAME);
        return ['return' => $return, 'session' => $session];
    }

    public function signOutAction()
    {
        $this->authService->clearIdentity();
        return $this->redirect()->toRoute(self::ROUTE_HOME_NAME);
    }
}
