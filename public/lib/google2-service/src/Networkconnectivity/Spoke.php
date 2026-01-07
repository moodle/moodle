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

class Spoke extends \Google\Collection
{
  /**
   * Unspecified spoke type.
   */
  public const SPOKE_TYPE_SPOKE_TYPE_UNSPECIFIED = 'SPOKE_TYPE_UNSPECIFIED';
  /**
   * Spokes associated with VPN tunnels.
   */
  public const SPOKE_TYPE_VPN_TUNNEL = 'VPN_TUNNEL';
  /**
   * Spokes associated with VLAN attachments.
   */
  public const SPOKE_TYPE_INTERCONNECT_ATTACHMENT = 'INTERCONNECT_ATTACHMENT';
  /**
   * Spokes associated with router appliance instances.
   */
  public const SPOKE_TYPE_ROUTER_APPLIANCE = 'ROUTER_APPLIANCE';
  /**
   * Spokes associated with VPC networks.
   */
  public const SPOKE_TYPE_VPC_NETWORK = 'VPC_NETWORK';
  /**
   * Spokes that are backed by a producer VPC network.
   */
  public const SPOKE_TYPE_PRODUCER_VPC_NETWORK = 'PRODUCER_VPC_NETWORK';
  /**
   * No state information available
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource's create operation is in progress.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The resource is active
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource's delete operation is in progress.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource's accept operation is in progress.
   */
  public const STATE_ACCEPTING = 'ACCEPTING';
  /**
   * The resource's reject operation is in progress.
   */
  public const STATE_REJECTING = 'REJECTING';
  /**
   * The resource's update operation is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The resource is inactive.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The hub associated with this spoke resource has been deleted. This state
   * applies to spoke resources only.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * The resource is in an undefined state due to resource creation or deletion
   * failure. You can try to delete the resource later or contact support for
   * help.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'reasons';
  /**
   * Output only. The time the spoke was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. An optional description of the spoke.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The list of fields waiting for hub administration's approval.
   *
   * @var string[]
   */
  public $fieldPathsPendingUpdate;
  /**
   * Optional. The name of the group that this spoke is associated with.
   *
   * @var string
   */
  public $group;
  /**
   * Immutable. The name of the hub that this spoke is attached to.
   *
   * @var string
   */
  public $hub;
  /**
   * Optional labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @var string[]
   */
  public $labels;
  protected $linkedInterconnectAttachmentsType = LinkedInterconnectAttachments::class;
  protected $linkedInterconnectAttachmentsDataType = '';
  protected $linkedProducerVpcNetworkType = LinkedProducerVpcNetwork::class;
  protected $linkedProducerVpcNetworkDataType = '';
  protected $linkedRouterApplianceInstancesType = LinkedRouterApplianceInstances::class;
  protected $linkedRouterApplianceInstancesDataType = '';
  protected $linkedVpcNetworkType = LinkedVpcNetwork::class;
  protected $linkedVpcNetworkDataType = '';
  protected $linkedVpnTunnelsType = LinkedVpnTunnels::class;
  protected $linkedVpnTunnelsDataType = '';
  /**
   * Immutable. The name of the spoke. Spoke names must be unique. They use the
   * following form:
   * `projects/{project_number}/locations/{region}/spokes/{spoke_id}`
   *
   * @var string
   */
  public $name;
  protected $reasonsType = StateReason::class;
  protected $reasonsDataType = 'array';
  /**
   * Output only. The type of resource associated with the spoke.
   *
   * @var string
   */
  public $spokeType;
  /**
   * Output only. The current lifecycle state of this spoke.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The Google-generated UUID for the spoke. This value is unique
   * across all spoke resources. If a spoke is deleted and another with the same
   * name is created, the new spoke is assigned a different `unique_id`.
   *
   * @var string
   */
  public $uniqueId;
  /**
   * Output only. The time the spoke was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the spoke was created.
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
   * Optional. An optional description of the spoke.
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
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The list of fields waiting for hub administration's approval.
   *
   * @param string[] $fieldPathsPendingUpdate
   */
  public function setFieldPathsPendingUpdate($fieldPathsPendingUpdate)
  {
    $this->fieldPathsPendingUpdate = $fieldPathsPendingUpdate;
  }
  /**
   * @return string[]
   */
  public function getFieldPathsPendingUpdate()
  {
    return $this->fieldPathsPendingUpdate;
  }
  /**
   * Optional. The name of the group that this spoke is associated with.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Immutable. The name of the hub that this spoke is attached to.
   *
   * @param string $hub
   */
  public function setHub($hub)
  {
    $this->hub = $hub;
  }
  /**
   * @return string
   */
  public function getHub()
  {
    return $this->hub;
  }
  /**
   * Optional labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. VLAN attachments that are associated with the spoke.
   *
   * @param LinkedInterconnectAttachments $linkedInterconnectAttachments
   */
  public function setLinkedInterconnectAttachments(LinkedInterconnectAttachments $linkedInterconnectAttachments)
  {
    $this->linkedInterconnectAttachments = $linkedInterconnectAttachments;
  }
  /**
   * @return LinkedInterconnectAttachments
   */
  public function getLinkedInterconnectAttachments()
  {
    return $this->linkedInterconnectAttachments;
  }
  /**
   * Optional. The linked producer VPC that is associated with the spoke.
   *
   * @param LinkedProducerVpcNetwork $linkedProducerVpcNetwork
   */
  public function setLinkedProducerVpcNetwork(LinkedProducerVpcNetwork $linkedProducerVpcNetwork)
  {
    $this->linkedProducerVpcNetwork = $linkedProducerVpcNetwork;
  }
  /**
   * @return LinkedProducerVpcNetwork
   */
  public function getLinkedProducerVpcNetwork()
  {
    return $this->linkedProducerVpcNetwork;
  }
  /**
   * Optional. Router appliance instances that are associated with the spoke.
   *
   * @param LinkedRouterApplianceInstances $linkedRouterApplianceInstances
   */
  public function setLinkedRouterApplianceInstances(LinkedRouterApplianceInstances $linkedRouterApplianceInstances)
  {
    $this->linkedRouterApplianceInstances = $linkedRouterApplianceInstances;
  }
  /**
   * @return LinkedRouterApplianceInstances
   */
  public function getLinkedRouterApplianceInstances()
  {
    return $this->linkedRouterApplianceInstances;
  }
  /**
   * Optional. VPC network that is associated with the spoke.
   *
   * @param LinkedVpcNetwork $linkedVpcNetwork
   */
  public function setLinkedVpcNetwork(LinkedVpcNetwork $linkedVpcNetwork)
  {
    $this->linkedVpcNetwork = $linkedVpcNetwork;
  }
  /**
   * @return LinkedVpcNetwork
   */
  public function getLinkedVpcNetwork()
  {
    return $this->linkedVpcNetwork;
  }
  /**
   * Optional. VPN tunnels that are associated with the spoke.
   *
   * @param LinkedVpnTunnels $linkedVpnTunnels
   */
  public function setLinkedVpnTunnels(LinkedVpnTunnels $linkedVpnTunnels)
  {
    $this->linkedVpnTunnels = $linkedVpnTunnels;
  }
  /**
   * @return LinkedVpnTunnels
   */
  public function getLinkedVpnTunnels()
  {
    return $this->linkedVpnTunnels;
  }
  /**
   * Immutable. The name of the spoke. Spoke names must be unique. They use the
   * following form:
   * `projects/{project_number}/locations/{region}/spokes/{spoke_id}`
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
   * Output only. The reasons for current state of the spoke.
   *
   * @param StateReason[] $reasons
   */
  public function setReasons($reasons)
  {
    $this->reasons = $reasons;
  }
  /**
   * @return StateReason[]
   */
  public function getReasons()
  {
    return $this->reasons;
  }
  /**
   * Output only. The type of resource associated with the spoke.
   *
   * Accepted values: SPOKE_TYPE_UNSPECIFIED, VPN_TUNNEL,
   * INTERCONNECT_ATTACHMENT, ROUTER_APPLIANCE, VPC_NETWORK,
   * PRODUCER_VPC_NETWORK
   *
   * @param self::SPOKE_TYPE_* $spokeType
   */
  public function setSpokeType($spokeType)
  {
    $this->spokeType = $spokeType;
  }
  /**
   * @return self::SPOKE_TYPE_*
   */
  public function getSpokeType()
  {
    return $this->spokeType;
  }
  /**
   * Output only. The current lifecycle state of this spoke.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ACCEPTING,
   * REJECTING, UPDATING, INACTIVE, OBSOLETE, FAILED
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
   * Output only. The Google-generated UUID for the spoke. This value is unique
   * across all spoke resources. If a spoke is deleted and another with the same
   * name is created, the new spoke is assigned a different `unique_id`.
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }
  /**
   * @return string
   */
  public function getUniqueId()
  {
    return $this->uniqueId;
  }
  /**
   * Output only. The time the spoke was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Spoke::class, 'Google_Service_Networkconnectivity_Spoke');
