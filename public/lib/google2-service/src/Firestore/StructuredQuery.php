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

namespace Google\Service\Firestore;

class StructuredQuery extends \Google\Collection
{
  protected $collection_key = 'orderBy';
  protected $endAtType = Cursor::class;
  protected $endAtDataType = '';
  protected $findNearestType = FindNearest::class;
  protected $findNearestDataType = '';
  protected $fromType = CollectionSelector::class;
  protected $fromDataType = 'array';
  /**
   * The maximum number of results to return. Applies after all other
   * constraints. Requires: * The value must be greater than or equal to zero if
   * specified.
   *
   * @var int
   */
  public $limit;
  /**
   * The number of documents to skip before returning the first result. This
   * applies after the constraints specified by the `WHERE`, `START AT`, & `END
   * AT` but before the `LIMIT` clause. Requires: * The value must be greater
   * than or equal to zero if specified.
   *
   * @var int
   */
  public $offset;
  protected $orderByType = Order::class;
  protected $orderByDataType = 'array';
  protected $selectType = Projection::class;
  protected $selectDataType = '';
  protected $startAtType = Cursor::class;
  protected $startAtDataType = '';
  protected $whereType = Filter::class;
  protected $whereDataType = '';

  /**
   * A potential prefix of a position in the result set to end the query at.
   * This is similar to `START_AT` but with it controlling the end position
   * rather than the start position. Requires: * The number of values cannot be
   * greater than the number of fields specified in the `ORDER BY` clause.
   *
   * @param Cursor $endAt
   */
  public function setEndAt(Cursor $endAt)
  {
    $this->endAt = $endAt;
  }
  /**
   * @return Cursor
   */
  public function getEndAt()
  {
    return $this->endAt;
  }
  /**
   * Optional. A potential nearest neighbors search. Applies after all other
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
   * The collections to query.
   *
   * @param CollectionSelector[] $from
   */
  public function setFrom($from)
  {
    $this->from = $from;
  }
  /**
   * @return CollectionSelector[]
   */
  public function getFrom()
  {
    return $this->from;
  }
  /**
   * The maximum number of results to return. Applies after all other
   * constraints. Requires: * The value must be greater than or equal to zero if
   * specified.
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
   * The number of documents to skip before returning the first result. This
   * applies after the constraints specified by the `WHERE`, `START AT`, & `END
   * AT` but before the `LIMIT` clause. Requires: * The value must be greater
   * than or equal to zero if specified.
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
   * The order to apply to the query results. Firestore allows callers to
   * provide a full ordering, a partial ordering, or no ordering at all. In all
   * cases, Firestore guarantees a stable ordering through the following rules:
   * * The `order_by` is required to reference all fields used with an
   * inequality filter. * All fields that are required to be in the `order_by`
   * but are not already present are appended in lexicographical ordering of the
   * field name. * If an order on `__name__` is not specified, it is appended by
   * default. Fields are appended with the same sort direction as the last order
   * specified, or 'ASCENDING' if no order was specified. For example: * `ORDER
   * BY a` becomes `ORDER BY a ASC, __name__ ASC` * `ORDER BY a DESC` becomes
   * `ORDER BY a DESC, __name__ DESC` * `WHERE a > 1` becomes `WHERE a > 1 ORDER
   * BY a ASC, __name__ ASC` * `WHERE __name__ > ... AND a > 1` becomes `WHERE
   * __name__ > ... AND a > 1 ORDER BY a ASC, __name__ ASC`
   *
   * @param Order[] $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return Order[]
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * Optional sub-set of the fields to return. This acts as a DocumentMask over
   * the documents returned from a query. When not set, assumes that the caller
   * wants all fields returned.
   *
   * @param Projection $select
   */
  public function setSelect(Projection $select)
  {
    $this->select = $select;
  }
  /**
   * @return Projection
   */
  public function getSelect()
  {
    return $this->select;
  }
  /**
   * A potential prefix of a position in the result set to start the query at.
   * The ordering of the result set is based on the `ORDER BY` clause of the
   * original query. ``` SELECT * FROM k WHERE a = 1 AND b > 2 ORDER BY b ASC,
   * __name__ ASC; ``` This query's results are ordered by `(b ASC, __name__
   * ASC)`. Cursors can reference either the full ordering or a prefix of the
   * location, though it cannot reference more fields than what are in the
   * provided `ORDER BY`. Continuing off the example above, attaching the
   * following start cursors will have varying impact: - `START BEFORE (2,
   * /k/123)`: start the query right before `a = 1 AND b > 2 AND __name__ >
   * /k/123`. - `START AFTER (10)`: start the query right after `a = 1 AND b >
   * 10`. Unlike `OFFSET` which requires scanning over the first N results to
   * skip, a start cursor allows the query to begin at a logical position. This
   * position is not required to match an actual result, it will scan forward
   * from this position to find the next document. Requires: * The number of
   * values cannot be greater than the number of fields specified in the `ORDER
   * BY` clause.
   *
   * @param Cursor $startAt
   */
  public function setStartAt(Cursor $startAt)
  {
    $this->startAt = $startAt;
  }
  /**
   * @return Cursor
   */
  public function getStartAt()
  {
    return $this->startAt;
  }
  /**
   * The filter to apply.
   *
   * @param Filter $where
   */
  public function setWhere(Filter $where)
  {
    $this->where = $where;
  }
  /**
   * @return Filter
   */
  public function getWhere()
  {
    return $this->where;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StructuredQuery::class, 'Google_Service_Firestore_StructuredQuery');
