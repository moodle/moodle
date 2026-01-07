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

class InternalRange extends \Google\Collection
{
  /**
   * If Peering is left unspecified in CreateInternalRange or
   * UpdateInternalRange, it will be defaulted to FOR_SELF.
   */
  public const PEERING_PEERING_UNSPECIFIED = 'PEERING_UNSPECIFIED';
  /**
   * This is the default behavior and represents the case that this internal
   * range is intended to be used in the VPC in which it is created and is
   * accessible from its peers. This implies that peers or peers-of-peers cannot
   * use this range.
   */
  public const PEERING_FOR_SELF = 'FOR_SELF';
  /**
   * This behavior can be set when the internal range is being reserved for
   * usage by peers. This means that no resource within the VPC in which it is
   * being created can use this to associate with a VPC resource, but one of the
   * peers can. This represents donating a range for peers to use.
   */
  public const PEERING_FOR_PEER = 'FOR_PEER';
  /**
   * This behavior can be set when the internal range is being reserved for
   * usage by the VPC in which it is created, but not shared with peers. In a
   * sense, it is local to the VPC. This can be used to create internal ranges
   * for various purposes like HTTP_INTERNAL_LOAD_BALANCER or for Interconnect
   * routes that are not shared with peers. This also implies that peers cannot
   * use this range in a way that is visible to this VPC, but can re-use this
   * range as long as it is NOT_SHARED from the peer VPC, too.
   */
  public const PEERING_NOT_SHARED = 'NOT_SHARED';
  /**
   * Unspecified usage is allowed in calls which identify the resource by other
   * fields and do not need Usage set to complete. These are, i.e.:
   * GetInternalRange and DeleteInternalRange. Usage needs to be specified
   * explicitly in CreateInternalRange or UpdateInternalRange calls.
   */
  public const USAGE_USAGE_UNSPECIFIED = 'USAGE_UNSPECIFIED';
  /**
   * A VPC resource can use the reserved CIDR block by associating it with the
   * internal range resource if usage is set to FOR_VPC.
   */
  public const USAGE_FOR_VPC = 'FOR_VPC';
  /**
   * Ranges created with EXTERNAL_TO_VPC cannot be associated with VPC resources
   * and are meant to block out address ranges for various use cases, like for
   * example, usage on-prem, with dynamic route announcements via interconnect.
   */
  public const USAGE_EXTERNAL_TO_VPC = 'EXTERNAL_TO_VPC';
  /**
   * Ranges created FOR_MIGRATION can be used to lock a CIDR range between a
   * source and target subnet. If usage is set to FOR_MIGRATION, the peering
   * value has to be set to FOR_SELF or default to FOR_SELF when unset.
   */
  public const USAGE_FOR_MIGRATION = 'FOR_MIGRATION';
  protected $collection_key = 'users';
  protected $allocationOptionsType = AllocationOptions::class;
  protected $allocationOptionsDataType = '';
  /**
   * Time when the internal range was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. ExcludeCidrRanges flag. Specifies a set of CIDR blocks that
   * allows exclusion of particular CIDR ranges from the auto-allocation
   * process, without having to reserve these blocks
   *
   * @var string[]
   */
  public $excludeCidrRanges;
  /**
   * Optional. Immutable ranges cannot have their fields modified, except for
   * labels and description.
   *
   * @var bool
   */
  public $immutable;
  /**
   * Optional. The IP range that this internal range defines. NOTE: IPv6 ranges
   * are limited to usage=EXTERNAL_TO_VPC and peering=FOR_SELF. NOTE: For IPv6
   * Ranges this field is compulsory, i.e. the address range must be specified
   * explicitly.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  protected $migrationType = Migration::class;
  protected $migrationDataType = '';
  /**
   * Identifier. The name of an internal range. Format:
   * projects/{project}/locations/{location}/internalRanges/{internal_range}
   * See: https://google.aip.dev/122#fields-representing-resource-names
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The URL or resource ID of the network in which to reserve the
   * internal range. The network cannot be deleted if there are any reserved
   * internal ranges referring to it. Legacy networks are not supported. For
   * example: https://www.googleapis.com/compute/v1/projects/{project}/locations
   * /global/networks/{network}
   * projects/{project}/locations/global/networks/{network} {network}
   *
   * @var string
   */
  public $network;
  /**
   * Optional. Types of resources that are allowed to overlap with the current
   * internal range.
   *
   * @var string[]
   */
  public $overlaps;
  /**
   * Optional. The type of peering set for this internal range.
   *
   * @var string
   */
  public $peering;
  /**
   * Optional. An alternate to ip_cidr_range. Can be set when trying to create
   * an IPv4 reservation that automatically finds a free range of the given
   * size. If both ip_cidr_range and prefix_length are set, there is an error if
   * the range sizes do not match. Can also be used during updates to change the
   * range size. NOTE: For IPv6 this field only works if ip_cidr_range is set as
   * well, and both fields must match. In other words, with IPv6 this field only
   * works as a redundant parameter.
   *
   * @var int
   */
  public $prefixLength;
  /**
   * Optional. Can be set to narrow down or pick a different address space while
   * searching for a free range. If not set, defaults to the ["10.0.0.0/8",
   * "172.16.0.0/12", "192.168.0.0/16"] address space (for auto-mode networks,
   * the "10.0.0.0/9" range is used instead of "10.0.0.0/8"). This can be used
   * to target the search in other rfc-1918 address spaces like "172.16.0.0/12"
   * and "192.168.0.0/16" or non-rfc-1918 address spaces used in the VPC.
   *
   * @var string[]
   */
  public $targetCidrRange;
  /**
   * Time when the internal range was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. The type of usage set for this InternalRange.
   *
   * @var string
   */
  public $usage;
  /**
   * Output only. The list of resources that refer to this internal range.
   * Resources that use the internal range for their range allocation are
   * referred to as users of the range. Other resources mark themselves as users
   * while doing so by creating a reference to this internal range. Having a
   * user, based on this reference, prevents deletion of the internal range
   * referred to. Can be empty.
   *
   * @var string[]
   */
  public $users;

