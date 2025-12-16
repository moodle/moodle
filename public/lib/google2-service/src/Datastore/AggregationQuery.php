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

class AggregationQuery extends \Google\Collection
{
  protected $collection_key = 'aggregations';
  protected $aggregationsType = Aggregation::class;
  protected $aggregationsDataType = 'array';
  protected $nestedQueryType = Query::class;
  protected $nestedQueryDataType = '';

  /**
   * Optional. Series of aggregations to apply over the results of the
   * `nested_query`. Requires: * A minimum of one and maximum of five
   * aggregations per query.
   *
   * @param Aggregation[] $aggregations
   */
  public function setAggregations($aggregations)
  {
    $this->aggregations = $aggregations;
  }
  /**
   * @return Aggregation[]
   */
  public function getAggregations()
  {
    return $this->aggregations;
  }
  /**
   * Nested query for aggregation
   *
   * @param Query $nestedQuery
   */
  public function setNestedQuery(Query $nestedQuery)
  {
    $this->nestedQuery = $nestedQuery;
  }
  /**
   * @return Query
   */
  public function getNestedQuery()
  {
    return $this->nestedQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationQuery::class, 'Google_Service_Datastore_AggregationQuery');
