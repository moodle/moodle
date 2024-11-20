<?php

namespace Basho\Riak\Command;

use Basho\Riak\Command;
use Basho\Riak\Location;

/**
 * Base class for Commands performing operations on Kv Objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class KVObject extends Command
{
    /**
     * @var KVObject\Response|null
     */
    protected $response = NULL;

    /**
     * @var \Basho\Riak\DataObject|null
     */
    protected $object = NULL;

    /**
     * @var Location|null
     */
    protected $location = NULL;

    protected $decodeAsAssociative = false;

    public function getObject()
    {
        return $this->object;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getEncodedData()
    {
        $data = $this->getData();

        if (in_array($this->object->getContentType(), ['application/json', 'text/json'])) {
            return json_encode($data);
        } elseif (in_array($this->object->getContentEncoding(), ['base64'])) {
            return base64_encode($data);
        } elseif (in_array($this->object->getContentEncoding(), ['binary','none'])) {
            return $data;
        }

        return rawurlencode($data);
    }

    public function getDecodedData($data, $contentType)
    {
        return static::decodeData($data, $contentType, $this->decodeAsAssociative);
    }

    public static function decodeData($data, $contentType = '', $decodeAsAssociative = false)
    {
        if (in_array($contentType, ['application/json', 'text/json'])) {
            return json_decode($data, $decodeAsAssociative);
        }

        return rawurldecode($data);
    }

    public function getData()
    {
        return $this->object->getData();
    }

    /**
     * @return Command\KVObject\Response
     */
    public function execute()
    {
        return parent::execute();
    }
}
