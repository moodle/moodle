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

namespace Google\Service\Container;

class IPEndpointsConfig extends \Google\Model
{
  protected $authorizedNetworksConfigType = MasterAuthorizedNetworksConfig::class;
  protected $authorizedNetworksConfigDataType = '';
  /**
   * Controls whether the control plane allows access through a public IP. It is
   * invalid to specify both PrivateClusterConfig.enablePrivateEndpoint and this
   * field at the same time.
   *
   * @var bool
   */
  public $enablePublicEndpoint;
  /**
   * Controls whether to allow direct IP access.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Controls whether the control plane's private endpoint is accessible from
   * sources in other regions. It is invalid to specify both
   * PrivateClusterMasterGlobalAccessConfig.enabled and this field at the same
   * time.
   *
   * @var bool
   */
  public $globalAccess;
  /**
   * Output only. The internal IP address of this cluster's control plane. Only
   * populated if enabled.
   *
   * @var string
   */
  public $privateEndpoint;
  /**
   * Subnet to provision the master's private endpoint during cluster creation.
   * Specified in projects/regions/subnetworks format. It is invalid to specify
   * both PrivateClusterConfig.privateEndpointSubnetwork and this field at the
   * same time.
   *
   * @var string
   */
  public $privateEndpointSubnetwork;
  /**
   * Output only. The external IP address of this cluster's control plane. Only
   * populated if enabled.
   *
   * @var string
   */
  public $publicEndpoint;

  /**
   * Configuration of authorized networks. If enabled, restricts access to the
   * control plane based on source IP. It is invalid to specify both
   * Cluster.masterAuthorizedNetworksConfig and this field at the same time.
   *
   * @param MasterAuthorizedNetworksConfig $authorizedNetworksConfig
   */
  public function setAuthorizedNetworksConfig(MasterAuthorizedNetworksConfig $authorizedNetworksConfig)
  {
    $this->authorizedNetworksConfig = $authorizedNetworksConfig;
  }
  /**
   * @return MasterAuthorizedNetworksConfig
   */
  public function getAuthorizedNetworksConfig()
  {
    return $this->authorizedNetworksConfig;
  }
  /**
   * Controls whether the control plane allows access through a public IP. It is
   * invalid to specify both PrivateClusterConfig.enablePrivateEndpoint and this
   * field at the same time.
   *
   * @param bool $enablePublicEndpoint
   */
  public function setEnablePublicEndpoint($enablePublicEndpoint)
  {
    $this->enablePublicEndpoint = $enablePublicEndpoint;
  }
  /**
   * @return bool
   */
  public function getEnablePublicEndpoint()
  {
    return $this->enablePublicEndpoint;
  }
  /**
   * Controls whether to allow direct IP access.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Controls whether the control plane's private endpoint is accessible from
   * sources in other regions. It is invalid to specify both
   * PrivateClusterMasterGlobalAccessConfig.enabled and this field at the same
   * time.
   *
   * @param bool $globalAccess
   */
  public function setGlobalAccess($globalAccess)
  {
    $this->globalAccess = $globalAccess;
  }
  /**
   * @return bool
   */
  public function getGlobalAccess()
  {
    return $this->globalAccess;
  }
  /**
   * Output only. The internal IP address of this cluster's control plane. Only
   * populated if enabled.
   *
   * @param string $privateEndpoint
   */
  public function setPrivateEndpoint($privateEndpoint)
  {
    $this->privateEndpoint = $privateEndpoint;
  }
  /**
   * @return string
   */
  public function getPrivateEndpoint()
  {
    return $this->privateEndpoint;
  }
  /**
   * Subnet to provision the master's private endpoint during cluster creation.
   * Specified in projects/regions/subnetworks format. It is invalid to specify
   * both PrivateClusterConfig.privateEndpointSubnetwork and this field at the
   * same time.
   *
   * @param string $privateEndpointSubnetwork
   */
  public function setPrivateEndpointSubnetwork($privateEndpointSubnetwork)
  {
    $this->privateEndpointSubnetwork = $privateEndpointSubnetwork;
  }
  /**
   * @return string
   */
  public function getPrivateEndpointSubnetwork()
  {
    return $this->privateEndpointSubnetwork;
  }
  /**
   * Output only. The external IP address of this cluster's control plane. Only
   * populated if enabled.
   *
   * @param string $publicEndpoint
   */
  public function setPublicEndpoint($publicEndpoint)
  {
    $this->publicEndpoint = $publicEndpoint;
  }
  /**
   * @return string
   */
  public function getPublicEndpoint()
  {
    return $this->publicEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IPEndpointsConfig::class, 'Google_Service_Container_IPEndpointsConfig');
