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

class AggregationResultBatch extends \Google\Collection
{
  /**
   * Unspecified. This value is never used.
   */
  public const MORE_RESULTS_MORE_RESULTS_TYPE_UNSPECIFIED = 'MORE_RESULTS_TYPE_UNSPECIFIED';
  /**
   * There may be additional batches to fetch from this query.
   */
  public const MORE_RESULTS_NOT_FINISHED = 'NOT_FINISHED';
  /**
   * The query is finished, but there may be more results after the limit.
   */
  public const MORE_RESULTS_MORE_RESULTS_AFTER_LIMIT = 'MORE_RESULTS_AFTER_LIMIT';
  /**
   * The query is finished, but there may be more results after the end cursor.
   */
  public const MORE_RESULTS_MORE_RESULTS_AFTER_CURSOR = 'MORE_RESULTS_AFTER_CURSOR';
  /**
   * The query is finished, and there are no more results.
   */
  public const MORE_RESULTS_NO_MORE_RESULTS = 'NO_MORE_RESULTS';
  protected $collection_key = 'aggregationResults';
  protected $aggregationResultsType = AggregationResult::class;
  protected $aggregationResultsDataType = 'array';
  /**
   * The state of the query after the current batch. Only COUNT(*) aggregations
   * are supported in the initial launch. Therefore, expected result type is
   * limited to `NO_MORE_RESULTS`.
   *
   * @var string
   */
  public $moreResults;
  /**
   * Read timestamp this batch was returned from. In a single transaction,
   * subsequent query result batches for the same query can have a greater
   * timestamp. Each batch's read timestamp is valid for all preceding batches.
   *
   * @var string
   */
  public $readTime;

  /**
   * The aggregation results for this batch.
   *
   * @param AggregationResult[] $aggregationResults
   */
  public function setAggregationResults($aggregationResults)
  {
    $this->aggregationResults = $aggregationResults;
  }
  /**
   * @return AggregationResult[]
   */
  public function getAggregationResults()
  {
    return $this->aggregationResults;
  }
  /**
   * The state of the query after the current batch. Only COUNT(*) aggregations
   * are supported in the initial launch. Therefore, expected result type is
   * limited to `NO_MORE_RESULTS`.
   *
   * Accepted values: MORE_RESULTS_TYPE_UNSPECIFIED, NOT_FINISHED,
   * MORE_RESULTS_AFTER_LIMIT, MORE_RESULTS_AFTER_CURSOR, NO_MORE_RESULTS
   *
   * @param self::MORE_RESULTS_* $moreResults
   */
  public function setMoreResults($moreResults)
  {
    $this->moreResults = $moreResults;
  }
  /**
   * @return self::MORE_RESULTS_*
   */
  public function getMoreResults()
  {
    return $this->moreResults;
  }
  /**
   * Read timestamp this batch was returned from. In a single transaction,
   * subsequent query result batches for the same query can have a greater
   * timestamp. Each batch's read timestamp is valid for all preceding batches.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationResultBatch::class, 'Google_Service_Datastore_AggregationResultBatch');
