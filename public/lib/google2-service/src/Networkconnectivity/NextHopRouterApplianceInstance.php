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

namespace Google\Service\Networkconnectivity;

class NextHopRouterApplianceInstance extends \Google\Model
{
  /**
   * Indicates whether site-to-site data transfer is allowed for this Router
   * appliance instance resource. Data transfer is available only in [supported
   * locations](https://cloud.google.com/network-connectivity/docs/network-
   * connectivity-center/concepts/locations).
   *
   * @var bool
   */
  public $siteToSiteDataTransfer;
  /**
   * The URI of the Router appliance instance.
   *
   * @var string
   */
  public $uri;
  /**
   * The VPC network where this VM is located.
   *
   * @var string
   */
  public $vpcNetwork;

  /**
   * Indicates whether site-to-site data transfer is allowed for this Router
   * appliance instance resource. Data transfer is available only in [supported
   * locations](https://cloud.google.com/network-connectivity/docs/network-
   * connectivity-center/concepts/locations).
   *
   * @param bool $siteToSiteDataTransfer
   */
  public function setSiteToSiteDataTransfer($siteToSiteDataTransfer)
  {
    $this->siteToSiteDataTransfer = $siteToSiteDataTransfer;
  }
  /**
   * @return bool
   */
  public function getSiteToSiteDataTransfer()
  {
    return $this->siteToSiteDataTransfer;
  }
  /**
   * The URI of the Router appliance instance.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * The VPC network where this VM is located.
   *
   * @param string $vpcNetwork
   */
  public function setVpcNetwork($vpcNetwork)
  {
    $this->vpcNetwork = $vpcNetwork;
  }
  /**
   * @return string
   */
  public function getVpcNetwork()
  {
    return $this->vpcNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NextHopRouterApplianceInstance::class, 'Google_Service_Networkconnectivity_NextHopRouterApplianceInstance');
