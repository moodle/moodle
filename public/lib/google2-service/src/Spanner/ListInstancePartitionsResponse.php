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

class ListInstancePartitionsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $instancePartitionsType = InstancePartition::class;
  protected $instancePartitionsDataType = 'array';
  /**
   * `next_page_token` can be sent in a subsequent ListInstancePartitions call
   * to fetch more of the matching instance partitions.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The list of unreachable instances or instance partitions. It includes the
   * names of instances or instance partitions whose metadata could not be
   * retrieved within instance_partition_deadline.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of requested instancePartitions.
   *
   * @param InstancePartition[] $instancePartitions
   */
  public function setInstancePartitions($instancePartitions)
  {
    $this->instancePartitions = $instancePartitions;
  }
  /**
   * @return InstancePartition[]
   */
  public function getInstancePartitions()
  {
    return $this->instancePartitions;
  }
  /**
   * `next_page_token` can be sent in a subsequent ListInstancePartitions call
   * to fetch more of the matching instance partitions.
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
   * The list of unreachable instances or instance partitions. It includes the
   * names of instances or instance partitions whose metadata could not be
   * retrieved within instance_partition_deadline.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListInstancePartitionsResponse::class, 'Google_Service_Spanner_ListInstancePartitionsResponse');
