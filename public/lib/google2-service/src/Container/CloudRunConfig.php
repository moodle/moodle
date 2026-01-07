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

namespace Google\Service\Container;

class CloudRunConfig extends \Google\Model
{
  /**
   * Load balancer type for Cloud Run is unspecified.
   */
  public const LOAD_BALANCER_TYPE_LOAD_BALANCER_TYPE_UNSPECIFIED = 'LOAD_BALANCER_TYPE_UNSPECIFIED';
  /**
   * Install external load balancer for Cloud Run.
   */
  public const LOAD_BALANCER_TYPE_LOAD_BALANCER_TYPE_EXTERNAL = 'LOAD_BALANCER_TYPE_EXTERNAL';
  /**
   * Install internal load balancer for Cloud Run.
   */
  public const LOAD_BALANCER_TYPE_LOAD_BALANCER_TYPE_INTERNAL = 'LOAD_BALANCER_TYPE_INTERNAL';
  /**
   * Whether Cloud Run addon is enabled for this cluster.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Which load balancer type is installed for Cloud Run.
   *
   * @var string
   */
  public $loadBalancerType;

  /**
   * Whether Cloud Run addon is enabled for this cluster.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Which load balancer type is installed for Cloud Run.
   *
   * Accepted values: LOAD_BALANCER_TYPE_UNSPECIFIED,
   * LOAD_BALANCER_TYPE_EXTERNAL, LOAD_BALANCER_TYPE_INTERNAL
   *
   * @param self::LOAD_BALANCER_TYPE_* $loadBalancerType
   */
  public function setLoadBalancerType($loadBalancerType)
  {
    $this->loadBalancerType = $loadBalancerType;
  }
  /**
   * @return self::LOAD_BALANCER_TYPE_*
   */
  public function getLoadBalancerType()
  {
    return $this->loadBalancerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRunConfig::class, 'Google_Service_Container_CloudRunConfig');
