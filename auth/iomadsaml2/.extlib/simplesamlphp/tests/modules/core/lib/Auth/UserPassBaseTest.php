<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth;

use PHPUnit\Framework\TestCase;
use SAML2\Constants;
use SimpleSAML\Error\Error as SspError;
use SimpleSAML\Module\core\Auth\UserPassBase;

class UserPassBaseTest extends TestCase
{
    /**
     * @return void
     */
    public function testAuthenticateECPCallsLoginAndSetsAttributes(): void
    {
        $state = [
            'saml:Binding' => Constants::BINDING_PAOS,
        ];
        $attributes = ['attrib' => 'val'];

        $username = $_SERVER['PHP_AUTH_USER'] = 'username';
        $password = $_SERVER['PHP_AUTH_PW'] = 'password';

        $stub = $this->getMockBuilder(UserPassBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['login'])
            ->getMockForAbstractClass();

        /** @var \SimpleSAML\Module\core\Auth\UserPassBase $stub */
        $stub->expects($this->once())
            ->method('login')
            ->with($username, $password)
            ->will($this->returnValue($attributes));

        $stub->authenticate($state);

        $this->assertSame($attributes, $state['Attributes']);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPMissingUsername(): void
    {
        $this->expectException(SspError::class);
        $this->expectExceptionMessage('WRONGUSERPASS');

        $state = [
            'saml:Binding' => Constants::BINDING_PAOS,
        ];

        unset($_SERVER['PHP_AUTH_USER']);
        $_SERVER['PHP_AUTH_PW'] = 'password';

        /** @var \SimpleSAML\Module\core\Auth\UserPassBase $stub */
        $stub = $this->getMockBuilder(UserPassBase::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $stub->authenticate($state);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPMissingPassword(): void
    {
        $this->expectException(SspError::class);
        $this->expectExceptionMessage('WRONGUSERPASS');

        $state = [
            'saml:Binding' => Constants::BINDING_PAOS,
        ];

        $_SERVER['PHP_AUTH_USER'] = 'username';
        unset($_SERVER['PHP_AUTH_PW']);

        $stub = $this->getMockBuilder(UserPassBase::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $stub->authenticate($state);
    }


    /**
     * @return void
     */
    public function testAuthenticateECPCallsLoginWithForcedUsername(): void
    {
        $state = [
            'saml:Binding' => Constants::BINDING_PAOS,
        ];
        $attributes = [];

        $forcedUsername = 'forcedUsername';

        $_SERVER['PHP_AUTH_USER'] = 'username';
        $password = $_SERVER['PHP_AUTH_PW'] = 'password';

        $stub = $this->getMockBuilder(UserPassBase::class)
            ->disableOriginalConstructor()
            ->setMethods(['login'])
            ->getMockForAbstractClass();

        /** @var \SimpleSAML\Module\core\Auth\UserPassBase $stub */
        $stub->expects($this->once())
            ->method('login')
            ->with($forcedUsername, $password)
            ->will($this->returnValue($attributes));

        $stub->setForcedUsername($forcedUsername);
        $stub->authenticate($state);
    }
}
