<?php

namespace SimpleSAML\Test\Module\ldap\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\ldap\Auth\Process\BaseFilter;

class BaseFilterTest extends TestCase
{
    /**
     * @return void
     */
    public function testVarExportHidesLdapPassword()
    {
        $stub = $this->getMockBuilder(BaseFilter::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $class = new \ReflectionClass($stub);
        $method = $class->getMethod('var_export');
        $method->setAccessible(true);

        $this->assertEquals(
            "array ( 'ldap.hostname' => 'ldap://172.17.101.32', 'ldap.port' => 389, 'ldap.password' => '********', )",
            $method->invokeArgs($stub, [[
                'ldap.hostname' => 'ldap://172.17.101.32',
                'ldap.port' => 389,
                'ldap.password' => 'password',
            ]])
        );
    }
}
