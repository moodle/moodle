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

class Route extends \Google\Model
{
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
  /**
   * No route type information specified
   */
  public const TYPE_ROUTE_TYPE_UNSPECIFIED = 'ROUTE_TYPE_UNSPECIFIED';
  /**
   * The route leads to a destination within the primary address range of the
   * VPC network's subnet.
   */
  public const TYPE_VPC_PRIMARY_SUBNET = 'VPC_PRIMARY_SUBNET';
  /**
   * The route leads to a destination within the secondary address range of the
   * VPC network's subnet.
   */
  public const TYPE_VPC_SECONDARY_SUBNET = 'VPC_SECONDARY_SUBNET';
  /**
   * The route leads to a destination in a dynamic route. Dynamic routes are
   * derived from Border Gateway Protocol (BGP) advertisements received from an
   * NCC hybrid spoke.
   */
  public const TYPE_DYNAMIC_ROUTE = 'DYNAMIC_ROUTE';
  /**
   * Output only. The time the route was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * An optional description of the route.
   *
   * @var string
   */
  public $description;
  /**
   * The destination IP address range.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Optional labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The origin location of the route. Uses the following form:
   * "projects/{project}/locations/{location}" Example:
   * projects/1234/locations/us-central1
   *
   * @var string
   */
  public $location;
  /**
   * Immutable. The name of the route. Route names must be unique. Route names
   * use the following form: `projects/{project_number}/locations/global/hubs/{h
   * ub}/routeTables/{route_table_id}/routes/{route_id}`
   *
   * @var string
   */
  public $name;
  protected $nextHopInterconnectAttachmentType = NextHopInterconnectAttachment::class;
  protected $nextHopInterconnectAttachmentDataType = '';
  protected $nextHopRouterApplianceInstanceType = NextHopRouterApplianceInstance::class;
  protected $nextHopRouterApplianceInstanceDataType = '';
  protected $nextHopSpokeType = NextHopSpoke::class;
  protected $nextHopSpokeDataType = '';
  protected $nextHopVpcNetworkType = NextHopVpcNetwork::class;
  protected $nextHopVpcNetworkDataType = '';
  protected $nextHopVpnTunnelType = NextHopVPNTunnel::class;
  protected $nextHopVpnTunnelDataType = '';
  /**
   * Output only. The priority of this route. Priority is used to break ties in
   * cases where a destination matches more than one route. In these cases the
   * route with the lowest-numbered priority value wins.
   *
   * @var string
   */
  public $priority;
  /**
   * Immutable. The spoke that this route leads to. Example:
   * projects/12345/locations/global/spokes/SPOKE
   *
   * @var string
   */
  public $spoke;
  /**
   * Output only. The current lifecycle state of the route.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The route's type. Its type is determined by the properties of
   * its IP address range.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The Google-generated UUID for the route. This value is unique
   * across all Network Connectivity Center route resources. If a route is
   * deleted and another with the same name is created, the new route is
   * assigned a different `uid`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time the route was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the route was created.
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
   * An optional description of the route.
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
   * The destination IP address range.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
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
   * Output only. The origin location of the route. Uses the following form:
   * "projects/{project}/locations/{location}" Example:
   * projects/1234/locations/us-central1
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Immutable. The name of the route. Route names must be unique. Route names
   * use the following form: `projects/{project_number}/locations/global/hubs/{h
   * ub}/routeTables/{route_table_id}/routes/{route_id}`
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
   * Immutable. The next-hop VLAN attachment for packets on this route.
   *
   * @param NextHopInterconnectAttachment $nextHopInterconnectAttachment
   */
  public function setNextHopInterconnectAttachment(NextHopInterconnectAttachment $nextHopInterconnectAttachment)
  {
    $this->nextHopInterconnectAttachment = $nextHopInterconnectAttachment;
  }
  /**
   * @return NextHopInterconnectAttachment
   */
  public function getNextHopInterconnectAttachment()
  {
    return $this->nextHopInterconnectAttachment;
  }
  /**
   * Immutable. The next-hop Router appliance instance for packets on this
   * route.
   *
   * @param NextHopRouterApplianceInstance $nextHopRouterApplianceInstance
   */
  public function setNextHopRouterApplianceInstance(NextHopRouterApplianceInstance $nextHopRouterApplianceInstance)
  {
    $this->nextHopRouterApplianceInstance = $nextHopRouterApplianceInstance;
  }
  /**
   * @return NextHopRouterApplianceInstance
   */
  public function getNextHopRouterApplianceInstance()
  {
    return $this->nextHopRouterApplianceInstance;
  }
  /**
   * Immutable. The next-hop spoke for packets on this route.
   *
   * @param NextHopSpoke $nextHopSpoke
   */
  public function setNextHopSpoke(NextHopSpoke $nextHopSpoke)
  {
    $this->nextHopSpoke = $nextHopSpoke;
  }
  /**
   * @return NextHopSpoke
   */
  public function getNextHopSpoke()
  {
    return $this->nextHopSpoke;
  }
  /**
   * Immutable. The destination VPC network for packets on this route.
   *
   * @param NextHopVpcNetwork $nextHopVpcNetwork
   */
  public function setNextHopVpcNetwork(NextHopVpcNetwork $nextHopVpcNetwork)
  {
    $this->nextHopVpcNetwork = $nextHopVpcNetwork;
  }
  /**
   * @return NextHopVpcNetwork
   */
  public function getNextHopVpcNetwork()
  {
    return $this->nextHopVpcNetwork;
  }
  /**
   * Immutable. The next-hop VPN tunnel for packets on this route.
   *
   * @param NextHopVPNTunnel $nextHopVpnTunnel
   */
  public function setNextHopVpnTunnel(NextHopVPNTunnel $nextHopVpnTunnel)
  {
    $this->nextHopVpnTunnel = $nextHopVpnTunnel;
  }
  /**
   * @return NextHopVPNTunnel
   */
  public function getNextHopVpnTunnel()
  {
    return $this->nextHopVpnTunnel;
  }
  /**
   * Output only. The priority of this route. Priority is used to break ties in
   * cases where a destination matches more than one route. In these cases the
   * route with the lowest-numbered priority value wins.
   *
   * @param string $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return string
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Immutable. The spoke that this route leads to. Example:
   * projects/12345/locations/global/spokes/SPOKE
   *
   * @param string $spoke
   */
  public function setSpoke($spoke)
  {
    $this->spoke = $spoke;
  }
  /**
   * @return string
   */
  public function getSpoke()
  {
    return $this->spoke;
  }
  /**
   * Output only. The current lifecycle state of the route.
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
   * Output only. The route's type. Its type is determined by the properties of
   * its IP address range.
   *
   * Accepted values: ROUTE_TYPE_UNSPECIFIED, VPC_PRIMARY_SUBNET,
   * VPC_SECONDARY_SUBNET, DYNAMIC_ROUTE
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
   * Output only. The Google-generated UUID for the route. This value is unique
   * across all Network Connectivity Center route resources. If a route is
   * deleted and another with the same name is created, the new route is
   * assigned a different `uid`.
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
   * Output only. The time the route was last updated.
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
class_alias(Route::class, 'Google_Service_Networkconnectivity_Route');
