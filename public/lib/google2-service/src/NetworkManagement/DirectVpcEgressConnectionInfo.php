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

namespace Google\Service\NetworkManagement;

class DirectVpcEgressConnectionInfo extends \Google\Model
{
  /**
   * URI of direct access network.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Region in which the Direct VPC egress is deployed.
   *
   * @var string
   */
  public $region;
  /**
   * Selected starting IP address, from the selected IP range.
   *
   * @var string
   */
  public $selectedIpAddress;
  /**
   * Selected IP range.
   *
   * @var string
   */
  public $selectedIpRange;
  /**
   * URI of direct access subnetwork.
   *
   * @var string
   */
  public $subnetworkUri;

  /**
   * URI of direct access network.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Region in which the Direct VPC egress is deployed.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Selected starting IP address, from the selected IP range.
   *
   * @param string $selectedIpAddress
   */
  public function setSelectedIpAddress($selectedIpAddress)
  {
    $this->selectedIpAddress = $selectedIpAddress;
  }
  /**
   * @return string
   */
  public function getSelectedIpAddress()
  {
    return $this->selectedIpAddress;
  }
  /**
   * Selected IP range.
   *
   * @param string $selectedIpRange
   */
  public function setSelectedIpRange($selectedIpRange)
  {
    $this->selectedIpRange = $selectedIpRange;
  }
  /**
   * @return string
   */
  public function getSelectedIpRange()
  {
    return $this->selectedIpRange;
  }
  /**
   * URI of direct access subnetwork.
   *
   * @param string $subnetworkUri
   */
  public function setSubnetworkUri($subnetworkUri)
  {
    $this->subnetworkUri = $subnetworkUri;
  }
  /**
   * @return string
   */
  public function getSubnetworkUri()
  {
    return $this->subnetworkUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectVpcEgressConnectionInfo::class, 'Google_Service_NetworkManagement_DirectVpcEgressConnectionInfo');
