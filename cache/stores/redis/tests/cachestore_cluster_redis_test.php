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

namespace cachestore_redis;

use cache_definition;
use cache_store;
use cachestore_redis;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../tests/fixtures/stores.php');
require_once(__DIR__ . '/../lib.php');

/**
 * Redis cluster test.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file:
 *
 * define('TEST_CACHESTORE_REDIS_SERVERSCLUSTER', 'localhost:7000,localhost:7001');
 * define('TEST_CACHESTORE_REDIS_ENCRYPTCLUSTER', true);
 * define('TEST_CACHESTORE_REDIS_AUTHCLUSTER', 'foobared');
 * define('TEST_CACHESTORE_REDIS_CASCLUSTER', '/cafile/dir/ca.crt');
 *
 * @package   cachestore_redis
 * @author    Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright 2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \cachestore_redis
 */
class cachestore_cluster_redis_test extends \advanced_testcase {
    /**
     * Create a cache store for testing the Redis cluster.
     *
     * @param string|null $seed The redis cluster servers.
     * @return cachestore_redis The created cache store instance.
     */
    public function create_store(?string $seed = null): cachestore_redis {
        global $DB;

        $definition = cache_definition::load_adhoc(
            mode: cache_store::MODE_APPLICATION,
            component: 'cachestore_redis',
            area: 'phpunit_test',
        );

        $servers = $seed ?? str_replace(",", "\n", TEST_CACHESTORE_REDIS_SERVERSCLUSTER);

        $config = [
            'server'      => $servers,
            'prefix'      => $DB->get_prefix(),
            'clustermode' => true,
        ];

        if (defined('TEST_CACHESTORE_REDIS_ENCRYPTCLUSTER') && TEST_CACHESTORE_REDIS_ENCRYPTCLUSTER === true) {
            $config['encryption'] = true;
        }
        if (defined('TEST_CACHESTORE_REDIS_AUTHCLUSTER') && TEST_CACHESTORE_REDIS_AUTHCLUSTER) {
            $config['password'] = TEST_CACHESTORE_REDIS_AUTHCLUSTER;
        }
        if (defined('TEST_CACHESTORE_REDIS_CASCLUSTER') && TEST_CACHESTORE_REDIS_CASCLUSTER) {
            $config['cafile'] = TEST_CACHESTORE_REDIS_CASCLUSTER;
        }

        $store = new cachestore_redis('TestCluster', $config);
        $store->initialise($definition);
        $store->purge();

        return $store;
    }

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        if (!cachestore_redis::are_requirements_met()) {
            $this->markTestSkipped('Could not test cachestore_redis with cluster, missing requirements.');
        } else if (!\cache_helper::is_cluster_available()) {
            $this->markTestSkipped('Could not test cachestore_redis with cluster, class RedisCluster is not available.');
        } else if (!defined('TEST_CACHESTORE_REDIS_SERVERSCLUSTER')) {
            $this->markTestSkipped('Could not test cachestore_redis with cluster, missing configuration. ' .
                                  "Example: define('TEST_CACHESTORE_REDIS_SERVERSCLUSTER', " .
                                  "'localhost:7000,localhost:7001,localhost:7002');");
        }
    }

    /**
     * Test if the cache store can be created successfully.
     *
     * @covers ::is_ready
     */
    public function test_it_can_create(): void {
        $store = $this->create_store();
        $this->assertNotNull($store);
        $this->assertTrue($store->is_ready());
    }

    /**
     * Test if the cache store trims server names correctly.
     *
     * @covers ::new_redis
     */
    public function test_it_trims_server_names(): void {
        // Add a time before and spaces after the first server. Also adds a blank line before second server.
        $servers = explode(',', TEST_CACHESTORE_REDIS_SERVERSCLUSTER);
        $servers[0] = "\t" . $servers[0] . "  \n";
        $servers = implode("\n", $servers);

        $store = $this->create_store($servers);

        $this->assertTrue($store->is_ready());
    }

    /**
     * Test if the cache store can successfully set and get a value.
     *
     * @covers ::set
     * @covers ::get
     */
    public function test_it_can_setget(): void {
        $store = $this->create_store();
        $store->set('the key', 'the value');
        $actual = $store->get('the key');

        $this->assertSame('the value', $actual);
    }

    /**
     * Test if the cache store can successfully set and get multiple values.
     *
     * @covers ::set_many
     * @covers ::get_many
     */
    public function test_it_can_setget_many(): void {
        $store = $this->create_store();

        // Create values.
        $values = [];
        $keys = [];
        $expected = [];
        for ($i = 0; $i < 10; $i++) {
            $key = "getkey_{$i}";
            $value = "getvalue #{$i}";
            $keys[] = $key;
            $values[] = [
                'key'   => $key,
                'value' => $value,
            ];
            $expected[$key] = $value;
        }

        $store->set_many($values);
        $actual = $store->get_many($keys);
        $this->assertSame($expected, $actual);
    }

    /**
     * Test if the cache store is marked as not ready if it fails to connect.
     *
     * @covers ::is_ready
     */
    public function test_it_is_marked_not_ready_if_failed_to_connect(): void {
        global $DB;

        $config = [
            'server'      => "abc:123",
            'prefix'      => $DB->get_prefix(),
            'clustermode' => true,
        ];
        $store = new cachestore_redis('TestCluster', $config);
        $debugging = $this->getDebuggingMessages();
        // Failed to connect should show a debugging message.
        $this->assertCount(1, \phpunit_util::get_debugging_messages() );
        $this->assertStringContainsString('Couldn\'t map cluster keyspace using any provided seed', $debugging[0]->message);
        $this->resetDebugging();
        $this->assertFalse($store->is_ready());
    }
}
