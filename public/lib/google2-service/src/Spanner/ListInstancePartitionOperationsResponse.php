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

namespace Google\Service\Spanner;

class ListInstancePartitionOperationsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachableInstancePartitions';
  /**
   * `next_page_token` can be sent in a subsequent
   * ListInstancePartitionOperations call to fetch more of the matching
   * metadata.
   *
   * @var string
   */
  public $nextPageToken;
  protected $operationsType = Operation::class;
  protected $operationsDataType = 'array';
  /**
   * The list of unreachable instance partitions. It includes the names of
   * instance partitions whose operation metadata could not be retrieved within
   * instance_partition_deadline.
   *
   * @var string[]
   */
  public $unreachableInstancePartitions;

  /**
   * `next_page_token` can be sent in a subsequent
   * ListInstancePartitionOperations call to fetch more of the matching
   * metadata.
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
   * The list of matching instance partition long-running operations. Each
   * operation's name will be prefixed by the instance partition's name. The
   * operation's metadata field type `metadata.type_url` describes the type of
   * the metadata.
   *
   * @param Operation[] $operations
   */
  public function setOperations($operations)
  {
    $this->operations = $operations;
  }
  /**
   * @return Operation[]
   */
  public function getOperations()
  {
    return $this->operations;
  }
  /**
   * The list of unreachable instance partitions. It includes the names of
   * instance partitions whose operation metadata could not be retrieved within
   * instance_partition_deadline.
   *
   * @param string[] $unreachableInstancePartitions
   */
  public function setUnreachableInstancePartitions($unreachableInstancePartitions)
  {
    $this->unreachableInstancePartitions = $unreachableInstancePartitions;
  }
  /**
   * @return string[]
   */
  public function getUnreachableInstancePartitions()
  {
    return $this->unreachableInstancePartitions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListInstancePartitionOperationsResponse::class, 'Google_Service_Spanner_ListInstancePartitionOperationsResponse');
