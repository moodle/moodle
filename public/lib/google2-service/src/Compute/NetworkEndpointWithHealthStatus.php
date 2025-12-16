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

class NetworkEndpointWithHealthStatus extends \Google\Collection
{
  protected $collection_key = 'healths';
  protected $healthsType = HealthStatusForNetworkEndpoint::class;
  protected $healthsDataType = 'array';
  protected $networkEndpointType = NetworkEndpoint::class;
  protected $networkEndpointDataType = '';

  /**
   * Output only. [Output only] The health status of network endpoint.
   *
   * Optional. Displayed only if the network endpoint has centralized health
   * checking configured.
   *
   * @param HealthStatusForNetworkEndpoint[] $healths
   */
  public function setHealths($healths)
  {
    $this->healths = $healths;
  }
  /**
   * @return HealthStatusForNetworkEndpoint[]
   */
  public function getHealths()
  {
    return $this->healths;
  }
  /**
   * Output only. [Output only] The network endpoint.
   *
   * @param NetworkEndpoint $networkEndpoint
   */
  public function setNetworkEndpoint(NetworkEndpoint $networkEndpoint)
  {
    $this->networkEndpoint = $networkEndpoint;
  }
  /**
   * @return NetworkEndpoint
   */
  public function getNetworkEndpoint()
  {
    return $this->networkEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointWithHealthStatus::class, 'Google_Service_Compute_NetworkEndpointWithHealthStatus');
