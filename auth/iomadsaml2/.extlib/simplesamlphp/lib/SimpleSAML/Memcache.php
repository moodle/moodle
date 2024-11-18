<?php

declare(strict_types=1);

namespace SimpleSAML;

use SimpleSAML\Utils;

/**
 * This file implements functions to read and write to a group of memcache
 * servers.
 *
 * The goals of this storage class is to provide failover, redudancy and load
 * balancing. This is accomplished by storing the data object to several
 * groups of memcache servers. Each data object is replicated to every group
 * of memcache servers, but it is only stored to one server in each group.
 *
 * For this code to work correctly, all web servers accessing the data must
 * have the same clock (as measured by the time()-function). Different clock
 * values will lead to incorrect behaviour.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class Memcache
{
    /**
     * Cache of the memcache servers we are using.
     *
     * @var \Memcache[]|\Memcached[]|null
     */
    private static $serverGroups = null;


    /**
     * The flavor of memcache PHP extension we are using.
     *
     * @var string
     */
    private static $extension = '';


    /**
     * Find data stored with a given key.
     *
     * @param string $key The key of the data.
     *
     * @return mixed The data stored with the given key, or null if no data matching the key was found.
     */
    public static function get($key)
    {
        Logger::debug("loading key $key from memcache");

        $latestInfo = null;
        $latestTime = 0.0;
        $latestData = null;
        $mustUpdate = false;
        $allDown = true;

        // search all the servers for the given id
        foreach (self::getMemcacheServers() as $server) {
            $serializedInfo = $server->get($key);
            if ($serializedInfo === false) {
                // either the server is down, or we don't have the value stored on that server
                $mustUpdate = true;
                $up = $server->getVersion();
                if ($up !== false) {
                    $allDown = false;
                }
                continue;
            }
            $allDown = false;

            // unserialize the object
            /** @var string $serializedInfo */
            $info = unserialize($serializedInfo);

            /*
             * Make sure that this is an array with two keys:
             * - 'timestamp': The time the data was saved.
             * - 'data': The data.
             */
            if (!is_array($info)) {
                Logger::warning(
                    'Retrieved invalid data from a memcache server. Data was not an array.'
                );
                continue;
            }
            if (!array_key_exists('timestamp', $info)) {
                Logger::warning(
                    'Retrieved invalid data from a memcache server. Missing timestamp.'
                );
                continue;
            }
            if (!array_key_exists('data', $info)) {
                Logger::warning(
                    'Retrieved invalid data from a memcache server. Missing data.'
                );
                continue;
            }

            if ($latestInfo === null) {
                // first info found
                $latestInfo = $serializedInfo;
                $latestTime = $info['timestamp'];
                $latestData = $info['data'];
                continue;
            }

            if ($info['timestamp'] === $latestTime && $serializedInfo === $latestInfo) {
                // this data matches the data from the other server(s)
                continue;
            }

            // different data from different servers. We need to update at least one of them to maintain sync
            $mustUpdate = true;

            // update if data in $info is newer than $latestData
            if ($latestTime < $info['timestamp']) {
                $latestInfo = $serializedInfo;
                $latestTime = $info['timestamp'];
                $latestData = $info['data'];
            }
        }

        if ($latestData === null) {
            if ($allDown) {
                // all servers are down, panic!
                $e = new Error\Error('MEMCACHEDOWN', null, 503);
                throw new Error\Exception('All memcache servers are down', 503, $e);
            }
            // we didn't find any data matching the key
            Logger::debug("key $key not found in memcache");
            return null;
        }

        if ($mustUpdate) {
            // we found data matching the key, but some of the servers need updating
            Logger::debug("Memcache servers out of sync for $key, forcing sync");
            self::set($key, $latestData);
        }

        return $latestData;
    }


    /**
     * Save a key-value pair to the memcache servers.
     *
     * @param string       $key The key of the data.
     * @param mixed        $value The value of the data.
     * @param integer|null $expire The expiration timestamp of the data.
     * @return void
     */
    public static function set($key, $value, $expire = null)
    {
        Logger::debug("saving key $key to memcache");
        $savedInfo = [
            'timestamp' => microtime(true),
            'data'      => $value
        ];

        if ($expire === null) {
            $expire = self::getExpireTime();
        }

        $savedInfoSerialized = serialize($savedInfo);

        // store this object to all groups of memcache servers
        foreach (self::getMemcacheServers() as $server) {
            if (self::$extension === \Memcached::class) {
                $server->set($key, $savedInfoSerialized, $expire);
            } else {
                $server->set($key, $savedInfoSerialized, 0, $expire);
            }
        }
    }


    /**
     * Delete a key-value pair from the memcache servers.
     *
     * @param string $key The key we should delete.
     * @return void
     */
    public static function delete($key)
    {
        assert(is_string($key));
        Logger::debug("deleting key $key from memcache");

        // store this object to all groups of memcache servers
        foreach (self::getMemcacheServers() as $server) {
            $server->delete($key);
        }
    }


    /**
     * This function adds a server from the 'memcache_store.servers'
     * configuration option to a Memcache object.
     *
     * The server parameter is an array with the following keys:
     *  - hostname
     *    Hostname or ip address to the memcache server.
     *  - port (optional)
     *    port number the memcache server is running on. This
     *    defaults to memcache.default_port if no value is given.
     *    The default value of memcache.default_port is 11211.
     *  - weight (optional)
     *    The weight of this server in the load balancing
     *    cluster.
     *  - timeout (optional)
     *    The timeout for contacting this server, in seconds.
     *    The default value is 3 seconds.
     *
     * @param \Memcache|\Memcached $memcache The Memcache object we should add this server to.
     * @param array    $server An associative array with the configuration options for the server to add.
     * @return void
     *
     * @throws \Exception If any configuration option for the server is invalid.
     */
    private static function addMemcacheServer($memcache, array $server): void
    {
        // the hostname option is required
        if (!array_key_exists('hostname', $server)) {
            throw new \Exception(
                "hostname setting missing from server in the 'memcache_store.servers' configuration option."
            );
        }

        $hostname = $server['hostname'];

        // the hostname must be a valid string
        if (!is_string($hostname)) {
            throw new \Exception(
                "Invalid hostname for server in the 'memcache_store.servers' configuration option. The hostname is" .
                ' supposed to be a string.'
            );
        }

        // check if the user has specified a port number
        if (strpos($hostname, 'unix:///') === 0) {
            // force port to be 0 for sockets
            $port = 0;
        } elseif (array_key_exists('port', $server)) {
            // get the port number from the array, and validate it
            $port = (int) $server['port'];
            if (($port <= 0) || ($port > 65535)) {
                throw new \Exception(
                    "Invalid port for server in the 'memcache_store.servers' configuration option. The port number" .
                    ' is supposed to be an integer between 0 and 65535.'
                );
            }
        } else {
            // use the default port number from the ini-file
            $port = (int) ini_get('memcache.default_port');
            if ($port <= 0 || $port > 65535) {
                // invalid port number from the ini-file. fall back to the default
                $port = 11211;
            }
        }

        // check if the user has specified a weight for this server
        if (array_key_exists('weight', $server)) {
            // get the weight and validate it
            $weight = (int) $server['weight'];
            if ($weight <= 0) {
                throw new \Exception(
                    "Invalid weight for server in the 'memcache_store.servers' configuration option. The weight is" .
                    ' supposed to be a positive integer.'
                );
            }
        } else {
            // use a default weight of 1
            $weight = 1;
        }

        // check if the user has specified a timeout for this server
        if (array_key_exists('timeout', $server)) {
            // get the timeout and validate it
            $timeout = (int) $server['timeout'];
            if ($timeout <= 0) {
                throw new \Exception(
                    "Invalid timeout for server in the 'memcache_store.servers' configuration option. The timeout is" .
                    ' supposed to be a positive integer.'
                );
            }
        } else {
            // use a default timeout of 3 seconds
            $timeout = 3;
        }

        // add this server to the Memcache object
        if ($memcache instanceof \Memcached) {
            $memcache->addServer($hostname, $port);
        } else {
            $memcache->addServer($hostname, $port, true, $weight, $timeout, $timeout, true);
        }
    }


    /**
     * This function takes in a list of servers belonging to a group and
     * creates a Memcache object from the servers in the group.
     *
     * @param array $group Array of servers which should be created as a group.
     * @param string $index The index for this group. Specify if persistent connections are desired.
     *
     * @return \Memcache|\Memcached A Memcache object of the servers in the group
     *
     * @throws \Exception If the servers configuration is invalid.
     */
    private static function loadMemcacheServerGroup(array $group, string $index = null)
    {
        if (class_exists(\Memcached::class)) {
            if (is_string($index)) {
                $memcache = new \Memcached($index);
            } else {
                $memcache = new \Memcached();
            }
            if (array_key_exists('options', $group)) {
                $memcache->setOptions($group['options']);
                unset($group['options']);
            }
            self::$extension = \Memcached::class;

            $servers = $memcache->getServerList();
            if (count($servers) === count($group) && !$memcache->isPristine()) {
                return $memcache;
            }
            $memcache->resetServerList();
        } elseif (class_exists(\Memcache::class)) {
            $memcache = new \Memcache();
            self::$extension = \Memcache::class;
        } else {
            throw new \Exception(
                'Missing Memcached implementation. You must install either the Memcache or Memcached extension.'
            );
        }

        if (self::$extension === \Memcache::class) {
            Logger::warning(
                "The use of PHP-extension memcache is deprecated. Please migrate to the memcached extension."
            );
        }

        // iterate over all the servers in the group and add them to the Memcache object
        foreach ($group as $index => $server) {
            // make sure that we don't have an index. An index would be a sign of invalid configuration
            if (!is_int($index)) {
                throw new \Exception(
                    "Invalid index on element in the 'memcache_store.servers' configuration option. Perhaps you" .
                    ' have forgotten to add an array(...) around one of the server groups? The invalid index was: ' .
                    $index
                );
            }

            // make sure that the server object is an array. Each server is an array with name-value pairs
            if (!is_array($server)) {
                throw new \Exception(
                    'Invalid value for the server with index ' . $index .
                    '. Remeber that the \'memcache_store.servers\' configuration option' .
                    ' contains an array of arrays of arrays.'
                );
            }

            self::addMemcacheServer($memcache, $server);
        }

        /** @var \Memcache|\Memcached */
        return $memcache;
    }


    /**
     * This function gets a list of all configured memcache servers. This list is initialized based
     * on the content of 'memcache_store.servers' in the configuration.
     *
     * @return \Memcache[]|\Memcached[] Array with Memcache objects.
     *
     * @throws \Exception If the servers configuration is invalid.
     */
    private static function getMemcacheServers(): array
    {
        // check if we have loaded the servers already
        if (self::$serverGroups != null) {
            return self::$serverGroups;
        }

        // initialize the servers-array
        self::$serverGroups = [];

        // load the configuration
        $config = Configuration::getInstance();


        $groups = $config->getArray('memcache_store.servers');

        // iterate over all the groups in the 'memcache_store.servers' configuration option
        foreach ($groups as $index => $group) {
            /*
             * Make sure that the group is an array. Each group is an array of servers. Each server is
             * an array of name => value pairs for that server.
             */
            if (!is_array($group)) {
                throw new \Exception(
                    "Invalid value for the server with index " . $index .
                    ". Remeber that the 'memcache_store.servers' configuration option" .
                    ' contains an array of arrays of arrays.'
                );
            }

            // make sure that the group doesn't have an index. An index would be a sign of invalid configuration
            if (is_int($index)) {
                $index = null;
            }

            // parse and add this group to the server group list
            self::$serverGroups[] = self::loadMemcacheServerGroup($group, $index);
        }

        return self::$serverGroups;
    }


    /**
     * This is a helper-function which returns the expire value of data
     * we should store to the memcache servers.
     *
     * The value is set depending on the configuration. If no value is
     * set in the configuration, then we will use a default value of 0.
     * 0 means that the item will never expire.
     *
     * @return integer The value which should be passed in the set(...) calls to the memcache objects.
     *
     * @throws \Exception If the option 'memcache_store.expires' has a negative value.
     */
    private static function getExpireTime(): int
    {
        // get the configuration instance
        $config = Configuration::getInstance();
        assert($config instanceof Configuration);

        // get the expire-value from the configuration
        $expire = $config->getInteger('memcache_store.expires', 0);

        // it must be a positive integer
        if ($expire < 0) {
            throw new \Exception(
                "The value of 'memcache_store.expires' in the configuration can't be a negative integer."
            );
        }

        /* If the configuration option is 0, then we should return 0. This allows the user to specify that the data
         * shouldn't expire.
         */
        if ($expire == 0) {
            return 0;
        }

        /* The expire option is given as the number of seconds into the future an item should expire. We convert this
         * to an actual timestamp.
         */
        return (time() + $expire);
    }


    /**
     * This function retrieves statistics about all memcache server groups.
     *
     * @return array Array with the names of each stat and an array with the value for each server group.
     *
     * @throws \Exception If memcache server status couldn't be retrieved.
     */
    public static function getStats()
    {
        $ret = [];

        foreach (self::getMemcacheServers() as $sg) {
            $stats = method_exists($sg, 'getExtendedStats') ? $sg->getExtendedStats() : $sg->getStats();
            foreach ($stats as $server => $data) {
                if ($data === false) {
                    throw new \Exception('Failed to get memcache server status.');
                }
            }

            $stats = Utils\Arrays::transpose($stats);

            $ret = array_merge_recursive($ret, $stats);
        }

        return $ret;
    }


    /**
     * Retrieve statistics directly in the form returned by getExtendedStats, for
     * all server groups.
     *
     * @return array An array with the extended stats output for each server group.
     */
    public static function getRawStats()
    {
        $ret = [];

        foreach (self::getMemcacheServers() as $sg) {
            $stats = method_exists($sg, 'getExtendedStats') ? $sg->getExtendedStats() : $sg->getStats();
            $ret[] = $stats;
        }

        return $ret;
    }
}
