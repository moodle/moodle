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
 * Memcached unit tests.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_MEMCACHED_TESTSERVERS', '127.0.0.1:11211');
 *
 * @package    cachestore_memcached
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/memcached/lib.php');

/**
 * Memcached unit test class.
 *
 * @package    cachestore_memcached
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_memcached_test extends cachestore_tests {
    /**
     * Returns the memcached class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_memcached';
    }

    /**
     * Tests the valid keys to ensure they work.
     */
    public function test_valid_keys() {
        if (!cachestore_memcached::are_requirements_met() || !defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_memcached. Requirements are not met.');
        }

        $this->resetAfterTest(true);

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcached', 'phpunit_test');
        $instance = new cachestore_memcached('Memcached Test', cachestore_memcached::unit_test_configuration());

        if (!$instance->is_ready()) {
            // Something prevented memcached store to be inited (extension, TEST_CACHESTORE_MEMCACHED_TESTSERVERS...).
            $this->markTestSkipped();
        }
        $instance->initialise($definition);

        $keys = array(
            // Alphanumeric.
            'abc', 'ABC', '123', 'aB1', '1aB',
            // Hyphens.
            'a-1', '1-a', '-a1', 'a1-',
            // Underscores.
            'a_1', '1_a', '_a1', 'a1_'
        );

        // Set some keys.
        foreach ($keys as $key) {
            $this->assertTrue($instance->set($key, $key), "Failed to set key `$key`");
        }

        // Get some keys.
        foreach ($keys as $key) {
            $this->assertEquals($key, $instance->get($key), "Failed to get key `$key`");
        }

        // Try get many.
        $values = $instance->get_many($keys);
        foreach ($values as $key => $value) {
            $this->assertEquals($key, $value);
        }

        // Reset a key.
        $this->assertTrue($instance->set($keys[0], 'New'), "Failed to reset key `$key`");
        $this->assertEquals('New', $instance->get($keys[0]), "Failed to get reset key `$key`");

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertTrue($instance->delete($key), "Failed to delete key `$key`");
            $this->assertFalse($instance->get($key), "Retrieved deleted key `$key`");
        }

        // Try set many, and check that count is correct.
        $many = array();
        foreach ($keys as $key) {
            $many[] = array('key' => $key, 'value' => $key);
        }
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Check keys retrieved with get_many.
        $values = $instance->get_many($keys);
        foreach ($keys as $key) {
            $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key`");
            $this->assertEquals($key, $values[$key], "Failed to match get_many key `$key`");
        }

        // Delete many, make sure count matches.
        $returncount = $instance->delete_many($keys);
        $this->assertEquals(count($many), $returncount, 'Delete many count didn\'t match');

        // Check that each key was deleted.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved many deleted key `$key`");
        }

        // Set the keys again.
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Purge.
        $this->assertTrue($instance->purge(), 'Failure to purge');

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved purged key `$key`");
        }
    }

    /**
     * Tests the clustering feature.
     */
    public function test_clustered() {
        if (!cachestore_memcached::are_requirements_met() || !defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_memcached. Requirements are not met.');
        }

        $this->resetAfterTest(true);

        $testservers = explode("\n", trim(TEST_CACHESTORE_MEMCACHED_TESTSERVERS));

        if (count($testservers) < 2) {
            $this->markTestSkipped('Could not test clustered memcached, there are not enough test servers defined.');
        }

        // Use the first server as our primary.
        // We need to set a prefix for all, otherwise it uses the name, which will not match between connections.
        set_config('testprefix', 'pre', 'cachestore_memcached');
        // We need to set a name, otherwise we get a reused connection.
        set_config('testname', 'cluster', 'cachestore_memcached');
        set_config('testservers', $testservers[0], 'cachestore_memcached');
        set_config('testsetservers', TEST_CACHESTORE_MEMCACHED_TESTSERVERS, 'cachestore_memcached');
        set_config('testclustered', true, 'cachestore_memcached');

        // First and instance that we can use to test the second server.
        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcached', 'phpunit_test');
        $instance = cachestore_memcached::initialise_test_instance($definition);

        if (!$instance->is_ready()) {
            $this->markTestSkipped();
        }

        // Now we are going to setup a connection to each independent server.
        set_config('testclustered', false, 'cachestore_memcached');
        set_config('testsetservers', '', 'cachestore_memcached');
        $checkinstances = array();
        foreach ($testservers as $testserver) {
            // We need to set a name, otherwise we get a reused connection.
            set_config('testname', $testserver, 'cachestore_memcached');
            set_config('testservers', $testserver, 'cachestore_memcached');
            $checkinstance = cachestore_memcached::initialise_test_instance($definition);
            if (!$checkinstance->is_ready()) {
                $this->markTestSkipped();
            }
            $checkinstances[] = $checkinstance;
        }

        $keys = array(
            // Alphanumeric.
            'abc', 'ABC', '123', 'aB1', '1aB',
            // Hyphens.
            'a-1', '1-a', '-a1', 'a1-',
            // Underscores.
            'a_1', '1_a', '_a1', 'a1_'
        );

        // Set each key.
        foreach ($keys as $key) {
            $this->assertTrue($instance->set($key, $key), "Failed to set key `$key`");
        }

        // Check each key.
        foreach ($keys as $key) {
            $this->assertEquals($key, $instance->get($key), "Failed to get key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertEquals($key, $checkinstance->get($key), "Failed to get key `$key` from server $id");
            }
        }

        // Reset a key.
        $this->assertTrue($instance->set($keys[0], 'New'), "Failed to reset key `$key`");
        $this->assertEquals('New', $instance->get($keys[0]), "Failed to get reset key `$key`");
        foreach ($checkinstances as $id => $checkinstance) {
            $this->assertEquals('New', $checkinstance->get($keys[0]), "Failed to get reset key `$key` from server $id");
        }

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertTrue($instance->delete($key), "Failed to delete key `$key`");
            $this->assertFalse($instance->get($key), "Retrieved deleted key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved deleted key `$key` from server $id");
            }
        }

        // Try set many, and check that count is correct.
        $many = array();
        foreach ($keys as $key) {
            $many[] = array('key' => $key, 'value' => $key);
        }
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Check keys retrieved with get_many.
        $values = $instance->get_many($keys);
        foreach ($keys as $key) {
            $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key`");
            $this->assertEquals($key, $values[$key], "Failed to match get_many key `$key`");
        }
        foreach ($checkinstances as $id => $checkinstance) {
            $values = $checkinstance->get_many($keys);
            foreach ($keys as $key) {
                $this->assertTrue(isset($values[$key]), "Failed to get_many key `$key` from server $id");
                $this->assertEquals($key, $values[$key], "Failed to get_many key `$key` from server $id");
            }
        }

        // Delete many, make sure count matches.
        $returncount = $instance->delete_many($keys);
        $this->assertEquals(count($many), $returncount, 'Delete many count didn\'t match');

        // Check that each key was deleted.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved many deleted key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved many deleted key `$key` from server $id");
            }
        }

        // Set the keys again.
        $returncount = $instance->set_many($many);
        $this->assertEquals(count($many), $returncount, 'Set many count didn\'t match');

        // Purge.
        $this->assertTrue($instance->purge(), 'Failure to purge');

        // Delete and check that we can't retrieve.
        foreach ($keys as $key) {
            $this->assertFalse($instance->get($key), "Retrieved purged key `$key`");
            foreach ($checkinstances as $id => $checkinstance) {
                $this->assertFalse($checkinstance->get($key), "Retrieved purged key `$key` from server 2");
            }
        }
    }

    /**
     * Tests that memcached cache store doesn't just flush everything and instead deletes only what belongs to it
     * when it is marked as a shared cache.
     */
    public function test_multi_use_compatibility() {
        if (!cachestore_memcached::are_requirements_met() || !defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_memcached. Requirements are not met.');
        }

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcached', 'phpunit_test');
        $cachestore = $this->create_test_cache_with_config($definition, array('isshared' => true));
        if (!$cachestore->is_connection_ready()) {
            $this->markTestSkipped('Could not test cachestore_memcached. Connection is not ready.');
        }

        $connection = new Memcached(crc32(__METHOD__));
        $connection->addServers($this->get_servers(TEST_CACHESTORE_MEMCACHED_TESTSERVERS));
        $connection->setOptions(array(
            Memcached::OPT_COMPRESSION => true,
            Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP,
            Memcached::OPT_PREFIX_KEY => 'phpunit_',
            Memcached::OPT_BUFFER_WRITES => false
        ));

        // We must flush first to make sure nothing is there.
        $connection->flush();

        // Test the cachestore.
        $this->assertFalse($cachestore->get('test'));
        $this->assertTrue($cachestore->set('test', 'cachestore'));
        $this->assertSame('cachestore', $cachestore->get('test'));

        // Test the connection.
        $this->assertFalse($connection->get('test'));
        $this->assertEquals(Memcached::RES_NOTFOUND, $connection->getResultCode());
        $this->assertTrue($connection->set('test', 'connection'));
        $this->assertSame('connection', $connection->get('test'));

        // Test both again and make sure the values are correct.
        $this->assertSame('cachestore', $cachestore->get('test'));
        $this->assertSame('connection', $connection->get('test'));

        // Purge the cachestore and check the connection was not purged.
        $this->assertTrue($cachestore->purge());
        $this->assertFalse($cachestore->get('test'));
        $this->assertSame('connection', $connection->get('test'));
    }

    /**
     * Tests that memcached cache store flushes entire cache when it is using a dedicated cache.
     */
    public function test_dedicated_cache() {
        if (!cachestore_memcached::are_requirements_met() || !defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_memcached. Requirements are not met.');
        }

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_memcached', 'phpunit_test');
        $cachestore = $this->create_test_cache_with_config($definition, array('isshared' => false));
        $connection = new Memcached(crc32(__METHOD__));
        $connection->addServers($this->get_servers(TEST_CACHESTORE_MEMCACHED_TESTSERVERS));
        $connection->setOptions(array(
            Memcached::OPT_COMPRESSION => true,
            Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP,
            Memcached::OPT_PREFIX_KEY => 'phpunit_',
            Memcached::OPT_BUFFER_WRITES => false
        ));

        // We must flush first to make sure nothing is there.
        $connection->flush();

        // Test the cachestore.
        $this->assertFalse($cachestore->get('test'));
        $this->assertTrue($cachestore->set('test', 'cachestore'));
        $this->assertSame('cachestore', $cachestore->get('test'));

        // Test the connection.
        $this->assertFalse($connection->get('test'));
        $this->assertEquals(Memcached::RES_NOTFOUND, $connection->getResultCode());
        $this->assertTrue($connection->set('test', 'connection'));
        $this->assertSame('connection', $connection->get('test'));

        // Test both again and make sure the values are correct.
        $this->assertSame('cachestore', $cachestore->get('test'));
        $this->assertSame('connection', $connection->get('test'));

        // Purge the cachestore and check the connection was also purged.
        $this->assertTrue($cachestore->purge());
        $this->assertFalse($cachestore->get('test'));
        $this->assertFalse($connection->get('test'));
    }

    /**
     * Given a server string this returns an array of servers.
     *
     * @param string $serverstring
     * @return array
     */
    public function get_servers($serverstring) {
        $servers = array();
        foreach (explode("\n", $serverstring) as $server) {
            if (!is_array($server)) {
                $server = explode(':', $server, 3);
            }
            if (!array_key_exists(1, $server)) {
                $server[1] = 11211;
                $server[2] = 100;
            } else if (!array_key_exists(2, $server)) {
                $server[2] = 100;
            }
            $servers[] = $server;
        }
        return $servers;
    }

    /**
     * Creates a test instance for unit tests.
     * @param cache_definition $definition
     * @param array $configuration
     * @return null|cachestore_memcached
     */
    private function create_test_cache_with_config(cache_definition $definition, $configuration = array()) {
        $class = $this->get_class_name();

        if (!$class::are_requirements_met()) {
            return null;
        }
        if (!defined('TEST_CACHESTORE_MEMCACHED_TESTSERVERS')) {
            return null;
        }

        $configuration['servers'] = explode("\n", TEST_CACHESTORE_MEMCACHED_TESTSERVERS);

        $store = new $class('Test memcached', $configuration);
        $store->initialise($definition);

        return $store;
    }
}
