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
 * The library file for the memcache cache store.
 *
 * This file is part of the memcache cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cache_memcache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The memcache store class.
 *
 * (Not to be confused with memcached store)
 *
 * Configuration options:
 *      servers:        string: host:port:weight , ...
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_store_memcache implements cache_store {

    /**
     * The name of the store
     * @var store
     */
    protected $name;

    /**
     * The memcache connection once established.
     * @var Memcache
     */
    protected $connection;

    /**
     * An array of servers to use in the connection args.
     * @var array
     */
    protected $servers = array();

    /**
     * An array of options used when establishing the connection.
     * @var array
     */
    protected $options = array();

    /**
     * Set to true when things are ready to be initialised.
     * @var bool
     */
    protected $isready = false;

    /**
     * The cache definition this store was initialised for.
     * @var cache_definition
     */
    protected $definition;

    /**
     * Constructs the store instance.
     *
     * Noting that this function is not an initialisation. It is used to prepare the store for use.
     * The store will be initialised when required and will be provided with a cache_definition at that time.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array()) {
        $this->name = $name;
        if (!array_key_exists('servers', $configuration) || empty($configuration['servers'])) {
            // Nothing configured.
            return;
        }
        foreach ($configuration['servers'] as $server) {
            if (!is_array($server)) {
                $server = explode(':', $server, 3);
            }
            if (!array_key_exists(1, $server)) {
                $server[1] = 11211;
                $server[2] = 100;
            } else if (!array_key_exists(2, $server)) {
                $server[2] = 100;
            }
            $this->servers[] = $server;
        }

        $this->isready = true;
    }

    /**
     * Initialises the cache.
     *
     * Once this has been done the cache is all set to be used.
     *
     * @param cache_definition $definition
     */
    public function initialise(cache_definition $definition) {
        if ($this->is_initialised()) {
            throw new coding_exception('This memcache instance has already been initialised.');
        }
        $this->definition = $definition;
        $this->connection = new Memcache;
        foreach ($this->servers as $server) {
            $this->connection->addServer($server[0], $server[1], true, $server[2]);
        }
    }

    /**
     * Returns true once this instance has been initialised.
     *
     * @return bool
     */
    public function is_initialised() {
        return ($this->connection !== null);
    }

    /**
     * Returns true if this store instance is ready to be used.
     * @return bool
     */
    public function is_ready() {
        return $this->isready;
    }

    /**
     * Returns true if the store requirements are met.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        return class_exists('Memcache');
    }

    /**
     * Returns true if the given mode is supported by this store.
     *
     * @param int $mode One of cache_store::MODE_*
     * @return bool
     */
    public static function is_supported_mode($mode) {
        return ($mode === self::MODE_APPLICATION || $mode === self::MODE_SESSION);
    }

    /**
     * Returns the supported features as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_features(array $configuration = array()) {
        return self::SUPPORTS_NATIVE_TTL;
    }

    /**
     * Returns true if the store instance supports multiple identifiers.
     *
     * @return bool
     */
    public function supports_multiple_indentifiers() {
        return false;
    }

    /**
     * Returns true if the store instance guarantees data.
     *
     * @return bool
     */
    public function supports_data_guarantee() {
        return false;
    }

    /**
     * Returns true if the store instance supports native ttl.
     *
     * @return bool
     */
    public function supports_native_ttl() {
        return true;
    }

    /**
     * Returns the supported modes as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION + self::MODE_SESSION;
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        return $this->connection->get($key);
    }

    /**
     * Retrieves several items from the cache store in a single transaction.
     *
     * If not all of the items are available in the cache then the data value for those that are missing will be set to false.
     *
     * @param array $keys The array of keys to retrieve
     * @return array An array of items from the cache. There will be an item for each key, those that were not in the store will
     *      be set to false.
     */
    public function get_many($keys) {
        $result = $this->connection->get($keys);
        if (!is_array($result)) {
            $result = array();
        }
        foreach ($keys as $key) {
            if (!array_key_exists($key, $result)) {
                $result[$key] = false;
            }
        }
        return $result;
    }

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        return $this->connection->set($key, $data, MEMCACHE_COMPRESSED, $this->definition->get_ttl());
    }

    /**
     * Sets many items in the cache in a single transaction.
     *
     * @param array $keyvaluearray An array of key value pairs. Each item in the array will be an associative array with two
     *      keys, 'key' and 'value'.
     * @return int The number of items successfully set. It is up to the developer to check this matches the number of items
     *      sent ... if they care that is.
     */
    public function set_many(array $keyvaluearray) {
        $count = 0;
        foreach ($keyvaluearray as $pair) {
            if ($this->connection->set($pair['key'], $pair['value'], MEMCACHE_COMPRESSED, $this->definition->get_ttl())) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Deletes an item from the cache store.
     *
     * @param string $key The key to delete.
     * @return bool Returns true if the operation was a success, false otherwise.
     */
    public function delete($key) {
        return $this->connection->delete($key);
    }

    /**
     * Deletes several keys from the cache in a single action.
     *
     * @param array $keys The keys to delete
     * @return int The number of items successfully deleted.
     */
    public function delete_many(array $keys) {
        $count = 0;
        foreach ($keys as $key) {
            if ($this->delete($key)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Purges the cache deleting all items within it.
     *
     * @return boolean True on success. False otherwise.
     */
    public function purge() {
        $this->connection->flush();
        return true;
    }

    /**
     * Given the data from the add instance form this function creates a configuration array.
     *
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data) {
        $lines = explode("\n", $data->servers);
        $servers = array();
        foreach ($lines as $line) {
            $line = trim($line, ':');
            $servers[] = explode(':', $line, 3);
        }
        return array(
            'servers' => $servers,
        );
    }

    /**
     * Returns true if the user can add an instance of the store plugin.
     *
     * @return bool
     */
    public static function can_add_instance() {
        return true;
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function cleanup() {
        $this->purge();
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * @param cache_definition $definition
     * @return false
     */
    public static function initialise_test_instance(cache_definition $definition) {
        if (!self::are_requirements_met()) {
            return false;
        }

        $config = get_config('cache_memcache');
        if (empty($config->testservers)) {
            return false;
        }

        $configuration = array();
        $configuration['servers'] = explode("\n", $config->testservers);

        $store = new cache_store_memcache('Test memcache', $configuration);
        $store->initialise($definition);

        return $store;
    }
}