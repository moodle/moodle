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

class Address extends \Google\Collection
{
  /**
   * A publicly visible external IP address.
   */
  public const ADDRESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * A private network IP address, for use with an Instance or Internal Load
   * Balancer forwarding rule.
   */
  public const ADDRESS_TYPE_INTERNAL = 'INTERNAL';
  public const ADDRESS_TYPE_UNSPECIFIED_TYPE = 'UNSPECIFIED_TYPE';
  public const IP_VERSION_IPV4 = 'IPV4';
  public const IP_VERSION_IPV6 = 'IPV6';
  public const IP_VERSION_UNSPECIFIED_VERSION = 'UNSPECIFIED_VERSION';
  /**
   * Reserved IPv6 address can be used on network load balancer.
   */
  public const IPV6_ENDPOINT_TYPE_NETLB = 'NETLB';
  /**
   * Reserved IPv6 address can be used on VM.
   */
  public const IPV6_ENDPOINT_TYPE_VM = 'VM';
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  /**
   * DNS resolver address in the subnetwork.
   */
  public const PURPOSE_DNS_RESOLVER = 'DNS_RESOLVER';
  /**
   * VM internal/alias IP, Internal LB service IP, etc.
   */
  public const PURPOSE_GCE_ENDPOINT = 'GCE_ENDPOINT';
  /**
   * A regional internal IP address range reserved for the VLAN attachment that
   * is used in HA VPN over Cloud Interconnect. This regional internal IP
   * address range must not overlap with any IP address range of subnet/route in
   * the VPC network and its peering networks. After the VLAN attachment is
   * created with the reserved IP address range, when creating a new VPN
   * gateway, its interface IP address is allocated from the associated VLAN
   * attachment’s IP address range.
   */
  public const PURPOSE_IPSEC_INTERCONNECT = 'IPSEC_INTERCONNECT';
  /**
   * External IP automatically reserved for Cloud NAT.
   */
  public const PURPOSE_NAT_AUTO = 'NAT_AUTO';
  /**
   * A private network IP address that can be used to configure Private Service
   * Connect. This purpose can be specified only forGLOBAL addresses of Type
   * INTERNAL
   */
  public const PURPOSE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * A regional internal IP address range reserved for Serverless.
   */
  public const PURPOSE_SERVERLESS = 'SERVERLESS';
  /**
   * A private network IP address that can be shared by multiple Internal Load
   * Balancer forwarding rules.
   */
  public const PURPOSE_SHARED_LOADBALANCER_VIP = 'SHARED_LOADBALANCER_VIP';
  /**
   * IP range for peer networks.
   */
  public const PURPOSE_VPC_PEERING = 'VPC_PEERING';
  /**
   * Address is being used by another resource and is not available.
   */
  public const STATUS_IN_USE = 'IN_USE';
  /**
   * Address is reserved and available to use.
   */
  public const STATUS_RESERVED = 'RESERVED';
  /**
   * Address is being reserved.
   */
  public const STATUS_RESERVING = 'RESERVING';
  protected $collection_key = 'users';
  /**
   * The static IP address represented by this resource.
   *
   * @var string
   */
  public $address;
  /**
   * The type of address to reserve, either INTERNAL orEXTERNAL. If unspecified,
   * defaults to EXTERNAL.
   *
   * @var string
   */
  public $addressType;
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
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Reference to the source of external IPv4 addresses, like a
   * PublicDelegatedPrefix (PDP) for BYOIP. The PDP must support enhanced IPv4
   * allocations.
   *
   * Use one of the following formats to specify a PDP when reserving an
   * external IPv4 address using BYOIP.        -     Full resource URL, as inhtt
   * ps://www.googleapis.com/compute/v1/projects/projectId/regions/region/public
   * DelegatedPrefixes/pdp-name    -     Partial URL, as in
   * - projects/projectId/regions/region/publicDelegatedPrefixes/pdp-name
   * - regions/region/publicDelegatedPrefixes/pdp-name
   *
   * @var string
   */
  public $ipCollection;
  /**
   * The IP version that will be used by this address. Valid options areIPV4 or
   * IPV6.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * The endpoint type of this address, which should be VM or NETLB. This is
   * used for deciding which type of endpoint this address can be used after the
   * external IPv6 address reservation.
   *
   * @var string
   */
  public $ipv6EndpointType;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#address for
   * addresses.
   *
   * @var string
   */
  public $kind;
  /**
   * A fingerprint for the labels being applied to this Address, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an Address.
   *
   * @var string
   */
  public $labelFingerprint;
  /**
   * Labels for this resource. These can only be added or modified by
   * thesetLabels method. Each label key/value pair must comply withRFC1035.
   * Label values may be empty.
   *
   * @var string[]
   */
  public $labels;
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
   * The URL of the network in which to reserve the address. This field can only
   * be used with INTERNAL type with theVPC_PEERING purpose.
   *
   * @var string
   */
  public $network;
  /**
   * This signifies the networking tier used for configuring this address and
   * can only take the following values: PREMIUM orSTANDARD. Internal IP
   * addresses are always Premium Tier; global external IP addresses are always
   * Premium Tier; regional external IP addresses can be either Standard or
   * Premium Tier.
   *
   * If this field is not specified, it is assumed to be PREMIUM.
   *
   * @var string
   */
  public $networkTier;
  /**
   * The prefix length if the resource represents an IP range.
   *
   * @var int
   */
  public $prefixLength;
  /**
   * The purpose of this resource, which can be one of the following values:
   * - GCE_ENDPOINT for addresses that are used by VM      instances, alias IP
   * ranges, load balancers, and similar resources.      - DNS_RESOLVER for a
   * DNS resolver address in a subnetwork        for a Cloud DNS  inbound
   * forwarder IP addresses (regional internal IP address in a subnet of
   * a VPC network)      - VPC_PEERING for global internal IP addresses used for
   * private services access allocated ranges.      - NAT_AUTO for the regional
   * external IP addresses used by           Cloud NAT when allocating addresses
   * using                      automatic NAT IP address allocation.      -
   * IPSEC_INTERCONNECT for addresses created from a private      IP range that
   * are reserved for a VLAN attachment in an      *HA VPN over Cloud
   * Interconnect* configuration. These addresses      are regional resources.
   * - `SHARED_LOADBALANCER_VIP` for an internal IP address that is assigned
   * to multiple internal forwarding rules.      - `PRIVATE_SERVICE_CONNECT` for
   * a private network address that is      used to configure Private Service
   * Connect. Only global internal addresses      can use this purpose.
   *
   * @var string
   */
  public $purpose;
  /**
   * Output only. [Output Only] The URL of the region where a regional address
   * resides. For regional addresses, you must specify the region as a path
   * parameter in the HTTP request URL. *This field is not applicable to global
   * addresses.*
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] The status of the address, which can be one
   * ofRESERVING, RESERVED, or IN_USE. An address that is RESERVING is currently
   * in the process of being reserved. A RESERVED address is currently reserved
   * and available to use. An IN_USE address is currently being used by another
   * resource and is not available.
   *
   * @var string
   */
  public $status;
  /**
   * The URL of the subnetwork in which to reserve the address. If an IP address
   * is specified, it must be within the subnetwork's IP range. This field can
   * only be used with INTERNAL type with aGCE_ENDPOINT or DNS_RESOLVER purpose.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * [Output Only] The URLs of the resources that are using this address.
   *
   * @var string[]
   */
  public $users;

