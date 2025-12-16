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

class NetworkEndpointGroupsListEndpointsRequest extends \Google\Model
{
  /**
   * Show the health status for each network endpoint. Impacts latency of the
   * call.
   */
  public const HEALTH_STATUS_SHOW = 'SHOW';
  /**
   * Health status for network endpoints will not be provided.
   */
  public const HEALTH_STATUS_SKIP = 'SKIP';
  /**
   * Optional query parameter for showing the health status of each network
   * endpoint. Valid options are SKIP or SHOW. If you don't specify this
   * parameter, the health status of network endpoints will not be provided.
   *
   * @var string
   */
  public $healthStatus;

  /**
   * Optional query parameter for showing the health status of each network
   * endpoint. Valid options are SKIP or SHOW. If you don't specify this
   * parameter, the health status of network endpoints will not be provided.
   *
   * Accepted values: SHOW, SKIP
   *
   * @param self::HEALTH_STATUS_* $healthStatus
   */
  public function setHealthStatus($healthStatus)
  {
    $this->healthStatus = $healthStatus;
  }
  /**
   * @return self::HEALTH_STATUS_*
   */
  public function getHealthStatus()
  {
    return $this->healthStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroupsListEndpointsRequest::class, 'Google_Service_Compute_NetworkEndpointGroupsListEndpointsRequest');
