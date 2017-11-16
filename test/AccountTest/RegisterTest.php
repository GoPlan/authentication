<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 11/16/17
 * Time: 11:25 AM
 */

namespace AccountTest;


use CreativeDelta\User\Account\AccountAuthenticationAdapter;
use CreativeDelta\User\Account\AccountMethod;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Core\Domain\UserRegisterMethodAdapter;
use CreativeDelta\User\Core\Impl\Row\IdentityRow;
use CreativeDelta\User\Core\Impl\Table\UserIdentityTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RegisterTest extends AbstractHttpControllerTestCase
{
    const USER_ACCOUNT  = "user01";
    const USER_PASSWORD = "password";

    protected function setUp()
    {
        $this->setApplicationConfig(include __DIR__ . '/../../example/config/development.config.php');
        parent::setUp();
    }

    public function testRegisterAndAuthenticate()
    {
        $dbAdapter = $this->getApplicationServiceLocator()->get(Adapter::class);

        /** @var UserIdentityServiceInterface $accountService */
        $accountService = $this->getApplicationServiceLocator()->get(UserIdentityServiceInterface::class);
        /** @var UserRegisterMethodAdapter $method */
        $method = new AccountMethod($dbAdapter);

        if ($accountService->hasAccount(self::USER_ACCOUNT)) {
            $identity    = $accountService->getIdentityByAccount(self::USER_ACCOUNT);
            $identityRow = new IdentityRow(new UserIdentityTable($dbAdapter), $identity->getArrayCopy());
            $identityRow->delete();
        }

        $newIdentityId = $accountService->register($method, self::USER_ACCOUNT, self::USER_PASSWORD);
        $this->assertNotNull($newIdentityId);

        $authAdapter = new AccountAuthenticationAdapter($accountService, self::USER_ACCOUNT, self::USER_PASSWORD);

        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->getApplicationServiceLocator()->get(AuthenticationService::class);

        /** @var Result $result */
        $result = $authenticationService->authenticate($authAdapter);

        $this->assertTrue($result->isValid() && $result->getCode() == Result::SUCCESS);
    }
}