  /**
   * The static IP address represented by this resource.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * The type of address to reserve, either INTERNAL orEXTERNAL. If unspecified,
   * defaults to EXTERNAL.
   *
   * Accepted values: EXTERNAL, INTERNAL, UNSPECIFIED_TYPE
   *
   * @param self::ADDRESS_TYPE_* $addressType
   */
  public function setAddressType($addressType)
  {
    $this->addressType = $addressType;
  }
  /**
   * @return self::ADDRESS_TYPE_*
   */
  public function getAddressType()
  {
    return $this->addressType;
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
   * Reference to the source of external IPv4 addresses, like a
   * PublicDelegatedPrefix (PDP) for BYOIP. The PDP must support enhanced IPv4
   * allocations.
   *
   * Use one of the following formats to specify a PDP when reserving an
   * external IPv4 address using BYOIP.        -     Full resource URL, as inhtt
   * ps://www.googleapis.com/compute/v1/projects/projectId/regions/region/public
   * DelegatedPrefixes/pdp-name    -     Partial URL, as in
   * - projects/projectId/regions/region/publicDelegatedPrefixes/pdp-name
   * - regions/region/publicDelegatedPrefixes/pdp-name
   *
   * @param string $ipCollection
   */
  public function setIpCollection($ipCollection)
  {
    $this->ipCollection = $ipCollection;
  }
  /**
   * @return string
   */
  public function getIpCollection()
  {
    return $this->ipCollection;
  }
  /**
   * The IP version that will be used by this address. Valid options areIPV4 or
   * IPV6.
   *
   * Accepted values: IPV4, IPV6, UNSPECIFIED_VERSION
   *
   * @param self::IP_VERSION_* $ipVersion
   */
  public function setIpVersion($ipVersion)
  {
    $this->ipVersion = $ipVersion;
  }
  /**
   * @return self::IP_VERSION_*
   */
  public function getIpVersion()
  {
    return $this->ipVersion;
  }
  /**
   * The endpoint type of this address, which should be VM or NETLB. This is
   * used for deciding which type of endpoint this address can be used after the
   * external IPv6 address reservation.
   *
   * Accepted values: NETLB, VM
   *
   * @param self::IPV6_ENDPOINT_TYPE_* $ipv6EndpointType
   */
  public function setIpv6EndpointType($ipv6EndpointType)
  {
    $this->ipv6EndpointType = $ipv6EndpointType;
  }
  /**
   * @return self::IPV6_ENDPOINT_TYPE_*
   */
  public function getIpv6EndpointType()
  {
    return $this->ipv6EndpointType;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#address for
   * addresses.
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
   * A fingerprint for the labels being applied to this Address, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an Address.
   *
   * @param string $labelFingerprint
   */
  public function setLabelFingerprint($labelFingerprint)
  {
    $this->labelFingerprint = $labelFingerprint;
  }
  /**
   * @return string
   */
  public function getLabelFingerprint()
  {
    return $this->labelFingerprint;
  }
  /**
   * Labels for this resource. These can only be added or modified by
   * thesetLabels method. Each label key/value pair must comply withRFC1035.
   * Label values may be empty.
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
   * The URL of the network in which to reserve the address. This field can only
   * be used with INTERNAL type with theVPC_PEERING purpose.
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
   * This signifies the networking tier used for configuring this address and
   * can only take the following values: PREMIUM orSTANDARD. Internal IP
   * addresses are always Premium Tier; global external IP addresses are always
   * Premium Tier; regional external IP addresses can be either Standard or
   * Premium Tier.
   *
   * If this field is not specified, it is assumed to be PREMIUM.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
   *
   * @param self::NETWORK_TIER_* $networkTier
   */
  public function setNetworkTier($networkTier)
  {
    $this->networkTier = $networkTier;
  }
  /**
   * @return self::NETWORK_TIER_*
   */
  public function getNetworkTier()
  {
    return $this->networkTier;
  }
  /**
   * The prefix length if the resource represents an IP range.
   *
   * @param int $prefixLength
   */
  public function setPrefixLength($prefixLength)
  {
    $this->prefixLength = $prefixLength;
  }
  /**
   * @return int
   */
  public function getPrefixLength()
  {
    return $this->prefixLength;
  }
  /**
   * The purpose of this resource, which can be one of the following values:
   * - GCE_ENDPOINT for addresses that are used by VM      instances, alias IP
   * ranges, load balancers, and similar resources.      - DNS_RESOLVER for a
   * DNS resolver address in a subnetwork        for a Cloud DNS  inbound
   * forwarder IP addresses (regional internal IP address in a subnet of
   * a VPC network)      - VPC_PEERING for global internal IP addresses used for
   * private services access allocated ranges.      - NAT_AUTO for the regional
   * external IP addresses used by           Cloud NAT when allocating addresses
   * using                      automatic NAT IP address allocation.      -
   * IPSEC_INTERCONNECT for addresses created from a private      IP range that
   * are reserved for a VLAN attachment in an      *HA VPN over Cloud
   * Interconnect* configuration. These addresses      are regional resources.
   * - `SHARED_LOADBALANCER_VIP` for an internal IP address that is assigned
   * to multiple internal forwarding rules.      - `PRIVATE_SERVICE_CONNECT` for
   * a private network address that is      used to configure Private Service
   * Connect. Only global internal addresses      can use this purpose.
   *
   * Accepted values: DNS_RESOLVER, GCE_ENDPOINT, IPSEC_INTERCONNECT, NAT_AUTO,
   * PRIVATE_SERVICE_CONNECT, SERVERLESS, SHARED_LOADBALANCER_VIP, VPC_PEERING
   *
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * Output only. [Output Only] The URL of the region where a regional address
   * resides. For regional addresses, you must specify the region as a path
   * parameter in the HTTP request URL. *This field is not applicable to global
   * addresses.*
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
   * [Output Only] Server-defined URL for the resource.
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
   * Output only. [Output Only] The status of the address, which can be one
   * ofRESERVING, RESERVED, or IN_USE. An address that is RESERVING is currently
   * in the process of being reserved. A RESERVED address is currently reserved
   * and available to use. An IN_USE address is currently being used by another
   * resource and is not available.
   *
   * Accepted values: IN_USE, RESERVED, RESERVING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The URL of the subnetwork in which to reserve the address. If an IP address
   * is specified, it must be within the subnetwork's IP range. This field can
   * only be used with INTERNAL type with aGCE_ENDPOINT or DNS_RESOLVER purpose.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * [Output Only] The URLs of the resources that are using this address.
   *
   * @param string[] $users
   */
  public function setUsers($users)
  {
    $this->users = $users;
  }
  /**
   * @return string[]
   */
  public function getUsers()
  {
    return $this->users;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Address::class, 'Google_Service_Compute_Address');
