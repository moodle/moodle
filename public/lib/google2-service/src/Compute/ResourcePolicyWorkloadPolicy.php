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

namespace Google\Service\Compute;

class ResourcePolicyWorkloadPolicy extends \Google\Model
{
  /**
   * VMs must be provisioned in the same block.
   */
  public const MAX_TOPOLOGY_DISTANCE_BLOCK = 'BLOCK';
  /**
   * VMs must be provisioned in the same cluster.
   */
  public const MAX_TOPOLOGY_DISTANCE_CLUSTER = 'CLUSTER';
  /**
   * VMs must be provisioned in the same subblock.
   */
  public const MAX_TOPOLOGY_DISTANCE_SUBBLOCK = 'SUBBLOCK';
  /**
   * MIG spreads out the instances as much as possible for high availability.
   */
  public const TYPE_HIGH_AVAILABILITY = 'HIGH_AVAILABILITY';
  /**
   * MIG provisions instances as close to each other as possible for high
   * throughput.
   */
  public const TYPE_HIGH_THROUGHPUT = 'HIGH_THROUGHPUT';
  /**
   * Specifies the topology required to create a partition for VMs that have
   * interconnected GPUs.
   *
   * @var string
   */
  public $acceleratorTopology;
  /**
   * Specifies the maximum distance between instances.
   *
   * @var string
   */
  public $maxTopologyDistance;
  /**
   * Specifies the intent of the instance placement in the MIG.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies the topology required to create a partition for VMs that have
   * interconnected GPUs.
   *
   * @param string $acceleratorTopology
   */
  public function setAcceleratorTopology($acceleratorTopology)
  {
    $this->acceleratorTopology = $acceleratorTopology;
  }
  /**
   * @return string
   */
  public function getAcceleratorTopology()
  {
    return $this->acceleratorTopology;
  }
  /**
   * Specifies the maximum distance between instances.
   *
   * Accepted values: BLOCK, CLUSTER, SUBBLOCK
   *
   * @param self::MAX_TOPOLOGY_DISTANCE_* $maxTopologyDistance
   */
  public function setMaxTopologyDistance($maxTopologyDistance)
  {
    $this->maxTopologyDistance = $maxTopologyDistance;
  }
  /**
   * @return self::MAX_TOPOLOGY_DISTANCE_*
   */
  public function getMaxTopologyDistance()
  {
    return $this->maxTopologyDistance;
  }
  /**
   * Specifies the intent of the instance placement in the MIG.
   *
   * Accepted values: HIGH_AVAILABILITY, HIGH_THROUGHPUT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyWorkloadPolicy::class, 'Google_Service_Compute_ResourcePolicyWorkloadPolicy');
