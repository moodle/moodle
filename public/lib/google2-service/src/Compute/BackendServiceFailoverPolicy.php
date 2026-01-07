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

class BackendServiceFailoverPolicy extends \Google\Model
{
  /**
   * This can be set to true only if the protocol isTCP.
   *
   * The default is false.
   *
   * @var bool
   */
  public $disableConnectionDrainOnFailover;
  /**
   * If set to true, connections to the load balancer are dropped when all
   * primary and all backup backend VMs are unhealthy.If set to false,
   * connections are distributed among all primary VMs when all primary and all
   * backup backend VMs are  unhealthy. For load balancers that have
   * configurable failover: [Internal passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/internal/failover-
   * overview) and [external passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/network/networklb-
   * failover-overview). The default is false.
   *
   * @var bool
   */
  public $dropTrafficIfUnhealthy;
  /**
   * The value of the field must be in the range[0, 1]. If the value is 0, the
   * load balancer performs a failover when the number of healthy primary VMs
   * equals zero. For all other values, the load balancer performs a failover
   * when the total number of healthy primary VMs is less than this ratio. For
   * load balancers that have configurable failover: [Internal TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/internal/failover-
   * overview) and [external TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * failover-overview).
   *
   * @var float
   */
  public $failoverRatio;

  /**
   * This can be set to true only if the protocol isTCP.
   *
   * The default is false.
   *
   * @param bool $disableConnectionDrainOnFailover
   */
  public function setDisableConnectionDrainOnFailover($disableConnectionDrainOnFailover)
  {
    $this->disableConnectionDrainOnFailover = $disableConnectionDrainOnFailover;
  }
  /**
   * @return bool
   */
  public function getDisableConnectionDrainOnFailover()
  {
    return $this->disableConnectionDrainOnFailover;
  }
  /**
   * If set to true, connections to the load balancer are dropped when all
   * primary and all backup backend VMs are unhealthy.If set to false,
   * connections are distributed among all primary VMs when all primary and all
   * backup backend VMs are  unhealthy. For load balancers that have
   * configurable failover: [Internal passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/internal/failover-
   * overview) and [external passthrough Network Load
   * Balancers](https://cloud.google.com/load-balancing/docs/network/networklb-
   * failover-overview). The default is false.
   *
   * @param bool $dropTrafficIfUnhealthy
   */
  public function setDropTrafficIfUnhealthy($dropTrafficIfUnhealthy)
  {
    $this->dropTrafficIfUnhealthy = $dropTrafficIfUnhealthy;
  }
  /**
   * @return bool
   */
  public function getDropTrafficIfUnhealthy()
  {
    return $this->dropTrafficIfUnhealthy;
  }
  /**
   * The value of the field must be in the range[0, 1]. If the value is 0, the
   * load balancer performs a failover when the number of healthy primary VMs
   * equals zero. For all other values, the load balancer performs a failover
   * when the total number of healthy primary VMs is less than this ratio. For
   * load balancers that have configurable failover: [Internal TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/internal/failover-
   * overview) and [external TCP/UDP Load
   * Balancing](https://cloud.google.com/load-balancing/docs/network/networklb-
   * failover-overview).
   *
   * @param float $failoverRatio
   */
  public function setFailoverRatio($failoverRatio)
  {
    $this->failoverRatio = $failoverRatio;
  }
  /**
   * @return float
   */
  public function getFailoverRatio()
  {
    return $this->failoverRatio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceFailoverPolicy::class, 'Google_Service_Compute_BackendServiceFailoverPolicy');
