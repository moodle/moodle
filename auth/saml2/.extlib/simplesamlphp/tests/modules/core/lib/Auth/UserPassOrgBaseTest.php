<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\core\Auth\UserPassOrgBase;

class UserPassOrgBaseTest extends TestCase
{
    /**
     * @return void
     */
    public function testRememberOrganizationEnabled(): void
    {
        $config = [
            'ldap:LDAPMulti',

            'remember.organization.enabled' => true,
            'remember.organization.checked' => false,

            'my-org' => [
                'description' => 'My organization',
                // The rest of the options are the same as those available for
                // the LDAP authentication source.
                'hostname' => 'ldap://ldap.myorg.com',
                'dnpattern' => 'uid=%username%,ou=employees,dc=example,dc=org',
                // Whether SSL/TLS should be used when contacting the LDAP server.
                'enable_tls' => false,
            ]
        ];

        /** @var \SimpleSAML\Module\core\Auth\UserPassOrgBase $mockUserPassOrgBase */
        $mockUserPassOrgBase = $this->getMockBuilder(\SimpleSAML\Module\core\Auth\UserPassOrgBase::class)
            ->setConstructorArgs([['AuthId' => 'my-org'], &$config])
            ->setMethods([])
            ->getMockForAbstractClass();
        $this->assertTrue($mockUserPassOrgBase->getRememberOrganizationEnabled());
    }
}
