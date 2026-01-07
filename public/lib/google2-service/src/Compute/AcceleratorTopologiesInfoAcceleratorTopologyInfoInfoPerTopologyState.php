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

class AcceleratorTopologiesInfoAcceleratorTopologyInfoInfoPerTopologyState extends \Google\Model
{
  /**
   * The accelerator topology is available.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * The accelerator topology is degraded. The underlying capacity is not in a
   * healthy state and is not available.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * The accelerator topology is running. If there are both running and degraded
   * hosts within a topology, DEGRADED state will be returned.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The state of the topology is unspecified.
   */
  public const STATE_TOPOLOGY_STATE_UNSPECIFIED = 'TOPOLOGY_STATE_UNSPECIFIED';
  /**
   * This value has been deprecated and is no longer used.
   *
   * @deprecated
   */
  public const STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * The number of accelerator topologies in this state.
   *
   * @var int
   */
  public $count;
  /**
   * The state of the accelerator topology.
   *
   * @var string
   */
  public $state;

  /**
   * The number of accelerator topologies in this state.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The state of the accelerator topology.
   *
   * Accepted values: AVAILABLE, DEGRADED, RUNNING, TOPOLOGY_STATE_UNSPECIFIED,
   * UNHEALTHY
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcceleratorTopologiesInfoAcceleratorTopologyInfoInfoPerTopologyState::class, 'Google_Service_Compute_AcceleratorTopologiesInfoAcceleratorTopologyInfoInfoPerTopologyState');
