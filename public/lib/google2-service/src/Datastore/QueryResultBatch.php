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

class QueryResultBatch extends \Google\Collection
{
  /**
   * Unspecified. This value is never used.
   */
  public const ENTITY_RESULT_TYPE_RESULT_TYPE_UNSPECIFIED = 'RESULT_TYPE_UNSPECIFIED';
  /**
   * The key and properties.
   */
  public const ENTITY_RESULT_TYPE_FULL = 'FULL';
  /**
   * A projected subset of properties. The entity may have no key.
   */
  public const ENTITY_RESULT_TYPE_PROJECTION = 'PROJECTION';
  /**
   * Only the key.
   */
  public const ENTITY_RESULT_TYPE_KEY_ONLY = 'KEY_ONLY';
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
  protected $collection_key = 'entityResults';
  /**
   * A cursor that points to the position after the last result in the batch.
   *
   * @var string
   */
  public $endCursor;
  /**
   * The result type for every entity in `entity_results`.
   *
   * @var string
   */
  public $entityResultType;
  protected $entityResultsType = EntityResult::class;
  protected $entityResultsDataType = 'array';
  /**
   * The state of the query after the current batch.
   *
   * @var string
   */
  public $moreResults;
  /**
   * Read timestamp this batch was returned from. This applies to the range of
   * results from the query's `start_cursor` (or the beginning of the query if
   * no cursor was given) to this batch's `end_cursor` (not the query's
   * `end_cursor`). In a single transaction, subsequent query result batches for
   * the same query can have a greater timestamp. Each batch's read timestamp is
   * valid for all preceding batches. This value will not be set for eventually
   * consistent queries in Cloud Datastore.
   *
   * @var string
   */
  public $readTime;
  /**
   * A cursor that points to the position after the last skipped result. Will be
   * set when `skipped_results` != 0.
   *
   * @var string
   */
  public $skippedCursor;
  /**
   * The number of results skipped, typically because of an offset.
   *
   * @var int
   */
  public $skippedResults;
  /**
   * The version number of the snapshot this batch was returned from. This
   * applies to the range of results from the query's `start_cursor` (or the
   * beginning of the query if no cursor was given) to this batch's `end_cursor`
   * (not the query's `end_cursor`). In a single transaction, subsequent query
   * result batches for the same query can have a greater snapshot version
   * number. Each batch's snapshot version is valid for all preceding batches.
   * The value will be zero for eventually consistent queries.
   *
   * @var string
   */
  public $snapshotVersion;

  /**
   * A cursor that points to the position after the last result in the batch.
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
   * The result type for every entity in `entity_results`.
   *
   * Accepted values: RESULT_TYPE_UNSPECIFIED, FULL, PROJECTION, KEY_ONLY
   *
   * @param self::ENTITY_RESULT_TYPE_* $entityResultType
   */
  public function setEntityResultType($entityResultType)
  {
    $this->entityResultType = $entityResultType;
  }
  /**
   * @return self::ENTITY_RESULT_TYPE_*
   */
  public function getEntityResultType()
  {
    return $this->entityResultType;
  }
  /**
   * The results for this batch.
   *
   * @param EntityResult[] $entityResults
   */
  public function setEntityResults($entityResults)
  {
    $this->entityResults = $entityResults;
  }
  /**
   * @return EntityResult[]
   */
  public function getEntityResults()
  {
    return $this->entityResults;
  }
  /**
   * The state of the query after the current batch.
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
   * Read timestamp this batch was returned from. This applies to the range of
   * results from the query's `start_cursor` (or the beginning of the query if
   * no cursor was given) to this batch's `end_cursor` (not the query's
   * `end_cursor`). In a single transaction, subsequent query result batches for
   * the same query can have a greater timestamp. Each batch's read timestamp is
   * valid for all preceding batches. This value will not be set for eventually
   * consistent queries in Cloud Datastore.
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
  /**
   * A cursor that points to the position after the last skipped result. Will be
   * set when `skipped_results` != 0.
   *
   * @param string $skippedCursor
   */
  public function setSkippedCursor($skippedCursor)
  {
    $this->skippedCursor = $skippedCursor;
  }
  /**
   * @return string
   */
  public function getSkippedCursor()
  {
    return $this->skippedCursor;
  }
  /**
   * The number of results skipped, typically because of an offset.
   *
   * @param int $skippedResults
   */
  public function setSkippedResults($skippedResults)
  {
    $this->skippedResults = $skippedResults;
  }
  /**
   * @return int
   */
  public function getSkippedResults()
  {
    return $this->skippedResults;
  }
  /**
   * The version number of the snapshot this batch was returned from. This
   * applies to the range of results from the query's `start_cursor` (or the
   * beginning of the query if no cursor was given) to this batch's `end_cursor`
   * (not the query's `end_cursor`). In a single transaction, subsequent query
   * result batches for the same query can have a greater snapshot version
   * number. Each batch's snapshot version is valid for all preceding batches.
   * The value will be zero for eventually consistent queries.
   *
   * @param string $snapshotVersion
   */
  public function setSnapshotVersion($snapshotVersion)
  {
    $this->snapshotVersion = $snapshotVersion;
  }
  /**
   * @return string
   */
  public function getSnapshotVersion()
  {
    return $this->snapshotVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryResultBatch::class, 'Google_Service_Datastore_QueryResultBatch');
