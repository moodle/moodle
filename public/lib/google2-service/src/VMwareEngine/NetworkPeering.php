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

namespace Google\Service\VMwareEngine;

class NetworkPeering extends \Google\Model
{
  /**
   * Unspecified
   */
  public const PEER_NETWORK_TYPE_PEER_NETWORK_TYPE_UNSPECIFIED = 'PEER_NETWORK_TYPE_UNSPECIFIED';
  /**
   * Peering connection used for connecting to another VPC network established
   * by the same user. For example, a peering connection to another VPC network
   * in the same project or to an on-premises network.
   */
  public const PEER_NETWORK_TYPE_STANDARD = 'STANDARD';
  /**
   * Peering connection used for connecting to another VMware Engine network.
   */
  public const PEER_NETWORK_TYPE_VMWARE_ENGINE_NETWORK = 'VMWARE_ENGINE_NETWORK';
  /**
   * Peering connection used for establishing [private services
   * access](https://cloud.google.com/vpc/docs/private-services-access).
   */
  public const PEER_NETWORK_TYPE_PRIVATE_SERVICES_ACCESS = 'PRIVATE_SERVICES_ACCESS';
  /**
   * Peering connection used for connecting to NetApp Cloud Volumes.
   */
  public const PEER_NETWORK_TYPE_NETAPP_CLOUD_VOLUMES = 'NETAPP_CLOUD_VOLUMES';
  /**
   * Peering connection used for connecting to third-party services. Most third-
   * party services require manual setup of reverse peering on the VPC network
   * associated with the third-party service.
   */
  public const PEER_NETWORK_TYPE_THIRD_PARTY_SERVICE = 'THIRD_PARTY_SERVICE';
  /**
   * Peering connection used for connecting to Dell PowerScale Filers
   */
  public const PEER_NETWORK_TYPE_DELL_POWERSCALE = 'DELL_POWERSCALE';
  /**
   * Peering connection used for connecting to Google Cloud NetApp Volumes.
   */
  public const PEER_NETWORK_TYPE_GOOGLE_CLOUD_NETAPP_VOLUMES = 'GOOGLE_CLOUD_NETAPP_VOLUMES';
  /**
   * Peering connection used for connecting to Google Cloud Filestore Instances.
   */
  public const PEER_NETWORK_TYPE_GOOGLE_CLOUD_FILESTORE_INSTANCES = 'GOOGLE_CLOUD_FILESTORE_INSTANCES';
  /**
   * Unspecified network peering state. This is the default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The peering is not active.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The peering is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The peering is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The peering is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description for this network peering.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. True if full mesh connectivity is created and managed
   * automatically between peered networks; false otherwise. Currently this
   * field is always true because Google Compute Engine automatically creates
   * and manages subnetwork routes between two VPC networks when peering state
   * is 'ACTIVE'.
   *
   * @var bool
   */
  public $exchangeSubnetRoutes;
  /**
   * Optional. True if custom routes are exported to the peered network; false
   * otherwise. The default value is true.
   *
   * @var bool
   */
  public $exportCustomRoutes;
  /**
   * Optional. True if all subnet routes with a public IP address range are
   * exported; false otherwise. The default value is true. IPv4 special-use
   * ranges (https://en.wikipedia.org/wiki/IPv4#Special_addresses) are always
   * exported to peers and are not controlled by this field.
   *
   * @var bool
   */
  public $exportCustomRoutesWithPublicIp;
  /**
   * Optional. True if custom routes are imported from the peered network; false
   * otherwise. The default value is true.
   *
   * @var bool
   */
  public $importCustomRoutes;
  /**
   * Optional. True if all subnet routes with public IP address range are
   * imported; false otherwise. The default value is true. IPv4 special-use
   * ranges (https://en.wikipedia.org/wiki/IPv4#Special_addresses) are always
   * imported to peers and are not controlled by this field.
   *
   * @var bool
   */
  public $importCustomRoutesWithPublicIp;
  /**
   * Output only. Identifier. The resource name of the network peering.
   * NetworkPeering is a global resource and location can only be global.
   * Resource names are scheme-less URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/networkPeerings/my-peering`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Maximum transmission unit (MTU) in bytes. The default value is
   * `1500`. If a value of `0` is provided for this field, VMware Engine uses
   * the default value instead.
   *
   * @var int
   */
  public $peerMtu;
  /**
   * Required. The relative resource name of the network to peer with a standard
   * VMware Engine network. The provided network can be a consumer VPC network
   * or another standard VMware Engine network. If the `peer_network_type` is
   * VMWARE_ENGINE_NETWORK, specify the name in the form: `projects/{project}/lo
   * cations/global/vmwareEngineNetworks/{vmware_engine_network_id}`. Otherwise
   * specify the name in the form:
   * `projects/{project}/global/networks/{network_id}`, where `{project}` can
   * either be a project number or a project ID.
   *
   * @var string
   */
  public $peerNetwork;
  /**
   * Required. The type of the network to peer with the VMware Engine network.
   *
   * @var string
   */
  public $peerNetworkType;
  /**
   * Output only. State of the network peering. This field has a value of
   * 'ACTIVE' when there's a matching configuration in the peer network. New
   * values may be added to this enum when appropriate.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Output Only. Details about the current state of the network
   * peering.
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. The relative resource name of the VMware Engine network. Specify
   * the name in the following form: `projects/{project}/locations/{location}/vm
   * wareEngineNetworks/{vmware_engine_network_id}` where `{project}` can either
   * be a project number or a project ID.
   *
   * @var string
   */
  public $vmwareEngineNetwork;

