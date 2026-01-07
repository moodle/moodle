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

namespace Google\Service\NetworkServices;

class ServiceLbPolicy extends \Google\Model
{
  /**
   * The type of the loadbalancing algorithm is unspecified.
   */
  public const LOAD_BALANCING_ALGORITHM_LOAD_BALANCING_ALGORITHM_UNSPECIFIED = 'LOAD_BALANCING_ALGORITHM_UNSPECIFIED';
  /**
   * Balance traffic across all backends across the world proportionally based
   * on capacity.
   */
  public const LOAD_BALANCING_ALGORITHM_SPRAY_TO_WORLD = 'SPRAY_TO_WORLD';
  /**
   * Direct traffic to the nearest region with endpoints and capacity before
   * spilling over to other regions and spread the traffic from each client to
   * all the MIGs/NEGs in a region.
   */
  public const LOAD_BALANCING_ALGORITHM_SPRAY_TO_REGION = 'SPRAY_TO_REGION';
  /**
   * Direct traffic to the nearest region with endpoints and capacity before
   * spilling over to other regions. All MIGs/NEGs within a region are evenly
   * loaded but each client might not spread the traffic to all the MIGs/NEGs in
   * the region.
   */
  public const LOAD_BALANCING_ALGORITHM_WATERFALL_BY_REGION = 'WATERFALL_BY_REGION';
  /**
   * Attempt to keep traffic in a single zone closest to the client, before
   * spilling over to other zones.
   */
  public const LOAD_BALANCING_ALGORITHM_WATERFALL_BY_ZONE = 'WATERFALL_BY_ZONE';
  protected $autoCapacityDrainType = ServiceLbPolicyAutoCapacityDrain::class;
  protected $autoCapacityDrainDataType = '';
  /**
   * Output only. The timestamp when this resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @var string
   */
  public $description;
  protected $failoverConfigType = ServiceLbPolicyFailoverConfig::class;
  protected $failoverConfigDataType = '';
  protected $isolationConfigType = ServiceLbPolicyIsolationConfig::class;
  protected $isolationConfigDataType = '';
  /**
   * Optional. Set of label tags associated with the ServiceLbPolicy resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The type of load balancing algorithm to be used. The default
   * behavior is WATERFALL_BY_REGION.
   *
   * @var string
   */
  public $loadBalancingAlgorithm;
  /**
   * Identifier. Name of the ServiceLbPolicy resource. It matches pattern `proje
   * cts/{project}/locations/{location}/serviceLbPolicies/{service_lb_policy_nam
   * e}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when this resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Configuration to automatically move traffic away for unhealthy
   * IG/NEG for the associated Backend Service.
   *
   * @param ServiceLbPolicyAutoCapacityDrain $autoCapacityDrain
   */
  public function setAutoCapacityDrain(ServiceLbPolicyAutoCapacityDrain $autoCapacityDrain)
  {
    $this->autoCapacityDrain = $autoCapacityDrain;
  }
  /**
   * @return ServiceLbPolicyAutoCapacityDrain
   */
  public function getAutoCapacityDrain()
  {
    return $this->autoCapacityDrain;
  }
  /**
   * Output only. The timestamp when this resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A free-text description of the resource. Max length 1024
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Configuration related to health based failover.
   *
   * @param ServiceLbPolicyFailoverConfig $failoverConfig
   */
  public function setFailoverConfig(ServiceLbPolicyFailoverConfig $failoverConfig)
  {
    $this->failoverConfig = $failoverConfig;
  }
  /**
   * @return ServiceLbPolicyFailoverConfig
   */
  public function getFailoverConfig()
  {
    return $this->failoverConfig;
  }
  /**
   * Optional. Configuration to provide isolation support for the associated
   * Backend Service.
   *
   * @param ServiceLbPolicyIsolationConfig $isolationConfig
   */
  public function setIsolationConfig(ServiceLbPolicyIsolationConfig $isolationConfig)
  {
    $this->isolationConfig = $isolationConfig;
  }
  /**
   * @return ServiceLbPolicyIsolationConfig
   */
  public function getIsolationConfig()
  {
    return $this->isolationConfig;
  }
  /**
   * Optional. Set of label tags associated with the ServiceLbPolicy resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The type of load balancing algorithm to be used. The default
   * behavior is WATERFALL_BY_REGION.
   *
   * Accepted values: LOAD_BALANCING_ALGORITHM_UNSPECIFIED, SPRAY_TO_WORLD,
   * SPRAY_TO_REGION, WATERFALL_BY_REGION, WATERFALL_BY_ZONE
   *
   * @param self::LOAD_BALANCING_ALGORITHM_* $loadBalancingAlgorithm
   */
  public function setLoadBalancingAlgorithm($loadBalancingAlgorithm)
  {
    $this->loadBalancingAlgorithm = $loadBalancingAlgorithm;
  }
  /**
   * @return self::LOAD_BALANCING_ALGORITHM_*
   */
  public function getLoadBalancingAlgorithm()
  {
    return $this->loadBalancingAlgorithm;
  }
  /**
   * Identifier. Name of the ServiceLbPolicy resource. It matches pattern `proje
   * cts/{project}/locations/{location}/serviceLbPolicies/{service_lb_policy_nam
   * e}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The timestamp when this resource was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceLbPolicy::class, 'Google_Service_NetworkServices_ServiceLbPolicy');
