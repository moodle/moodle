<?php

declare(strict_types=1);

namespace SimpleSAML\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Module;

class ModuleTest extends TestCase
{
    /**
     * Test for SimpleSAML\Module::isModuleEnabled().
     * @return void
     */
    public function testIsModuleEnabled(): void
    {
        // test for the most basic functionality
        $this->assertTrue(Module::isModuleEnabled('core'));
    }


    /**
     * Test for SimpleSAML\Module::getModuleDir().
     * @return void
     */
    public function testGetModuleDir(): void
    {
        // test for the most basic functionality
        $this->assertEquals(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/modules/module',
            Module::getModuleDir('module')
        );
    }


    /**
     * Test for SimpleSAML\Module::getModuleURL().
     * @return void
     */
    public function testGetModuleURL(): void
    {
        Configuration::loadFromArray([
            'baseurlpath' => 'https://example.com/simplesaml/'
        ], '', 'simplesaml');
        $this->assertEquals(
            'https://example.com/simplesaml/module.php/module/script.php',
            Module::getModuleURL('module/script.php')
        );
        $this->assertEquals(
            'https://example.com/simplesaml/module.php/module/script.php?param1=value1&param2=value2',
            Module::getModuleURL('module/script.php', [
                'param1' => 'value1',
                'param2' => 'value2',
            ])
        );
    }


    /**
     * Test for SimpleSAML\Module::getModules().
     * @return void
     */
    public function testGetModules(): void
    {
        $this->assertGreaterThan(0, count(Module::getModules()));
    }


    /**
     * Test for SimpleSAML\Module::resolveClass(). It will make sure that an exception is thrown if we are not asking
     * for a class inside a module (that is, there is no colon separating the name of the module and the name of the
     * class).
     * @return void
     */
    public function testResolveClassNoModule(): void
    {
        $this->expectException(Exception::class);
        Module::resolveClass('nomodule', '');
    }


    /**
     * Test for SimpleSAML\Module::resolveClass(). It will make sure that an exception is thrown if the class we are
     * asking for cannot be found.
     * @return void
     */
    public function testResolveClassNotFound(): void
    {
        $this->expectException(Exception::class);
        Module::resolveClass('core:Missing', '');
    }


    /**
     * Test for SimpleSAML\Module::resolveClass(). It will make sure that an exception is thrown if the class we are
     * asking for can be resolved, but does not extend a given class.
     * @return void
     */
    public function testResolveClassNotSubclass(): void
    {
        $this->expectException(Exception::class);
        Module::resolveClass('core:PHP', 'Auth_Process', '\Exception');
    }


    /**
     * Test for SimpleSAML\Module::resolveClass(). It covers all the valid use cases.
     * @return void
     */
    public function testResolveClass(): void
    {
        // most basic test
        $this->assertEquals('SimpleSAML\Module\cron\Cron', Module::resolveClass('cron:Cron', ''));

        // test for the $type parameter correctly translated into a path
        $this->assertEquals(
            'SimpleSAML\Module\core\Auth\Process\PHP',
            Module::resolveClass('core:PHP', 'Auth\Process')
        );

        // test for valid subclasses
        $this->assertEquals('SimpleSAML\Module\core\Auth\Process\PHP', Module::resolveClass(
            'core:PHP',
            'Auth\Process',
            '\SimpleSAML\Auth\ProcessingFilter'
        ));
    }
}
