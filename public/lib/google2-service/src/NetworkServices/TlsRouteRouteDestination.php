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

class TlsRouteRouteDestination extends \Google\Model
{
  /**
   * Required. The URL of a BackendService to route traffic to.
   *
   * @var string
   */
  public $serviceName;
  /**
   * Optional. Specifies the proportion of requests forwarded to the backend
   * referenced by the service_name field. This is computed as: -
   * weight/Sum(weights in destinations) Weights in all destinations does not
   * need to sum up to 100.
   *
   * @var int
   */
  public $weight;

  /**
   * Required. The URL of a BackendService to route traffic to.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * Optional. Specifies the proportion of requests forwarded to the backend
   * referenced by the service_name field. This is computed as: -
   * weight/Sum(weights in destinations) Weights in all destinations does not
   * need to sum up to 100.
   *
   * @param int $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return int
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TlsRouteRouteDestination::class, 'Google_Service_NetworkServices_TlsRouteRouteDestination');