  /**
   * Optional. Range auto-allocation options, may be set only when auto-
   * allocation is selected by not setting ip_cidr_range (and setting
   * prefix_length).
   *
   * @param AllocationOptions $allocationOptions
   */
  public function setAllocationOptions(AllocationOptions $allocationOptions)
  {
    $this->allocationOptions = $allocationOptions;
  }
  /**
   * @return AllocationOptions
   */
  public function getAllocationOptions()
  {
    return $this->allocationOptions;
  }
  /**
   * Time when the internal range was created.
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
   * Optional. A description of this resource.
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
   * Optional. ExcludeCidrRanges flag. Specifies a set of CIDR blocks that
   * allows exclusion of particular CIDR ranges from the auto-allocation
   * process, without having to reserve these blocks
   *
   * @param string[] $excludeCidrRanges
   */
  public function setExcludeCidrRanges($excludeCidrRanges)
  {
    $this->excludeCidrRanges = $excludeCidrRanges;
  }
  /**
   * @return string[]
   */
  public function getExcludeCidrRanges()
  {
    return $this->excludeCidrRanges;
  }
  /**
   * Optional. Immutable ranges cannot have their fields modified, except for
   * labels and description.
   *
   * @param bool $immutable
   */
  public function setImmutable($immutable)
  {
    $this->immutable = $immutable;
  }
  /**
   * @return bool
   */
  public function getImmutable()
  {
    return $this->immutable;
  }
  /**
   * Optional. The IP range that this internal range defines. NOTE: IPv6 ranges
   * are limited to usage=EXTERNAL_TO_VPC and peering=FOR_SELF. NOTE: For IPv6
   * Ranges this field is compulsory, i.e. the address range must be specified
   * explicitly.
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
   * User-defined labels.
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
   * Optional. Must be present if usage is set to FOR_MIGRATION.
   *
   * @param Migration $migration
   */
  public function setMigration(Migration $migration)
  {
    $this->migration = $migration;
  }
  /**
   * @return Migration
   */
  public function getMigration()
  {
    return $this->migration;
  }
  /**
   * Identifier. The name of an internal range. Format:
   * projects/{project}/locations/{location}/internalRanges/{internal_range}
   * See: https://google.aip.dev/122#fields-representing-resource-names
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
   * Immutable. The URL or resource ID of the network in which to reserve the
   * internal range. The network cannot be deleted if there are any reserved
   * internal ranges referring to it. Legacy networks are not supported. For
   * example: https://www.googleapis.com/compute/v1/projects/{project}/locations
   * /global/networks/{network}
   * projects/{project}/locations/global/networks/{network} {network}
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
   * Optional. Types of resources that are allowed to overlap with the current
   * internal range.
   *
   * @param string[] $overlaps
   */
  public function setOverlaps($overlaps)
  {
    $this->overlaps = $overlaps;
  }
  /**
   * @return string[]
   */
  public function getOverlaps()
  {
    return $this->overlaps;
  }
  /**
   * Optional. The type of peering set for this internal range.
   *
   * Accepted values: PEERING_UNSPECIFIED, FOR_SELF, FOR_PEER, NOT_SHARED
   *
   * @param self::PEERING_* $peering
   */
  public function setPeering($peering)
  {
    $this->peering = $peering;
  }
  /**
   * @return self::PEERING_*
   */
  public function getPeering()
  {
    return $this->peering;
  }
  /**
   * Optional. An alternate to ip_cidr_range. Can be set when trying to create
   * an IPv4 reservation that automatically finds a free range of the given
   * size. If both ip_cidr_range and prefix_length are set, there is an error if
   * the range sizes do not match. Can also be used during updates to change the
   * range size. NOTE: For IPv6 this field only works if ip_cidr_range is set as
   * well, and both fields must match. In other words, with IPv6 this field only
   * works as a redundant parameter.
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
   * Optional. Can be set to narrow down or pick a different address space while
   * searching for a free range. If not set, defaults to the ["10.0.0.0/8",
   * "172.16.0.0/12", "192.168.0.0/16"] address space (for auto-mode networks,
   * the "10.0.0.0/9" range is used instead of "10.0.0.0/8"). This can be used
   * to target the search in other rfc-1918 address spaces like "172.16.0.0/12"
   * and "192.168.0.0/16" or non-rfc-1918 address spaces used in the VPC.
   *
   * @param string[] $targetCidrRange
   */
  public function setTargetCidrRange($targetCidrRange)
  {
    $this->targetCidrRange = $targetCidrRange;
  }
  /**
   * @return string[]
   */
  public function getTargetCidrRange()
  {
    return $this->targetCidrRange;
  }
  /**
   * Time when the internal range was updated.
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
  /**
   * Optional. The type of usage set for this InternalRange.
   *
   * Accepted values: USAGE_UNSPECIFIED, FOR_VPC, EXTERNAL_TO_VPC, FOR_MIGRATION
   *
   * @param self::USAGE_* $usage
   */
  public function setUsage($usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return self::USAGE_*
   */
  public function getUsage()
  {
    return $this->usage;
  }
  /**
   * Output only. The list of resources that refer to this internal range.
   * Resources that use the internal range for their range allocation are
   * referred to as users of the range. Other resources mark themselves as users
   * while doing so by creating a reference to this internal range. Having a
   * user, based on this reference, prevents deletion of the internal range
   * referred to. Can be empty.
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
class_alias(InternalRange::class, 'Google_Service_Networkconnectivity_InternalRange');
