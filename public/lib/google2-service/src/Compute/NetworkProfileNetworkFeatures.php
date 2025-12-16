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

class NetworkProfileNetworkFeatures extends \Google\Collection
{
  public const ALLOW_ALIAS_IP_RANGES_ALIAS_IP_RANGES_ALLOWED = 'ALIAS_IP_RANGES_ALLOWED';
  public const ALLOW_ALIAS_IP_RANGES_ALIAS_IP_RANGES_BLOCKED = 'ALIAS_IP_RANGES_BLOCKED';
  public const ALLOW_AUTO_MODE_SUBNET_AUTO_MODE_SUBNET_ALLOWED = 'AUTO_MODE_SUBNET_ALLOWED';
  public const ALLOW_AUTO_MODE_SUBNET_AUTO_MODE_SUBNET_BLOCKED = 'AUTO_MODE_SUBNET_BLOCKED';
  public const ALLOW_CLASS_DF_IREWALLS_CLASS_D_FIREWALLS_ALLOWED = 'CLASS_D_FIREWALLS_ALLOWED';
  public const ALLOW_CLASS_DF_IREWALLS_CLASS_D_FIREWALLS_BLOCKED = 'CLASS_D_FIREWALLS_BLOCKED';
  public const ALLOW_CLOUD_NAT_CLOUD_NAT_ALLOWED = 'CLOUD_NAT_ALLOWED';
  public const ALLOW_CLOUD_NAT_CLOUD_NAT_BLOCKED = 'CLOUD_NAT_BLOCKED';
  public const ALLOW_CLOUD_ROUTER_CLOUD_ROUTER_ALLOWED = 'CLOUD_ROUTER_ALLOWED';
  public const ALLOW_CLOUD_ROUTER_CLOUD_ROUTER_BLOCKED = 'CLOUD_ROUTER_BLOCKED';
  public const ALLOW_DEFAULT_NIC_ATTACHMENT_DEFAULT_NIC_ATTACHMENT_ALLOWED = 'DEFAULT_NIC_ATTACHMENT_ALLOWED';
  public const ALLOW_DEFAULT_NIC_ATTACHMENT_DEFAULT_NIC_ATTACHMENT_BLOCKED = 'DEFAULT_NIC_ATTACHMENT_BLOCKED';
  public const ALLOW_EXTERNAL_IP_ACCESS_EXTERNAL_IP_ACCESS_ALLOWED = 'EXTERNAL_IP_ACCESS_ALLOWED';
  public const ALLOW_EXTERNAL_IP_ACCESS_EXTERNAL_IP_ACCESS_BLOCKED = 'EXTERNAL_IP_ACCESS_BLOCKED';
  public const ALLOW_INTERCONNECT_INTERCONNECT_ALLOWED = 'INTERCONNECT_ALLOWED';
  public const ALLOW_INTERCONNECT_INTERCONNECT_BLOCKED = 'INTERCONNECT_BLOCKED';
  public const ALLOW_IP_FORWARDING_IP_FORWARDING_ALLOWED = 'IP_FORWARDING_ALLOWED';
  public const ALLOW_IP_FORWARDING_IP_FORWARDING_BLOCKED = 'IP_FORWARDING_BLOCKED';
  public const ALLOW_LOAD_BALANCING_LOAD_BALANCING_ALLOWED = 'LOAD_BALANCING_ALLOWED';
  public const ALLOW_LOAD_BALANCING_LOAD_BALANCING_BLOCKED = 'LOAD_BALANCING_BLOCKED';
  public const ALLOW_MULTI_NIC_IN_SAME_NETWORK_MULTI_NIC_IN_SAME_NETWORK_ALLOWED = 'MULTI_NIC_IN_SAME_NETWORK_ALLOWED';
  public const ALLOW_MULTI_NIC_IN_SAME_NETWORK_MULTI_NIC_IN_SAME_NETWORK_BLOCKED = 'MULTI_NIC_IN_SAME_NETWORK_BLOCKED';
  public const ALLOW_MULTICAST_MULTICAST_ALLOWED = 'MULTICAST_ALLOWED';
  public const ALLOW_MULTICAST_MULTICAST_BLOCKED = 'MULTICAST_BLOCKED';
  public const ALLOW_NCC_NCC_ALLOWED = 'NCC_ALLOWED';
  public const ALLOW_NCC_NCC_BLOCKED = 'NCC_BLOCKED';
  public const ALLOW_NETWORK_MIGRATION_NETWORK_MIGRATION_ALLOWED = 'NETWORK_MIGRATION_ALLOWED';
  public const ALLOW_NETWORK_MIGRATION_NETWORK_MIGRATION_BLOCKED = 'NETWORK_MIGRATION_BLOCKED';
  public const ALLOW_PACKET_MIRRORING_PACKET_MIRRORING_ALLOWED = 'PACKET_MIRRORING_ALLOWED';
  public const ALLOW_PACKET_MIRRORING_PACKET_MIRRORING_BLOCKED = 'PACKET_MIRRORING_BLOCKED';
  public const ALLOW_PRIVATE_GOOGLE_ACCESS_PRIVATE_GOOGLE_ACCESS_ALLOWED = 'PRIVATE_GOOGLE_ACCESS_ALLOWED';
  public const ALLOW_PRIVATE_GOOGLE_ACCESS_PRIVATE_GOOGLE_ACCESS_BLOCKED = 'PRIVATE_GOOGLE_ACCESS_BLOCKED';
  public const ALLOW_PSC_PSC_ALLOWED = 'PSC_ALLOWED';
  public const ALLOW_PSC_PSC_BLOCKED = 'PSC_BLOCKED';
  public const ALLOW_SAME_NETWORK_UNICAST_SAME_NETWORK_UNICAST_ALLOWED = 'SAME_NETWORK_UNICAST_ALLOWED';
  public const ALLOW_SAME_NETWORK_UNICAST_SAME_NETWORK_UNICAST_BLOCKED = 'SAME_NETWORK_UNICAST_BLOCKED';
  public const ALLOW_STATIC_ROUTES_STATIC_ROUTES_ALLOWED = 'STATIC_ROUTES_ALLOWED';
  public const ALLOW_STATIC_ROUTES_STATIC_ROUTES_BLOCKED = 'STATIC_ROUTES_BLOCKED';
  public const ALLOW_SUB_INTERFACES_SUBINTERFACES_ALLOWED = 'SUBINTERFACES_ALLOWED';
  public const ALLOW_SUB_INTERFACES_SUBINTERFACES_BLOCKED = 'SUBINTERFACES_BLOCKED';
  public const ALLOW_VPC_PEERING_VPC_PEERING_ALLOWED = 'VPC_PEERING_ALLOWED';
  public const ALLOW_VPC_PEERING_VPC_PEERING_BLOCKED = 'VPC_PEERING_BLOCKED';
  public const ALLOW_VPN_VPN_ALLOWED = 'VPN_ALLOWED';
  public const ALLOW_VPN_VPN_BLOCKED = 'VPN_BLOCKED';
  public const MULTICAST_MULTICAST_SDN = 'MULTICAST_SDN';
  public const MULTICAST_MULTICAST_ULL = 'MULTICAST_ULL';
  public const UNICAST_UNICAST_SDN = 'UNICAST_SDN';
  public const UNICAST_UNICAST_ULL = 'UNICAST_ULL';
  protected $collection_key = 'subnetworkStackTypes';
  /**
   * Specifies what address purposes are supported. If empty, all address
   * purposes are supported.
   *
   * @var string[]
   */
  public $addressPurposes;
  /**
   * Specifies whether alias IP ranges (and secondary address ranges) are
   * allowed.
   *
   * @var string
   */
  public $allowAliasIpRanges;
  /**
   * Specifies whether auto mode subnet creation is allowed.
   *
   * @var string
   */
  public $allowAutoModeSubnet;
  /**
   * Specifies whether firewalls for Class D address ranges are supported.
   *
   * @var string
   */
  public $allowClassDFirewalls;
  /**
   * Specifies whether cloud NAT creation is allowed.
   *
   * @var string
   */
  public $allowCloudNat;
  /**
   * Specifies whether cloud router creation is allowed.
   *
   * @var string
   */
  public $allowCloudRouter;
  /**
   * Specifies whether default NIC attachment is allowed.
   *
   * @var string
   */
  public $allowDefaultNicAttachment;
  /**
   * Specifies whether VMs are allowed to have external IP access on network
   * interfaces connected to this VPC.
   *
   * @var string
   */
  public $allowExternalIpAccess;
  /**
   * Specifies whether Cloud Interconnect creation is allowed.
   *
   * @var string
   */
  public $allowInterconnect;
  /**
   * Specifies whether IP forwarding is allowed.
   *
   * @var string
   */
  public $allowIpForwarding;
  /**
   * Specifies whether cloud load balancing is allowed.
   *
   * @var string
   */
  public $allowLoadBalancing;
  /**
   * Specifies whether multi-nic in the same network is allowed.
   *
   * @var string
   */
  public $allowMultiNicInSameNetwork;
  /**
   * Specifies whether multicast is allowed.
   *
   * @var string
   */
  public $allowMulticast;
  /**
   * Specifies whether NCC is allowed.
   *
   * @var string
   */
  public $allowNcc;
  /**
   * Specifies whether VM network migration is allowed.
   *
   * @var string
   */
  public $allowNetworkMigration;
  /**
   * Specifies whether Packet Mirroring 1.0 is supported.
   *
   * @var string
   */
  public $allowPacketMirroring;
  /**
   * Specifies whether private Google access is allowed.
   *
   * @var string
   */
  public $allowPrivateGoogleAccess;
  /**
   * Specifies whether PSC creation is allowed.
   *
   * @var string
   */
  public $allowPsc;
  /**
   * Specifies whether unicast within the same network is allowed.
   *
   * @var string
   */
  public $allowSameNetworkUnicast;
  /**
   * Specifies whether static route creation is allowed.
   *
   * @var string
   */
  public $allowStaticRoutes;
  /**
   * Specifies whether sub interfaces are allowed.
   *
   * @var string
   */
  public $allowSubInterfaces;
  /**
   * Specifies whether VPC peering is allowed.
   *
   * @var string
   */
  public $allowVpcPeering;
  /**
   * Specifies whether VPN creation is allowed.
   *
   * @var string
   */
  public $allowVpn;
  /**
   * If set, limits the interface types that the network supports. If empty, all
   * interface types are supported.
   *
   * @var string[]
   */
  public $interfaceTypes;
  /**
   * Specifies which type of multicast is supported.
   *
   * @var string
   */
  public $multicast;
  /**
   * Specifies which subnetwork purposes are supported.
   *
   * @var string[]
   */
  public $subnetPurposes;
  /**
   * Specifies which subnetwork stack types are supported.
   *
   * @var string[]
   */
  public $subnetStackTypes;
  /**
   * Output only. Specifies which subnetwork purposes are supported.
   *
   * @var string[]
   */
  public $subnetworkPurposes;
  /**
   * Output only. Specifies which subnetwork stack types are supported.
   *
   * @var string[]
   */
  public $subnetworkStackTypes;
  /**
   * Specifies which type of unicast is supported.
   *
   * @var string
   */
  public $unicast;

