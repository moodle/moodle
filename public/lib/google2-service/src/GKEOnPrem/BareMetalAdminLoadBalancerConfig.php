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

namespace Google\Service\GKEOnPrem;

class BareMetalAdminLoadBalancerConfig extends \Google\Model
{
  protected $bgpLbConfigType = BareMetalAdminBgpLbConfig::class;
  protected $bgpLbConfigDataType = '';
  protected $manualLbConfigType = BareMetalAdminManualLbConfig::class;
  protected $manualLbConfigDataType = '';
  protected $portConfigType = BareMetalAdminPortConfig::class;
  protected $portConfigDataType = '';
  protected $vipConfigType = BareMetalAdminVipConfig::class;
  protected $vipConfigDataType = '';

  /**
   * Configuration for BGP typed load balancers.
   *
   * @param BareMetalAdminBgpLbConfig $bgpLbConfig
   */
  public function setBgpLbConfig(BareMetalAdminBgpLbConfig $bgpLbConfig)
  {
    $this->bgpLbConfig = $bgpLbConfig;
  }
  /**
   * @return BareMetalAdminBgpLbConfig
   */
  public function getBgpLbConfig()
  {
    return $this->bgpLbConfig;
  }
  /**
   * Manually configured load balancers.
   *
   * @param BareMetalAdminManualLbConfig $manualLbConfig
   */
  public function setManualLbConfig(BareMetalAdminManualLbConfig $manualLbConfig)
  {
    $this->manualLbConfig = $manualLbConfig;
  }
  /**
   * @return BareMetalAdminManualLbConfig
   */
  public function getManualLbConfig()
  {
    return $this->manualLbConfig;
  }
  /**
   * Configures the ports that the load balancer will listen on.
   *
   * @param BareMetalAdminPortConfig $portConfig
   */
  public function setPortConfig(BareMetalAdminPortConfig $portConfig)
  {
    $this->portConfig = $portConfig;
  }
  /**
   * @return BareMetalAdminPortConfig
   */
  public function getPortConfig()
  {
    return $this->portConfig;
  }
  /**
   * The VIPs used by the load balancer.
   *
   * @param BareMetalAdminVipConfig $vipConfig
   */
  public function setVipConfig(BareMetalAdminVipConfig $vipConfig)
  {
    $this->vipConfig = $vipConfig;
  }
  /**
   * @return BareMetalAdminVipConfig
   */
  public function getVipConfig()
  {
    return $this->vipConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminLoadBalancerConfig::class, 'Google_Service_GKEOnPrem_BareMetalAdminLoadBalancerConfig');
