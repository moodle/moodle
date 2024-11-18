<?php

namespace Basho\Riak\Command\Builder\Search;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Builds the command to fetch a collection of objects from Riak using Yokozuna search
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchObjects extends Command\Builder implements Command\BuilderInterface
{
    protected $default_field = '';

    protected $default_operation = '';

    protected $index_name = '';

    public function __construct(Riak $riak)
    {
        parent::__construct($riak);

        $this->parameters['wt'] = 'json';
        $this->parameters['rows'] = 10;
        $this->parameters['start'] = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @return Command\Search\Fetch;
     */
    public function build()
    {
        $this->validate();

        return new Command\Search\Fetch($this);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $this->required('IndexName');
        $this->required('Query');
        $this->required('MaxRows');
        $this->required('StartRow');
    }

    public function withIndexName($name)
    {
        $this->index_name = $name;

        return $this;
    }

    public function getIndexName()
    {
        return $this->index_name;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->parameters['q'];
    }

    /**
     * @return int
     */
    public function getMaxRows()
    {
        return $this->parameters['rows'];
    }

    /**
     * @return int
     */
    public function getStartRow()
    {
        return $this->parameters['start'];
    }

    /**
     * @return string
     */
    public function getFilterQuery()
    {
        return $this->parameters['fq'];
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->parameters['sort'];
    }

    /**
     * @return string
     */
    public function getDefaultField()
    {
        return $this->default_field;
    }

    /**
     * @return string
     */
    public function getDefaultOperation()
    {
        return $this->default_operation;
    }

    /**
     * @return string
     */
    public function getReturnFields()
    {
        return $this->parameters['fl'];
    }

    public function withQuery($query)
    {
        $this->parameters['q'] = $query;

        return $this;
    }

    public function withMaxRows($rows)
    {
        $this->parameters['rows'] = $rows;

        return $this;
    }

    public function withStartRow($row_num)
    {
        $this->parameters['start'] = $row_num;

        return $this;
    }

    public function withSortField($field_name)
    {
        $this->parameters['sort'] = $field_name;

        return $this;
    }

    public function withFilterQuery($filter_query)
    {
        $this->parameters['fq'] = $filter_query;

        return $this;
    }

    public function withDefaultField($default_field)
    {
        $this->parameters['df'] = $default_field;

        return $this;
    }

    public function withDefaultOperation($default_operation)
    {
        $this->parameters['op'] = $default_operation;

        return $this;
    }

    public function withReturnFields($return_fields)
    {
        $this->parameters['fl'] = $return_fields;

        return $this;
    }
}
