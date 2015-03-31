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
 * @package    cachestore_memcache
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
class cachestore_memcache extends cache_store implements cache_is_configurable {

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
     * Key prefix for this memcache.
     * @var string
     */
    protected $prefix;

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
     * Set to true once this store instance has been initialised.
     * @var bool
     */
    protected $isinitialised = false;

    /**
     * The cache definition this store was initialised for.
     * @var cache_definition
     */
    protected $definition;

    /**
     * Set to true when this store is clustered.
     * @var bool
     */
    protected $clustered = false;

    /**
     * Array of servers to set when in clustered mode.
     * @var array
     */
    protected $setservers = array();

    /**
     * The an array of memcache connections for the set servers, once established.
     * @var array
     */
    protected $setconnections = array();

    /**
     * If true data going in and out will be encoded.
     * @var bool
     */
    protected $encode = true;

    /**
     * Default prefix for key names.
     * @var string
     */
    const DEFAULT_PREFIX = 'mdl_';

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
        if (!is_array($configuration['servers'])) {
            $configuration['servers'] = array($configuration['servers']);
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

        $this->clustered = array_key_exists('clustered', $configuration) ? (bool)$configuration['clustered'] : false;

        if ($this->clustered) {
            if (!array_key_exists('setservers', $configuration) || (count($configuration['setservers']) < 1)) {
                // Can't setup clustering without set servers.
                return;
            }
            if (count($this->servers) !== 1) {
                // Can only setup cluster with exactly 1 get server.
                return;
            }
            foreach ($configuration['setservers'] as $server) {
                // We do not use weights (3rd part) on these servers.
                if (!is_array($server)) {
                    $server = explode(':', $server, 3);
                }
                if (!array_key_exists(1, $server)) {
                    $server[1] = 11211;
                }
                $this->setservers[] = $server;
            }
        }

        if (empty($configuration['prefix'])) {
            $this->prefix = self::DEFAULT_PREFIX;
        } else {
            $this->prefix = $configuration['prefix'];
        }

        $this->connection = new Memcache;
        foreach ($this->servers as $server) {
            $this->connection->addServer($server[0], (int) $server[1], true, (int) $server[2]);
        }

        if ($this->clustered) {
            foreach ($this->setservers as $setserver) {
                // Since we will have a number of them with the same name, append server and port.
                $connection = new Memcache;
                $connection->addServer($setserver[0], $setserver[1]);
                $this->setconnections[] = $connection;
            }
        }

        // Test the connection to the pool of servers.
        $this->isready = @$this->connection->set($this->parse_key('ping'), 'ping', MEMCACHE_COMPRESSED, 1);
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
        $this->isinitialised = true;
        $this->encode = self::require_encoding();
    }

    /**
     * Tests if encoding is going to be required.
     *
     * Prior to memcache 3.0.3 scalar data types were not preserved.
     * For earlier versions of the memcache extension we need to encode and decode scalar types
     * to ensure that it is preserved.
     *
     * @param string $version The version to check, if null it is fetched from PHP.
     * @return bool
     */
    public static function require_encoding($version = null) {
        if (!$version) {
            $version = phpversion('memcache');
        }
        return (version_compare($version, '3.0.3', '<'));
    }

