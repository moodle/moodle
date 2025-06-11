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
 * RedisCluster Cache Store - Main library
 *
 * @package   cachestore_rediscluster
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * RedisCluster Cache Store
 *
 * Forked from the cachestore_redis plugin.
 */
class cachestore_rediscluster extends cache_store implements cache_is_key_aware, cache_is_lockable,
    cache_is_configurable, cache_is_searchable {

    const DEFAULT_SHARD_SIZE = 8;

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
     * Track iterators. Scan calls rely on these to track where they're up to.
     */
    protected $iterators = [];

    /**
     * Cache definition for this store.
     *
     * @var cache_definition
     */
    protected $definition = null;

    /**
     * @var bool Is this definition sharded across multiple hashes?
     */
    protected $sharded = false;

    /**
     * Connection to Redis for this store.
     *
     * @var RedisCluster
     */
    protected $redis;

    /**
     * Connection config.
     *
     * @var array
     */
    protected $config;

    /**
     * How many times the next command called should be retried on error.
     *
     * @var int
     */
    protected $retrylimit = 0;


    /**
     * The actual prefix used with the redis backend. This can differ from what is set on the cache definition.
     *
     * @var string
     */
    private $internalprefix = '';

    /** @var ?array Array of current locks, or null if we haven't registered shutdown function */
    protected $currentlocks = null;

    /**
     * Determines if the requirements for this type of store are met.
     *
     * @return bool
     */
    public static function are_requirements_met() {
        // The existance of the unlink method means we're running at least phpredis 4.0.
        return class_exists('RedisCluster') && method_exists('RedisCluster', 'unlink');
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
    public static function get_supported_features(array $configuration = []) {
        return self::SUPPORTS_DATA_GUARANTEE + self::DEREFERENCES_OBJECTS + self::IS_SEARCHABLE;
    }

    /**
     * Get the supported modes of this type of cache store.
     *
     * @param array $configuration
     * @return int
     */
    public static function get_supported_modes(array $configuration = []) {
        return self::MODE_APPLICATION + self::MODE_SESSION;
    }

    /**
     * Constructs an instance of this type of store.
     *
     * @param string $name
     * @param array $configuration
     */
    public function __construct($name, array $configuration = []) {
        global $CFG;
        $this->name = $name;

        // During unit test purge, it goes off process and no config is passed.
        if (PHPUNIT_TEST && empty($configuration)) {
            // The name is important because it is part of the prefix.
            $this->name    = self::get_testing_name();
            $configuration = self::unit_test_configuration();
        } else if (empty($configuration['server'])) {
            return;
        }

        // Default values.
        $this->config = [
            'compression' => Redis::COMPRESSION_NONE,
            'failover' => RedisCluster::FAILOVER_DISTRIBUTE,
            'lockwait' => 60,
            'locktimeout' => 600,
            'persist' => false,
            'preferrednodes' => null,
            'prefix' => '',
            'readtimeout' => 3.0,
            'serializer' => Redis::SERIALIZER_IGBINARY,
            'server' => null,
            'serversecondary' => null,
            'session' => false,
            'shardsize' => !empty($CFG->redis_shardsize) ? $CFG->redis_shardsize : self::DEFAULT_SHARD_SIZE,
            'timeout' => 3.0,
        ];

        // Override defaults.
        foreach (array_keys($this->config) as $key) {
            if (isset($configuration[$key])) {
                $this->config[$key] = $configuration[$key];
            }
        }

        $this->connect();
    }

    public function __destruct() {
        if (!empty($this->redis) && $this->redis instanceof RedisCluster && empty($this->config['persist'])) {
            $this->redis->close();
        }
    }

    protected function connect() {
        try {
            $this->redis = $this->new_rediscluster();
        } catch (Exception $e) {
            if (empty($this->config['serversecondary'])) {
                $this->fatal_error();
            }
            $subsys = $this->config['session'] ? 'SESSION' : 'MUC';
            trigger_error($subsys.': Primary redis seed list failed, trying with fallback seed list ('.$e->getMessage().')',
                E_USER_WARNING);
            try {
                $this->redis = $this->new_rediscluster(false);
            } catch (Exception $e) {
                trigger_error($subsys.': Redis failure, message: '.$e->getMessage(), E_USER_WARNING);
                $this->fatal_error();
            }
        }
    }

    protected function fatal_error() {
        global $CFG;
        @header('HTTP/1.0 '.$CFG->fatalhttpstatus);
        echo "<p>Error: Cache store connection failed</p><p>Try again later</p>";
        exit(1);
    }

    /**
     * Create a new RedisCluster instance and connect to the cluster.
     *
     * @return RedisCluster
     */
    protected function new_rediscluster($primary = true) {
        $dsn = $primary ? $this->config['server'] : $this->config['serversecondary'];
        $servers = explode(',', $dsn);

        $this->isready = false;
        $this->internalprefix = $this->config['session'] ? $this->config['prefix'] : $this->config['prefix'].$this->name.'-';
        if ($redis = new RedisCluster(null, $servers, $this->config['timeout'],
            $this->config['readtimeout'], $this->config['persist'])) {
            $redis->setOption(Redis::OPT_COMPRESSION, $this->config['compression']);
            $redis->setOption(Redis::OPT_SERIALIZER, $this->config['serializer']);
            $redis->setOption(Redis::OPT_PREFIX, $this->internalprefix);
            $redis->setOption(RedisCluster::OPT_SLAVE_FAILOVER, $this->config['failover']);
            if (defined('RedisCluster::FAILOVER_PREFERRED') && $this->config['failover'] == RedisCluster::FAILOVER_PREFERRED) {
                $nodes = explode(',', $this->config['preferrednodes']);
                $redis->setOption(RedisCluster::OPT_PREFERRED_NODES, $nodes);
            }
            $this->isready = true;
        }
        return $redis;
    }

    /**
     * See if we can ping a Redis server in the cluster
     *
     * @param string $server The specific server to ping.
     * @return bool
     */
    protected function ping($server) {
        try {
            if ($this->command('ping', $server) === false) {
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
        global $CFG;
        $this->definition = $definition;
        $this->hash       = $definition->generate_definition_hash();

        // Standard set of definitions we always shard.
        $sharded = [
            'adhoc/cachestore_rediscluster_phpunit_shard_test',
        ];

        // Customisable list of definitions to shard.
        if (!empty($CFG->redis_sharded) && is_array($CFG->redis_sharded)) {
            $sharded = array_merge($sharded, $CFG->redis_sharded);
        }
        if (in_array($this->definition->get_id(), $sharded)) {
            $this->sharded = true;
        }
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
     * Set how many times the next command (and only the next command) should
     * attempt ito retry before giving up. This value is reset after every
     * successful command.
     *
     * @param int $limit
     * @return void
     */
    public function set_retry_limit($limit = null) {
        if ($limit === null || $limit != (int) $limit || $limit < 0) {
            $limit = 0;
        }
        $this->retrylimit = $limit;
    }

    /**
     * First argument should be the function to call in redis.
     * The following arguments should be the arguments for that function.
     *
     * Exception for hscan/sscan/zscan - calls to command for these should not include
     * the iterator argument, it needs to be managed via $this->iterators separately.
     */
    public function command() {
        $args = func_get_args();

        $function = array_shift($args);

        // If this is a scan call, we need to pass an iterator by reference.
        if ($function == 'hscan' || $function == 'sscan' || $function == 'zscan') {
            $args[2] = $args[1];
            $args[1] = &$this->iterators[$args[0]];
        }

        if ($this->retrylimit < 0) {
            $this->retrylimit = 0;
        }

        $success = false;
        $lastexception = null;
        $result = null;

        while ($this->retrylimit >= 0) {
            $this->retrylimit--;
            try {
                $result = call_user_func_array([$this->redis, $function], $args);
                $success = true;
                break;
            } catch (Exception $e) {
                $lastexception = $e;
                // Always retry once on CLUSTERDOWN after a short delay.
                if (preg_match('#CLUSTERDOWN#', $e->getMessage())) {
                    $this->retrylimit--;
                    usleep(rand(100000, 200000));
                    try {
                        $result = call_user_func_array([$this->redis, $function], $args);
                        $success = true;
                        break;
                    } catch (Exception $e) {
                        $lastexception = $e;
                    }
                }
            }
        }
        $this->retrylimit = 0;

        if (!$success) {
            throw $lastexception;
        }

        return $result;
    }

    /**
     * Run a command with no prefix and no serializer.
     *
     * @return result of self::command().
     */
    public function command_raw() {
        $args = func_get_args();
        $prefix = $this->redis->getOption(Redis::OPT_PREFIX);

        $this->redis->setOption(Redis::OPT_COMPRESSION, Redis::COMPRESSION_NONE);
        $this->redis->setOption(Redis::OPT_PREFIX, '');
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        // The phpredis library treats raw commands like readonly ones and distributes
        // them to slaves if configured to do so. So for the life of this command, turn off failover/distribution.
        $this->redis->setOption(RedisCluster::OPT_SLAVE_FAILOVER, RedisCluster::FAILOVER_NONE);

        $result = call_user_func_array([$this, 'command'], $args);

        // Return the redis client to the previous state.
        $this->redis->setOption(Redis::OPT_COMPRESSION, $this->config['compression']);
        $this->redis->setOption(Redis::OPT_PREFIX, $prefix);
        $this->redis->setOption(Redis::OPT_SERIALIZER, $this->config['serializer']);
        $this->redis->setOption(RedisCluster::OPT_SLAVE_FAILOVER, $this->config['failover']);
        return $result;
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
     * Finds all of the keys being used by this cache store instance.
     *
     * @return array of all keys in the hash as a numbered array.
     */
    public function find_all() {
        return $this->find_by_prefix('');
    }

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
     *
     * @return array List of keys that match this prefix.
     */
    public function find_by_prefix($prefix) {
        $hashes = [$this->hash];

        if ($this->sharded) {
            $hashes = [];
            for ($shard = 0; $shard < $this->config['shardsize']; $shard++) {
                $hashes[] = "{$this->hash}-{$shard}";
            }
        }

        $keys = [];
        foreach ($hashes as $hash) {
            $this->iterators[$hash] = null;
            while ($this->iterators[$hash] !== 0) {
                $keys = array_merge($keys, array_keys($this->command('hscan', $hash, "{$prefix}*")));
            }
        }
        return $keys;
    }

    /**
     * Get the value associated with a given key.
     *
     * @param string $key The key to get the value of.
     * @return mixed The value of the key, or false if there is no value associated with the key.
     */
    public function get($key) {
        $hash = $this->hash_shard($key);
        return $this->command('hGet', $hash, $key);
    }

    /**
     * Get the values associated with a list of keys.
     *
     * @param array $keys The keys to get the values of.
     * @return array An array of the values of the given keys.
     */
    public function get_many($keys) {
        $return = array_fill_keys($keys, false);

        $hashpairs = [];
        foreach ($keys as $key) {
            $hash = $this->hash_shard($key);
            $hashpairs[$hash][] = $key;
        }

        foreach ($hashpairs as $hash => $hkeys) {
            if ($result = $this->command('hMGet', $hash, $hkeys)) {
                $return = array_merge($return, $result);
            }
        }
        return $return;
    }

    /**
     * Set the value of a key.
     *
     * @param string $key The key to set the value of.
     * @param mixed $value The value.
     * @return bool True if the operation succeeded, false otherwise.
     */
    public function set($key, $value) {
        $hash = $this->hash_shard($key);
        return ($this->command('hSet', $hash, $key, $value) !== false);
    }

    /**
     * Get the particular hash-shard for a given key.
     *
     * @param string $key The key to set the value of.
     * @return string The key of the hash shard for the requested $key.
     */
    protected function hash_shard($key) {
        $hash = $this->hash;
        if ($this->sharded) {
            $shard = crc32($key) % $this->config['shardsize'];
            $hash = "{$this->hash}-{$shard}";
        }
        return $hash;
    }

    /**
     * Set the values of many keys.
     *
     * @param array $keyvaluearray An array of key/value pairs. Each item in the array is an associative array
     *      with two keys, 'key' and 'value'.
     * @return int The number of key/value pairs successfuly set.
     */
    public function set_many(array $keyvaluearray) {
        $hashpairs = [];
        foreach ($keyvaluearray as $pair) {
            $hash = $this->hash_shard($pair['key']);
            $hashpairs[$hash][$pair['key']] = $pair['value'];
        }
        $count = 0;
        foreach ($hashpairs as $hash => $pairs) {
            if ($this->command('hMSet', $hash, $pairs)) {
                $count += count($pairs);
            }
        }
        return $count;
    }

    /**
     * Delete the given key.
     *
     * @param string $key The key to delete.
     * @return bool True if the delete operation succeeds, false otherwise.
     */
    public function delete($key) {
        $hash = $this->hash_shard($key);
        return $this->command('hDel', $hash, $key) > 0;
    }

    /**
     * Delete many keys.
     *
     * @param array $keys The keys to delete.
     * @return int The number of keys successfully deleted.
     */
    public function delete_many(array $keys) {
        $hashpairs = [];
        foreach ($keys as $key) {
            $hash = $this->hash_shard($key);
            $hashpairs[$hash][] = $key;
        }

        $result = 0;
        foreach ($hashpairs as $hash => $hkeys) {
            array_unshift($hkeys, $hash);
            array_unshift($hkeys, 'hDel');
            $result += call_user_func_array([$this, 'command'], $hkeys);
        }
        return $result;
    }

    /**
     * Purges all keys from the store.
     *
     * @return bool
     */
    public function purge() {
        $hashes = [$this->hash];
        if ($this->sharded) {
            for ($shard = 0; $shard < $this->config['shardsize']; $shard++) {
                $hashes[] = "{$this->hash}-{$shard}";
            }
        }

        $result = true;
        foreach ($hashes as $hash) {
            $result = ($this->command('unlink', $hash) !== false) && $result;
        }
        return $result;
    }

    /**
     * Cleans up after an instance of the store.
     */
    public function instance_deleted() {
        $this->purge();
        $this->redis->close();
        unset($this->redis);
    }

    public function close() {
        $this->redis->close();
        unset($this->redis);
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
        $config = get_config('cachestore_rediscluster');
        if (empty($config->test_server)) {
            return false;
        }
        $cache = new cachestore_rediscluster('RedisCluster test', ['server' => $config->test_server]);
        $cache->initialise($definition);

        return $cache;
    }

    /**
     * Determines if the store has a given key.
     *
     * @see cache_is_key_aware
     * @param string $key The key to check for.
     * @return bool True if the key exists, false if it does not.
     */
    public function has($key) {
        $hash = $this->hash_shard($key);
        return !empty($this->command('hExists', $hash, $key));
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
        $timelimit = time() + $this->config['lockwait'];

        $params = ['nx'];
        if ($this->config['locktimeout'] > 0) {
            // Ensure Redis deletes the key after a bit in case something goes wrong.
            $params['ex'] = $this->config['locktimeout'];
        }
        do {
            if ($this->command('set', $key, $ownerid, $params)) {
                // If we haven't got it already, better register a shutdown function.
                if ($this->currentlocks === null) {
                    core_shutdown_manager::register_function([$this, 'shutdown_release_locks']);
                    $this->currentlocks = [];
                }
                $this->currentlocks[$key] = $ownerid;
                return true;
            }
            // Wait 1 second then retry.
            sleep(1);
        } while (time() < $timelimit);
        return false;
    }

    /**
     * Releases any locks when the system shuts down, in case there is a crash or somebody forgets
     * to use 'try-finally'.
     *
     * Do not call this function manually (except from unit test).
     */
    public function shutdown_release_locks() {
        foreach ($this->currentlocks as $key => $ownerid) {
            debugging('Automatically releasing Redis cache lock: ' . $key . ' (' . $ownerid .
                    ') - did somebody forget to call release_lock()?', DEBUG_DEVELOPER);
            $this->release_lock($key, $ownerid);
        }
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
        $result = $this->command('get', $key);
        if ($result === (string)$ownerid) {
            return true;
        }
        if ($result === false) {
            return null;
        }
        return false;
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
            unset($this->currentlocks[$key]);
            return ($this->command('del', $key) !== false);
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
        return [
            'compression' => $data->compression,
            'failover' => $data->failover,
            'persist' => !empty($data->persist),
            'prefix' => $data->prefix,
            'readtimeout' => $data->readtimeout,
            'serializer' => $data->serializer,
            'server' => $data->server,
            'serversecondary' => $data->serversecondary,
            'timeout' => $data->timeout,
        ];
    }

    /**
     * Sets form data from a configuration array.
     *
     * @see cache_is_configurable
     * @param moodleform $editform
     * @param array $config
     */
    public static function config_set_edit_form_data(moodleform $editform, array $config) {
        $data = [
            'compression' => Redis::COMPRESSION_NONE,
            'failover' => RedisCluster::FAILOVER_NONE,
            'persist' => false,
            'prefix' => '',
            'readtimeout' => 3.0,
            'serializer' => Redis::SERIALIZER_IGBINARY,
            'server' => null,
            'serversecondary' => null,
            'timeout' => 3.0,
        ];

        // Override defaults.
        foreach (array_keys($data) as $key) {
            if (!empty($config[$key])) {
                $data[$key] = $config[$key];
            }
        }

        $editform->set_data($data);
    }

    public static function ready_to_be_used_for_testing() {
        return defined('CACHESTORE_REDISCLUSTER_TEST_SERVER');
    }

    /**
     * Get the name to use when unit testing.
     *
     * @return string
     */
    private static function get_testing_name() {
        return 'test_application';
    }

    /**
     * Generates the appropriate configuration required for unit testing.
     *
     * @return array Array of unit test configuration data to be used by initialise().
     */
    public static function unit_test_configuration() {
        global $DB;

        // If the configuration is not defined correctly, return only the configuration know about.
        $config = [
            // In unit testing, we don't want to have to wait for consistency to replicas.
            'failover' => RedisCluster::FAILOVER_ERROR,
            'persist' => true,
            'prefix' => $DB->get_prefix(),
        ];
        if (!defined('CACHESTORE_REDISCLUSTER_TEST_SERVER')) {
            return $config;
        }
        $config['server'] = CACHESTORE_REDISCLUSTER_TEST_SERVER;
        return $config;
    }

}
