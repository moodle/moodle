<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Datastore;

class Query extends \Google\Collection
{
  protected $collection_key = 'projection';
  protected $distinctOnType = PropertyReference::class;
  protected $distinctOnDataType = 'array';
  /**
   * An ending point for the query results. Query cursors are returned in query
   * result batches and [can only be used to limit the same query](https://cloud
   * .google.com/datastore/docs/concepts/queries#cursors_limits_and_offsets).
   *
   * @var string
   */
  public $endCursor;
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  protected $findNearestType = FindNearest::class;
  protected $findNearestDataType = '';
  protected $kindType = KindExpression::class;
  protected $kindDataType = 'array';
  /**
   * The maximum number of results to return. Applies after all other
   * constraints. Optional. Unspecified is interpreted as no limit. Must be >= 0
   * if specified.
   *
   * @var int
   */
  public $limit;
  /**
   * The number of results to skip. Applies before limit, but after all other
   * constraints. Optional. Must be >= 0 if specified.
   *
   * @var int
   */
  public $offset;
  protected $orderType = PropertyOrder::class;
  protected $orderDataType = 'array';
  protected $projectionType = Projection::class;
  protected $projectionDataType = 'array';
  /**
   * A starting point for the query results. Query cursors are returned in query
   * result batches and [can only be used to continue the same query](https://cl
   * oud.google.com/datastore/docs/concepts/queries#cursors_limits_and_offsets).
   *
   * @var string
   */
  public $startCursor;

  /**
   * The properties to make distinct. The query results will contain the first
   * result for each distinct combination of values for the given properties (if
   * empty, all results are returned). Requires: * If `order` is specified, the
   * set of distinct on properties must appear before the non-distinct on
   * properties in `order`.
   *
   * @param PropertyReference[] $distinctOn
   */
  public function setDistinctOn($distinctOn)
  {
    $this->distinctOn = $distinctOn;
  }
  /**
   * @return PropertyReference[]
   */
  public function getDistinctOn()
  {
    return $this->distinctOn;
  }
  /**
   * An ending point for the query results. Query cursors are returned in query
   * result batches and [can only be used to limit the same query](https://cloud
   * .google.com/datastore/docs/concepts/queries#cursors_limits_and_offsets).
   *
   * @param string $endCursor
   */
  public function setEndCursor($endCursor)
  {
    $this->endCursor = $endCursor;
  }
  /**
   * @return string
   */
  public function getEndCursor()
  {
    return $this->endCursor;
  }
  /**
   * The filter to apply.
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. A potential Nearest Neighbors Search. Applies after all other
   * filters and ordering. Finds the closest vector embeddings to the given
   * query vector.
   *
   * @param FindNearest $findNearest
   */
  public function setFindNearest(FindNearest $findNearest)
  {
    $this->findNearest = $findNearest;
  }
  /**
   * @return FindNearest
   */
  public function getFindNearest()
  {
    return $this->findNearest;
  }
  /**
   * The kinds to query (if empty, returns entities of all kinds). Currently at
   * most 1 kind may be specified.
   *
   * @param KindExpression[] $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return KindExpression[]
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The maximum number of results to return. Applies after all other
   * constraints. Optional. Unspecified is interpreted as no limit. Must be >= 0
   * if specified.
   *
   * @param int $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return int
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * The number of results to skip. Applies before limit, but after all other
   * constraints. Optional. Must be >= 0 if specified.
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The order to apply to the query results (if empty, order is unspecified).
   *
   * @param PropertyOrder[] $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return PropertyOrder[]
   */
  public function getOrder()
  {
    return $this->order;
  }
  /**
   * The projection to return. Defaults to returning all properties.
   *
   * @param Projection[] $projection
   */
  public function setProjection($projection)
  {
    $this->projection = $projection;
  }
  /**
   * @return Projection[]
   */
  public function getProjection()
  {
    return $this->projection;
  }
  /**
   * A starting point for the query results. Query cursors are returned in query
   * result batches and [can only be used to continue the same query](https://cl
   * oud.google.com/datastore/docs/concepts/queries#cursors_limits_and_offsets).
   *
   * @param string $startCursor
   */
  public function setStartCursor($startCursor)
  {
    $this->startCursor = $startCursor;
  }
  /**
   * @return string
   */
  public function getStartCursor()
  {
    return $this->startCursor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Query::class, 'Google_Service_Datastore_Query');
