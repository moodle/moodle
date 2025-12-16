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

class RouterBgpPeer extends \Google\Collection
{
  public const ADVERTISE_MODE_CUSTOM = 'CUSTOM';
  public const ADVERTISE_MODE_DEFAULT = 'DEFAULT';
  public const ENABLE_FALSE = 'FALSE';
  public const ENABLE_TRUE = 'TRUE';
  /**
   * The BGP peer is automatically created for PARTNER type
   * InterconnectAttachment; Google will automatically create/delete this BGP
   * peer when the PARTNER InterconnectAttachment is created/deleted, and Google
   * will update the ipAddress and peerIpAddress when the PARTNER
   * InterconnectAttachment is provisioned. This type of BGP peer cannot be
   * created or deleted, but can be modified for all fields except for name,
   * ipAddress and peerIpAddress.
   */
  public const MANAGEMENT_TYPE_MANAGED_BY_ATTACHMENT = 'MANAGED_BY_ATTACHMENT';
  /**
   * Default value, the BGP peer is manually created and managed by user.
   */
  public const MANAGEMENT_TYPE_MANAGED_BY_USER = 'MANAGED_BY_USER';
  protected $collection_key = 'importPolicies';
  /**
   * User-specified flag to indicate which mode to use for advertisement.
   *
   * @var string
   */
  public $advertiseMode;
  /**
   * User-specified list of prefix groups to advertise in custom mode, which
   * currently supports the following option:        - ALL_SUBNETS: Advertises
   * all of the router's own VPC subnets. This    excludes any routes learned
   * for subnets that use    VPC Network Peering.
   *
   * Note that this field can only be populated if advertise_mode is CUSTOM and
   * overrides the list defined for the router (in the "bgp" message). These
   * groups are advertised in addition to any specified prefixes. Leave this
   * field blank to advertise no custom groups.
   *
   * @var string[]
   */
  public $advertisedGroups;
  protected $advertisedIpRangesType = RouterAdvertisedIpRange::class;
  protected $advertisedIpRangesDataType = 'array';
  /**
   * The priority of routes advertised to this BGP peer. Where there is more
   * than one matching route of maximum length, the routes with the lowest
   * priority value win.
   *
   * @var string
   */
  public $advertisedRoutePriority;
  protected $bfdType = RouterBgpPeerBfd::class;
  protected $bfdDataType = '';
  protected $customLearnedIpRangesType = RouterBgpPeerCustomLearnedIpRange::class;
  protected $customLearnedIpRangesDataType = 'array';
  /**
   * The user-defined custom learned route priority for a BGP session. This
   * value is applied to all custom learned route ranges for the session. You
   * can choose a value from `0` to `65335`. If you don't provide a value,
   * Google Cloud assigns a priority of `100` to the ranges.
   *
   * @var int
   */
  public $customLearnedRoutePriority;
  /**
   * The status of the BGP peer connection.
   *
   * If set to FALSE, any active session with the peer is terminated and all
   * associated routing information is removed. If set to TRUE, the peer
   * connection can be established with routing information. The default is
   * TRUE.
   *
   * @var string
   */
  public $enable;
  /**
   * Enable IPv4 traffic over BGP Peer. It is enabled by default if the
   * peerIpAddress is version 4.
   *
   * @var bool
   */
  public $enableIpv4;
  /**
   * Enable IPv6 traffic over BGP Peer. It is enabled by default if the
   * peerIpAddress is version 6.
   *
   * @var bool
   */
  public $enableIpv6;
  /**
   * List of export policies applied to this peer, in the order they must be
   * evaluated. The name must correspond to an existing policy that has
   * ROUTE_POLICY_TYPE_EXPORT type.
   *
   * @var string[]
   */
  public $exportPolicies;
  /**
   * List of import policies applied to this peer, in the order they must be
   * evaluated. The name must correspond to an existing policy that has
   * ROUTE_POLICY_TYPE_IMPORT type.
   *
   * @var string[]
   */
  public $importPolicies;
  /**
   * Name of the interface the BGP peer is associated with.
   *
   * @var string
   */
  public $interfaceName;
  /**
   * IP address of the interface inside Google Cloud Platform.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * IPv4 address of the interface inside Google Cloud Platform.
   *
   * @var string
   */
  public $ipv4NexthopAddress;
  /**
   * IPv6 address of the interface inside Google Cloud Platform.
   *
   * @var string
   */
  public $ipv6NexthopAddress;
  /**
   * Output only. [Output Only] The resource that configures and manages this
   * BGP peer.        -  MANAGED_BY_USER is the default value and can be managed
   * by you    or other users    - MANAGED_BY_ATTACHMENT is a BGP peer that is
   * configured and managed    by Cloud Interconnect, specifically by an
   * InterconnectAttachment of type    PARTNER. Google automatically creates,
   * updates, and deletes this type of    BGP peer when the PARTNER
   * InterconnectAttachment is created, updated,    or deleted.
   *
   * @var string
   */
  public $managementType;
  /**
   * Present if MD5 authentication is enabled for the peering. Must be the name
   * of one of the entries in the Router.md5_authentication_keys. The field must
   * comply with RFC1035.
   *
   * @var string
   */
  public $md5AuthenticationKeyName;
  /**
   * Name of this BGP peer. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * Peer BGP Autonomous System Number (ASN). Each BGP interface may use a
   * different value.
   *
   * @var string
   */
  public $peerAsn;
  /**
   * IP address of the BGP interface outside Google Cloud Platform.
   *
   * @var string
   */
  public $peerIpAddress;
  /**
   * IPv4 address of the BGP interface outside Google Cloud Platform.
   *
   * @var string
   */
  public $peerIpv4NexthopAddress;
  /**
   * IPv6 address of the BGP interface outside Google Cloud Platform.
   *
   * @var string
   */
  public $peerIpv6NexthopAddress;
  /**
   * URI of the VM instance that is used as third-party router appliances such
   * as Next Gen Firewalls, Virtual Routers, or Router Appliances. The VM
   * instance must be located in zones contained in the same region as this
   * Cloud Router. The VM instance is the peer side of the BGP session.
   *
   * @var string
   */
  public $routerApplianceInstance;

