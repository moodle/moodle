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

class PartitionQueryResponse extends \Google\Collection
{
  protected $collection_key = 'partitions';
  /**
   * A page token that may be used to request an additional set of results, up
   * to the number specified by `partition_count` in the PartitionQuery request.
   * If blank, there are no more results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $partitionsType = Cursor::class;
  protected $partitionsDataType = 'array';

  /**
   * A page token that may be used to request an additional set of results, up
   * to the number specified by `partition_count` in the PartitionQuery request.
   * If blank, there are no more results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Partition results. Each partition is a split point that can be used by
   * RunQuery as a starting or end point for the query results. The RunQuery
   * requests must be made with the same query supplied to this PartitionQuery
   * request. The partition cursors will be ordered according to same ordering
   * as the results of the query supplied to PartitionQuery. For example, if a
   * PartitionQuery request returns partition cursors A and B, running the
   * following three queries will return the entire result set of the original
   * query: * query, end_at A * query, start_at A, end_at B * query, start_at B
   * An empty result may indicate that the query has too few results to be
   * partitioned, or that the query is not yet supported for partitioning.
   *
   * @param Cursor[] $partitions
   */
  public function setPartitions($partitions)
  {
    $this->partitions = $partitions;
  }
  /**
   * @return Cursor[]
   */
  public function getPartitions()
  {
    return $this->partitions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionQueryResponse::class, 'Google_Service_Firestore_PartitionQueryResponse');
