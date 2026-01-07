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

namespace Google\Service\CloudRedis;

class UpdateInfo extends \Google\Model
{
  /**
   * Node type unspecified
   */
  public const TARGET_NODE_TYPE_NODE_TYPE_UNSPECIFIED = 'NODE_TYPE_UNSPECIFIED';
  /**
   * Redis shared core nano node_type.
   */
  public const TARGET_NODE_TYPE_REDIS_SHARED_CORE_NANO = 'REDIS_SHARED_CORE_NANO';
  /**
   * Redis highmem medium node_type.
   */
  public const TARGET_NODE_TYPE_REDIS_HIGHMEM_MEDIUM = 'REDIS_HIGHMEM_MEDIUM';
  /**
   * Redis highmem xlarge node_type.
   */
  public const TARGET_NODE_TYPE_REDIS_HIGHMEM_XLARGE = 'REDIS_HIGHMEM_XLARGE';
  /**
   * Redis standard small node_type.
   */
  public const TARGET_NODE_TYPE_REDIS_STANDARD_SMALL = 'REDIS_STANDARD_SMALL';
  /**
   * Target node type for redis cluster.
   *
   * @var string
   */
  public $targetNodeType;
  /**
   * Target number of replica nodes per shard.
   *
   * @var int
   */
  public $targetReplicaCount;
  /**
   * Target number of shards for redis cluster
   *
   * @var int
   */
  public $targetShardCount;

  /**
   * Target node type for redis cluster.
   *
   * Accepted values: NODE_TYPE_UNSPECIFIED, REDIS_SHARED_CORE_NANO,
   * REDIS_HIGHMEM_MEDIUM, REDIS_HIGHMEM_XLARGE, REDIS_STANDARD_SMALL
   *
   * @param self::TARGET_NODE_TYPE_* $targetNodeType
   */
  public function setTargetNodeType($targetNodeType)
  {
    $this->targetNodeType = $targetNodeType;
  }
  /**
   * @return self::TARGET_NODE_TYPE_*
   */
  public function getTargetNodeType()
  {
    return $this->targetNodeType;
  }
  /**
   * Target number of replica nodes per shard.
   *
   * @param int $targetReplicaCount
   */
  public function setTargetReplicaCount($targetReplicaCount)
  {
    $this->targetReplicaCount = $targetReplicaCount;
  }
  /**
   * @return int
   */
  public function getTargetReplicaCount()
  {
    return $this->targetReplicaCount;
  }
  /**
   * Target number of shards for redis cluster
   *
   * @param int $targetShardCount
   */
  public function setTargetShardCount($targetShardCount)
  {
    $this->targetShardCount = $targetShardCount;
  }
  /**
   * @return int
   */
  public function getTargetShardCount()
  {
    return $this->targetShardCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateInfo::class, 'Google_Service_CloudRedis_UpdateInfo');