  /**
   * User-specified flag to indicate which mode to use for advertisement.
   *
   * Accepted values: CUSTOM, DEFAULT
   *
   * @param self::ADVERTISE_MODE_* $advertiseMode
   */
  public function setAdvertiseMode($advertiseMode)
  {
    $this->advertiseMode = $advertiseMode;
  }
  /**
   * @return self::ADVERTISE_MODE_*
   */
  public function getAdvertiseMode()
  {
    return $this->advertiseMode;
  }
  /**
   * User-specified list of prefix groups to advertise in custom mode, which
   * currently supports the following option:        - ALL_SUBNETS: Advertises
   * all of the router's own VPC subnets. This    excludes any routes learned
   * for subnets that use    VPC Network Peering.
   *
   * Note that this field can only be populated if advertise_mode is CUSTOM and
   * overrides the list defined for the router (in the "bgp" message). These
   * groups are advertised in addition to any specified prefixes. Leave this
   * field blank to advertise no custom groups.
   *
   * @param string[] $advertisedGroups
   */
  public function setAdvertisedGroups($advertisedGroups)
  {
    $this->advertisedGroups = $advertisedGroups;
  }
  /**
   * @return string[]
   */
  public function getAdvertisedGroups()
  {
    return $this->advertisedGroups;
  }
  /**
   * User-specified list of individual IP ranges to advertise in custom mode.
   * This field can only be populated if advertise_mode is CUSTOM and overrides
   * the list defined for the router (in the "bgp" message). These IP ranges are
   * advertised in addition to any specified groups. Leave this field blank to
   * advertise no custom IP ranges.
   *
   * @param RouterAdvertisedIpRange[] $advertisedIpRanges
   */
  public function setAdvertisedIpRanges($advertisedIpRanges)
  {
    $this->advertisedIpRanges = $advertisedIpRanges;
  }
  /**
   * @return RouterAdvertisedIpRange[]
   */
  public function getAdvertisedIpRanges()
  {
    return $this->advertisedIpRanges;
  }
  /**
   * The priority of routes advertised to this BGP peer. Where there is more
   * than one matching route of maximum length, the routes with the lowest
   * priority value win.
   *
   * @param string $advertisedRoutePriority
   */
  public function setAdvertisedRoutePriority($advertisedRoutePriority)
  {
    $this->advertisedRoutePriority = $advertisedRoutePriority;
  }
  /**
   * @return string
   */
  public function getAdvertisedRoutePriority()
  {
    return $this->advertisedRoutePriority;
  }
  /**
   * BFD configuration for the BGP peering.
   *
   * @param RouterBgpPeerBfd $bfd
   */
  public function setBfd(RouterBgpPeerBfd $bfd)
  {
    $this->bfd = $bfd;
  }
  /**
   * @return RouterBgpPeerBfd
   */
  public function getBfd()
  {
    return $this->bfd;
  }
  /**
   * A list of user-defined custom learned route IP address ranges for a BGP
   * session.
   *
   * @param RouterBgpPeerCustomLearnedIpRange[] $customLearnedIpRanges
   */
  public function setCustomLearnedIpRanges($customLearnedIpRanges)
  {
    $this->customLearnedIpRanges = $customLearnedIpRanges;
  }
  /**
   * @return RouterBgpPeerCustomLearnedIpRange[]
   */
  public function getCustomLearnedIpRanges()
  {
    return $this->customLearnedIpRanges;
  }
  /**
   * The user-defined custom learned route priority for a BGP session. This
   * value is applied to all custom learned route ranges for the session. You
   * can choose a value from `0` to `65335`. If you don't provide a value,
   * Google Cloud assigns a priority of `100` to the ranges.
   *
   * @param int $customLearnedRoutePriority
   */
  public function setCustomLearnedRoutePriority($customLearnedRoutePriority)
  {
    $this->customLearnedRoutePriority = $customLearnedRoutePriority;
  }
  /**
   * @return int
   */
  public function getCustomLearnedRoutePriority()
  {
    return $this->customLearnedRoutePriority;
  }
  /**
   * The status of the BGP peer connection.
   *
   * If set to FALSE, any active session with the peer is terminated and all
   * associated routing information is removed. If set to TRUE, the peer
   * connection can be established with routing information. The default is
   * TRUE.
   *
   * Accepted values: FALSE, TRUE
   *
   * @param self::ENABLE_* $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return self::ENABLE_*
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Enable IPv4 traffic over BGP Peer. It is enabled by default if the
   * peerIpAddress is version 4.
   *
   * @param bool $enableIpv4
   */
  public function setEnableIpv4($enableIpv4)
  {
    $this->enableIpv4 = $enableIpv4;
  }
  /**
   * @return bool
   */
  public function getEnableIpv4()
  {
    return $this->enableIpv4;
  }
  /**
   * Enable IPv6 traffic over BGP Peer. It is enabled by default if the
   * peerIpAddress is version 6.
   *
   * @param bool $enableIpv6
   */
  public function setEnableIpv6($enableIpv6)
  {
    $this->enableIpv6 = $enableIpv6;
  }
  /**
   * @return bool
   */
  public function getEnableIpv6()
  {
    return $this->enableIpv6;
  }
  /**
   * List of export policies applied to this peer, in the order they must be
   * evaluated. The name must correspond to an existing policy that has
   * ROUTE_POLICY_TYPE_EXPORT type.
   *
   * @param string[] $exportPolicies
   */
  public function setExportPolicies($exportPolicies)
  {
    $this->exportPolicies = $exportPolicies;
  }
  /**
   * @return string[]
   */
  public function getExportPolicies()
  {
    return $this->exportPolicies;
  }
  /**
   * List of import policies applied to this peer, in the order they must be
   * evaluated. The name must correspond to an existing policy that has
   * ROUTE_POLICY_TYPE_IMPORT type.
   *
   * @param string[] $importPolicies
   */
  public function setImportPolicies($importPolicies)
  {
    $this->importPolicies = $importPolicies;
  }
  /**
   * @return string[]
   */
  public function getImportPolicies()
  {
    return $this->importPolicies;
  }
  /**
   * Name of the interface the BGP peer is associated with.
   *
   * @param string $interfaceName
   */
  public function setInterfaceName($interfaceName)
  {
    $this->interfaceName = $interfaceName;
  }
  /**
   * @return string
   */
  public function getInterfaceName()
  {
    return $this->interfaceName;
  }
  /**
   * IP address of the interface inside Google Cloud Platform.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * IPv4 address of the interface inside Google Cloud Platform.
   *
   * @param string $ipv4NexthopAddress
   */
  public function setIpv4NexthopAddress($ipv4NexthopAddress)
  {
    $this->ipv4NexthopAddress = $ipv4NexthopAddress;
  }
  /**
   * @return string
   */
  public function getIpv4NexthopAddress()
  {
    return $this->ipv4NexthopAddress;
  }
  /**
   * IPv6 address of the interface inside Google Cloud Platform.
   *
   * @param string $ipv6NexthopAddress
   */
  public function setIpv6NexthopAddress($ipv6NexthopAddress)
  {
    $this->ipv6NexthopAddress = $ipv6NexthopAddress;
  }
  /**
   * @return string
   */
  public function getIpv6NexthopAddress()
  {
    return $this->ipv6NexthopAddress;
  }
  /**
   * Output only. [Output Only] The resource that configures and manages this
   * BGP peer.        -  MANAGED_BY_USER is the default value and can be managed
   * by you    or other users    - MANAGED_BY_ATTACHMENT is a BGP peer that is
   * configured and managed    by Cloud Interconnect, specifically by an
   * InterconnectAttachment of type    PARTNER. Google automatically creates,
   * updates, and deletes this type of    BGP peer when the PARTNER
   * InterconnectAttachment is created, updated,    or deleted.
   *
   * Accepted values: MANAGED_BY_ATTACHMENT, MANAGED_BY_USER
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
  }
  /**
   * Present if MD5 authentication is enabled for the peering. Must be the name
   * of one of the entries in the Router.md5_authentication_keys. The field must
   * comply with RFC1035.
   *
   * @param string $md5AuthenticationKeyName
   */
  public function setMd5AuthenticationKeyName($md5AuthenticationKeyName)
  {
    $this->md5AuthenticationKeyName = $md5AuthenticationKeyName;
  }
  /**
   * @return string
   */
  public function getMd5AuthenticationKeyName()
  {
    return $this->md5AuthenticationKeyName;
  }
  /**
   * Name of this BGP peer. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * Peer BGP Autonomous System Number (ASN). Each BGP interface may use a
   * different value.
   *
   * @param string $peerAsn
   */
  public function setPeerAsn($peerAsn)
  {
    $this->peerAsn = $peerAsn;
  }
  /**
   * @return string
   */
  public function getPeerAsn()
  {
    return $this->peerAsn;
  }
  /**
   * IP address of the BGP interface outside Google Cloud Platform.
   *
   * @param string $peerIpAddress
   */
  public function setPeerIpAddress($peerIpAddress)
  {
    $this->peerIpAddress = $peerIpAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpAddress()
  {
    return $this->peerIpAddress;
  }
  /**
   * IPv4 address of the BGP interface outside Google Cloud Platform.
   *
   * @param string $peerIpv4NexthopAddress
   */
  public function setPeerIpv4NexthopAddress($peerIpv4NexthopAddress)
  {
    $this->peerIpv4NexthopAddress = $peerIpv4NexthopAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpv4NexthopAddress()
  {
    return $this->peerIpv4NexthopAddress;
  }
  /**
   * IPv6 address of the BGP interface outside Google Cloud Platform.
   *
   * @param string $peerIpv6NexthopAddress
   */
  public function setPeerIpv6NexthopAddress($peerIpv6NexthopAddress)
  {
    $this->peerIpv6NexthopAddress = $peerIpv6NexthopAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpv6NexthopAddress()
  {
    return $this->peerIpv6NexthopAddress;
  }
  /**
   * URI of the VM instance that is used as third-party router appliances such
   * as Next Gen Firewalls, Virtual Routers, or Router Appliances. The VM
   * instance must be located in zones contained in the same region as this
   * Cloud Router. The VM instance is the peer side of the BGP session.
   *
   * @param string $routerApplianceInstance
   */
  public function setRouterApplianceInstance($routerApplianceInstance)
  {
    $this->routerApplianceInstance = $routerApplianceInstance;
  }
  /**
   * @return string
   */
  public function getRouterApplianceInstance()
  {
    return $this->routerApplianceInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterBgpPeer::class, 'Google_Service_Compute_RouterBgpPeer');
