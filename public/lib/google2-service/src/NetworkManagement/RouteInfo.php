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

namespace Google\Service\NetworkManagement;

class RouteInfo extends \Google\Collection
{
  /**
   * Unspecified type. Default value.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_TYPE_UNSPECIFIED = 'NEXT_HOP_TYPE_UNSPECIFIED';
  /**
   * Next hop is an IP address.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_IP = 'NEXT_HOP_IP';
  /**
   * Next hop is a Compute Engine instance.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_INSTANCE = 'NEXT_HOP_INSTANCE';
  /**
   * Next hop is a VPC network gateway.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_NETWORK = 'NEXT_HOP_NETWORK';
  /**
   * Next hop is a peering VPC. This scenario only happens when the user doesn't
   * have permissions to the project where the next hop resource is located.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_PEERING = 'NEXT_HOP_PEERING';
  /**
   * Next hop is an interconnect.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_INTERCONNECT = 'NEXT_HOP_INTERCONNECT';
  /**
   * Next hop is a VPN tunnel.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_VPN_TUNNEL = 'NEXT_HOP_VPN_TUNNEL';
  /**
   * Next hop is a VPN gateway. This scenario only happens when tracing
   * connectivity from an on-premises network to Google Cloud through a VPN. The
   * analysis simulates a packet departing from the on-premises network through
   * a VPN tunnel and arriving at a Cloud VPN gateway.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_VPN_GATEWAY = 'NEXT_HOP_VPN_GATEWAY';
  /**
   * Next hop is an internet gateway.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_INTERNET_GATEWAY = 'NEXT_HOP_INTERNET_GATEWAY';
  /**
   * Next hop is blackhole; that is, the next hop either does not exist or is
   * unusable.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_BLACKHOLE = 'NEXT_HOP_BLACKHOLE';
  /**
   * Next hop is the forwarding rule of an Internal Load Balancer.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_ILB = 'NEXT_HOP_ILB';
  /**
   * Next hop is a [router appliance instance](https://cloud.google.com/network-
   * connectivity/docs/network-connectivity-center/concepts/ra-overview).
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_ROUTER_APPLIANCE = 'NEXT_HOP_ROUTER_APPLIANCE';
  /**
   * Next hop is an NCC hub. This scenario only happens when the user doesn't
   * have permissions to the project where the next hop resource is located.
   */
  public const NEXT_HOP_TYPE_NEXT_HOP_NCC_HUB = 'NEXT_HOP_NCC_HUB';
  /**
   * Next hop is Secure Web Proxy Gateway.
   */
  public const NEXT_HOP_TYPE_SECURE_WEB_PROXY_GATEWAY = 'SECURE_WEB_PROXY_GATEWAY';
  /**
   * Unspecified scope. Default value.
   */
  public const ROUTE_SCOPE_ROUTE_SCOPE_UNSPECIFIED = 'ROUTE_SCOPE_UNSPECIFIED';
  /**
   * Route is applicable to packets in Network.
   */
  public const ROUTE_SCOPE_NETWORK = 'NETWORK';
  /**
   * Route is applicable to packets using NCC Hub's routing table.
   */
  public const ROUTE_SCOPE_NCC_HUB = 'NCC_HUB';
  /**
   * Unspecified type. Default value.
   */
  public const ROUTE_TYPE_ROUTE_TYPE_UNSPECIFIED = 'ROUTE_TYPE_UNSPECIFIED';
  /**
   * Route is a subnet route automatically created by the system.
   */
  public const ROUTE_TYPE_SUBNET = 'SUBNET';
  /**
   * Static route created by the user, including the default route to the
   * internet.
   */
  public const ROUTE_TYPE_STATIC = 'STATIC';
  /**
   * Dynamic route exchanged between BGP peers.
   */
  public const ROUTE_TYPE_DYNAMIC = 'DYNAMIC';
  /**
   * A subnet route received from peering network or NCC Hub.
   */
  public const ROUTE_TYPE_PEERING_SUBNET = 'PEERING_SUBNET';
  /**
   * A static route received from peering network.
   */
  public const ROUTE_TYPE_PEERING_STATIC = 'PEERING_STATIC';
  /**
   * A dynamic route received from peering network or NCC Hub.
   */
  public const ROUTE_TYPE_PEERING_DYNAMIC = 'PEERING_DYNAMIC';
  /**
   * Policy based route.
   */
  public const ROUTE_TYPE_POLICY_BASED = 'POLICY_BASED';
  /**
   * Advertised route. Synthetic route which is used to transition from the
   * StartFromPrivateNetwork state in Connectivity tests.
   */
  public const ROUTE_TYPE_ADVERTISED = 'ADVERTISED';
  protected $collection_key = 'srcPortRanges';
  /**
   * For ADVERTISED routes, the URI of their next hop, i.e. the URI of the
   * hybrid endpoint (VPN tunnel, Interconnect attachment, NCC router appliance)
   * the advertised prefix is advertised through, or URI of the source peered
   * network. Deprecated in favor of the next_hop_uri field, not used in new
   * tests.
   *
   * @deprecated
   * @var string
   */
  public $advertisedRouteNextHopUri;
  /**
   * For ADVERTISED dynamic routes, the URI of the Cloud Router that advertised
   * the corresponding IP prefix.
   *
   * @var string
   */
  public $advertisedRouteSourceRouterUri;
  /**
   * Destination IP range of the route.
   *
   * @var string
   */
  public $destIpRange;
  /**
   * Destination port ranges of the route. POLICY_BASED routes only.
   *
   * @var string[]
   */
  public $destPortRanges;
  /**
   * Name of a route.
   *
   * @var string
   */
  public $displayName;
  /**
   * Instance tags of the route.
   *
   * @var string[]
   */
  public $instanceTags;
  /**
   * For PEERING_SUBNET and PEERING_DYNAMIC routes that are advertised by NCC
   * Hub, the URI of the corresponding route in NCC Hub's routing table.
   *
   * @var string
   */
  public $nccHubRouteUri;
  /**
   * URI of the NCC Hub the route is advertised by. PEERING_SUBNET and
   * PEERING_DYNAMIC routes that are advertised by NCC Hub only.
   *
   * @var string
   */
  public $nccHubUri;
  /**
   * URI of the destination NCC Spoke. PEERING_SUBNET and PEERING_DYNAMIC routes
   * that are advertised by NCC Hub only.
   *
   * @var string
   */
  public $nccSpokeUri;
  /**
   * URI of a VPC network where route is located.
   *
   * @var string
   */
  public $networkUri;
  /**
   * String type of the next hop of the route (for example, "VPN tunnel").
   * Deprecated in favor of the next_hop_type and next_hop_uri fields, not used
   * in new tests.
   *
   * @deprecated
   * @var string
   */
  public $nextHop;
  /**
   * URI of a VPC network where the next hop resource is located.
   *
   * @var string
   */
  public $nextHopNetworkUri;
  /**
   * Type of next hop.
   *
   * @var string
   */
  public $nextHopType;
  /**
   * URI of the next hop resource.
   *
   * @var string
   */
  public $nextHopUri;
  /**
   * For PEERING_SUBNET, PEERING_STATIC and PEERING_DYNAMIC routes, the name of
   * the originating SUBNET/STATIC/DYNAMIC route.
   *
   * @var string
   */
  public $originatingRouteDisplayName;
  /**
   * For PEERING_SUBNET and PEERING_STATIC routes, the URI of the originating
   * SUBNET/STATIC route.
   *
   * @var string
   */
  public $originatingRouteUri;
  /**
   * Priority of the route.
   *
   * @var int
   */
  public $priority;
  /**
   * Protocols of the route. POLICY_BASED routes only.
   *
   * @var string[]
   */
  public $protocols;
  /**
   * Region of the route. DYNAMIC, PEERING_DYNAMIC, POLICY_BASED and ADVERTISED
   * routes only. If set for POLICY_BASED route, this is a region of VLAN
   * attachments for Cloud Interconnect the route applies to.
   *
   * @var string
   */
  public $region;
  /**
   * Indicates where route is applicable. Deprecated, routes with NCC_HUB scope
   * are not included in the trace in new tests.
   *
   * @deprecated
   * @var string
   */
  public $routeScope;
  /**
   * Type of route.
   *
   * @var string
   */
  public $routeType;
  /**
   * Source IP address range of the route. POLICY_BASED routes only.
   *
   * @var string
   */
  public $srcIpRange;
  /**
   * Source port ranges of the route. POLICY_BASED routes only.
   *
   * @var string[]
   */
  public $srcPortRanges;
  /**
   * URI of a route. SUBNET, STATIC, PEERING_SUBNET (only for peering network)
   * and POLICY_BASED routes only.
   *
   * @var string
   */
  public $uri;