  /**
   * Output only. Creation time of this resource.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. User-provided description for this network peering.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. True if full mesh connectivity is created and managed
   * automatically between peered networks; false otherwise. Currently this
   * field is always true because Google Compute Engine automatically creates
   * and manages subnetwork routes between two VPC networks when peering state
   * is 'ACTIVE'.
   *
   * @param bool $exchangeSubnetRoutes
   */
  public function setExchangeSubnetRoutes($exchangeSubnetRoutes)
  {
    $this->exchangeSubnetRoutes = $exchangeSubnetRoutes;
  }
  /**
   * @return bool
   */
  public function getExchangeSubnetRoutes()
  {
    return $this->exchangeSubnetRoutes;
  }
  /**
   * Optional. True if custom routes are exported to the peered network; false
   * otherwise. The default value is true.
   *
   * @param bool $exportCustomRoutes
   */
  public function setExportCustomRoutes($exportCustomRoutes)
  {
    $this->exportCustomRoutes = $exportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getExportCustomRoutes()
  {
    return $this->exportCustomRoutes;
  }
  /**
   * Optional. True if all subnet routes with a public IP address range are
   * exported; false otherwise. The default value is true. IPv4 special-use
   * ranges (https://en.wikipedia.org/wiki/IPv4#Special_addresses) are always
   * exported to peers and are not controlled by this field.
   *
   * @param bool $exportCustomRoutesWithPublicIp
   */
  public function setExportCustomRoutesWithPublicIp($exportCustomRoutesWithPublicIp)
  {
    $this->exportCustomRoutesWithPublicIp = $exportCustomRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getExportCustomRoutesWithPublicIp()
  {
    return $this->exportCustomRoutesWithPublicIp;
  }
  /**
   * Optional. True if custom routes are imported from the peered network; false
   * otherwise. The default value is true.
   *
   * @param bool $importCustomRoutes
   */
  public function setImportCustomRoutes($importCustomRoutes)
  {
    $this->importCustomRoutes = $importCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getImportCustomRoutes()
  {
    return $this->importCustomRoutes;
  }
  /**
   * Optional. True if all subnet routes with public IP address range are
   * imported; false otherwise. The default value is true. IPv4 special-use
   * ranges (https://en.wikipedia.org/wiki/IPv4#Special_addresses) are always
   * imported to peers and are not controlled by this field.
   *
   * @param bool $importCustomRoutesWithPublicIp
   */
  public function setImportCustomRoutesWithPublicIp($importCustomRoutesWithPublicIp)
  {
    $this->importCustomRoutesWithPublicIp = $importCustomRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getImportCustomRoutesWithPublicIp()
  {
    return $this->importCustomRoutesWithPublicIp;
  }
  /**
   * Output only. Identifier. The resource name of the network peering.
   * NetworkPeering is a global resource and location can only be global.
   * Resource names are scheme-less URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/networkPeerings/my-peering`
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
   * Optional. Maximum transmission unit (MTU) in bytes. The default value is
   * `1500`. If a value of `0` is provided for this field, VMware Engine uses
   * the default value instead.
   *
   * @param int $peerMtu
   */
  public function setPeerMtu($peerMtu)
  {
    $this->peerMtu = $peerMtu;
  }
  /**
   * @return int
   */
  public function getPeerMtu()
  {
    return $this->peerMtu;
  }
  /**
   * Required. The relative resource name of the network to peer with a standard
   * VMware Engine network. The provided network can be a consumer VPC network
   * or another standard VMware Engine network. If the `peer_network_type` is
   * VMWARE_ENGINE_NETWORK, specify the name in the form: `projects/{project}/lo
   * cations/global/vmwareEngineNetworks/{vmware_engine_network_id}`. Otherwise
   * specify the name in the form:
   * `projects/{project}/global/networks/{network_id}`, where `{project}` can
   * either be a project number or a project ID.
   *
   * @param string $peerNetwork
   */
  public function setPeerNetwork($peerNetwork)
  {
    $this->peerNetwork = $peerNetwork;
  }
  /**
   * @return string
   */
  public function getPeerNetwork()
  {
    return $this->peerNetwork;
  }
  /**
   * Required. The type of the network to peer with the VMware Engine network.
   *
   * Accepted values: PEER_NETWORK_TYPE_UNSPECIFIED, STANDARD,
   * VMWARE_ENGINE_NETWORK, PRIVATE_SERVICES_ACCESS, NETAPP_CLOUD_VOLUMES,
   * THIRD_PARTY_SERVICE, DELL_POWERSCALE, GOOGLE_CLOUD_NETAPP_VOLUMES,
   * GOOGLE_CLOUD_FILESTORE_INSTANCES
   *
   * @param self::PEER_NETWORK_TYPE_* $peerNetworkType
   */
  public function setPeerNetworkType($peerNetworkType)
  {
    $this->peerNetworkType = $peerNetworkType;
  }
  /**
   * @return self::PEER_NETWORK_TYPE_*
   */
  public function getPeerNetworkType()
  {
    return $this->peerNetworkType;
  }
  /**
   * Output only. State of the network peering. This field has a value of
   * 'ACTIVE' when there's a matching configuration in the peer network. New
   * values may be added to this enum when appropriate.
   *
   * Accepted values: STATE_UNSPECIFIED, INACTIVE, ACTIVE, CREATING, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Output Only. Details about the current state of the network
   * peering.
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Required. The relative resource name of the VMware Engine network. Specify
   * the name in the following form: `projects/{project}/locations/{location}/vm
   * wareEngineNetworks/{vmware_engine_network_id}` where `{project}` can either
   * be a project number or a project ID.
   *
   * @param string $vmwareEngineNetwork
   */
  public function setVmwareEngineNetwork($vmwareEngineNetwork)
  {
    $this->vmwareEngineNetwork = $vmwareEngineNetwork;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetwork()
  {
    return $this->vmwareEngineNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPeering::class, 'Google_Service_VMwareEngine_NetworkPeering');
