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

class ServiceLbPolicyIsolationConfig extends \Google\Model
{
  /**
   * No isolation is configured for the backend service. Traffic can overflow
   * based on the load balancing algorithm.
   */
  public const ISOLATION_GRANULARITY_ISOLATION_GRANULARITY_UNSPECIFIED = 'ISOLATION_GRANULARITY_UNSPECIFIED';
  /**
   * Traffic for this service will be isolated at the cloud region level.
   */
  public const ISOLATION_GRANULARITY_REGION = 'REGION';
  /**
   * No isolation mode is configured for the backend service.
   */
  public const ISOLATION_MODE_ISOLATION_MODE_UNSPECIFIED = 'ISOLATION_MODE_UNSPECIFIED';
  /**
   * Traffic will be sent to the nearest region.
   */
  public const ISOLATION_MODE_NEAREST = 'NEAREST';
  /**
   * Traffic will fail if no serving backends are available in the same region
   * as the load balancer.
   */
  public const ISOLATION_MODE_STRICT = 'STRICT';
  /**
   * Optional. The isolation granularity of the load balancer.
   *
   * @var string
   */
  public $isolationGranularity;
  /**
   * Optional. The isolation mode of the load balancer.
   *
   * @var string
   */
  public $isolationMode;

  /**
   * Optional. The isolation granularity of the load balancer.
   *
   * Accepted values: ISOLATION_GRANULARITY_UNSPECIFIED, REGION
   *
   * @param self::ISOLATION_GRANULARITY_* $isolationGranularity
   */
  public function setIsolationGranularity($isolationGranularity)
  {
    $this->isolationGranularity = $isolationGranularity;
  }
  /**
   * @return self::ISOLATION_GRANULARITY_*
   */
  public function getIsolationGranularity()
  {
    return $this->isolationGranularity;
  }
  /**
   * Optional. The isolation mode of the load balancer.
   *
   * Accepted values: ISOLATION_MODE_UNSPECIFIED, NEAREST, STRICT
   *
   * @param self::ISOLATION_MODE_* $isolationMode
   */
  public function setIsolationMode($isolationMode)
  {
    $this->isolationMode = $isolationMode;
  }
  /**
   * @return self::ISOLATION_MODE_*
   */
  public function getIsolationMode()
  {
    return $this->isolationMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceLbPolicyIsolationConfig::class, 'Google_Service_NetworkServices_ServiceLbPolicyIsolationConfig');
