<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak\Command;

/**
 * Response object for TS Fetch, Store, Delete
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Response extends Command\Response
{
    protected $rows = [];

    public function __construct($success = true, $code = 0, $message = '', $rows = [])
    {
        parent::__construct($success, $code, $message);

        $this->rows = $rows;
    }

    /**
     * @return \Basho\Riak\TimeSeries\Cell[]|null
     */
    public function getRow()
    {
        return !empty($this->rows[0]) ? $this->rows[0] : null;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }
}
