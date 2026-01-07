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

class BareMetalNetworkConfig extends \Google\Model
{
  /**
   * Enables the use of advanced Anthos networking features, such as Bundled
   * Load Balancing with BGP or the egress NAT gateway. Setting configuration
   * for advanced networking features will automatically set this flag.
   *
   * @var bool
   */
  public $advancedNetworking;
  protected $islandModeCidrType = BareMetalIslandModeCidrConfig::class;
  protected $islandModeCidrDataType = '';
  protected $multipleNetworkInterfacesConfigType = BareMetalMultipleNetworkInterfacesConfig::class;
  protected $multipleNetworkInterfacesConfigDataType = '';
  protected $srIovConfigType = BareMetalSrIovConfig::class;
  protected $srIovConfigDataType = '';

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
   * Configuration for island mode CIDR. In an island-mode network, nodes have
   * unique IP addresses, but pods don't have unique addresses across clusters.
   * This doesn't cause problems because pods in one cluster never directly
   * communicate with pods in another cluster. Instead, there are gateways that
   * mediate between a pod in one cluster and a pod in another cluster.
   *
   * @param BareMetalIslandModeCidrConfig $islandModeCidr
   */
  public function setIslandModeCidr(BareMetalIslandModeCidrConfig $islandModeCidr)
  {
    $this->islandModeCidr = $islandModeCidr;
  }
  /**
   * @return BareMetalIslandModeCidrConfig
   */
  public function getIslandModeCidr()
  {
    return $this->islandModeCidr;
  }
  /**
   * Configuration for multiple network interfaces.
   *
   * @param BareMetalMultipleNetworkInterfacesConfig $multipleNetworkInterfacesConfig
   */
  public function setMultipleNetworkInterfacesConfig(BareMetalMultipleNetworkInterfacesConfig $multipleNetworkInterfacesConfig)
  {
    $this->multipleNetworkInterfacesConfig = $multipleNetworkInterfacesConfig;
  }
  /**
   * @return BareMetalMultipleNetworkInterfacesConfig
   */
  public function getMultipleNetworkInterfacesConfig()
  {
    return $this->multipleNetworkInterfacesConfig;
  }
  /**
   * Configuration for SR-IOV.
   *
   * @param BareMetalSrIovConfig $srIovConfig
   */
  public function setSrIovConfig(BareMetalSrIovConfig $srIovConfig)
  {
    $this->srIovConfig = $srIovConfig;
  }
  /**
   * @return BareMetalSrIovConfig
   */
  public function getSrIovConfig()
  {
    return $this->srIovConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalNetworkConfig::class, 'Google_Service_GKEOnPrem_BareMetalNetworkConfig');
