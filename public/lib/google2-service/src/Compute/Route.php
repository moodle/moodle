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

namespace Google\Service\Compute;

class Route extends \Google\Collection
{
  public const NEXT_HOP_ORIGIN_EGP = 'EGP';
  public const NEXT_HOP_ORIGIN_IGP = 'IGP';
  public const NEXT_HOP_ORIGIN_INCOMPLETE = 'INCOMPLETE';
  /**
   * This route is processed and active.
   */
  public const ROUTE_STATUS_ACTIVE = 'ACTIVE';
  /**
   * The route is dropped due to the VPC exceeding the dynamic route limit.  For
   * dynamic route limit, please refer to the Learned route example
   */
  public const ROUTE_STATUS_DROPPED = 'DROPPED';
  /**
   * This route is processed but inactive due to failure from the backend. The
   * backend may have rejected the route
   */
  public const ROUTE_STATUS_INACTIVE = 'INACTIVE';
  /**
   * This route is being processed internally. The status will change once
   * processed.
   */
  public const ROUTE_STATUS_PENDING = 'PENDING';
  public const ROUTE_TYPE_BGP = 'BGP';
  public const ROUTE_TYPE_STATIC = 'STATIC';
  public const ROUTE_TYPE_SUBNET = 'SUBNET';
  public const ROUTE_TYPE_TRANSIT = 'TRANSIT';
  protected $collection_key = 'warnings';
  protected $asPathsType = RouteAsPath::class;
  protected $asPathsDataType = 'array';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this field when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * The destination range of outgoing packets that this route applies to. Both
   * IPv4 and IPv6 are supported. Must specify an IPv4 range (e.g. 192.0.2.0/24)
   * or an IPv6 range in RFC 4291 format (e.g. 2001:db8::/32). IPv6 range will
   * be displayed using RFC 5952 compressed format.
   *
   * @var string
   */
  public $destRange;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of this resource. Always compute#routes for
   * Route resources.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
   *
   * @var string
   */
  public $name;
  /**
   * Fully-qualified URL of the network that this route applies to.
   *
   * @var string
   */
  public $network;
  /**
   * The URL to a gateway that should handle matching packets. You can only
   * specify the internet gateway using a full or partial valid URL:
   * projects/project/global/gateways/default-internet-gateway
   *
   * @var string
   */
  public $nextHopGateway;
  /**
   * Output only. [Output Only] The full resource name of the Network
   * Connectivity Center hub that will handle matching packets.
   *
   * @var string
   */
  public $nextHopHub;
  /**
   * The URL to a forwarding rule of typeloadBalancingScheme=INTERNAL that
   * should handle matching packets or the IP address of the forwarding Rule.
   * For example, the following are all valid URLs:               - https://www.
   * googleapis.com/compute/v1/projects/project/regions/region/forwardingRules/f
   * orwardingRule     - regions/region/forwardingRules/forwardingRule
   *
   * If an IP address is provided, must specify an IPv4 address in dot-decimal
   * notation or an IPv6 address in RFC 4291 format. For example, the following
   * are all valid IP addresses:               - 10.128.0.56       -
   * 2001:db8::2d9:51:0:0       - 2001:db8:0:0:2d9:51:0:0
   *
   * IPv6 addresses will be displayed using RFC 5952 compressed format (e.g.
   * 2001:db8::2d9:51:0:0). Should never be an IPv4-mapped IPv6 address.
   *
   * @var string
   */
  public $nextHopIlb;
  /**
   * The URL to an instance that should handle matching packets. You can specify
   * this as a full or partial URL. For example:  https://www.googleapis.com/com
   * pute/v1/projects/project/zones/zone/instances/
   *
   * @var string
   */
  public $nextHopInstance;
  /**
   * Output only. [Output only] Internal fixed region-to-region cost that Google
   * Cloud calculates based on factors such as network performance, distance,
   * and available bandwidth between regions.
   *
   * @var string
   */
  public $nextHopInterRegionCost;
  /**
   * Output only. [Output Only] The URL to an InterconnectAttachment which is
   * the next hop for the route. This field will only be populated for dynamic
   * routes generated by Cloud Router with a linked interconnectAttachment or
   * the static route generated by each L2 Interconnect Attachment.
   *
   * @var string
   */
  public $nextHopInterconnectAttachment;
  /**
   * The network IP address of an instance that should handle matching packets.
   * Both IPv6 address and IPv4 addresses are supported. Must specify an IPv4
   * address in dot-decimal notation (e.g. 192.0.2.99) or an IPv6 address in RFC
   * 4291 format (e.g. 2001:db8::2d9:51:0:0 or 2001:db8:0:0:2d9:51:0:0). IPv6
   * addresses will be displayed using RFC 5952 compressed format (e.g.
   * 2001:db8::2d9:51:0:0). Should never be an IPv4-mapped IPv6 address.
   *
   * @var string
   */
  public $nextHopIp;
  /**
   * Output only. [Output Only] Multi-Exit Discriminator, a BGP route metric
   * that indicates the desirability of a particular route in a network.
   *
   * @var string
   */
  public $nextHopMed;
  /**
   * The URL of the local network if it should handle matching packets.
   *
   * @var string
   */
  public $nextHopNetwork;
  /**
   * Output only. [Output Only] Indicates the origin of the route. Can be IGP
   * (Interior Gateway Protocol), EGP (Exterior Gateway Protocol), or
   * INCOMPLETE.
   *
   * @var string
   */
  public $nextHopOrigin;
  /**
   * Output only. [Output Only] The network peering name that should handle
   * matching packets, which should conform to RFC1035.
   *
   * @var string
   */
  public $nextHopPeering;
  /**
   * The URL to a VpnTunnel that should handle matching packets.
   *
   * @var string
   */
  public $nextHopVpnTunnel;
  protected $paramsType = RouteParams::class;
  protected $paramsDataType = '';
  /**
   * The priority of this route. Priority is used to break ties in cases where
   * there is more than one matching route of equal prefix length. In cases
   * where multiple routes have equal prefix length, the one with the lowest-
   * numbered priority value wins. The default value is `1000`. The priority
   * value must be from `0` to `65535`, inclusive.
   *
   * @var string
   */
  public $priority;
  /**
   * [Output only] The status of the route. This status applies to dynamic
   * routes learned by Cloud Routers. It is also applicable to routes undergoing
   * migration.
   *
   * @var string
   */
  public $routeStatus;
  /**
   * Output only. [Output Only] The type of this route, which can be one of the
   * following values: - 'TRANSIT' for a transit route that this router learned
   * from another Cloud Router and will readvertise to one of its BGP peers  -
   * 'SUBNET' for a route from a subnet of the VPC  - 'BGP' for a route learned
   * from a BGP peer of this router  - 'STATIC' for a static route
   *
   * @var string
   */
  public $routeType;
  /**
   * [Output Only] Server-defined fully-qualified URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * A list of instance tags to which this route applies.
   *
   * @var string[]
   */
  public $tags;
  protected $warningsType = RouteWarnings::class;
  protected $warningsDataType = 'array';

