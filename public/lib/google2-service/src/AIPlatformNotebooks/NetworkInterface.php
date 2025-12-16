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

namespace Google\Service\AIPlatformNotebooks;

class NetworkInterface extends \Google\Collection
{
  /**
   * No type specified.
   */
  public const NIC_TYPE_NIC_TYPE_UNSPECIFIED = 'NIC_TYPE_UNSPECIFIED';
  /**
   * VIRTIO
   */
  public const NIC_TYPE_VIRTIO_NET = 'VIRTIO_NET';
  /**
   * GVNIC
   */
  public const NIC_TYPE_GVNIC = 'GVNIC';
  protected $collection_key = 'accessConfigs';
  protected $accessConfigsType = AccessConfig::class;
  protected $accessConfigsDataType = 'array';
  /**
   * Optional. The name of the VPC that this VM instance is in. Format:
   * `projects/{project_id}/global/networks/{network_id}`
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The type of vNIC to be used on this interface. This may be gVNIC
   * or VirtioNet.
   *
   * @var string
   */
  public $nicType;
  /**
   * Optional. The name of the subnet that this VM instance is in. Format:
   * `projects/{project_id}/regions/{region}/subnetworks/{subnetwork_id}`
   *
   * @var string
   */
  public $subnet;

  /**
   * Optional. An array of configurations for this interface. Currently, only
   * one access config, ONE_TO_ONE_NAT, is supported. If no accessConfigs
   * specified, the instance will have an external internet access through an
   * ephemeral external IP address.
   *
   * @param AccessConfig[] $accessConfigs
   */
  public function setAccessConfigs($accessConfigs)
  {
    $this->accessConfigs = $accessConfigs;
  }
  /**
   * @return AccessConfig[]
   */
  public function getAccessConfigs()
  {
    return $this->accessConfigs;
  }
  /**
   * Optional. The name of the VPC that this VM instance is in. Format:
   * `projects/{project_id}/global/networks/{network_id}`
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
   * Optional. The type of vNIC to be used on this interface. This may be gVNIC
   * or VirtioNet.
   *
   * Accepted values: NIC_TYPE_UNSPECIFIED, VIRTIO_NET, GVNIC
   *
   * @param self::NIC_TYPE_* $nicType
   */
  public function setNicType($nicType)
  {
    $this->nicType = $nicType;
  }
  /**
   * @return self::NIC_TYPE_*
   */
  public function getNicType()
  {
    return $this->nicType;
  }
  /**
   * Optional. The name of the subnet that this VM instance is in. Format:
   * `projects/{project_id}/regions/{region}/subnetworks/{subnetwork_id}`
   *
   * @param string $subnet
   */
  public function setSubnet($subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return string
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkInterface::class, 'Google_Service_AIPlatformNotebooks_NetworkInterface');
