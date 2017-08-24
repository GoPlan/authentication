<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 11:44 AM
 */

namespace Application\Controller;


use CreativeDelta\User\Core\Controller\AbstractSecuredActionController;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;

/**
 * Class UserController
 * @package Application\Controller
 *
 * You need to define controller behaviour (noIdentityDispatch) when a secured page is unauthenticated accessed.
 * This behaviour should return an instance of Response.
 */
class UserController extends AbstractSecuredActionController
{
    const ROUTE_NAME = "user";

    function noIdentityDispatch(MvcEvent $e)
    {
        /** @var Request $req */
        $req    = $this->getRequest();
        $return = urlencode($req->getUriString());
        $query  = ['return' => $return];
        return $this->redirect()->toRoute(IndexController::ROUTE_APPLICATION_NAME, ['action' => 'sign-in'], ['query' => $query]);
    }

    public function indexAction()
    {
        return [];
    }

}