  /**
   * Output only. [Output Only] AS path.
   *
   * @param RouteAsPath[] $asPaths
   */
  public function setAsPaths($asPaths)
  {
    $this->asPaths = $asPaths;
  }
  /**
   * @return RouteAsPath[]
   */
  public function getAsPaths()
  {
    return $this->asPaths;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this field when you
   * create the resource.
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
   * The destination range of outgoing packets that this route applies to. Both
   * IPv4 and IPv6 are supported. Must specify an IPv4 range (e.g. 192.0.2.0/24)
   * or an IPv6 range in RFC 4291 format (e.g. 2001:db8::/32). IPv6 range will
   * be displayed using RFC 5952 compressed format.
   *
   * @param string $destRange
   */
  public function setDestRange($destRange)
  {
    $this->destRange = $destRange;
  }
  /**
   * @return string
   */
  public function getDestRange()
  {
    return $this->destRange;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] Type of this resource. Always compute#routes for
   * Route resources.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?`. The first character must be a
   * lowercase letter, and all following characters (except for the last
   * character) must be a dash, lowercase letter, or digit. The last character
   * must be a lowercase letter or digit.
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
   * Fully-qualified URL of the network that this route applies to.
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
   * The URL to a gateway that should handle matching packets. You can only
   * specify the internet gateway using a full or partial valid URL:
   * projects/project/global/gateways/default-internet-gateway
   *
   * @param string $nextHopGateway
   */
  public function setNextHopGateway($nextHopGateway)
  {
    $this->nextHopGateway = $nextHopGateway;
  }
  /**
   * @return string
   */
  public function getNextHopGateway()
  {
    return $this->nextHopGateway;
  }
  /**
   * Output only. [Output Only] The full resource name of the Network
   * Connectivity Center hub that will handle matching packets.
   *
   * @param string $nextHopHub
   */
  public function setNextHopHub($nextHopHub)
  {
    $this->nextHopHub = $nextHopHub;
  }
  /**
   * @return string
   */
  public function getNextHopHub()
  {
    return $this->nextHopHub;
  }
  /**
   * The URL to a forwarding rule of typeloadBalancingScheme=INTERNAL that
   * should handle matching packets or the IP address of the forwarding Rule.
   * For example, the following are all valid URLs:               - https://www.
   * googleapis.com/compute/v1/projects/project/regions/region/forwardingRules/f
   * orwardingRule     - regions/region/forwardingRules/forwardingRule
   *
   * If an IP address is provided, must specify an IPv4 address in dot-decimal
   * notation or an IPv6 address in RFC 4291 format. For example, the following
   * are all valid IP addresses:               - 10.128.0.56       -
   * 2001:db8::2d9:51:0:0       - 2001:db8:0:0:2d9:51:0:0
   *
   * IPv6 addresses will be displayed using RFC 5952 compressed format (e.g.
   * 2001:db8::2d9:51:0:0). Should never be an IPv4-mapped IPv6 address.
   *
   * @param string $nextHopIlb
   */
  public function setNextHopIlb($nextHopIlb)
  {
    $this->nextHopIlb = $nextHopIlb;
  }
  /**
   * @return string
   */
  public function getNextHopIlb()
  {
    return $this->nextHopIlb;
  }
  /**
   * The URL to an instance that should handle matching packets. You can specify
   * this as a full or partial URL. For example:  https://www.googleapis.com/com
   * pute/v1/projects/project/zones/zone/instances/
   *
   * @param string $nextHopInstance
   */
  public function setNextHopInstance($nextHopInstance)
  {
    $this->nextHopInstance = $nextHopInstance;
  }
  /**
   * @return string
   */
  public function getNextHopInstance()
  {
    return $this->nextHopInstance;
  }
  /**
   * Output only. [Output only] Internal fixed region-to-region cost that Google
   * Cloud calculates based on factors such as network performance, distance,
   * and available bandwidth between regions.
   *
   * @param string $nextHopInterRegionCost
   */
  public function setNextHopInterRegionCost($nextHopInterRegionCost)
  {
    $this->nextHopInterRegionCost = $nextHopInterRegionCost;
  }
  /**
   * @return string
   */
  public function getNextHopInterRegionCost()
  {
    return $this->nextHopInterRegionCost;
  }
  /**
   * Output only. [Output Only] The URL to an InterconnectAttachment which is
   * the next hop for the route. This field will only be populated for dynamic
   * routes generated by Cloud Router with a linked interconnectAttachment or
   * the static route generated by each L2 Interconnect Attachment.
   *
   * @param string $nextHopInterconnectAttachment
   */
  public function setNextHopInterconnectAttachment($nextHopInterconnectAttachment)
  {
    $this->nextHopInterconnectAttachment = $nextHopInterconnectAttachment;
  }
  /**
   * @return string
   */
  public function getNextHopInterconnectAttachment()
  {
    return $this->nextHopInterconnectAttachment;
  }
  /**
   * The network IP address of an instance that should handle matching packets.
   * Both IPv6 address and IPv4 addresses are supported. Must specify an IPv4
   * address in dot-decimal notation (e.g. 192.0.2.99) or an IPv6 address in RFC
   * 4291 format (e.g. 2001:db8::2d9:51:0:0 or 2001:db8:0:0:2d9:51:0:0). IPv6
   * addresses will be displayed using RFC 5952 compressed format (e.g.
   * 2001:db8::2d9:51:0:0). Should never be an IPv4-mapped IPv6 address.
   *
   * @param string $nextHopIp
   */
  public function setNextHopIp($nextHopIp)
  {
    $this->nextHopIp = $nextHopIp;
  }
  /**
   * @return string
   */
  public function getNextHopIp()
  {
    return $this->nextHopIp;
  }
  /**
   * Output only. [Output Only] Multi-Exit Discriminator, a BGP route metric
   * that indicates the desirability of a particular route in a network.
   *
   * @param string $nextHopMed
   */
  public function setNextHopMed($nextHopMed)
  {
    $this->nextHopMed = $nextHopMed;
  }
  /**
   * @return string
   */
  public function getNextHopMed()
  {
    return $this->nextHopMed;
  }
  /**
   * The URL of the local network if it should handle matching packets.
   *
   * @param string $nextHopNetwork
   */
  public function setNextHopNetwork($nextHopNetwork)
  {
    $this->nextHopNetwork = $nextHopNetwork;
  }
  /**
   * @return string
   */
  public function getNextHopNetwork()
  {
    return $this->nextHopNetwork;
  }
  /**
   * Output only. [Output Only] Indicates the origin of the route. Can be IGP
   * (Interior Gateway Protocol), EGP (Exterior Gateway Protocol), or
   * INCOMPLETE.
   *
   * Accepted values: EGP, IGP, INCOMPLETE
   *
   * @param self::NEXT_HOP_ORIGIN_* $nextHopOrigin
   */
  public function setNextHopOrigin($nextHopOrigin)
  {
    $this->nextHopOrigin = $nextHopOrigin;
  }
  /**
   * @return self::NEXT_HOP_ORIGIN_*
   */
  public function getNextHopOrigin()
  {
    return $this->nextHopOrigin;
  }
  /**
   * Output only. [Output Only] The network peering name that should handle
   * matching packets, which should conform to RFC1035.
   *
   * @param string $nextHopPeering
   */
  public function setNextHopPeering($nextHopPeering)
  {
    $this->nextHopPeering = $nextHopPeering;
  }
  /**
   * @return string
   */
  public function getNextHopPeering()
  {
    return $this->nextHopPeering;
  }
  /**
   * The URL to a VpnTunnel that should handle matching packets.
   *
   * @param string $nextHopVpnTunnel
   */
  public function setNextHopVpnTunnel($nextHopVpnTunnel)
  {
    $this->nextHopVpnTunnel = $nextHopVpnTunnel;
  }
  /**
   * @return string
   */
  public function getNextHopVpnTunnel()
  {
    return $this->nextHopVpnTunnel;
  }
  /**
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param RouteParams $params
   */
  public function setParams(RouteParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return RouteParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * The priority of this route. Priority is used to break ties in cases where
   * there is more than one matching route of equal prefix length. In cases
   * where multiple routes have equal prefix length, the one with the lowest-
   * numbered priority value wins. The default value is `1000`. The priority
   * value must be from `0` to `65535`, inclusive.
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
   * [Output only] The status of the route. This status applies to dynamic
   * routes learned by Cloud Routers. It is also applicable to routes undergoing
   * migration.
   *
   * Accepted values: ACTIVE, DROPPED, INACTIVE, PENDING
   *
   * @param self::ROUTE_STATUS_* $routeStatus
   */
  public function setRouteStatus($routeStatus)
  {
    $this->routeStatus = $routeStatus;
  }
  /**
   * @return self::ROUTE_STATUS_*
   */
  public function getRouteStatus()
  {
    return $this->routeStatus;
  }
  /**
   * Output only. [Output Only] The type of this route, which can be one of the
   * following values: - 'TRANSIT' for a transit route that this router learned
   * from another Cloud Router and will readvertise to one of its BGP peers  -
   * 'SUBNET' for a route from a subnet of the VPC  - 'BGP' for a route learned
   * from a BGP peer of this router  - 'STATIC' for a static route
   *
   * Accepted values: BGP, STATIC, SUBNET, TRANSIT
   *
   * @param self::ROUTE_TYPE_* $routeType
   */
  public function setRouteType($routeType)
  {
    $this->routeType = $routeType;
  }
  /**
   * @return self::ROUTE_TYPE_*
   */
  public function getRouteType()
  {
    return $this->routeType;
  }
  /**
   * [Output Only] Server-defined fully-qualified URL for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * A list of instance tags to which this route applies.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Output only. [Output Only] If potential misconfigurations are detected for
   * this route, this field will be populated with warning messages.
   *
   * @param RouteWarnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return RouteWarnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Route::class, 'Google_Service_Compute_Route');
