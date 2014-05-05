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
 * The library file for the memcached cache store.
 *
 * This file is part of the memcached cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_memcached
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The memcached store.
 *
 * (Not to be confused with the memcache store)
 *
 * Configuration options:
 *      servers:        string: host:port:weight , ...
 *      compression:    true, false
 *      serialiser:     SERIALIZER_PHP, SERIALIZER_JSON, SERIALIZER_IGBINARY
 *      prefix:         string: defaults to instance name
 *      hashmethod:     HASH_DEFAULT, HASH_MD5, HASH_CRC, HASH_FNV1_64, HASH_FNV1A_64, HASH_FNV1_32,
 *                      HASH_FNV1A_32, HASH_HSIEH, HASH_MURMUR
 *      bufferwrites:   true, false
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_memcached extends cache_store implements cache_is_configurable {
    /**
     * The name of the store
     * @var store
     */
    protected $name;

    /**
     * The memcached connection
     * @var Memcached
     */
    protected $connection;

    /**
     * An array of servers to use during connection
     * @var array
     */
    protected $servers = array();

    /**
     * The options used when establishing the connection
     * @var array
     */
    protected $options = array();

    /**
     * True when this instance is ready to be initialised.
     * @var bool
     */
    protected $isready = false;

    /**
     * Set to true when this store instance has been initialised.
     * @var bool
     */
    protected $isinitialised = false;

    /**
     * The cache definition this store was initialised with.
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
        if (!is_array($configuration['servers'])) {
            $configuration['servers'] = array($configuration['servers']);
        }

        $compression = array_key_exists('compression', $configuration) ? (bool)$configuration['compression'] : true;
        if (array_key_exists('serialiser', $configuration)) {
            $serialiser = (int)$configuration['serialiser'];
        } else {
            $serialiser = Memcached::SERIALIZER_PHP;
        }
        $prefix = (!empty($configuration['prefix'])) ? (string)$configuration['prefix'] : crc32($name);
        $hashmethod = (array_key_exists('hash', $configuration)) ? (int)$configuration['hash'] : Memcached::HASH_DEFAULT;
        $bufferwrites = array_key_exists('bufferwrites', $configuration) ? (bool)$configuration['bufferwrites'] : false;

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
        $this->options[Memcached::OPT_COMPRESSION] = $compression;
        $this->options[Memcached::OPT_SERIALIZER] = $serialiser;
        $this->options[Memcached::OPT_PREFIX_KEY] = $prefix;
        $this->options[Memcached::OPT_HASH] = $hashmethod;
        $this->options[Memcached::OPT_BUFFER_WRITES] = $bufferwrites;

        $this->connection = new Memcached(crc32($this->name));
        $servers = $this->connection->getServerList();
        if (empty($servers)) {
            foreach ($this->options as $key => $value) {
                $this->connection->setOption($key, $value);
            }
            $this->connection->addServers($this->servers);
        }
        // Test the connection to the pool of servers.
        $this->isready = @$this->connection->set("ping", 'ping', 1);
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
            throw new coding_exception('This memcached instance has already been initialised.');
        }
        $this->definition = $definition;
        $this->isinitialised = true;
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
        return class_exists('Memcached');
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
        $result = $this->connection->getMulti($keys);
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
        return $this->connection->set($key, $data, $this->definition->get_ttl());
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
        $pairs = array();
        foreach ($keyvaluearray as $pair) {
            $pairs[$pair['key']] = $pair['value'];
        }
        if ($this->connection->setMulti($pairs, $this->definition->get_ttl())) {
            return count($keyvaluearray);
        }
        return 0;
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
            if ($this->connection->delete($key)) {
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
            $this->connection->flush();
        }

        return true;
    }

    /**
     * Gets an array of options to use as the serialiser.
     * @return array
     */
    public static function config_get_serialiser_options() {
        $options = array(
            Memcached::SERIALIZER_PHP => get_string('serialiser_php', 'cachestore_memcached')
        );
        if (Memcached::HAVE_JSON) {
            $options[Memcached::SERIALIZER_JSON] = get_string('serialiser_json', 'cachestore_memcached');
        }
        if (Memcached::HAVE_IGBINARY) {
            $options[Memcached::SERIALIZER_IGBINARY] = get_string('serialiser_igbinary', 'cachestore_memcached');
        }
        return $options;
    }

    /**
     * Gets an array of hash options available during configuration.
     * @return array
     */
    public static function config_get_hash_options() {
        $options = array(
            Memcached::HASH_DEFAULT => get_string('hash_default', 'cachestore_memcached'),
            Memcached::HASH_MD5 => get_string('hash_md5', 'cachestore_memcached'),
            Memcached::HASH_CRC => get_string('hash_crc', 'cachestore_memcached'),
            Memcached::HASH_FNV1_64 => get_string('hash_fnv1_64', 'cachestore_memcached'),
            Memcached::HASH_FNV1A_64 => get_string('hash_fnv1a_64', 'cachestore_memcached'),
            Memcached::HASH_FNV1_32 => get_string('hash_fnv1_32', 'cachestore_memcached'),
            Memcached::HASH_FNV1A_32 => get_string('hash_fnv1a_32', 'cachestore_memcached'),
            Memcached::HASH_HSIEH => get_string('hash_hsieh', 'cachestore_memcached'),
            Memcached::HASH_MURMUR => get_string('hash_murmur', 'cachestore_memcached'),
        );
        return $options;
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
        return array(
            'servers' => $servers,
            'compression' => $data->compression,
            'serialiser' => $data->serialiser,
            'prefix' => $data->prefix,
            'hash' => $data->hash,
            'bufferwrites' => $data->bufferwrites,
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
        if (isset($config['compression'])) {
            $data['compression'] = (bool)$config['compression'];
        }
        if (!empty($config['serialiser'])) {
            $data['serialiser'] = $config['serialiser'];
        }
        if (!empty($config['prefix'])) {
            $data['prefix'] = $config['prefix'];
        }
        if (!empty($config['hash'])) {
            $data['hash'] = $config['hash'];
        }
        if (isset($config['bufferwrites'])) {
            $data['bufferwrites'] = (bool)$config['bufferwrites'];
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
            $connection = new Memcached(crc32($this->name));
            $servers = $connection->getServerList();
            if (empty($servers)) {
                foreach ($this->options as $key => $value) {
                    $connection->setOption($key, $value);
                }
                $connection->addServers($this->servers);
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
     * @return cachestore_memcached|false
     */
    public static function initialise_test_instance(cache_definition $definition) {

        if (!self::are_requirements_met()) {
            return false;
        }

        $config = get_config('cachestore_memcached');
        if (empty($config->testservers)) {
            return false;
        }

        $configuration = array();
        $configuration['servers'] = $config->testservers;
        if (!empty($config->testcompression)) {
            $configuration['compression'] = $config->testcompression;
        }
        if (!empty($config->testserialiser)) {
            $configuration['serialiser'] = $config->testserialiser;
        }
        if (!empty($config->testprefix)) {
            $configuration['prefix'] = $config->testprefix;
        }
        if (!empty($config->testhash)) {
            $configuration['hash'] = $config->testhash;
        }
        if (!empty($config->testbufferwrites)) {
            $configuration['bufferwrites'] = $config->testbufferwrites;
        }

        $store = new cachestore_memcached('Test memcached', $configuration);
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
}
