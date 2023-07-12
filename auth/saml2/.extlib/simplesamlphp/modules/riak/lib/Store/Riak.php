<?php

/*
 * Copyright (c) 2012 The University of Queensland
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 * Written by David Gwynne <dlg@uq.edu.au> as part of the IT
 * Infrastructure Group in the Faculty of Engineering, Architecture
 * and Information Technology.
 *
 * @package SimpleSAMLphp
 * Rewriten by Tim van Dijen for SimpleSAMLphp
 */

namespace SimpleSAML\Module\riak\Store;

use Basho\Riak\Bucket;
use Basho\Riak\Command\Builder\DeleteObject;
use Basho\Riak\Command\Builder\FetchObject;
use Basho\Riak\Command\Builder\StoreObject;
use Basho\Riak\Command\Builder\QueryIndex;
use Basho\Riak\Location;
use Basho\Riak\Node;
use Basho\Riak\DataObject;
use Basho\Riak as RiakClient;
use SimpleSAML\Configuration;
use SimpleSAML\Error\CriticalConfigurationError;
use Webmozart\Assert\Assert;

class Riak extends \SimpleSAML\Store
{
    /** @var \Basho\Riak */
    protected $client;

    /** @var string */
    protected $bucket_name;

    /** @var \Basho\Riak\Bucket */
    protected $bucket;

    /** @var \Basho\Riak\Location|null */
    protected $location = null;


    public function __construct()
    {
        $config = Configuration::getConfig('module_riak.php');

        $host = $config->getString('host', 'localhost');
        $port = $config->getInteger('port', 8098);

        $node = (new Node\Builder())
            ->atHost($host)
            ->onPort($port)
            ->build();

        $this->client = new RiakClient([$node]);
        $this->bucket_name = $config->getString('bucket', 'simpleSAMLphp');

        $this->bucket = new Bucket($this->bucket_name);
    }


    /**
     * Retrieve a value from the datastore.
     *
     * @param string $type The datatype.
     * @param string $key The key.
     * @return mixed|null The value.
     */
    public function get($type, $key)
    {
        assert(is_string($type));
        assert(is_string($key));

        $key = 'key_' . $key;
        $this->location = new Location($key, $this->bucket);

        $response = (new FetchObject($this->client))
            ->atLocation($this->location)
            ->build()
            ->execute();

        if ($response->getObject() === null) {
            return null;
        }

        $data = $response->getObject()->getData();
        $data_decoded = unserialize(json_decode($data, true));

        return $data_decoded[$key];
    }


    /**
     * Save a value to the datastore.
     *
     * @param string $type The datatype.
     * @param string $key The key.
     * @param mixed $value The value.
     * @param int|null $expire The expiration time (unix timestamp), or NULL if it never expires.
     * @return void
     */
    public function set($type, $key, $value, $expire = null)
    {
        assert(is_string($type));
        assert(is_string($key));
        assert(is_null($expire) || is_int($expire));
        assert($expire > 2592000);

        $key = 'key_' . $key;
        $this->location = new Location($key, $this->bucket);

        $data = serialize([$key => $value]);
        if (is_null($expire)) {
            $object = new DataObject(json_encode($data), ['Content-type' => 'application/json']);
        } else {
            $object = (new DataObject(json_encode($data), ['Content-type' => 'application/json']))
              ->addValueToIndex('expire_int', time() + $expire);
        }

        $storecmd = (new StoreObject($this->client))
            ->withObject($object)
            ->atLocation($this->location)
            ->build();
        $storecmd->execute();
    }


    /**
     * @return array|null
     */
    public function getExpired()
    {
        $results = (new QueryIndex($this->client))
          ->inBucket($this->bucket)
          ->withIndexName('expire_int')
          ->withRangeValue(0, time())
          ->build()
          ->execute()
          ->getResults();

        return $results;
    }


    /**
     * Delete a value from the datastore.
     *
     * @param string $type The datatype.
     * @param string $key The key.
     * @return void
     */
    public function delete($type, $key)
    {
        assert(is_string($type));
        assert(is_string($key));

        $key = 'key_' . $key;
        $this->location = new Location($key, $this->bucket);

        (new DeleteObject($this->client))->atLocation($this->location)->build()->execute();
    }
}
