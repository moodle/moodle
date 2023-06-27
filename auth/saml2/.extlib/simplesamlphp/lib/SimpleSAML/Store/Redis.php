<?php

declare(strict_types=1);

namespace SimpleSAML\Store;

use Predis\Client;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Store;

/**
 * A data store using Redis to keep the data.
 *
 * @package SimpleSAMLphp
 */
class Redis extends Store
{
    /** @var \Predis\Client */
    public $redis;

    /**
     * Initialize the Redis data store.
     * @param \Predis\Client|null $redis
     */
    public function __construct($redis = null)
    {
        assert($redis === null || is_subclass_of($redis, Client::class));

        if (!class_exists(Client::class)) {
            throw new Error\CriticalConfigurationError('predis/predis is not available.');
        }

        if ($redis === null) {
            $config = Configuration::getInstance();

            $host = $config->getString('store.redis.host', 'localhost');
            $port = $config->getInteger('store.redis.port', 6379);
            $prefix = $config->getString('store.redis.prefix', 'SimpleSAMLphp');
            $password = $config->getString('store.redis.password', '');
            $database = $config->getInteger('store.redis.database', 0);

            $redis = new Client(
                [
                    'scheme' => 'tcp',
                    'host' => $host,
                    'port' => $port,
                    'database' => $database,
                ] + (!empty($password) ? ['password' => $password] : []),
                [
                    'prefix' => $prefix,
                ]
            );
        }

        $this->redis = $redis;
    }


    /**
     * Deconstruct the Redis data store.
     */
    public function __destruct()
    {
        if (method_exists($this->redis, 'disconnect')) {
            $this->redis->disconnect();
        }
    }


    /**
     * Retrieve a value from the data store.
     *
     * @param string $type The type of the data.
     * @param string $key The key to retrieve.
     *
     * @return mixed|null The value associated with that key, or null if there's no such key.
     */
    public function get($type, $key)
    {
        assert(is_string($type));
        assert(is_string($key));

        $result = $this->redis->get("{$type}.{$key}");

        if ($result === false || $result === null) {
            return null;
        }

        return unserialize($result);
    }


    /**
     * Save a value in the data store.
     *
     * @param string $type The type of the data.
     * @param string $key The key to insert.
     * @param mixed $value The value itself.
     * @param int|null $expire The expiration time (unix timestamp), or null if it never expires.
     * @return void
     */
    public function set($type, $key, $value, $expire = null)
    {
        assert(is_string($type));
        assert(is_string($key));
        assert($expire === null || (is_int($expire) && $expire > 2592000));

        $serialized = serialize($value);

        if ($expire === null) {
            $this->redis->set("{$type}.{$key}", $serialized);
        } else {
            // setex expire time is in seconds (not unix timestamp)
            $this->redis->setex("{$type}.{$key}", $expire - time(), $serialized);
        }
    }


    /**
     * Delete an entry from the data store.
     *
     * @param string $type The type of the data
     * @param string $key The key to delete.
     * @return void
     */
    public function delete($type, $key)
    {
        assert(is_string($type));
        assert(is_string($key));

        $this->redis->del("{$type}.{$key}");
    }
}
