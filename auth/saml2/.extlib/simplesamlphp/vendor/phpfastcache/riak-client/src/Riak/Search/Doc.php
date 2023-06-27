<?php

namespace Basho\Riak\Search;

use Basho\Riak\Bucket;
use Basho\Riak\Location;

/**
 * Data structure for document objects returned from Solr
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Doc
{
    protected $data = null;

    protected $_yz_id = '';
    protected $_yz_rk = '';
    protected $_yz_rt = '';
    protected $_yz_rb = '';

    public function __construct(\stdClass $data)
    {
        if (isset($data->_yz_id)) {
            $this->_yz_id = $data->_yz_id;
            unset($data->_yz_id);
        }

        if (isset($data->_yz_rk)) {
            $this->_yz_rk = $data->_yz_rk;
            unset($data->_yz_rk);
        }

        if (isset($data->_yz_rt)) {
            $this->_yz_rt = $data->_yz_rt;
            unset($data->_yz_rt);
        }

        if (isset($data->_yz_rb)) {
            $this->_yz_rb = $data->_yz_rb;
            unset($data->_yz_rb);
        }

        $this->data = $data;
    }

    /**
     * Returns object location
     *
     * @return Location
     */
    public function getLocation()
    {
        return new Location($this->_yz_rk, new Bucket($this->_yz_rb, $this->_yz_rt));
    }

    /**
     * Returns a single value from Solr result document
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data->{$name};
    }

    /**
     * Returns all values as array from Solr result document
     *
     * @return array
     */
    public function getData()
    {
        return (array)$this->data;
    }
}
