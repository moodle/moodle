<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Store;

use PHPUnit\Framework\TestCase;
use Predis\Client;
use ReflectionClass;
use SimpleSAML\Configuration;
use SimpleSAML\Store;

/**
 * Tests for the Redis store.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 *
 * @package simplesamlphp/simplesamlphp
 */
class RedisTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mocked_redis;

    /** @var \SimpleSAML\Store\Redis */
    protected $redis;

    /** @var array */
    protected $config;


    /**
     * @return void
     */
    protected function setUp()
    {
        $this->config = [];

        $this->mocked_redis = $this->getMockBuilder(Client::class)
                                   ->setMethods(['get', 'set', 'setex', 'del', 'disconnect'])
                                   ->disableOriginalConstructor()
                                   ->getMock();

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $this->mocked_redis->method('get')
                           ->will($this->returnCallback([$this, 'getMocked']));

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $this->mocked_redis->method('set')
                           ->will($this->returnCallback([$this, 'setMocked']));

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $this->mocked_redis->method('setex')
                           ->will($this->returnCallback([$this, 'setexMocked']));

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $this->mocked_redis->method('del')
                           ->will($this->returnCallback([$this, 'delMocked']));

        $nop = /** @return void */ function () {
            return;
        };

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $this->mocked_redis->method('disconnect')
                           ->will($this->returnCallback($nop));

        /** @var \Predis\Client $this->mocked_redis */
        $this->redis = new Store\Redis($this->mocked_redis);
    }


    /**
     * @param string $key
     * @return string|null
     */
    public function getMocked(string $key): ?string
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setMocked(string $key, $value): void
    {
        $this->config[$key] = $value;
    }


    /**
     * @param string $key
     * @param int $expire
     * @param mixed $value
     * @return void
     */
    public function setexMocked(string $key, int $expire, $value): void
    {
        // Testing expiring data is more trouble than it's worth for now
        $this->setMocked($key, $value);
    }


    /**
     * @param string $key
     * @return void
     */
    public function delMocked(string $key): void
    {
        unset($this->config[$key]);
    }


    /**
     * @covers \SimpleSAML\Store::getInstance
     * @covers \SimpleSAML\Store\Redis::__construct
     * @test
     * @return void
     */
    public function testRedisInstance(): void
    {
        $config = Configuration::loadFromArray([
            'store.type' => 'redis',
            'store.redis.prefix' => 'phpunit_',
        ], '[ARRAY]', 'simplesaml');

        /** @var \SimpleSAML\Store\Redis $store */
        $store = Store::getInstance();

        $this->assertInstanceOf(Store\Redis::class, $store);

        $this->clearInstance($config, Configuration::class);
        $this->clearInstance($store, Store::class);
    }


    /**
     * @covers \SimpleSAML\Store::getInstance
     * @covers \SimpleSAML\Store\Redis::__construct
     * @test
     * @return void
     */
    public function testRedisInstanceWithPassword(): void
    {
        $config = Configuration::loadFromArray([
            'store.type' => 'redis',
            'store.redis.prefix' => 'phpunit_',
            'store.redis.password' => 'password',
        ], '[ARRAY]', 'simplesaml');

        /** @var \SimpleSAML\Store\Redis $store */
        $store = Store::getInstance();

        $this->assertInstanceOf(Store\Redis::class, $store);

        $this->clearInstance($config, Configuration::class);
        $this->clearInstance($store, Store::class);
    }


    /**
     * @covers \SimpleSAML\Store\Redis::get
     * @covers \SimpleSAML\Store\Redis::set
     * @test
     * @return void
     */
    public function testInsertData(): void
    {
        $value = 'TEST';

        $this->redis->set('test', 'key', $value);
        $res = $this->redis->get('test', 'key');
        $expected = $value;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Store\Redis::get
     * @covers \SimpleSAML\Store\Redis::set
     * @test
     * @return void
     */
    public function testInsertExpiringData(): void
    {
        $value = 'TEST';

        $this->redis->set('test', 'key', $value, $expire = 80808080);
        $res = $this->redis->get('test', 'key');
        $expected = $value;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Store\Redis::get
     * @test
     * @return void
     */
    public function testGetEmptyData(): void
    {
        $res = $this->redis->get('test', 'key');

        $this->assertNull($res);
    }


    /**
     * @covers \SimpleSAML\Store\Redis::get
     * @covers \SimpleSAML\Store\Redis::set
     * @test
     * @return void
     */
    public function testOverwriteData(): void
    {
        $value1 = 'TEST1';
        $value2 = 'TEST2';

        $this->redis->set('test', 'key', $value1);
        $this->redis->set('test', 'key', $value2);
        $res = $this->redis->get('test', 'key');
        $expected = $value2;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Store\Redis::get
     * @covers \SimpleSAML\Store\Redis::set
     * @covers \SimpleSAML\Store\Redis::delete
     * @test
     * @return void
     */
    public function testDeleteData(): void
    {
        $this->redis->set('test', 'key', 'TEST');
        $this->redis->delete('test', 'key');
        $res = $this->redis->get('test', 'key');

        $this->assertNull($res);
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
