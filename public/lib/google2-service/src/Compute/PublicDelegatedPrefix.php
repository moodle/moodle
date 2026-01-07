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

class PublicDelegatedPrefix extends \Google\Collection
{
  /**
   * This public delegated prefix usually takes 4 weeks to delete, and the BGP
   * status cannot be changed. Announce and Withdraw APIs can not be used on
   * this prefix.
   */
  public const BYOIP_API_VERSION_V1 = 'V1';
  /**
   * This public delegated prefix takes minutes to delete. Announce and Withdraw
   * APIs can be used on this prefix to change the BGP status.
   */
  public const BYOIP_API_VERSION_V2 = 'V2';
  /**
   * The parent public advertised prefix will be announced to the internet. All
   * children public delegated prefixes will have IPv6 access type as EXTERNAL.
   */
  public const IPV6_ACCESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * The parent public advertised prefix will not be announced to the internet.
   * Prefix will be used privately within Cloud. All children public delegated
   * prefixes will have IPv6 access type as INTERNAL.
   */
  public const IPV6_ACCESS_TYPE_INTERNAL = 'INTERNAL';
  /**
   * The public delegated prefix is used for further sub-delegation only. Such
   * prefixes cannot set allocatablePrefixLength.
   */
  public const MODE_DELEGATION = 'DELEGATION';
  /**
   * The public delegated prefix is used for creating forwarding rules only.
   * Such prefixes cannot set publicDelegatedSubPrefixes. Parent public
   * delegated prefix must have IPv6 access type as EXTERNAL.
   */
  public const MODE_EXTERNAL_IPV6_FORWARDING_RULE_CREATION = 'EXTERNAL_IPV6_FORWARDING_RULE_CREATION';
  /**
   * The public delegated prefix is used for creating dual-mode subnetworks
   * only. Such prefixes cannot set publicDelegatedSubPrefixes. Parent public
   * delegated prefix must have IPv6 access type as EXTERNAL.
   */
  public const MODE_EXTERNAL_IPV6_SUBNETWORK_CREATION = 'EXTERNAL_IPV6_SUBNETWORK_CREATION';
  /**
   * The public delegated prefix is used for creating dual stack or IPv6-only
   * subnetwork with internal access only. Such prefixes cannot set
   * publicDelegatedSubPrefixes and allocatablePrefixLength. Parent public
   * delegated prefix must have IPv6 access type as INTERNAL.
   */
  public const MODE_INTERNAL_IPV6_SUBNETWORK_CREATION = 'INTERNAL_IPV6_SUBNETWORK_CREATION';
  /**
   * The public delegated prefix is ready to use.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The public delegated prefix is announced and ready to use.
   */
  public const STATUS_ANNOUNCED = 'ANNOUNCED';
  /**
   * The prefix is announced within Google network.
   */
  public const STATUS_ANNOUNCED_TO_GOOGLE = 'ANNOUNCED_TO_GOOGLE';
  /**
   * The prefix is announced to Internet and within Google.
   */
  public const STATUS_ANNOUNCED_TO_INTERNET = 'ANNOUNCED_TO_INTERNET';
  /**
   * The public delegated prefix is being deprovsioned.
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * The public delegated prefix is being initialized and addresses cannot be
   * created yet.
   */
  public const STATUS_INITIALIZING = 'INITIALIZING';
  /**
   * The public delegated prefix is currently withdrawn but ready to be
   * announced.
   */
  public const STATUS_READY_TO_ANNOUNCE = 'READY_TO_ANNOUNCE';
  protected $collection_key = 'publicDelegatedSubPrefixs';
  /**
   * The allocatable prefix length supported by this public delegated prefix.
   * This field is optional and cannot be set for prefixes in DELEGATION mode.
   * It cannot be set for IPv4 prefixes either, and it always defaults to 32.
   *
   * @var int
   */
  public $allocatablePrefixLength;
  /**
   * Output only. [Output Only] The version of BYOIP API.
   *
   * @var string
   */
  public $byoipApiVersion;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] Whether this PDP supports enhanced IPv4
   * allocations. Applicable for IPv4 PDPs only.
   *
   * @var bool
   */
  public $enableEnhancedIpv4Allocation;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a new PublicDelegatedPrefix. An up-to-date fingerprint must be
   * provided in order to update thePublicDelegatedPrefix, otherwise the request
   * will fail with error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * PublicDelegatedPrefix.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * The IP address range, in CIDR format, represented by this public delegated
   * prefix.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Output only. [Output Only] The internet access type for IPv6 Public
   * Delegated Prefixes. Inherited from parent prefix.
   *
   * @var string
   */
  public $ipv6AccessType;
  /**
   * If true, the prefix will be live migrated.
   *
   * @var bool
   */
  public $isLiveMigration;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#publicDelegatedPrefix for public delegated prefixes.
   *
   * @var string
   */
  public $kind;
  /**
   * The public delegated prefix mode for IPv6 only.
   *
   * @var string
   */
  public $mode;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * The URL of parent prefix. Either PublicAdvertisedPrefix or
   * PublicDelegatedPrefix.
   *
   * @var string
   */
  public $parentPrefix;
  protected $publicDelegatedSubPrefixsType = PublicDelegatedPrefixPublicDelegatedSubPrefix::class;
  protected $publicDelegatedSubPrefixsDataType = 'array';
  /**
   * Output only. [Output Only] URL of the region where the public delegated
   * prefix resides. This field applies only to the region resource. You must
   * specify this field as part of the HTTP request URL. It is not settable as a
   * field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] The status of the public delegated prefix, which can be one
   * of following values:              - `INITIALIZING` The public delegated
   * prefix is being initialized and      addresses cannot be created yet.
   * - `READY_TO_ANNOUNCE` The public delegated prefix is a live migration
   * prefix and is active.      - `ANNOUNCED` The public delegated prefix is
   * announced and ready to      use.      - `DELETING` The public delegated
   * prefix is being deprovsioned.      - `ACTIVE` The public delegated prefix
   * is ready to use.
   *
   * @var string
   */
  public $status;

  /**
   * The allocatable prefix length supported by this public delegated prefix.
   * This field is optional and cannot be set for prefixes in DELEGATION mode.
   * It cannot be set for IPv4 prefixes either, and it always defaults to 32.
   *
   * @param int $allocatablePrefixLength
   */
  public function setAllocatablePrefixLength($allocatablePrefixLength)
  {
    $this->allocatablePrefixLength = $allocatablePrefixLength;
  }
  /**
   * @return int
   */
  public function getAllocatablePrefixLength()
  {
    return $this->allocatablePrefixLength;
  }
  /**
   * Output only. [Output Only] The version of BYOIP API.
   *
   * Accepted values: V1, V2
   *
   * @param self::BYOIP_API_VERSION_* $byoipApiVersion
   */
  public function setByoipApiVersion($byoipApiVersion)
  {
    $this->byoipApiVersion = $byoipApiVersion;
  }
  /**
   * @return self::BYOIP_API_VERSION_*
   */
  public function getByoipApiVersion()
  {
    return $this->byoipApiVersion;
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
   * An optional description of this resource. Provide this property when you
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
   * Output only. [Output Only] Whether this PDP supports enhanced IPv4
   * allocations. Applicable for IPv4 PDPs only.
   *
   * @param bool $enableEnhancedIpv4Allocation
   */
  public function setEnableEnhancedIpv4Allocation($enableEnhancedIpv4Allocation)
  {
    $this->enableEnhancedIpv4Allocation = $enableEnhancedIpv4Allocation;
  }
  /**
   * @return bool
   */
  public function getEnableEnhancedIpv4Allocation()
  {
    return $this->enableEnhancedIpv4Allocation;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a new PublicDelegatedPrefix. An up-to-date fingerprint must be
   * provided in order to update thePublicDelegatedPrefix, otherwise the request
   * will fail with error 412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * PublicDelegatedPrefix.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
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
   * The IP address range, in CIDR format, represented by this public delegated
   * prefix.
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
   * Output only. [Output Only] The internet access type for IPv6 Public
   * Delegated Prefixes. Inherited from parent prefix.
   *
   * Accepted values: EXTERNAL, INTERNAL
   *
   * @param self::IPV6_ACCESS_TYPE_* $ipv6AccessType
   */
  public function setIpv6AccessType($ipv6AccessType)
  {
    $this->ipv6AccessType = $ipv6AccessType;
  }
  /**
   * @return self::IPV6_ACCESS_TYPE_*
   */
  public function getIpv6AccessType()
  {
    return $this->ipv6AccessType;
  }
  /**
   * If true, the prefix will be live migrated.
   *
   * @param bool $isLiveMigration
   */
  public function setIsLiveMigration($isLiveMigration)
  {
    $this->isLiveMigration = $isLiveMigration;
  }
  /**
   * @return bool
   */
  public function getIsLiveMigration()
  {
    return $this->isLiveMigration;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#publicDelegatedPrefix for public delegated prefixes.
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
   * The public delegated prefix mode for IPv6 only.
   *
   * Accepted values: DELEGATION, EXTERNAL_IPV6_FORWARDING_RULE_CREATION,
   * EXTERNAL_IPV6_SUBNETWORK_CREATION, INTERNAL_IPV6_SUBNETWORK_CREATION
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * The URL of parent prefix. Either PublicAdvertisedPrefix or
   * PublicDelegatedPrefix.
   *
   * @param string $parentPrefix
   */
  public function setParentPrefix($parentPrefix)
  {
    $this->parentPrefix = $parentPrefix;
  }
  /**
   * @return string
   */
  public function getParentPrefix()
  {
    return $this->parentPrefix;
  }
  /**
   * The list of sub public delegated prefixes that exist for this public
   * delegated prefix.
   *
   * @param PublicDelegatedPrefixPublicDelegatedSubPrefix[] $publicDelegatedSubPrefixs
   */
  public function setPublicDelegatedSubPrefixs($publicDelegatedSubPrefixs)
  {
    $this->publicDelegatedSubPrefixs = $publicDelegatedSubPrefixs;
  }
  /**
   * @return PublicDelegatedPrefixPublicDelegatedSubPrefix[]
   */
  public function getPublicDelegatedSubPrefixs()
  {
    return $this->publicDelegatedSubPrefixs;
  }
  /**
   * Output only. [Output Only] URL of the region where the public delegated
   * prefix resides. This field applies only to the region resource. You must
   * specify this field as part of the HTTP request URL. It is not settable as a
   * field in the request body.
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
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * [Output Only] The status of the public delegated prefix, which can be one
   * of following values:              - `INITIALIZING` The public delegated
   * prefix is being initialized and      addresses cannot be created yet.
   * - `READY_TO_ANNOUNCE` The public delegated prefix is a live migration
   * prefix and is active.      - `ANNOUNCED` The public delegated prefix is
   * announced and ready to      use.      - `DELETING` The public delegated
   * prefix is being deprovsioned.      - `ACTIVE` The public delegated prefix
   * is ready to use.
   *
   * Accepted values: ACTIVE, ANNOUNCED, ANNOUNCED_TO_GOOGLE,
   * ANNOUNCED_TO_INTERNET, DELETING, INITIALIZING, READY_TO_ANNOUNCE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublicDelegatedPrefix::class, 'Google_Service_Compute_PublicDelegatedPrefix');
