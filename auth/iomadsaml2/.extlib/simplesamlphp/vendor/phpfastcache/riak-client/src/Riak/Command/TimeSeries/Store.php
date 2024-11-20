<?php

namespace Basho\Riak\Command\TimeSeries;

use Basho\Riak;
use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to store data within a TS table
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Store extends Command implements CommandInterface
{
    protected $method = 'POST';

    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $table = NULL;

    /**
     * Stores the rows
     *
     * @var array $rows
     */
    protected $rows = [];

    public function getTable()
    {
        return $this->table;
    }

    public function getData()
    {
        return $this->rows;
    }

    public function getEncodedData()
    {
        $rows = [];
        foreach ($this->getData() as $row) {
            $cells = [];
            foreach ($row as $cell) {
                /** @var $cell Riak\TimeSeries\Cell */
                if ($cell->getType() == Riak\TimeSeries\Cell::BLOB_TYPE) {
                    $cells[$cell->getName()] = base64_encode($cell->getValue());
                } else {
                    $cells[$cell->getName()] = $cell->getValue();
                }
            }
            $rows[] = $cells;
        }
        return json_encode($rows);
    }

    public function __construct(Command\Builder\TimeSeries\StoreRows $builder)
    {
        parent::__construct($builder);

        $this->table = $builder->getTable();
        $this->rows = $builder->getRows();
    }
}