<?php

namespace Basho\Riak\Command\Indexes;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Riak 2i query information.
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class Query extends Command implements CommandInterface
{
    /**
     * @var string
     */
    protected $indexName = NULL;

    protected $match = NULL;
    protected $lowerBound = NULL;
    protected $upperBound = NULL;

    protected $isMatchQuery = false;
    protected $isRangeQuery = false;

    /**
     * @var Command\Indexes\Response|null
     */
    protected $response = NULL;

    public function __construct(Command\Builder\QueryIndex $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->indexName = $builder->getIndexName();

        if($builder->isRangeQuery()) {
            $this->lowerBound = $builder->getLowerBound();
            $this->upperBound = $builder->getUpperBound();
            $this->isRangeQuery = true;
        }
        else {
            $this->match = $builder->getMatchValue();
            $this->isMatchQuery = true;
        }

        $continuation = $builder->getContinuation();
        if(!empty($continuation)) {
            $this->parameters['continuation'] = $continuation;
        }

        $maxResults = $builder->getMaxResults();
        if(!empty($maxResults)) {
            $this->parameters['max_results'] = $maxResults;
        }

        $returnTerms = $builder->getReturnTerms();
        if(!empty($returnTerms)) {
            $this->parameters['return_terms'] = ($returnTerms) ? 'true' : 'false';
        }

        $paginationSort = $builder->getPaginationSort();
        if(!empty($paginationSort)) {
            $this->parameters['pagination_sort'] = ($paginationSort) ? 'true' : 'false';
        }

        $termRegex = $builder->getTermFilter();
        if(!empty($termRegex)) {
            $this->parameters['term_regex'] = $termRegex;
        }

        $timeout = $builder->getTimeout();
        if(!empty($timeout)) {
            $this->parameters['timeout'] = $timeout;
        }
    }

    public function getIndexName() {
        return $this->indexName;
    }

    public function getMatchValue() {
        return $this->match;
    }

    public function getLowerBound() {
        return $this->lowerBound;
    }

    public function getUpperBound() {
        return $this->upperBound;
    }

    public function isMatchQuery()
    {
        return $this->isMatchQuery;
    }

    public function isRangeQuery()
    {
        return $this->isRangeQuery;
    }

    public function hasParameters()
    {
        return true;
    }

    /**
     * @return Command\Indexes\Response
     */
    public function execute()
    {
        return parent::execute();
    }

    public function getData()
    {
        return '';
    }

    public function getEncodedData()
    {
        return '';
    }
}
