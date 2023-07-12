<?php

namespace Basho\Riak\Command\Builder\TimeSeries;

/**
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
trait TableTrait
{
    /**
     * Stores the table name
     *
     * @var string|null
     */
    protected $table = NULL;

    /**
     * Gets the table name
     *
     * @return string|null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Attach the provided table name to the Command
     *
     * @param string $table
     *
     * @return $this
     */
    public function inTable($table)
    {
        $this->table = $table;

        return $this;
    }
}
