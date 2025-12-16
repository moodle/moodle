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

class PublicDelegatedPrefixPublicDelegatedSubPrefix extends \Google\Model
{
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
  public const STATUS_ACTIVE = 'ACTIVE';
  public const STATUS_INACTIVE = 'INACTIVE';
  /**
   * The allocatable prefix length supported by this PublicDelegatedSubPrefix.
   *
   * @var int
   */
  public $allocatablePrefixLength;
  /**
   * Name of the project scoping this PublicDelegatedSubPrefix.
   *
   * @var string
   */
  public $delegateeProject;
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
   * The IP address range, in CIDR format, represented by this sub public
   * delegated prefix.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Output only. [Output Only] The internet access type for IPv6 Public
   * Delegated Sub Prefixes. Inherited from parent prefix.
   *
   * @var string
   */
  public $ipv6AccessType;
  /**
   * Whether the sub prefix is delegated to create Address resources in the
   * delegatee project.
   *
   * @var bool
   */
  public $isAddress;
  /**
   * The PublicDelegatedSubPrefix mode for IPv6 only.
   *
   * @var string
   */
  public $mode;
  /**
   * The name of the sub public delegated prefix.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The region of the sub public delegated prefix if
   * it is regional. If absent, the sub prefix is global.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] The status of the sub public delegated prefix.
   *
   * @var string
   */
  public $status;

  /**
   * The allocatable prefix length supported by this PublicDelegatedSubPrefix.
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
   * Name of the project scoping this PublicDelegatedSubPrefix.
   *
   * @param string $delegateeProject
   */
  public function setDelegateeProject($delegateeProject)
  {
    $this->delegateeProject = $delegateeProject;
  }
  /**
   * @return string
   */
  public function getDelegateeProject()
  {
    return $this->delegateeProject;
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
   * The IP address range, in CIDR format, represented by this sub public
   * delegated prefix.
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
   * Delegated Sub Prefixes. Inherited from parent prefix.
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
   * Whether the sub prefix is delegated to create Address resources in the
   * delegatee project.
   *
   * @param bool $isAddress
   */
  public function setIsAddress($isAddress)
  {
    $this->isAddress = $isAddress;
  }
  /**
   * @return bool
   */
  public function getIsAddress()
  {
    return $this->isAddress;
  }
  /**
   * The PublicDelegatedSubPrefix mode for IPv6 only.
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
   * The name of the sub public delegated prefix.
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
   * Output only. [Output Only] The region of the sub public delegated prefix if
   * it is regional. If absent, the sub prefix is global.
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
   * Output only. [Output Only] The status of the sub public delegated prefix.
   *
   * Accepted values: ACTIVE, INACTIVE
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
class_alias(PublicDelegatedPrefixPublicDelegatedSubPrefix::class, 'Google_Service_Compute_PublicDelegatedPrefixPublicDelegatedSubPrefix');
