<?php

namespace Basho\Riak;

use Basho\Riak;
use Basho\Riak\Api\Http\Translator\SecondaryIndex;

/**
 * Main class for data objects in Riak
 *
 * When working with base64 encoded or binary data over HTTP, you need to make use of the setContentEncoding() to
 * bypass rawurlencode when storing data and getRawData() to bypass
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class DataObject
{
    /**
     * Stored data or object
     *
     * @var mixed|null
     */
    protected $data = null;

    /**
     * Stores raw non-decoded response data
     *
     * @var mixed|null
     */
    protected $raw_data = null;

    protected $indexes = [];

    protected $vclock = '';

    protected $content_type = 'text/plain';

    protected $content_encoding = 'utf-8';

    protected $charset = 'utf-8';

    protected $metadata = [];

    /**
     * @param mixed|null $data
     * @param array|null $headers DEPRECATED
     */
    public function __construct($data = null, $headers = [])
    {
        $this->data = $data;

        if (empty($headers) || !is_array($headers)) {
            return;
        }

        $this->indexes = (new SecondaryIndex)->extractIndexesFromHeaders($headers);

        // to prevent breaking the interface, parse $headers and place important stuff in new home
        if (!empty($headers[Riak\Api\Http::CONTENT_TYPE_KEY])) {
            // if charset is defined within the Content-Type header
            if (strpos($headers[Riak\Api\Http::CONTENT_TYPE_KEY], 'charset')) {
                $parts = explode(';', trim($headers[Riak\Api\Http::CONTENT_TYPE_KEY]));
                $this->content_type = $parts[0];
                $this->charset = trim(strrpos($parts[1], '='));
            } else {
                $this->content_type = $headers[Riak\Api\Http::CONTENT_TYPE_KEY];
            }
        }

        if (!empty($headers[Riak\Api\Http::VCLOCK_KEY])) {
            $this->vclock = $headers[Riak\Api\Http::VCLOCK_KEY];
        }

        // pull out metadata headers
        foreach($headers as $key => $value) {
            if (strpos($key, Riak\Api\Http::METADATA_PREFIX) !== false) {
                $this->metadata[substr($key, strlen(Riak\Api\Http::METADATA_PREFIX))] = $value;
            }
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Used to identify the mime-type of the object data
     *
     * If set to `application/json` or `text/json` the object data will automatically be json_encoded upon transfer to
     * Riak.
     *
     * @param string $content_type
     * @return $this
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->content_encoding;
    }

    /**
     * Used to identify the encoding of the object data
     *
     * If set to `base64`, object data will be automatically encoded to base64 upon transfer to Riak.
     * If set to `binary` or `none`, object data will NOT be rawurlencoded upon transfer to Riak.
     *
     * @param string $content_encoding
     * @return $this
     */
    public function setContentEncoding($content_encoding)
    {
        $this->content_encoding = $content_encoding;
        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function getVclock()
    {
        return $this->vclock;
    }

    /**
     * @param string $vclock
     * @return $this
     */
    public function setVclock($vclock)
    {
        $this->vclock = $vclock;
        return $this;
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getIndex($indexName)
    {
        return isset($this->indexes[$indexName]) ? $this->indexes[$indexName] : null;
    }

    public function addValueToIndex($indexName, $value)
    {
        $this->validateIndexNameAndValue($indexName, $value);

        if (!isset($this->indexes[$indexName])) {
            $this->indexes[$indexName] = [];
        }

        $this->indexes[$indexName][] = $value;

        return $this;
    }

    private function validateIndexNameAndValue($indexName, $value)
    {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException("Invalid index type for '" . $indexName .
                "'index. Expecting '*_int' for an integer index, or '*_bin' for a string index.");
        }

        $isIntIndex = SecondaryIndex::isIntIndex($indexName);
        $isStringIndex = SecondaryIndex::isStringIndex($indexName);

        if (!$isIntIndex && !$isStringIndex) {
            throw new \InvalidArgumentException("Invalid index type for '" . $indexName .
                "'index. Expecting '*_int' for an integer index, or '*_bin' for a string index.");
        }

        if ($isIntIndex && !is_int($value)) {
            throw new \InvalidArgumentException("Invalid type for '" . $indexName .
                "'index. Expecting 'integer', value was '" . gettype($value) . "''");
        }

        if ($isStringIndex && !is_string($value)) {
            throw new \InvalidArgumentException("Invalid type for '" . $indexName .
                "'index. Expecting 'string', value was '" . gettype($value) . "''");
        }
    }

    public function removeValueFromIndex($indexName, $value)
    {
        if (!isset($this->indexes[$indexName])) {
            return $this;
        }

        $valuePos = array_search($value, $this->indexes[$indexName]);

        if ($valuePos !== false) {
            array_splice($this->indexes[$indexName], $valuePos, 1);
        }

        if (count($this->indexes[$indexName]) == 0) {
            unset($this->indexes[$indexName]);
        }

        return $this;
    }

    public function setMetaDataValue($key, $value = '')
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    public function getMetaDataValue($key)
    {
        return $this->metadata[$key];
    }

    public function removeMetaDataValue($key)
    {
        unset($this->metadata[$key]);
        return $this;
    }

    public function getMetaData()
    {
        return $this->metadata;
    }

    /**
     * Getter for raw non-decoded response data [HTTP ONLY]
     *
     * @return mixed|null
     */
    public function getRawData()
    {
        return $this->raw_data;
    }

    /**
     *
     * @param mixed|null $raw_data
     * @return $this
     */
    public function setRawData($raw_data)
    {
        $this->raw_data = $raw_data;

        return $this;
    }
}
