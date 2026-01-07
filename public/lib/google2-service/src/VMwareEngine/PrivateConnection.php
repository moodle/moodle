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

class PrivateConnection extends \Google\Model
{
  /**
   * The default value. This value is used if the peering state is omitted or
   * unknown.
   */
  public const PEERING_STATE_PEERING_STATE_UNSPECIFIED = 'PEERING_STATE_UNSPECIFIED';
  /**
   * The peering is in active state.
   */
  public const PEERING_STATE_PEERING_ACTIVE = 'PEERING_ACTIVE';
  /**
   * The peering is in inactive state.
   */
  public const PEERING_STATE_PEERING_INACTIVE = 'PEERING_INACTIVE';
  /**
   * The default value. This value should never be used.
   */
  public const ROUTING_MODE_ROUTING_MODE_UNSPECIFIED = 'ROUTING_MODE_UNSPECIFIED';
  /**
   * Global Routing Mode
   */
  public const ROUTING_MODE_GLOBAL = 'GLOBAL';
  /**
   * Regional Routing Mode
   */
  public const ROUTING_MODE_REGIONAL = 'REGIONAL';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The private connection is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The private connection is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The private connection is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The private connection is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The private connection is not provisioned, since no private cloud is
   * present for which this private connection is needed.
   */
  public const STATE_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * The private connection is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The default value. This value should never be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Connection used for establishing [private services
   * access](https://cloud.google.com/vpc/docs/private-services-access).
   */
  public const TYPE_PRIVATE_SERVICE_ACCESS = 'PRIVATE_SERVICE_ACCESS';
  /**
   * Connection used for connecting to NetApp Cloud Volumes.
   */
  public const TYPE_NETAPP_CLOUD_VOLUMES = 'NETAPP_CLOUD_VOLUMES';
  /**
   * Connection used for connecting to Dell PowerScale.
   */
  public const TYPE_DELL_POWERSCALE = 'DELL_POWERSCALE';
  /**
   * Connection used for connecting to third-party services.
   */
  public const TYPE_THIRD_PARTY_SERVICE = 'THIRD_PARTY_SERVICE';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description for this private connection.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The resource name of the private connection. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateConnections/my-
   * connection`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. VPC network peering id between given network VPC and
   * VMwareEngineNetwork.
   *
   * @var string
   */
  public $peeringId;
  /**
   * Output only. Peering state between service network and VMware Engine
   * network.
   *
   * @var string
   */
  public $peeringState;
  /**
   * Optional. Routing Mode. Default value is set to GLOBAL. For type =
   * PRIVATE_SERVICE_ACCESS, this field can be set to GLOBAL or REGIONAL, for
   * other types only GLOBAL is supported.
   *
   * @var string
   */
  public $routingMode;
  /**
   * Required. Service network to create private connection. Specify the name in
   * the following form: `projects/{project}/global/networks/{network_id}` For
   * type = PRIVATE_SERVICE_ACCESS, this field represents servicenetworking VPC,
   * e.g. projects/project-tp/global/networks/servicenetworking. For type =
   * NETAPP_CLOUD_VOLUME, this field represents NetApp service VPC, e.g.
   * projects/project-tp/global/networks/netapp-tenant-vpc. For type =
   * DELL_POWERSCALE, this field represent Dell service VPC, e.g.
   * projects/project-tp/global/networks/dell-tenant-vpc. For type=
   * THIRD_PARTY_SERVICE, this field could represent a consumer VPC or any other
   * producer VPC to which the VMware Engine Network needs to be connected, e.g.
   * projects/project/global/networks/vpc.
   *
   * @var string
   */
  public $serviceNetwork;
  /**
   * Output only. State of the private connection.
   *
   * @var string
   */
  public $state;
  /**
   * Required. Private connection type.
   *
   * @var string
   */
  public $type;
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
   * Required. The relative resource name of Legacy VMware Engine network.
   * Specify the name in the following form: `projects/{project}/locations/{loca
   * tion}/vmwareEngineNetworks/{vmware_engine_network_id}` where `{project}`,
   * `{location}` will be same as specified in private connection resource name
   * and `{vmware_engine_network_id}` will be in the form of
   * `{location}`-default e.g. projects/project/locations/us-
   * central1/vmwareEngineNetworks/us-central1-default.
   *
   * @var string
   */
  public $vmwareEngineNetwork;
  /**
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @var string
   */
  public $vmwareEngineNetworkCanonical;

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
   * Optional. User-provided description for this private connection.
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
   * Output only. The resource name of the private connection. Resource names
   * are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateConnections/my-
   * connection`
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
   * Output only. VPC network peering id between given network VPC and
   * VMwareEngineNetwork.
   *
   * @param string $peeringId
   */
  public function setPeeringId($peeringId)
  {
    $this->peeringId = $peeringId;
  }
  /**
   * @return string
   */
  public function getPeeringId()
  {
    return $this->peeringId;
  }
  /**
   * Output only. Peering state between service network and VMware Engine
   * network.
   *
   * Accepted values: PEERING_STATE_UNSPECIFIED, PEERING_ACTIVE,
   * PEERING_INACTIVE
   *
   * @param self::PEERING_STATE_* $peeringState
   */
  public function setPeeringState($peeringState)
  {
    $this->peeringState = $peeringState;
  }
  /**
   * @return self::PEERING_STATE_*
   */
  public function getPeeringState()
  {
    return $this->peeringState;
  }
  /**
   * Optional. Routing Mode. Default value is set to GLOBAL. For type =
   * PRIVATE_SERVICE_ACCESS, this field can be set to GLOBAL or REGIONAL, for
   * other types only GLOBAL is supported.
   *
   * Accepted values: ROUTING_MODE_UNSPECIFIED, GLOBAL, REGIONAL
   *
   * @param self::ROUTING_MODE_* $routingMode
   */
  public function setRoutingMode($routingMode)
  {
    $this->routingMode = $routingMode;
  }
  /**
   * @return self::ROUTING_MODE_*
   */
  public function getRoutingMode()
  {
    return $this->routingMode;
  }
  /**
   * Required. Service network to create private connection. Specify the name in
   * the following form: `projects/{project}/global/networks/{network_id}` For
   * type = PRIVATE_SERVICE_ACCESS, this field represents servicenetworking VPC,
   * e.g. projects/project-tp/global/networks/servicenetworking. For type =
   * NETAPP_CLOUD_VOLUME, this field represents NetApp service VPC, e.g.
   * projects/project-tp/global/networks/netapp-tenant-vpc. For type =
   * DELL_POWERSCALE, this field represent Dell service VPC, e.g.
   * projects/project-tp/global/networks/dell-tenant-vpc. For type=
   * THIRD_PARTY_SERVICE, this field could represent a consumer VPC or any other
   * producer VPC to which the VMware Engine Network needs to be connected, e.g.
   * projects/project/global/networks/vpc.
   *
   * @param string $serviceNetwork
   */
  public function setServiceNetwork($serviceNetwork)
  {
    $this->serviceNetwork = $serviceNetwork;
  }
  /**
   * @return string
   */
  public function getServiceNetwork()
  {
    return $this->serviceNetwork;
  }
  /**
   * Output only. State of the private connection.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, UPDATING, DELETING,
   * UNPROVISIONED, FAILED
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
   * Required. Private connection type.
   *
   * Accepted values: TYPE_UNSPECIFIED, PRIVATE_SERVICE_ACCESS,
   * NETAPP_CLOUD_VOLUMES, DELL_POWERSCALE, THIRD_PARTY_SERVICE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
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
   * Required. The relative resource name of Legacy VMware Engine network.
   * Specify the name in the following form: `projects/{project}/locations/{loca
   * tion}/vmwareEngineNetworks/{vmware_engine_network_id}` where `{project}`,
   * `{location}` will be same as specified in private connection resource name
   * and `{vmware_engine_network_id}` will be in the form of
   * `{location}`-default e.g. projects/project/locations/us-
   * central1/vmwareEngineNetworks/us-central1-default.
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
  /**
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @param string $vmwareEngineNetworkCanonical
   */
  public function setVmwareEngineNetworkCanonical($vmwareEngineNetworkCanonical)
  {
    $this->vmwareEngineNetworkCanonical = $vmwareEngineNetworkCanonical;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetworkCanonical()
  {
    return $this->vmwareEngineNetworkCanonical;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateConnection::class, 'Google_Service_VMwareEngine_PrivateConnection');
