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
 * Redis Cache Store - Main library
 *
 * @package   cachestore_redis
 * @copyright 2013 Adam Durana
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Redis Cache Store
 *
 * To allow separation of definitions in Moodle and faster purging, each cache
 * is implemented as a Redis hash.  That is a trade-off between having functionality of TTL
 * and being able to manage many caches in a single redis instance.  Given the recommendation
 * not to use TTL if at all possible and the benefits of having many stores in Redis using the
 * hash configuration, the hash implementation has been used.
 *
 * @copyright   2013 Adam Durana
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_redis extends cache_store implements cache_is_key_aware, cache_is_lockable,
        cache_is_configurable, cache_is_searchable {
    /**
     * Name of this store.
     *
     * @var string
     */
    protected $name;

    /**
     * The definition hash, used for hash key
     *
     * @var string
     */
    protected $hash;

    /**
     * Flag for readiness!
     *
     * @var boolean
     */
    protected $isready = false;

    /**
     * Cache definition for this store.
     *
     * @var cache_definition
     */
    protected $definition = null;

    /**
     * Connection to Redis for this store.
     *
     * @var Redis
     */
    protected $redis;

    /**
     * Serializer for this store.
     *
     * @var int
     */
    protected $serializer = Redis::SERIALIZER_PHP;

    /**
     * Determines if the requirements for this type of store are met.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        return class_exists('Redis');
    }

    /**
     * Determines if this type of store supports a given mode.
     *
     * @param int $mode
     * @return bool
     */
    public static function is_supported_mode($mode) {
        return ($mode === self::MODE_APPLICATION || $mode === self::MODE_SESSION);
    }

    /**
     * Get the features of this type of cache store.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_features(array $configuration = array()) {
        return self::SUPPORTS_DATA_GUARANTEE + self::DEREFERENCES_OBJECTS + self::IS_SEARCHABLE;
    }

    /**
     * Get the supported modes of this type of cache store.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = array()) {
        return self::MODE_APPLICATION + self::MODE_SESSION;
    }

    /**
     * Constructs an instance of this type of store.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = array()) {
        $this->name = $name;

        if (!array_key_exists('server', $configuration) || empty($configuration['server'])) {
            return;
        }
        if (array_key_exists('serializer', $configuration)) {
            $this->serializer = (int)$configuration['serializer'];
        }
        $password = !empty($configuration['password']) ? $configuration['password'] : '';
        $prefix = !empty($configuration['prefix']) ? $configuration['prefix'] : '';
        $this->redis = $this->new_redis($configuration['server'], $prefix, $password);
    }

    /**
     * Create a new Redis instance and
     * connect to the server.
     *
     * @param string $server The server connection string
     * @param string $prefix The key prefix
     * @param string $password The server connection password
     * @return Redis
     */
    protected function new_redis($server, $prefix = '', $password = '') {
        $redis = new Redis();
        // Check if it isn't a Unix socket to set default port.
        $port = ($server[0] === '/') ? null : 6379;
        if (strpos($server, ':')) {
            $serverconf = explode(':', $server);
            $server = $serverconf[0];
            $port = $serverconf[1];
        }
        if ($redis->connect($server, $port)) {
            if (!empty($password)) {
                $redis->auth($password);
            }
            $redis->setOption(Redis::OPT_SERIALIZER, $this->serializer);
            if (!empty($prefix)) {
                $redis->setOption(Redis::OPT_PREFIX, $prefix);
            }
            // Database setting option...
            $this->isready = $this->ping($redis);
        } else {
            $this->isready = false;
        }
        return $redis;
    }

    /**
     * See if we can ping Redis server
     *
     * @param Redis $redis
     * @return bool
     */
    protected function ping(Redis $redis) {
        try {
            if ($redis->ping() === false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the name of the store.
     *
     * @return string
     */
    public function my_name() {
        return $this->name;
    }

    /**
     * Initialize the store.
     *
     * @param cache_definition $definition
     * @return bool
     */
    public function initialise(cache_definition $definition) {
        $this->definition = $definition;
        $this->hash       = $definition->generate_definition_hash();
        return true;
    }

    /**
     * Determine if the store is initialized.
     *
     * @return bool
     */
    public function is_initialised() {
        return ($this->definition !== null);
    }

    /**
     * Determine if the store is ready for use.
     *
     * @return bool
     */
    public function is_ready() {
        return $this->isready;
    }

    /**
     * Get the value associated with a given key.
     *
     * @param string $key The key to get the value of.
     * @return mixed The value of the key, or false if there is no value associated with the key.
     */
    public function get($key) {
        return $this->redis->hGet($this->hash, $key);
    }

    /**
     * Get the values associated with a list of keys.
     *
     * @param array $keys The keys to get the values of.
     * @return array An array of the values of the given keys.
     */
    public function get_many($keys) {
        return $this->redis->hMGet($this->hash, $keys);
    }

    /**
     * Set the value of a key.
     *
     * @param string $key The key to set the value of.
     * @param mixed $value The value.
     * @return bool True if the operation succeeded, false otherwise.
     */
    public function set($key, $value) {
        return ($this->redis->hSet($this->hash, $key, $value) !== false);
    }

    /**
     * Set the values of many keys.
     *
     * @param array $keyvaluearray An array of key/value pairs. Each item in the array is an associative array
     *      with two keys, 'key' and 'value'.
     * @return int The number of key/value pairs successfuly set.
     */
    public function set_many(array $keyvaluearray) {
        $pairs = [];
        foreach ($keyvaluearray as $pair) {
            $pairs[$pair['key']] = $pair['value'];
        }
        if ($this->redis->hMSet($this->hash, $pairs)) {
            return count($pairs);
        }
        return 0;
    }

    /**
     * Delete the given key.
     *
     * @param string $key The key to delete.
     * @return bool True if the delete operation succeeds, false otherwise.
     */
    public function delete($key) {
        return ($this->redis->hDel($this->hash, $key) > 0);
    }

    /**
     * Delete many keys.
     *
     * @param array $keys The keys to delete.
     * @return int The number of keys successfully deleted.
     */
    public function delete_many(array $keys) {
        // Redis needs the hash as the first argument, so we have to put it at the start of the array.
        array_unshift($keys, $this->hash);
        return call_user_func_array(array($this->redis, 'hDel'), $keys);
    }

    /**
     * Purges all keys from the store.
     *
     * @return bool
     */
    public function purge() {
        return ($this->redis->del($this->hash) !== false);
    }

    /**
     * Cleans up after an instance of the store.
     */
    public function instance_deleted() {
        $this->purge();
        $this->redis->close();
        unset($this->redis);
    }

    /**
     * Determines if the store has a given key.
     *
     * @see cache_is_key_aware
     * @param string $key The key to check for.
     * @return bool True if the key exists, false if it does not.
     */
    public function has($key) {
        return !empty($this->redis->hExists($this->hash, $key));
    }

    /**
     * Determines if the store has any of the keys in a list.
     *
     * @see cache_is_key_aware
     * @param array $keys The keys to check for.
     * @return bool True if any of the keys are found, false none of the keys are found.
     */
    public function has_any(array $keys) {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines if the store has all of the keys in a list.
     *
     * @see cache_is_key_aware
     * @param array $keys The keys to check for.
     * @return bool True if all of the keys are found, false otherwise.
     */
    public function has_all(array $keys) {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Tries to acquire a lock with a given name.
     *
     * @see cache_is_lockable
     * @param string $key Name of the lock to acquire.
     * @param string $ownerid Information to identify owner of lock if acquired.
     * @return bool True if the lock was acquired, false if it was not.
     */
    public function acquire_lock($key, $ownerid) {
        return $this->redis->setnx($key, $ownerid);
    }

    /**
     * Checks a lock with a given name and owner information.
     *
     * @see cache_is_lockable
     * @param string $key Name of the lock to check.
     * @param string $ownerid Owner information to check existing lock against.
     * @return mixed True if the lock exists and the owner information matches, null if the lock does not
     *      exist, and false otherwise.
     */
    public function check_lock_state($key, $ownerid) {
        $result = $this->redis->get($key);
        if ($result === $ownerid) {
            return true;
        }
        if ($result === false) {
            return null;
        }
        return false;
    }

    /**
     * Finds all of the keys being used by this cache store instance.
     *
     * @return array of all keys in the hash as a numbered array.
     */
    public function find_all() {
        return $this->redis->hKeys($this->hash);
    }

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
     *
     * @return array List of keys that match this prefix.
     */
    public function find_by_prefix($prefix) {
        $return = [];
        foreach ($this->find_all() as $key) {
            if (strpos($key, $prefix) === 0) {
                $return[] = $key;
            }
        }
        return $return;
    }

    /**
     * Releases a given lock if the owner information matches.
     *
     * @see cache_is_lockable
     * @param string $key Name of the lock to release.
     * @param string $ownerid Owner information to use.
     * @return bool True if the lock is released, false if it is not.
     */
    public function release_lock($key, $ownerid) {
        if ($this->check_lock_state($key, $ownerid)) {
            return ($this->redis->del($key) !== false);
        }
        return false;
    }

    /**
     * Creates a configuration array from given 'add instance' form data.
     *
     * @see cache_is_configurable
     * @param stdClass $data
     * @return array
     */
    public static function config_get_configuration_array($data) {
        return array(
            'server' => $data->server,
            'prefix' => $data->prefix,
            'password' => $data->password,
            'serializer' => $data->serializer
        );
    }

    /**
     * Sets form data from a configuration array.
     *
     * @see cache_is_configurable
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config) {
        $data = array();
        $data['server'] = $config['server'];
        $data['prefix'] = !empty($config['prefix']) ? $config['prefix'] : '';
        $data['password'] = !empty($config['password']) ? $config['password'] : '';
        if (!empty($config['serializer'])) {
            $data['serializer'] = $config['serializer'];
        }
        $editform->set_data($data);
    }


    /**
     * Creates an instance of the store for testing.
     *
     * @param cache_definition $definition
     * @return mixed An instance of the store, or false if an instance cannot be created.
     */
    public static function initialise_test_instance(cache_definition $definition) {
        if (!self::are_requirements_met()) {
            return false;
        }
        $config = get_config('cachestore_redis');
        if (empty($config->test_server)) {
            return false;
        }
        $configuration = array('server' => $config->test_server);
        if (!empty($config->test_serializer)) {
            $configuration['serializer'] = $config->test_serializer;
        }
        if (!empty($config->test_password)) {
            $configuration['password'] = $config->test_password;
        }
        $cache = new cachestore_redis('Redis test', $configuration);
        $cache->initialise($definition);

        return $cache;
    }

    /**
     * Return configuration to use when unit testing.
     *
     * @return array
     */
    public static function unit_test_configuration() {
        global $DB;

        if (!self::are_requirements_met() || !self::ready_to_be_used_for_testing()) {
            throw new moodle_exception('TEST_CACHESTORE_REDIS_TESTSERVERS not configured, unable to create test configuration');
        }

        return ['server' => TEST_CACHESTORE_REDIS_TESTSERVERS,
                'prefix' => $DB->get_prefix(),
        ];
    }

    /**
     * Returns true if this cache store instance is both suitable for testing, and ready for testing.
     *
     * When TEST_CACHESTORE_REDIS_TESTSERVERS is set, then we are ready to be use d for testing.
     *
     * @return bool
     */
    public static function ready_to_be_used_for_testing() {
        return defined('TEST_CACHESTORE_REDIS_TESTSERVERS');
    }

    /**
     * Gets an array of options to use as the serialiser.
     * @return array
     */
    public static function config_get_serializer_options() {
        $options = array(
            Redis::SERIALIZER_PHP => get_string('serializer_php', 'cachestore_redis')
        );

        if (defined('Redis::SERIALIZER_IGBINARY')) {
            $options[Redis::SERIALIZER_IGBINARY] = get_string('serializer_igbinary', 'cachestore_redis');
        }
        return $options;
    }
}
