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

class PartitionQueryRequest extends \Google\Model
{
  /**
   * The maximum number of partitions to return in this call, subject to
   * `partition_count`. For example, if `partition_count` = 10 and `page_size` =
   * 8, the first call to PartitionQuery will return up to 8 partitions and a
   * `next_page_token` if more results exist. A second call to PartitionQuery
   * will return up to 2 partitions, to complete the total of 10 specified in
   * `partition_count`.
   *
   * @var int
   */
  public $pageSize;
  /**
   * The `next_page_token` value returned from a previous call to PartitionQuery
   * that may be used to get an additional set of results. There are no ordering
   * guarantees between sets of results. Thus, using multiple sets of results
   * will require merging the different result sets. For example, two subsequent
   * calls using a page_token may return: * cursor B, cursor M, cursor Q *
   * cursor A, cursor U, cursor W To obtain a complete result set ordered with
   * respect to the results of the query supplied to PartitionQuery, the results
   * sets should be merged: cursor A, cursor B, cursor M, cursor Q, cursor U,
   * cursor W
   *
   * @var string
   */
  public $pageToken;
  /**
   * The desired maximum number of partition points. The partitions may be
   * returned across multiple pages of results. The number must be positive. The
   * actual number of partitions returned may be fewer. For example, this may be
   * set to one fewer than the number of parallel queries to be run, or in
   * running a data pipeline job, one fewer than the number of workers or
   * compute instances available.
   *
   * @var string
   */
  public $partitionCount;
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @var string
   */
  public $readTime;
  protected $structuredQueryType = StructuredQuery::class;
  protected $structuredQueryDataType = '';

  /**
   * The maximum number of partitions to return in this call, subject to
   * `partition_count`. For example, if `partition_count` = 10 and `page_size` =
   * 8, the first call to PartitionQuery will return up to 8 partitions and a
   * `next_page_token` if more results exist. A second call to PartitionQuery
   * will return up to 2 partitions, to complete the total of 10 specified in
   * `partition_count`.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * The `next_page_token` value returned from a previous call to PartitionQuery
   * that may be used to get an additional set of results. There are no ordering
   * guarantees between sets of results. Thus, using multiple sets of results
   * will require merging the different result sets. For example, two subsequent
   * calls using a page_token may return: * cursor B, cursor M, cursor Q *
   * cursor A, cursor U, cursor W To obtain a complete result set ordered with
   * respect to the results of the query supplied to PartitionQuery, the results
   * sets should be merged: cursor A, cursor B, cursor M, cursor Q, cursor U,
   * cursor W
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * The desired maximum number of partition points. The partitions may be
   * returned across multiple pages of results. The number must be positive. The
   * actual number of partitions returned may be fewer. For example, this may be
   * set to one fewer than the number of parallel queries to be run, or in
   * running a data pipeline job, one fewer than the number of workers or
   * compute instances available.
   *
   * @param string $partitionCount
   */
  public function setPartitionCount($partitionCount)
  {
    $this->partitionCount = $partitionCount;
  }
  /**
   * @return string
   */
  public function getPartitionCount()
  {
    return $this->partitionCount;
  }
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
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
   * A structured query. Query must specify collection with all descendants and
   * be ordered by name ascending. Other filters, order bys, limits, offsets,
   * and start/end cursors are not supported.
   *
   * @param StructuredQuery $structuredQuery
   */
  public function setStructuredQuery(StructuredQuery $structuredQuery)
  {
    $this->structuredQuery = $structuredQuery;
  }
  /**
   * @return StructuredQuery
   */
  public function getStructuredQuery()
  {
    return $this->structuredQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionQueryRequest::class, 'Google_Service_Firestore_PartitionQueryRequest');
