<?php

namespace Basho\Riak\Command\Builder;

use Basho\Riak\Api\Http;
use Basho\Riak\DataObject;

/**
 * Allows easy code sharing for Object getters / setters within the Command Builders
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait ObjectTrait
{
    /**
     * @var \Basho\Riak\DataObject|null
     */
    protected $dataObject = NULL;

    /**
     * @return \Basho\Riak\DataObject|null
     */
    public function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * Mint a new Object instance with supplied params and attach it to the Command
     *
     * @param string $data
     * @param array $headers
     *
     * @return $this
     */
    public function buildObject($data = NULL, $headers = NULL)
    {
        $this->dataObject = new DataObject($data, $headers);

        return $this;
    }

    /**
     * Attach an already instantiated Object to the Command
     *
     * @param \Basho\Riak\DataObject $object
     *
     * @return $this
     */
    public function withObject(DataObject $object)
    {
        $this->dataObject = $object;

        return $this;
    }

    /**
     * Mint a new Object instance with a json encoded string
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function buildJsonObject($data)
    {
        $this->dataObject = new DataObject();
        $this->dataObject->setData($data);
        $this->dataObject->setContentType(Http::CONTENT_TYPE_JSON);

        return $this;
    }
}
