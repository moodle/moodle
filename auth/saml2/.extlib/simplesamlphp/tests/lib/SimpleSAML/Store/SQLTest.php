<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Store;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimpleSAML\Configuration;
use SimpleSAML\Store;

/**
 * Tests for the SQL store.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 *
 * @author Sergio Gómez <sergio@uco.es>
 * @package simplesamlphp/simplesamlphp
 */
class SQLTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp()
    {
        Configuration::loadFromArray([
            'store.type'                    => 'sql',
            'store.sql.dsn'                 => 'sqlite::memory:',
            'store.sql.prefix'              => 'phpunit_',
        ], '[ARRAY]', 'simplesaml');
    }


    /**
     * @covers \SimpleSAML\Store::getInstance
     * @covers \SimpleSAML\Store\SQL::__construct
     * @test
     * @return void
     */
    public function SQLInstance(): void
    {
        $store = Store::getInstance();

        $this->assertInstanceOf('SimpleSAML\Store\SQL', $store);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::initTableVersionTable
     * @covers \SimpleSAML\Store\SQL::initKVTable
     * @test
     * @return void
     */
    public function kvstoreTableVersion(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $version = $store->getTableVersion('kvstore');

        $this->assertEquals(2, $version);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::getTableVersion
     * @test
     * @return void
     */
    public function newTableVersion(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $version = $store->getTableVersion('test');

        $this->assertEquals(0, $version);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::setTableVersion
     * @covers \SimpleSAML\Store\SQL::insertOrUpdate
     * @test
     * @return void
     */
    public function testSetTableVersion(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $store->setTableVersion('kvstore', 2);
        $version = $store->getTableVersion('kvstore');

        $this->assertEquals(2, $version);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::get
     * @test
     * @return void
     */
    public function testGetEmptyData(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $value = $store->get('test', 'foo');

        $this->assertNull($value);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::get
     * @covers \SimpleSAML\Store\SQL::set
     * @covers \SimpleSAML\Store\SQL::insertOrUpdate
     * @test
     * @return void
     */
    public function testInsertData(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $store->set('test', 'foo', 'bar');
        $value = $store->get('test', 'foo');

        $this->assertEquals('bar', $value);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::get
     * @covers \SimpleSAML\Store\SQL::set
     * @covers \SimpleSAML\Store\SQL::insertOrUpdate
     * @test
     * @return void
     */
    public function testOverwriteData(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $store->set('test', 'foo', 'bar');
        $store->set('test', 'foo', 'baz');
        $value = $store->get('test', 'foo');

        $this->assertEquals('baz', $value);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::get
     * @covers \SimpleSAML\Store\SQL::set
     * @covers \SimpleSAML\Store\SQL::insertOrUpdate
     * @covers \SimpleSAML\Store\SQL::delete
     * @test
     * @return void
     */
    public function testDeleteData(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $store->set('test', 'foo', 'bar');
        $store->delete('test', 'foo');
        $value = $store->get('test', 'foo');

        $this->assertNull($value);
    }


    /**
     * @covers \SimpleSAML\Store\SQL::get
     * @covers \SimpleSAML\Store\SQL::set
     * @covers \SimpleSAML\Store\SQL::insertOrUpdate
     * @covers \SimpleSAML\Store\SQL::delete
     * @test
     * @return void
     */
    public function testVeryLongKey(): void
    {
        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $key = str_repeat('x', 100);
        $store->set('test', $key, 'bar');
        $store->delete('test', $key);
        $value = $store->get('test', $key);

        $this->assertNull($value);
    }


    /**
     * @return void
     */
    protected function tearDown()
    {
        $config = Configuration::getInstance();

        /** @var \SimpleSAML\Store\SQL $store */
        $store = Store::getInstance();

        $this->clearInstance($config, Configuration::class);
        $this->clearInstance($store, Store::class);
    }


    /**
     * @param \SimpleSAML\Configuration|\SimpleSAML\Store $service
     * @param class-string $className
     * @return void
     */
    protected function clearInstance($service, string $className): void
    {
        $reflectedClass = new ReflectionClass($className);
        $reflectedInstance = $reflectedClass->getProperty('instance');
        $reflectedInstance->setAccessible(true);
        $reflectedInstance->setValue($service, null);
        $reflectedInstance->setAccessible(false);
    }
}
