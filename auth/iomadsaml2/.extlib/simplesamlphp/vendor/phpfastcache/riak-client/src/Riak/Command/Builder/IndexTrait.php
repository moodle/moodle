<?php

namespace Basho\Riak\Command\Builder;

/**
 * Allows easy code sharing for Bucket getters / setters within the Command Builders
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
trait IndexTrait
{
    /**
     * The name of the index to query.
     *
     * @var string|null
     */
    protected $indexName = NULL;

    /**
     * The index match value for scalar queries.
     *
     * @var string|integer|null
     */
    protected $match = NULL;

    /**
     * The index lower bound value for range queries.
     *
     * @var string|integer|null
     */
    protected $lowerBound = NULL;

    /**
     * The index upper bound value for range queries.
     *
     * @var string|integer|null
     */
    protected $upperBound = NULL;

    /**
     * The continuation string for this query.
     * Used to page results when combined with MaxResults.
     *
     * @var string|null
     */
    protected $continuation = NULL; //Binary

    /**
     * The maximum number of results returned by the query.
     *
     * @var integer|null
     */
    protected $maxResults; // Int

    /**
     * The option to return the index keys with the Riak object keys.
     *
     * @var boolean|null
     */
    protected $returnTerms; // Bool

    /**
     * The option to sort, or not sort, the results of a non-paginated secondary index query.
     * If MaxResults is set, this property is ignored.
     * By default results are sorted first by index value, then by key value.
     *
     * @var boolean|null
     */
    protected $paginationSort; // Bool

    /**
     * The option to filter result terms with a Regex.
     *
     * @var string|null
     */
    protected $termFilter; // string
    /**
     *
     * @var integer|null
     */
    protected $timeout; // timeout

    /**
     * Gets the index name
     *
     * @return string|null
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string|integer|null
     */
    public function getMatchValue()
    {
        return $this->match;
    }

    /**
     * @return string|integer|null
     */
    public function getLowerBound()
    {
        return $this->lowerBound;
    }

    /**
     * @return string|integer|null
     */
    public function getUpperBound()
    {
        return $this->upperBound;
    }

    /**
     * @return boolean
     */
    public function isMatchQuery()
    {
        return isset($this->match);
    }

    /**
     * @return boolean
     */
    public function isRangeQuery()
    {
        return isset($this->lowerBound) && isset($this->upperBound);
    }

    /**
     * @return null|string
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * @return int|null
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return bool|null
     */
    public function getReturnTerms()
    {
        return $this->returnTerms;
    }

    /**
     * @return bool|null
     */
    public function getPaginationSort()
    {
        return $this->paginationSort;
    }

    /**
     * @return null|string
     */
    public function getTermFilter()
    {
        return $this->termFilter;
    }

    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }


    /**
     * @param $param
     * @return bool
     */
    public function isParamSet($param)
    {
        return !empty($this->$param);
    }

    /**
     * Adds the index name information to the Command
     *
     * @param $indexName
     *
     * @return $this
     */
    public function withIndexName($indexName)
    {
        $this->indexName = $indexName;
        return $this;
    }


    /**
     * Adds the scalar index query information to the Command
     *
     * @param $value
     *
     * @return $this
     */
    public function withScalarValue($value)
    {
        $this->match = $value;
        $this->lowerBound = null;
        $this->upperBound = null;
        return $this;
    }


    /**
     * Adds the range index query information to the Command
     *
     * @param $lowerBound
     * @param $upperBound
     *
     * @return $this
     */
    public function withRangeValue($lowerBound, $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->match = null;

        return $this;
    }

    /**
     * @param null|string $continuation
     *
     * @return $this
     */
    public function withContinuation($continuation)
    {
        $this->continuation = $continuation;
        return $this;
    }

    /**
     * @param int|null $maxResults
     *
     * @return $this
     */
    public function withMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
        return $this;
    }

    /**
     * @param bool|null $returnTerms
     *
     * @return $this
     */
    public function withReturnTerms($returnTerms = true)
    {
        $this->returnTerms = $returnTerms;
        return $this;
    }

    /**
     * @param bool|null $paginationSort
     *
     * @return $this
     */
    public function withPaginationSort($paginationSort)
    {
        $this->paginationSort = $paginationSort;
        return $this;
    }

    /**
     * @param null|string $termFilter
     *
     * @return $this
     */
    public function withTermFilter($termFilter)
    {
        $this->termFilter = $termFilter;
        return $this;
    }

    /**
     * @param int|null $timeout
     *
     * @return $this
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
}
