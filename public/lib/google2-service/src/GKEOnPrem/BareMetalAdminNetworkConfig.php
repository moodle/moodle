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

class BareMetalAdminNetworkConfig extends \Google\Model
{
  /**
   * Enables the use of advanced Anthos networking features, such as Bundled
   * Load Balancing with BGP or the egress NAT gateway. Setting configuration
   * for advanced networking features will automatically set this flag.
   *
   * @var bool
   */
  public $advancedNetworking;
  protected $islandModeCidrType = BareMetalAdminIslandModeCidrConfig::class;
  protected $islandModeCidrDataType = '';
  protected $multipleNetworkInterfacesConfigType = BareMetalAdminMultipleNetworkInterfacesConfig::class;
  protected $multipleNetworkInterfacesConfigDataType = '';

  /**
   * Enables the use of advanced Anthos networking features, such as Bundled
   * Load Balancing with BGP or the egress NAT gateway. Setting configuration
   * for advanced networking features will automatically set this flag.
   *
   * @param bool $advancedNetworking
   */
  public function setAdvancedNetworking($advancedNetworking)
  {
    $this->advancedNetworking = $advancedNetworking;
  }
  /**
   * @return bool
   */
  public function getAdvancedNetworking()
  {
    return $this->advancedNetworking;
  }
  /**
   * Configuration for Island mode CIDR.
   *
   * @param BareMetalAdminIslandModeCidrConfig $islandModeCidr
   */
  public function setIslandModeCidr(BareMetalAdminIslandModeCidrConfig $islandModeCidr)
  {
    $this->islandModeCidr = $islandModeCidr;
  }
  /**
   * @return BareMetalAdminIslandModeCidrConfig
   */
  public function getIslandModeCidr()
  {
    return $this->islandModeCidr;
  }
  /**
   * Configuration for multiple network interfaces.
   *
   * @param BareMetalAdminMultipleNetworkInterfacesConfig $multipleNetworkInterfacesConfig
   */
  public function setMultipleNetworkInterfacesConfig(BareMetalAdminMultipleNetworkInterfacesConfig $multipleNetworkInterfacesConfig)
  {
    $this->multipleNetworkInterfacesConfig = $multipleNetworkInterfacesConfig;
  }
  /**
   * @return BareMetalAdminMultipleNetworkInterfacesConfig
   */
  public function getMultipleNetworkInterfacesConfig()
  {
    return $this->multipleNetworkInterfacesConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminNetworkConfig::class, 'Google_Service_GKEOnPrem_BareMetalAdminNetworkConfig');