  /**
   * For ADVERTISED routes, the URI of their next hop, i.e. the URI of the
   * hybrid endpoint (VPN tunnel, Interconnect attachment, NCC router appliance)
   * the advertised prefix is advertised through, or URI of the source peered
   * network. Deprecated in favor of the next_hop_uri field, not used in new
   * tests.
   *
   * @deprecated
   * @param string $advertisedRouteNextHopUri
   */
  public function setAdvertisedRouteNextHopUri($advertisedRouteNextHopUri)
  {
    $this->advertisedRouteNextHopUri = $advertisedRouteNextHopUri;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getAdvertisedRouteNextHopUri()
  {
    return $this->advertisedRouteNextHopUri;
  }
  /**
   * For ADVERTISED dynamic routes, the URI of the Cloud Router that advertised
   * the corresponding IP prefix.
   *
   * @param string $advertisedRouteSourceRouterUri
   */
  public function setAdvertisedRouteSourceRouterUri($advertisedRouteSourceRouterUri)
  {
    $this->advertisedRouteSourceRouterUri = $advertisedRouteSourceRouterUri;
  }
  /**
   * @return string
   */
  public function getAdvertisedRouteSourceRouterUri()
  {
    return $this->advertisedRouteSourceRouterUri;
  }
  /**
   * Destination IP range of the route.
   *
   * @param string $destIpRange
   */
  public function setDestIpRange($destIpRange)
  {
    $this->destIpRange = $destIpRange;
  }
  /**
   * @return string
   */
  public function getDestIpRange()
  {
    return $this->destIpRange;
  }
  /**
   * Destination port ranges of the route. POLICY_BASED routes only.
   *
   * @param string[] $destPortRanges
   */
  public function setDestPortRanges($destPortRanges)
  {
    $this->destPortRanges = $destPortRanges;
  }
  /**
   * @return string[]
   */
  public function getDestPortRanges()
  {
    return $this->destPortRanges;
  }
  /**
   * Name of a route.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Instance tags of the route.
   *
   * @param string[] $instanceTags
   */
  public function setInstanceTags($instanceTags)
  {
    $this->instanceTags = $instanceTags;
  }
  /**
   * @return string[]
   */
  public function getInstanceTags()
  {
    return $this->instanceTags;
  }
  /**
   * For PEERING_SUBNET and PEERING_DYNAMIC routes that are advertised by NCC
   * Hub, the URI of the corresponding route in NCC Hub's routing table.
   *
   * @param string $nccHubRouteUri
   */
  public function setNccHubRouteUri($nccHubRouteUri)
  {
    $this->nccHubRouteUri = $nccHubRouteUri;
  }
  /**
   * @return string
   */
  public function getNccHubRouteUri()
  {
    return $this->nccHubRouteUri;
  }
  /**
   * URI of the NCC Hub the route is advertised by. PEERING_SUBNET and
   * PEERING_DYNAMIC routes that are advertised by NCC Hub only.
   *
   * @param string $nccHubUri
   */
  public function setNccHubUri($nccHubUri)
  {
    $this->nccHubUri = $nccHubUri;
  }
  /**
   * @return string
   */
  public function getNccHubUri()
  {
    return $this->nccHubUri;
  }
  /**
   * URI of the destination NCC Spoke. PEERING_SUBNET and PEERING_DYNAMIC routes
   * that are advertised by NCC Hub only.
   *
   * @param string $nccSpokeUri
   */
  public function setNccSpokeUri($nccSpokeUri)
  {
    $this->nccSpokeUri = $nccSpokeUri;
  }
  /**
   * @return string
   */
  public function getNccSpokeUri()
  {
    return $this->nccSpokeUri;
  }
  /**
   * URI of a VPC network where route is located.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * String type of the next hop of the route (for example, "VPN tunnel").
   * Deprecated in favor of the next_hop_type and next_hop_uri fields, not used
   * in new tests.
   *
   * @deprecated
   * @param string $nextHop
   */
  public function setNextHop($nextHop)
  {
    $this->nextHop = $nextHop;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNextHop()
  {
    return $this->nextHop;
  }
  /**
   * URI of a VPC network where the next hop resource is located.
   *
   * @param string $nextHopNetworkUri
   */
  public function setNextHopNetworkUri($nextHopNetworkUri)
  {
    $this->nextHopNetworkUri = $nextHopNetworkUri;
  }
  /**
   * @return string
   */
  public function getNextHopNetworkUri()
  {
    return $this->nextHopNetworkUri;
  }
  /**
   * Type of next hop.
   *
   * Accepted values: NEXT_HOP_TYPE_UNSPECIFIED, NEXT_HOP_IP, NEXT_HOP_INSTANCE,
   * NEXT_HOP_NETWORK, NEXT_HOP_PEERING, NEXT_HOP_INTERCONNECT,
   * NEXT_HOP_VPN_TUNNEL, NEXT_HOP_VPN_GATEWAY, NEXT_HOP_INTERNET_GATEWAY,
   * NEXT_HOP_BLACKHOLE, NEXT_HOP_ILB, NEXT_HOP_ROUTER_APPLIANCE,
   * NEXT_HOP_NCC_HUB, SECURE_WEB_PROXY_GATEWAY
   *
   * @param self::NEXT_HOP_TYPE_* $nextHopType
   */
  public function setNextHopType($nextHopType)
  {
    $this->nextHopType = $nextHopType;
  }
  /**
   * @return self::NEXT_HOP_TYPE_*
   */
  public function getNextHopType()
  {
    return $this->nextHopType;
  }
  /**
   * URI of the next hop resource.
   *
   * @param string $nextHopUri
   */
  public function setNextHopUri($nextHopUri)
  {
    $this->nextHopUri = $nextHopUri;
  }
  /**
   * @return string
   */
  public function getNextHopUri()
  {
    return $this->nextHopUri;
  }
  /**
   * For PEERING_SUBNET, PEERING_STATIC and PEERING_DYNAMIC routes, the name of
   * the originating SUBNET/STATIC/DYNAMIC route.
   *
   * @param string $originatingRouteDisplayName
   */
  public function setOriginatingRouteDisplayName($originatingRouteDisplayName)
  {
    $this->originatingRouteDisplayName = $originatingRouteDisplayName;
  }
  /**
   * @return string
   */
  public function getOriginatingRouteDisplayName()
  {
    return $this->originatingRouteDisplayName;
  }
  /**
   * For PEERING_SUBNET and PEERING_STATIC routes, the URI of the originating
   * SUBNET/STATIC route.
   *
   * @param string $originatingRouteUri
   */
  public function setOriginatingRouteUri($originatingRouteUri)
  {
    $this->originatingRouteUri = $originatingRouteUri;
  }
  /**
   * @return string
   */
  public function getOriginatingRouteUri()
  {
    return $this->originatingRouteUri;
  }
  /**
   * Priority of the route.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Protocols of the route. POLICY_BASED routes only.
   *
   * @param string[] $protocols
   */
  public function setProtocols($protocols)
  {
    $this->protocols = $protocols;
  }
  /**
   * @return string[]
   */
  public function getProtocols()
  {
    return $this->protocols;
  }
  /**
   * Region of the route. DYNAMIC, PEERING_DYNAMIC, POLICY_BASED and ADVERTISED
   * routes only. If set for POLICY_BASED route, this is a region of VLAN
   * attachments for Cloud Interconnect the route applies to.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Indicates where route is applicable. Deprecated, routes with NCC_HUB scope
   * are not included in the trace in new tests.
   *
   * Accepted values: ROUTE_SCOPE_UNSPECIFIED, NETWORK, NCC_HUB
   *
   * @deprecated
   * @param self::ROUTE_SCOPE_* $routeScope
   */
  public function setRouteScope($routeScope)
  {
    $this->routeScope = $routeScope;
  }
  /**
   * @deprecated
   * @return self::ROUTE_SCOPE_*
   */
  public function getRouteScope()
  {
    return $this->routeScope;
  }
  /**
   * Type of route.
   *
   * Accepted values: ROUTE_TYPE_UNSPECIFIED, SUBNET, STATIC, DYNAMIC,
   * PEERING_SUBNET, PEERING_STATIC, PEERING_DYNAMIC, POLICY_BASED, ADVERTISED
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
   * Source IP address range of the route. POLICY_BASED routes only.
   *
   * @param string $srcIpRange
   */
  public function setSrcIpRange($srcIpRange)
  {
    $this->srcIpRange = $srcIpRange;
  }
  /**
   * @return string
   */
  public function getSrcIpRange()
  {
    return $this->srcIpRange;
  }
  /**
   * Source port ranges of the route. POLICY_BASED routes only.
   *
   * @param string[] $srcPortRanges
   */
  public function setSrcPortRanges($srcPortRanges)
  {
    $this->srcPortRanges = $srcPortRanges;
  }
  /**
   * @return string[]
   */
  public function getSrcPortRanges()
  {
    return $this->srcPortRanges;
  }
  /**
   * URI of a route. SUBNET, STATIC, PEERING_SUBNET (only for peering network)
   * and POLICY_BASED routes only.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouteInfo::class, 'Google_Service_NetworkManagement_RouteInfo');
