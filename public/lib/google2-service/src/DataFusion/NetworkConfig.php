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

namespace Google\Service\DataFusion;

class NetworkConfig extends \Google\Model
{
  /**
   * No specific connection type was requested, the default value of VPC_PEERING
   * is chosen.
   */
  public const CONNECTION_TYPE_CONNECTION_TYPE_UNSPECIFIED = 'CONNECTION_TYPE_UNSPECIFIED';
  /**
   * Requests the use of VPC peerings for connecting the consumer and tenant
   * projects.
   */
  public const CONNECTION_TYPE_VPC_PEERING = 'VPC_PEERING';
  /**
   * Requests the use of Private Service Connect Interfaces for connecting the
   * consumer and tenant projects.
   */
  public const CONNECTION_TYPE_PRIVATE_SERVICE_CONNECT_INTERFACES = 'PRIVATE_SERVICE_CONNECT_INTERFACES';
  /**
   * Optional. Type of connection for establishing private IP connectivity
   * between the Data Fusion customer project VPC and the corresponding tenant
   * project from a predefined list of available connection modes. If this field
   * is unspecified for a private instance, VPC peering is used.
   *
   * @var string
   */
  public $connectionType;
  /**
   * Optional. The IP range in CIDR notation to use for the managed Data Fusion
   * instance nodes. This range must not overlap with any other ranges used in
   * the Data Fusion instance network. This is required only when using
   * connection type VPC_PEERING. Format: a.b.c.d/22 Example: 192.168.0.0/22
   *
   * @var string
   */
  public $ipAllocation;
  /**
   * Optional. Name of the network in the customer project with which the Tenant
   * Project will be peered for executing pipelines. In case of shared VPC where
   * the network resides in another host project the network should specified in
   * the form of projects/{host-project-id}/global/networks/{network}. This is
   * only required for connectivity type VPC_PEERING.
   *
   * @var string
   */
  public $network;
  protected $privateServiceConnectConfigType = PrivateServiceConnectConfig::class;
  protected $privateServiceConnectConfigDataType = '';

  /**
   * Optional. Type of connection for establishing private IP connectivity
   * between the Data Fusion customer project VPC and the corresponding tenant
   * project from a predefined list of available connection modes. If this field
   * is unspecified for a private instance, VPC peering is used.
   *
   * Accepted values: CONNECTION_TYPE_UNSPECIFIED, VPC_PEERING,
   * PRIVATE_SERVICE_CONNECT_INTERFACES
   *
   * @param self::CONNECTION_TYPE_* $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return self::CONNECTION_TYPE_*
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
  /**
   * Optional. The IP range in CIDR notation to use for the managed Data Fusion
   * instance nodes. This range must not overlap with any other ranges used in
   * the Data Fusion instance network. This is required only when using
   * connection type VPC_PEERING. Format: a.b.c.d/22 Example: 192.168.0.0/22
   *
   * @param string $ipAllocation
   */
  public function setIpAllocation($ipAllocation)
  {
    $this->ipAllocation = $ipAllocation;
  }
  /**
   * @return string
   */
  public function getIpAllocation()
  {
    return $this->ipAllocation;
  }
  /**
   * Optional. Name of the network in the customer project with which the Tenant
   * Project will be peered for executing pipelines. In case of shared VPC where
   * the network resides in another host project the network should specified in
   * the form of projects/{host-project-id}/global/networks/{network}. This is
   * only required for connectivity type VPC_PEERING.
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
   * Optional. Configuration for Private Service Connect. This is required only
   * when using connection type PRIVATE_SERVICE_CONNECT_INTERFACES.
   *
   * @param PrivateServiceConnectConfig $privateServiceConnectConfig
   */
  public function setPrivateServiceConnectConfig(PrivateServiceConnectConfig $privateServiceConnectConfig)
  {
    $this->privateServiceConnectConfig = $privateServiceConnectConfig;
  }
  /**
   * @return PrivateServiceConnectConfig
   */
  public function getPrivateServiceConnectConfig()
  {
    return $this->privateServiceConnectConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_DataFusion_NetworkConfig');