    /**
     * Returns true once this instance has been initialised.
     *
     * @return bool
     */
    public function is_initialised() {
        return ($this->isinitialised);
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
     * Returns false as this store does not support multiple identifiers.
     * (This optional function is a performance optimisation; it must be
     * consistent with the value from get_supported_features.)
     *
     * @return bool False
     */
    public function supports_multiple_identifiers() {
        return false;
    }

    /**
     * Returns the supported modes as a combined int.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION;
    }

    /**
     * Parses the given key to make it work for this memcache backend.
     *
     * @param string $key The raw key.
     * @return string The resulting key.
     */
    protected function parse_key($key) {
        if (strlen($key) > 245) {
            $key = '_sha1_'.sha1($key);
        }
        $key = $this->prefix . $key;
        return $key;
    }

    /**
     * Retrieves an item from the cache store given its key.
     *
     * @param string $key The key to retrieve
     * @return mixed The data that was associated with the key, or false if the key did not exist.
     */
    public function get($key) {
        $result = $this->connection->get($this->parse_key($key));
        if ($this->encode && $result !== false) {
            return @unserialize($result);
        }
        return $result;
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
        $mkeys = array();
        foreach ($keys as $key) {
            $mkeys[$key] = $this->parse_key($key);
        }
        $result = $this->connection->get($mkeys);
        if (!is_array($result)) {
            $result = array();
        }
        $return = array();
        foreach ($mkeys as $key => $mkey) {
            if (!array_key_exists($mkey, $result)) {
                $return[$key] = false;
            } else {
                $return[$key] = $result[$mkey];
                if ($this->encode && $return[$key] !== false) {
                    $return[$key] = @unserialize($return[$key]);
                }
            }
        }
        return $return;
    }

    /**
     * Sets an item in the cache given its key and data value.
     *
     * @param string $key The key to use.
     * @param mixed $data The data to set.
     * @return bool True if the operation was a success false otherwise.
     */
    public function set($key, $data) {
        if ($this->encode) {
            // We must serialise this data.
            $data = serialize($data);
        }

        if ($this->clustered) {
            $status = true;
            foreach ($this->setconnections as $connection) {
                $status = $connection->set($this->parse_key($key), $data, MEMCACHE_COMPRESSED, $this->definition->get_ttl())
                        && $status;
            }
            return $status;
        }

        return $this->connection->set($this->parse_key($key), $data, MEMCACHE_COMPRESSED, $this->definition->get_ttl());
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
            if ($this->set($pair['key'], $pair['value'])) {
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
        if ($this->clustered) {
            $status = true;
            foreach ($this->setconnections as $connection) {
                $status = $connection->delete($this->parse_key($key)) && $status;
            }
            return $status;
        }

        return $this->connection->delete($this->parse_key($key));
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
        if ($this->isready) {
            if ($this->clustered) {
                foreach ($this->setconnections as $connection) {
                    $connection->flush();
                }
            } else {
                $this->connection->flush();
            }
        }

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
            // Trim surrounding colons and default whitespace.
            $line = trim(trim($line), ":");
            // Skip blank lines.
            if ($line === '') {
                continue;
            }
            $servers[] = explode(':', $line, 3);
        }

        $clustered = false;
        $setservers = array();
        if (isset($data->clustered)) {
            $clustered = true;

            $lines = explode("\n", $data->setservers);
            foreach ($lines as $line) {
                // Trim surrounding colons and default whitespace.
                $line = trim(trim($line), ":");
                if ($line === '') {
                    continue;
                }
                $setserver = explode(':', $line, 3);
                // We don't use weights, so display a debug message.
                if (count($setserver) > 2) {
                    debugging('Memcache Set Server '.$setserver[0].' has too many parameters.');
                }
                $setservers[] = $setserver;
            }
        }

        return array(
            'servers' => $servers,
            'prefix' => $data->prefix,
            'clustered' => $clustered,
            'setservers' => $setservers
        );
    }

    /**
     * Allows the cache store to set its data against the edit form before it is shown to the user.
     *
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config) {
        $data = array();
        if (!empty($config['servers'])) {
            $servers = array();
            foreach ($config['servers'] as $server) {
                $servers[] = join(":", $server);
            }
            $data['servers'] = join("\n", $servers);
        }
        if (!empty($config['prefix'])) {
            $data['prefix'] = $config['prefix'];
        } else {
            $data['prefix'] = self::DEFAULT_PREFIX;
        }
        if (isset($config['clustered'])) {
            $data['clustered'] = (bool)$config['clustered'];
        }
        if (!empty($config['setservers'])) {
            $servers = array();
            foreach ($config['setservers'] as $server) {
                $servers[] = join(":", $server);
            }
            $data['setservers'] = join("\n", $servers);
        }

        $editform->set_data($data);
    }

    /**
     * Performs any necessary clean up when the store instance is being deleted.
     */
    public function instance_deleted() {
        if ($this->connection) {
            $connection = $this->connection;
        } else {
            $connection = new Memcache;
            foreach ($this->servers as $server) {
                $connection->addServer($server[0], $server[1], true, $server[2]);
            }
        }
        @$connection->flush();
        unset($connection);
        unset($this->connection);
    }

    /**
     * Generates an instance of the cache store that can be used for testing.
     *
     * @param cache_definition $definition
     * @return cachestore_memcache|false
     */
    public static function initialise_test_instance(cache_definition $definition) {
        if (!self::are_requirements_met()) {
            return false;
        }

        $config = get_config('cachestore_memcache');
        if (empty($config->testservers)) {
            return false;
        }

        $configuration = array();
        $configuration['servers'] = explode("\n", $config->testservers);
        if (!empty($config->testclustered)) {
            $configuration['clustered'] = $config->testclustered;
        }
        if (!empty($config->testsetservers)) {
            $configuration['setservers'] = explode("\n", $config->testsetservers);
        }

        $store = new cachestore_memcache('Test memcache', $configuration);
        $store->initialise($definition);

        return $store;
    }

    /**
     * Creates a test instance for unit tests if possible.
     * @param cache_definition $definition
     * @return bool|cachestore_memcache
     */
    public static function initialise_unit_test_instance(cache_definition $definition) {
        if (!self::are_requirements_met()) {
            return false;
        }
        if (!defined('TEST_CACHESTORE_MEMCACHE_TESTSERVERS')) {
            return false;
        }
        $configuration = array();
        $configuration['servers'] = explode("\n", TEST_CACHESTORE_MEMCACHE_TESTSERVERS);

        $store = new cachestore_memcache('Test memcache', $configuration);
        $store->initialise($definition);

        return $store;
    }

    /**
     * Returns the name of this instance.
     * @return string
     */
    public function my_name() {
        return $this->name;
    }

    /**
     * Used to notify of configuration conflicts.
     *
     * The warnings returned here will be displayed on the cache configuration screen.
     *
     * @return string[] Returns an array of warnings (strings)
     */
    public function get_warnings() {
        global $CFG;
        $warnings = array();
        if (isset($CFG->session_memcached_save_path) && count($this->servers)) {
            $bits = explode(':', $CFG->session_memcached_save_path, 3);
            $host = array_shift($bits);
            $port = (count($bits)) ? array_shift($bits) : '11211';
            foreach ($this->servers as $server) {
                if ($server[0] === $host && $server[1] == $port) {
                    $warnings[] = get_string('sessionhandlerconflict', 'cachestore_memcache', $this->my_name());
                    break;
                }
            }
        }
        return $warnings;
    }

    /**
     * Returns true if this cache store instance is both suitable for testing, and ready for testing.
     *
     * Cache stores that support being used as the default store for unit and acceptance testing should
     * override this function and return true if there requirements have been met.
     *
     * @return bool
     */
    public static function ready_to_be_used_for_testing() {
        return defined('TEST_CACHESTORE_MEMCACHE_TESTSERVERS');
    }
}
