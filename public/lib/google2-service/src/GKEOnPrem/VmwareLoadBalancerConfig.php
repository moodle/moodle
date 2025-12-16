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

class VmwareLoadBalancerConfig extends \Google\Model
{
  protected $f5ConfigType = VmwareF5BigIpConfig::class;
  protected $f5ConfigDataType = '';
  protected $manualLbConfigType = VmwareManualLbConfig::class;
  protected $manualLbConfigDataType = '';
  protected $metalLbConfigType = VmwareMetalLbConfig::class;
  protected $metalLbConfigDataType = '';
  protected $seesawConfigType = VmwareSeesawConfig::class;
  protected $seesawConfigDataType = '';
  protected $vipConfigType = VmwareVipConfig::class;
  protected $vipConfigDataType = '';

  /**
   * Configuration for F5 Big IP typed load balancers.
   *
   * @param VmwareF5BigIpConfig $f5Config
   */
  public function setF5Config(VmwareF5BigIpConfig $f5Config)
  {
    $this->f5Config = $f5Config;
  }
  /**
   * @return VmwareF5BigIpConfig
   */
  public function getF5Config()
  {
    return $this->f5Config;
  }
  /**
   * Manually configured load balancers.
   *
   * @param VmwareManualLbConfig $manualLbConfig
   */
  public function setManualLbConfig(VmwareManualLbConfig $manualLbConfig)
  {
    $this->manualLbConfig = $manualLbConfig;
  }
  /**
   * @return VmwareManualLbConfig
   */
  public function getManualLbConfig()
  {
    return $this->manualLbConfig;
  }
  /**
   * Configuration for MetalLB typed load balancers.
   *
   * @param VmwareMetalLbConfig $metalLbConfig
   */
  public function setMetalLbConfig(VmwareMetalLbConfig $metalLbConfig)
  {
    $this->metalLbConfig = $metalLbConfig;
  }
  /**
   * @return VmwareMetalLbConfig
   */
  public function getMetalLbConfig()
  {
    return $this->metalLbConfig;
  }
  /**
   * Output only. Configuration for Seesaw typed load balancers.
   *
   * @param VmwareSeesawConfig $seesawConfig
   */
  public function setSeesawConfig(VmwareSeesawConfig $seesawConfig)
  {
    $this->seesawConfig = $seesawConfig;
  }
  /**
   * @return VmwareSeesawConfig
   */
  public function getSeesawConfig()
  {
    return $this->seesawConfig;
  }
  /**
   * The VIPs used by the load balancer.
   *
   * @param VmwareVipConfig $vipConfig
   */
  public function setVipConfig(VmwareVipConfig $vipConfig)
  {
    $this->vipConfig = $vipConfig;
  }
  /**
   * @return VmwareVipConfig
   */
  public function getVipConfig()
  {
    return $this->vipConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareLoadBalancerConfig::class, 'Google_Service_GKEOnPrem_VmwareLoadBalancerConfig');
