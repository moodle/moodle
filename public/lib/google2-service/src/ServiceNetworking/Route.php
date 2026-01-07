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

namespace Google\Service\ServiceNetworking;

class Route extends \Google\Model
{
  /**
   * Destination CIDR range that this route applies to.
   *
   * @var string
   */
  public $destRange;
  /**
   * Route name. See https://cloud.google.com/vpc/docs/routes
   *
   * @var string
   */
  public $name;
  /**
   * Fully-qualified URL of the VPC network in the producer host tenant project
   * that this route applies to. For example:
   * `projects/123456/global/networks/host-network`
   *
   * @var string
   */
  public $network;
  /**
   * Fully-qualified URL of the gateway that should handle matching packets that
   * this route applies to. For example:
   * `projects/123456/global/gateways/default-internet-gateway`
   *
   * @var string
   */
  public $nextHopGateway;

  /**
   * Destination CIDR range that this route applies to.
   *
   * @param string $destRange
   */
  public function setDestRange($destRange)
  {
    $this->destRange = $destRange;
  }
  /**
   * @return string
   */
  public function getDestRange()
  {
    return $this->destRange;
  }
  /**
   * Route name. See https://cloud.google.com/vpc/docs/routes
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
   * Fully-qualified URL of the VPC network in the producer host tenant project
   * that this route applies to. For example:
   * `projects/123456/global/networks/host-network`
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Fully-qualified URL of the gateway that should handle matching packets that
   * this route applies to. For example:
   * `projects/123456/global/gateways/default-internet-gateway`
   *
   * @param string $nextHopGateway
   */
  public function setNextHopGateway($nextHopGateway)
  {
    $this->nextHopGateway = $nextHopGateway;
  }
  /**
   * @return string
   */
  public function getNextHopGateway()
  {
    return $this->nextHopGateway;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Route::class, 'Google_Service_ServiceNetworking_Route');
