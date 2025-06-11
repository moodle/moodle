<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * RedisCluster cache test.
 *
 * @package   cachestore_rediscluster
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');

/**
 * RedisCluster cache test.
 *
 * @package   cachestore_rediscluster
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_rediscluster_test extends cachestore_tests {
    /**
     * @var cachestore_rediscluster
     */
    protected $store;

    /**
     * Returns the MongoDB class name
     *
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_rediscluster';
    }

    protected function tearDown(): void {
        parent::tearDown();

        if ($this->store instanceof cachestore_rediscluster) {
            $this->store->purge();
        }
    }

    /**
     * @return cachestore_rediscluster
     */
    protected function create_cachestore_rediscluster($sharded = false) {
        /** @var cache_definition $definition */
        $cachename = $sharded ? 'phpunit_shard_test' : 'phpunit_test';
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_rediscluster', $cachename);
        $instance = new cachestore_rediscluster('RedisCluster Test', cachestore_rediscluster::unit_test_configuration());

        if (!$instance->is_ready()) {
            // We're not configured to use RedisCluster. Skip.
            $this->markTestSkipped();
        }
        $instance->initialise($definition);
        return $this->store = $instance;
    }

    public function test_has() {
        $store = $this->create_cachestore_rediscluster();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has('foo'));
        $this->assertFalse($store->has('bat'));
    }

    public function test_has_any() {
        $store = $this->create_cachestore_rediscluster();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->has_any(['bat', 'foo']));
        $this->assertFalse($store->has_any(['bat', 'baz']));
    }

    public function test_has_all() {
        $store = $this->create_cachestore_rediscluster();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertTrue($store->set('bat', 'baz'));
        $this->assertTrue($store->has_all(['foo', 'bat']));
        $this->assertFalse($store->has_all(['foo', 'bat', 'this']));
    }

    public function test_set_get() {
        $store = $this->create_cachestore_rediscluster();

        $this->assertTrue($store->set('foo', 'bar'));
        $this->assertEquals('bar', $store->get('foo'));
    }

    public function test_set_many() {
        $store = $this->create_cachestore_rediscluster();

        $kv = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $data = [];
        foreach ($kv as $key => $value) {
            $data[] = ['key' => $key, 'value' => $value];
        }

        // Verify the keys dont exist yet.
        $this->assertFalse($store->has_any(array_keys($kv)));

        // Verify the store suceeds in setting them.
        $this->assertEquals(count($data), $store->set_many($data));

        // Verify they now all exist in the store.
        $this->assertTrue($store->has_all(array_keys($kv)));

        // Verify their content is correct.
        $this->assertEquals($kv, $store->get_many(array_keys($kv)));
    }

    public function test_get_many() {
        $store = $this->create_cachestore_rediscluster();

        $kv = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4',
            'key5' => 'value5',
            'key6' => 'value6',
            'key7' => 'value7',
            'key8' => 'value8',
        ];

        $data = [];
        foreach ($kv as $key => $value) {
            $data[] = ['key' => $key, 'value' => $value];
        }

        $store->set_many($data);

        // Verify selecting just some succeeds.
        $subset = ['key1', 'key3', 'key8'];
        $expected = [
            'key1' => 'value1',
            'key3' => 'value3',
            'key8' => 'value8',
        ];
        $output = $store->get_many($subset);
        $this->assertEquals($expected, $output);
    }

    public function test_lock() {
        $store = $this->create_cachestore_rediscluster();

        $this->assertTrue($store->acquire_lock('lock', '123'));
        $this->assertTrue($store->check_lock_state('lock', '123'));
        $this->assertFalse($store->check_lock_state('lock', '321'));
        $this->assertNull($store->check_lock_state('notalock', '123'));
        $this->assertFalse($store->release_lock('lock', '321'));
        $this->assertTrue($store->release_lock('lock', '123'));
    }

    public function test_delete() {
        $store = $this->create_cachestore_rediscluster();

        $store->set('flange', 'pipe');
        $this->store->delete('flange');
        $this->assertFalse($store->has('flange'));
        $store->set('flange', 'xxx');
        $store->set('foo', 'bar');
        $this->assertTrue($store->has_all(['flange', 'foo']));
        $store->delete_many(['flange', 'foo']);
        $this->assertFalse($store->has_any(['flange', 'foo']));
    }

    public function test_purge() {
        $store = $this->create_cachestore_rediscluster();

        $store->set('flange', 'pipe');
        $this->store->purge();
        $this->assertFalse($store->has('flange'));
        $store->set('flange', 'xxx');
        $this->assertTrue($store->has('flange'));
        $store->purge();
        $this->assertFalse($store->has('flange'));
    }

    public function test_find_all() {
        $stores = [
            $this->create_cachestore_rediscluster(false),
            $this->create_cachestore_rediscluster(true),
        ];
        foreach ($stores as $store) {
            $store = $this->create_cachestore_rediscluster();
            for ($i = 0; $i < 20; $i++) {
                $store->set("key{$i}", 1);
            }
            $this->assertCount(20, $store->find_all());
        }
    }

    public function test_find_by_prefix() {
        $stores = [
            $this->create_cachestore_rediscluster(false),
            $this->create_cachestore_rediscluster(true),
        ];
        foreach ($stores as $store) {
            // Lets fill enough keys that SCAN is very likely to
            // not return all data in one hit.
            for ($i = 0; $i < 500; $i++) {
                $store->set("abc{$i}", 1);
                $store->set("ayz{$i}", 1);
            }
            $this->assertCount(500, $store->find_by_prefix('abc'));
            $this->assertCount(500, $store->find_by_prefix('ayz'));
            $this->assertCount(1000, $store->find_by_prefix('a'));
        }
    }
}
