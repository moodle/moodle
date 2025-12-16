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

class UsableSubnetwork extends \Google\Collection
{
  /**
   * VMs on this subnet will be assigned IPv6 addresses that are accessible via
   * the Internet, as well as the VPC network.
   */
  public const IPV6_ACCESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * VMs on this subnet will be assigned IPv6 addresses that are only accessible
   * over the VPC network.
   */
  public const IPV6_ACCESS_TYPE_INTERNAL = 'INTERNAL';
  /**
   * Subnet reserved for Global Envoy-based Load Balancing.
   */
  public const PURPOSE_GLOBAL_MANAGED_PROXY = 'GLOBAL_MANAGED_PROXY';
  /**
   * Subnet reserved for Internal HTTP(S) Load Balancing. This is a legacy
   * purpose, please use REGIONAL_MANAGED_PROXY instead.
   */
  public const PURPOSE_INTERNAL_HTTPS_LOAD_BALANCER = 'INTERNAL_HTTPS_LOAD_BALANCER';
  /**
   * Subnetwork will be used for Migration from one peered VPC to another. (a
   * transient state of subnetwork while migrating resources from one project to
   * another).
   */
  public const PURPOSE_PEER_MIGRATION = 'PEER_MIGRATION';
  /**
   * Regular user created or automatically created subnet.
   */
  public const PURPOSE_PRIVATE = 'PRIVATE';
  /**
   * Subnetwork used as source range for Private NAT Gateways.
   */
  public const PURPOSE_PRIVATE_NAT = 'PRIVATE_NAT';
  /**
   * Regular user created or automatically created subnet.
   */
  public const PURPOSE_PRIVATE_RFC_1918 = 'PRIVATE_RFC_1918';
  /**
   * Subnetworks created for Private Service Connect in the producer network.
   */
  public const PURPOSE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * Subnetwork used for Regional Envoy-based Load Balancing.
   */
  public const PURPOSE_REGIONAL_MANAGED_PROXY = 'REGIONAL_MANAGED_PROXY';
  /**
   * The ACTIVE subnet that is currently used.
   */
  public const ROLE_ACTIVE = 'ACTIVE';
  /**
   * The BACKUP subnet that could be promoted to ACTIVE.
   */
  public const ROLE_BACKUP = 'BACKUP';
  /**
   * New VMs in this subnet can have both IPv4 and IPv6 addresses.
   */
  public const STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  /**
   * New VMs in this subnet will only be assigned IPv4 addresses.
   */
  public const STACK_TYPE_IPV4_ONLY = 'IPV4_ONLY';
  /**
   * New VMs in this subnet will only be assigned IPv6 addresses.
   */
  public const STACK_TYPE_IPV6_ONLY = 'IPV6_ONLY';
  protected $collection_key = 'secondaryIpRanges';
  /**
   * Output only. [Output Only] The external IPv6 address range that is assigned
   * to this subnetwork.
   *
   * @var string
   */
  public $externalIpv6Prefix;
  /**
   * Output only. [Output Only] The internal IPv6 address range that is assigned
   * to this subnetwork.
   *
   * @var string
   */
  public $internalIpv6Prefix;
  /**
   * The range of internal addresses that are owned by this subnetwork.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * The access type of IPv6 address this subnet holds. It's immutable and can
   * only be specified during creation or the first time the subnet is updated
   * into IPV4_IPV6 dual stack.
   *
   * @var string
   */
  public $ipv6AccessType;
  /**
   * Network URL.
   *
   * @var string
   */
  public $network;
  /**
   * @var string
   */
  public $purpose;
  /**
   * The role of subnetwork. Currently, this field is only used when purpose is
   * set to GLOBAL_MANAGED_PROXY orREGIONAL_MANAGED_PROXY. The value can be set
   * toACTIVE or BACKUP. An ACTIVE subnetwork is one that is currently being
   * used for Envoy-based load balancers in a region. A BACKUP subnetwork is one
   * that is ready to be promoted to ACTIVE or is currently draining. This field
   * can be updated with a patch request.
   *
   * @var string
   */
  public $role;
  protected $secondaryIpRangesType = UsableSubnetworkSecondaryRange::class;
  protected $secondaryIpRangesDataType = 'array';
  /**
   * The stack type for the subnet. If set to IPV4_ONLY, new VMs in the subnet
   * are assigned IPv4 addresses only. If set toIPV4_IPV6, new VMs in the subnet
   * can be assigned both IPv4 and IPv6 addresses. If not specified, IPV4_ONLY
   * is used.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * @var string
   */
  public $stackType;
  /**
   * Subnetwork URL.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * Output only. [Output Only] The external IPv6 address range that is assigned
   * to this subnetwork.
   *
   * @param string $externalIpv6Prefix
   */
  public function setExternalIpv6Prefix($externalIpv6Prefix)
  {
    $this->externalIpv6Prefix = $externalIpv6Prefix;
  }
  /**
   * @return string
   */
  public function getExternalIpv6Prefix()
  {
    return $this->externalIpv6Prefix;
  }
  /**
   * Output only. [Output Only] The internal IPv6 address range that is assigned
   * to this subnetwork.
   *
   * @param string $internalIpv6Prefix
   */
  public function setInternalIpv6Prefix($internalIpv6Prefix)
  {
    $this->internalIpv6Prefix = $internalIpv6Prefix;
  }
  /**
   * @return string
   */
  public function getInternalIpv6Prefix()
  {
    return $this->internalIpv6Prefix;
  }
  /**
   * The range of internal addresses that are owned by this subnetwork.
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
   * The access type of IPv6 address this subnet holds. It's immutable and can
   * only be specified during creation or the first time the subnet is updated
   * into IPV4_IPV6 dual stack.
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
   * Network URL.
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
   * The role of subnetwork. Currently, this field is only used when purpose is
   * set to GLOBAL_MANAGED_PROXY orREGIONAL_MANAGED_PROXY. The value can be set
   * toACTIVE or BACKUP. An ACTIVE subnetwork is one that is currently being
   * used for Envoy-based load balancers in a region. A BACKUP subnetwork is one
   * that is ready to be promoted to ACTIVE or is currently draining. This field
   * can be updated with a patch request.
   *
   * Accepted values: ACTIVE, BACKUP
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Secondary IP ranges.
   *
   * @param UsableSubnetworkSecondaryRange[] $secondaryIpRanges
   */
  public function setSecondaryIpRanges($secondaryIpRanges)
  {
    $this->secondaryIpRanges = $secondaryIpRanges;
  }
  /**
   * @return UsableSubnetworkSecondaryRange[]
   */
  public function getSecondaryIpRanges()
  {
    return $this->secondaryIpRanges;
  }
  /**
   * The stack type for the subnet. If set to IPV4_ONLY, new VMs in the subnet
   * are assigned IPv4 addresses only. If set toIPV4_IPV6, new VMs in the subnet
   * can be assigned both IPv4 and IPv6 addresses. If not specified, IPV4_ONLY
   * is used.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * Accepted values: IPV4_IPV6, IPV4_ONLY, IPV6_ONLY
   *
   * @param self::STACK_TYPE_* $stackType
   */
  public function setStackType($stackType)
  {
    $this->stackType = $stackType;
  }
  /**
   * @return self::STACK_TYPE_*
   */
  public function getStackType()
  {
    return $this->stackType;
  }
  /**
   * Subnetwork URL.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsableSubnetwork::class, 'Google_Service_Compute_UsableSubnetwork');