  /**
   * Specifies what address purposes are supported. If empty, all address
   * purposes are supported.
   *
   * @param string[] $addressPurposes
   */
  public function setAddressPurposes($addressPurposes)
  {
    $this->addressPurposes = $addressPurposes;
  }
  /**
   * @return string[]
   */
  public function getAddressPurposes()
  {
    return $this->addressPurposes;
  }
  /**
   * Specifies whether alias IP ranges (and secondary address ranges) are
   * allowed.
   *
   * Accepted values: ALIAS_IP_RANGES_ALLOWED, ALIAS_IP_RANGES_BLOCKED
   *
   * @param self::ALLOW_ALIAS_IP_RANGES_* $allowAliasIpRanges
   */
  public function setAllowAliasIpRanges($allowAliasIpRanges)
  {
    $this->allowAliasIpRanges = $allowAliasIpRanges;
  }
  /**
   * @return self::ALLOW_ALIAS_IP_RANGES_*
   */
  public function getAllowAliasIpRanges()
  {
    return $this->allowAliasIpRanges;
  }
  /**
   * Specifies whether auto mode subnet creation is allowed.
   *
   * Accepted values: AUTO_MODE_SUBNET_ALLOWED, AUTO_MODE_SUBNET_BLOCKED
   *
   * @param self::ALLOW_AUTO_MODE_SUBNET_* $allowAutoModeSubnet
   */
  public function setAllowAutoModeSubnet($allowAutoModeSubnet)
  {
    $this->allowAutoModeSubnet = $allowAutoModeSubnet;
  }
  /**
   * @return self::ALLOW_AUTO_MODE_SUBNET_*
   */
  public function getAllowAutoModeSubnet()
  {
    return $this->allowAutoModeSubnet;
  }
  /**
   * Specifies whether firewalls for Class D address ranges are supported.
   *
   * Accepted values: CLASS_D_FIREWALLS_ALLOWED, CLASS_D_FIREWALLS_BLOCKED
   *
   * @param self::ALLOW_CLASS_DF_IREWALLS_* $allowClassDFirewalls
   */
  public function setAllowClassDFirewalls($allowClassDFirewalls)
  {
    $this->allowClassDFirewalls = $allowClassDFirewalls;
  }
  /**
   * @return self::ALLOW_CLASS_DF_IREWALLS_*
   */
  public function getAllowClassDFirewalls()
  {
    return $this->allowClassDFirewalls;
  }
  /**
   * Specifies whether cloud NAT creation is allowed.
   *
   * Accepted values: CLOUD_NAT_ALLOWED, CLOUD_NAT_BLOCKED
   *
   * @param self::ALLOW_CLOUD_NAT_* $allowCloudNat
   */
  public function setAllowCloudNat($allowCloudNat)
  {
    $this->allowCloudNat = $allowCloudNat;
  }
  /**
   * @return self::ALLOW_CLOUD_NAT_*
   */
  public function getAllowCloudNat()
  {
    return $this->allowCloudNat;
  }
  /**
   * Specifies whether cloud router creation is allowed.
   *
   * Accepted values: CLOUD_ROUTER_ALLOWED, CLOUD_ROUTER_BLOCKED
   *
   * @param self::ALLOW_CLOUD_ROUTER_* $allowCloudRouter
   */
  public function setAllowCloudRouter($allowCloudRouter)
  {
    $this->allowCloudRouter = $allowCloudRouter;
  }
  /**
   * @return self::ALLOW_CLOUD_ROUTER_*
   */
  public function getAllowCloudRouter()
  {
    return $this->allowCloudRouter;
  }
  /**
   * Specifies whether default NIC attachment is allowed.
   *
   * Accepted values: DEFAULT_NIC_ATTACHMENT_ALLOWED,
   * DEFAULT_NIC_ATTACHMENT_BLOCKED
   *
   * @param self::ALLOW_DEFAULT_NIC_ATTACHMENT_* $allowDefaultNicAttachment
   */
  public function setAllowDefaultNicAttachment($allowDefaultNicAttachment)
  {
    $this->allowDefaultNicAttachment = $allowDefaultNicAttachment;
  }
  /**
   * @return self::ALLOW_DEFAULT_NIC_ATTACHMENT_*
   */
  public function getAllowDefaultNicAttachment()
  {
    return $this->allowDefaultNicAttachment;
  }
  /**
   * Specifies whether VMs are allowed to have external IP access on network
   * interfaces connected to this VPC.
   *
   * Accepted values: EXTERNAL_IP_ACCESS_ALLOWED, EXTERNAL_IP_ACCESS_BLOCKED
   *
   * @param self::ALLOW_EXTERNAL_IP_ACCESS_* $allowExternalIpAccess
   */
  public function setAllowExternalIpAccess($allowExternalIpAccess)
  {
    $this->allowExternalIpAccess = $allowExternalIpAccess;
  }
  /**
   * @return self::ALLOW_EXTERNAL_IP_ACCESS_*
   */
  public function getAllowExternalIpAccess()
  {
    return $this->allowExternalIpAccess;
  }
  /**
   * Specifies whether Cloud Interconnect creation is allowed.
   *
   * Accepted values: INTERCONNECT_ALLOWED, INTERCONNECT_BLOCKED
   *
   * @param self::ALLOW_INTERCONNECT_* $allowInterconnect
   */
  public function setAllowInterconnect($allowInterconnect)
  {
    $this->allowInterconnect = $allowInterconnect;
  }
  /**
   * @return self::ALLOW_INTERCONNECT_*
   */
  public function getAllowInterconnect()
  {
    return $this->allowInterconnect;
  }
  /**
   * Specifies whether IP forwarding is allowed.
   *
   * Accepted values: IP_FORWARDING_ALLOWED, IP_FORWARDING_BLOCKED
   *
   * @param self::ALLOW_IP_FORWARDING_* $allowIpForwarding
   */
  public function setAllowIpForwarding($allowIpForwarding)
  {
    $this->allowIpForwarding = $allowIpForwarding;
  }
  /**
   * @return self::ALLOW_IP_FORWARDING_*
   */
  public function getAllowIpForwarding()
  {
    return $this->allowIpForwarding;
  }
  /**
   * Specifies whether cloud load balancing is allowed.
   *
   * Accepted values: LOAD_BALANCING_ALLOWED, LOAD_BALANCING_BLOCKED
   *
   * @param self::ALLOW_LOAD_BALANCING_* $allowLoadBalancing
   */
  public function setAllowLoadBalancing($allowLoadBalancing)
  {
    $this->allowLoadBalancing = $allowLoadBalancing;
  }
  /**
   * @return self::ALLOW_LOAD_BALANCING_*
   */
  public function getAllowLoadBalancing()
  {
    return $this->allowLoadBalancing;
  }
  /**
   * Specifies whether multi-nic in the same network is allowed.
   *
   * Accepted values: MULTI_NIC_IN_SAME_NETWORK_ALLOWED,
   * MULTI_NIC_IN_SAME_NETWORK_BLOCKED
   *
   * @param self::ALLOW_MULTI_NIC_IN_SAME_NETWORK_* $allowMultiNicInSameNetwork
   */
  public function setAllowMultiNicInSameNetwork($allowMultiNicInSameNetwork)
  {
    $this->allowMultiNicInSameNetwork = $allowMultiNicInSameNetwork;
  }
  /**
   * @return self::ALLOW_MULTI_NIC_IN_SAME_NETWORK_*
   */
  public function getAllowMultiNicInSameNetwork()
  {
    return $this->allowMultiNicInSameNetwork;
  }
  /**
   * Specifies whether multicast is allowed.
   *
   * Accepted values: MULTICAST_ALLOWED, MULTICAST_BLOCKED
   *
   * @param self::ALLOW_MULTICAST_* $allowMulticast
   */
  public function setAllowMulticast($allowMulticast)
  {
    $this->allowMulticast = $allowMulticast;
  }
  /**
   * @return self::ALLOW_MULTICAST_*
   */
  public function getAllowMulticast()
  {
    return $this->allowMulticast;
  }
  /**
   * Specifies whether NCC is allowed.
   *
   * Accepted values: NCC_ALLOWED, NCC_BLOCKED
   *
   * @param self::ALLOW_NCC_* $allowNcc
   */
  public function setAllowNcc($allowNcc)
  {
    $this->allowNcc = $allowNcc;
  }
  /**
   * @return self::ALLOW_NCC_*
   */
  public function getAllowNcc()
  {
    return $this->allowNcc;
  }
  /**
   * Specifies whether VM network migration is allowed.
   *
   * Accepted values: NETWORK_MIGRATION_ALLOWED, NETWORK_MIGRATION_BLOCKED
   *
   * @param self::ALLOW_NETWORK_MIGRATION_* $allowNetworkMigration
   */
  public function setAllowNetworkMigration($allowNetworkMigration)
  {
    $this->allowNetworkMigration = $allowNetworkMigration;
  }
  /**
   * @return self::ALLOW_NETWORK_MIGRATION_*
   */
  public function getAllowNetworkMigration()
  {
    return $this->allowNetworkMigration;
  }
  /**
   * Specifies whether Packet Mirroring 1.0 is supported.
   *
   * Accepted values: PACKET_MIRRORING_ALLOWED, PACKET_MIRRORING_BLOCKED
   *
   * @param self::ALLOW_PACKET_MIRRORING_* $allowPacketMirroring
   */
  public function setAllowPacketMirroring($allowPacketMirroring)
  {
    $this->allowPacketMirroring = $allowPacketMirroring;
  }
  /**
   * @return self::ALLOW_PACKET_MIRRORING_*
   */
  public function getAllowPacketMirroring()
  {
    return $this->allowPacketMirroring;
  }
  /**
   * Specifies whether private Google access is allowed.
   *
   * Accepted values: PRIVATE_GOOGLE_ACCESS_ALLOWED,
   * PRIVATE_GOOGLE_ACCESS_BLOCKED
   *
   * @param self::ALLOW_PRIVATE_GOOGLE_ACCESS_* $allowPrivateGoogleAccess
   */
  public function setAllowPrivateGoogleAccess($allowPrivateGoogleAccess)
  {
    $this->allowPrivateGoogleAccess = $allowPrivateGoogleAccess;
  }
  /**
   * @return self::ALLOW_PRIVATE_GOOGLE_ACCESS_*
   */
  public function getAllowPrivateGoogleAccess()
  {
    return $this->allowPrivateGoogleAccess;
  }
  /**
   * Specifies whether PSC creation is allowed.
   *
   * Accepted values: PSC_ALLOWED, PSC_BLOCKED
   *
   * @param self::ALLOW_PSC_* $allowPsc
   */
  public function setAllowPsc($allowPsc)
  {
    $this->allowPsc = $allowPsc;
  }
  /**
   * @return self::ALLOW_PSC_*
   */
  public function getAllowPsc()
  {
    return $this->allowPsc;
  }
  /**
   * Specifies whether unicast within the same network is allowed.
   *
   * Accepted values: SAME_NETWORK_UNICAST_ALLOWED, SAME_NETWORK_UNICAST_BLOCKED
   *
   * @param self::ALLOW_SAME_NETWORK_UNICAST_* $allowSameNetworkUnicast
   */
  public function setAllowSameNetworkUnicast($allowSameNetworkUnicast)
  {
    $this->allowSameNetworkUnicast = $allowSameNetworkUnicast;
  }
  /**
   * @return self::ALLOW_SAME_NETWORK_UNICAST_*
   */
  public function getAllowSameNetworkUnicast()
  {
    return $this->allowSameNetworkUnicast;
  }
  /**
   * Specifies whether static route creation is allowed.
   *
   * Accepted values: STATIC_ROUTES_ALLOWED, STATIC_ROUTES_BLOCKED
   *
   * @param self::ALLOW_STATIC_ROUTES_* $allowStaticRoutes
   */
  public function setAllowStaticRoutes($allowStaticRoutes)
  {
    $this->allowStaticRoutes = $allowStaticRoutes;
  }
  /**
   * @return self::ALLOW_STATIC_ROUTES_*
   */
  public function getAllowStaticRoutes()
  {
    return $this->allowStaticRoutes;
  }
  /**
   * Specifies whether sub interfaces are allowed.
   *
   * Accepted values: SUBINTERFACES_ALLOWED, SUBINTERFACES_BLOCKED
   *
   * @param self::ALLOW_SUB_INTERFACES_* $allowSubInterfaces
   */
  public function setAllowSubInterfaces($allowSubInterfaces)
  {
    $this->allowSubInterfaces = $allowSubInterfaces;
  }
  /**
   * @return self::ALLOW_SUB_INTERFACES_*
   */
  public function getAllowSubInterfaces()
  {
    return $this->allowSubInterfaces;
  }
  /**
   * Specifies whether VPC peering is allowed.
   *
   * Accepted values: VPC_PEERING_ALLOWED, VPC_PEERING_BLOCKED
   *
   * @param self::ALLOW_VPC_PEERING_* $allowVpcPeering
   */
  public function setAllowVpcPeering($allowVpcPeering)
  {
    $this->allowVpcPeering = $allowVpcPeering;
  }
  /**
   * @return self::ALLOW_VPC_PEERING_*
   */
  public function getAllowVpcPeering()
  {
    return $this->allowVpcPeering;
  }
  /**
   * Specifies whether VPN creation is allowed.
   *
   * Accepted values: VPN_ALLOWED, VPN_BLOCKED
   *
   * @param self::ALLOW_VPN_* $allowVpn
   */
  public function setAllowVpn($allowVpn)
  {
    $this->allowVpn = $allowVpn;
  }
  /**
   * @return self::ALLOW_VPN_*
   */
  public function getAllowVpn()
  {
    return $this->allowVpn;
  }
  /**
   * If set, limits the interface types that the network supports. If empty, all
   * interface types are supported.
   *
   * @param string[] $interfaceTypes
   */
  public function setInterfaceTypes($interfaceTypes)
  {
    $this->interfaceTypes = $interfaceTypes;
  }
  /**
   * @return string[]
   */
  public function getInterfaceTypes()
  {
    return $this->interfaceTypes;
  }
  /**
   * Specifies which type of multicast is supported.
   *
   * Accepted values: MULTICAST_SDN, MULTICAST_ULL
   *
   * @param self::MULTICAST_* $multicast
   */
  public function setMulticast($multicast)
  {
    $this->multicast = $multicast;
  }
  /**
   * @return self::MULTICAST_*
   */
  public function getMulticast()
  {
    return $this->multicast;
  }
  /**
   * Specifies which subnetwork purposes are supported.
   *
   * @param string[] $subnetPurposes
   */
  public function setSubnetPurposes($subnetPurposes)
  {
    $this->subnetPurposes = $subnetPurposes;
  }
  /**
   * @return string[]
   */
  public function getSubnetPurposes()
  {
    return $this->subnetPurposes;
  }
  /**
   * Specifies which subnetwork stack types are supported.
   *
   * @param string[] $subnetStackTypes
   */
  public function setSubnetStackTypes($subnetStackTypes)
  {
    $this->subnetStackTypes = $subnetStackTypes;
  }
  /**
   * @return string[]
   */
  public function getSubnetStackTypes()
  {
    return $this->subnetStackTypes;
  }
  /**
   * Output only. Specifies which subnetwork purposes are supported.
   *
   * @param string[] $subnetworkPurposes
   */
  public function setSubnetworkPurposes($subnetworkPurposes)
  {
    $this->subnetworkPurposes = $subnetworkPurposes;
  }
  /**
   * @return string[]
   */
  public function getSubnetworkPurposes()
  {
    return $this->subnetworkPurposes;
  }
  /**
   * Output only. Specifies which subnetwork stack types are supported.
   *
   * @param string[] $subnetworkStackTypes
   */
  public function setSubnetworkStackTypes($subnetworkStackTypes)
  {
    $this->subnetworkStackTypes = $subnetworkStackTypes;
  }
  /**
   * @return string[]
   */
  public function getSubnetworkStackTypes()
  {
    return $this->subnetworkStackTypes;
  }
  /**
   * Specifies which type of unicast is supported.
   *
   * Accepted values: UNICAST_SDN, UNICAST_ULL
   *
   * @param self::UNICAST_* $unicast
   */
  public function setUnicast($unicast)
  {
    $this->unicast = $unicast;
  }
  /**
   * @return self::UNICAST_*
   */
  public function getUnicast()
  {
    return $this->unicast;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkProfileNetworkFeatures::class, 'Google_Service_Compute_NetworkProfileNetworkFeatures');
