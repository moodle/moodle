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

namespace Google\Service\VMMigrationService;

class AwsVmDetails extends \Google\Collection
{
  /**
   * The architecture is unknown.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_UNSPECIFIED = 'VM_ARCHITECTURE_UNSPECIFIED';
  /**
   * The architecture is I386.
   */
  public const ARCHITECTURE_I386 = 'I386';
  /**
   * The architecture is X86_64.
   */
  public const ARCHITECTURE_X86_64 = 'X86_64';
  /**
   * The architecture is ARM64.
   */
  public const ARCHITECTURE_ARM64 = 'ARM64';
  /**
   * The architecture is X86_64_MAC.
   */
  public const ARCHITECTURE_X86_64_MAC = 'X86_64_MAC';
  /**
   * The boot option is unknown.
   */
  public const BOOT_OPTION_BOOT_OPTION_UNSPECIFIED = 'BOOT_OPTION_UNSPECIFIED';
  /**
   * The boot option is UEFI.
   */
  public const BOOT_OPTION_EFI = 'EFI';
  /**
   * The boot option is LEGACY-BIOS.
   */
  public const BOOT_OPTION_BIOS = 'BIOS';
  /**
   * Power state is not specified.
   */
  public const POWER_STATE_POWER_STATE_UNSPECIFIED = 'POWER_STATE_UNSPECIFIED';
  /**
   * The VM is turned on.
   */
  public const POWER_STATE_ON = 'ON';
  /**
   * The VM is turned off.
   */
  public const POWER_STATE_OFF = 'OFF';
  /**
   * The VM is suspended. This is similar to hibernation or sleep mode.
   */
  public const POWER_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The VM is starting.
   */
  public const POWER_STATE_PENDING = 'PENDING';
  /**
   * The virtualization type is unknown.
   */
  public const VIRTUALIZATION_TYPE_VM_VIRTUALIZATION_TYPE_UNSPECIFIED = 'VM_VIRTUALIZATION_TYPE_UNSPECIFIED';
  /**
   * The virtualziation type is HVM.
   */
  public const VIRTUALIZATION_TYPE_HVM = 'HVM';
  /**
   * The virtualziation type is PARAVIRTUAL.
   */
  public const VIRTUALIZATION_TYPE_PARAVIRTUAL = 'PARAVIRTUAL';
  protected $collection_key = 'securityGroups';
  /**
   * The CPU architecture.
   *
   * @var string
   */
  public $architecture;
  /**
   * The VM Boot Option.
   *
   * @var string
   */
  public $bootOption;
  /**
   * The total size of the storage allocated to the VM in MB.
   *
   * @var string
   */
  public $committedStorageMb;
  /**
   * The number of CPU cores the VM has.
   *
   * @var int
   */
  public $cpuCount;
  /**
   * The number of disks the VM has.
   *
   * @var int
   */
  public $diskCount;
  /**
   * The display name of the VM. Note that this value is not necessarily unique.
   *
   * @var string
   */
  public $displayName;
  /**
   * The instance type of the VM.
   *
   * @var string
   */
  public $instanceType;
  /**
   * The memory size of the VM in MB.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * The VM's OS.
   *
   * @var string
   */
  public $osDescription;
  /**
   * Output only. The power state of the VM at the moment list was taken.
   *
   * @var string
   */
  public $powerState;
  protected $securityGroupsType = AwsSecurityGroup::class;
  protected $securityGroupsDataType = 'array';
  /**
   * The descriptive name of the AWS's source this VM is connected to.
   *
   * @var string
   */
  public $sourceDescription;
  /**
   * The id of the AWS's source this VM is connected to.
   *
   * @var string
   */
  public $sourceId;
  /**
   * The tags of the VM.
   *
   * @var string[]
   */
  public $tags;
  /**
   * The number of vCPUs the VM has. It is calculated as the number of CPU cores
   * * threads per CPU the VM has.
   *
   * @var int
   */
  public $vcpuCount;
  /**
   * The virtualization type.
   *
   * @var string
   */
  public $virtualizationType;
  /**
   * The VM ID in AWS.
   *
   * @var string
   */
  public $vmId;
  /**
   * The VPC ID the VM belongs to.
   *
   * @var string
   */
  public $vpcId;
  /**
   * The AWS zone of the VM.
   *
   * @var string
   */
  public $zone;

  /**
   * The CPU architecture.
   *
   * Accepted values: VM_ARCHITECTURE_UNSPECIFIED, I386, X86_64, ARM64,
   * X86_64_MAC
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * The VM Boot Option.
   *
   * Accepted values: BOOT_OPTION_UNSPECIFIED, EFI, BIOS
   *
   * @param self::BOOT_OPTION_* $bootOption
   */
  public function setBootOption($bootOption)
  {
    $this->bootOption = $bootOption;
  }
  /**
   * @return self::BOOT_OPTION_*
   */
  public function getBootOption()
  {
    return $this->bootOption;
  }
  /**
   * The total size of the storage allocated to the VM in MB.
   *
   * @param string $committedStorageMb
   */
  public function setCommittedStorageMb($committedStorageMb)
  {
    $this->committedStorageMb = $committedStorageMb;
  }
  /**
   * @return string
   */
  public function getCommittedStorageMb()
  {
    return $this->committedStorageMb;
  }
  /**
   * The number of CPU cores the VM has.
   *
   * @param int $cpuCount
   */
  public function setCpuCount($cpuCount)
  {
    $this->cpuCount = $cpuCount;
  }
  /**
   * @return int
   */
  public function getCpuCount()
  {
    return $this->cpuCount;
  }
  /**
   * The number of disks the VM has.
   *
   * @param int $diskCount
   */
  public function setDiskCount($diskCount)
  {
    $this->diskCount = $diskCount;
  }
  /**
   * @return int
   */
  public function getDiskCount()
  {
    return $this->diskCount;
  }
  /**
   * The display name of the VM. Note that this value is not necessarily unique.
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
   * The instance type of the VM.
   *
   * @param string $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return string
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * The memory size of the VM in MB.
   *
   * @param int $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * The VM's OS.
   *
   * @param string $osDescription
   */
  public function setOsDescription($osDescription)
  {
    $this->osDescription = $osDescription;
  }
  /**
   * @return string
   */
  public function getOsDescription()
  {
    return $this->osDescription;
  }
  /**
   * Output only. The power state of the VM at the moment list was taken.
   *
   * Accepted values: POWER_STATE_UNSPECIFIED, ON, OFF, SUSPENDED, PENDING
   *
   * @param self::POWER_STATE_* $powerState
   */
  public function setPowerState($powerState)
  {
    $this->powerState = $powerState;
  }
  /**
   * @return self::POWER_STATE_*
   */
  public function getPowerState()
  {
    return $this->powerState;
  }
  /**
   * The security groups the VM belongs to.
   *
   * @param AwsSecurityGroup[] $securityGroups
   */
  public function setSecurityGroups($securityGroups)
  {
    $this->securityGroups = $securityGroups;
  }
  /**
   * @return AwsSecurityGroup[]
   */
  public function getSecurityGroups()
  {
    return $this->securityGroups;
  }
  /**
   * The descriptive name of the AWS's source this VM is connected to.
   *
   * @param string $sourceDescription
   */
  public function setSourceDescription($sourceDescription)
  {
    $this->sourceDescription = $sourceDescription;
  }
  /**
   * @return string
   */
  public function getSourceDescription()
  {
    return $this->sourceDescription;
  }
  /**
   * The id of the AWS's source this VM is connected to.
   *
   * @param string $sourceId
   */
  public function setSourceId($sourceId)
  {
    $this->sourceId = $sourceId;
  }
  /**
   * @return string
   */
  public function getSourceId()
  {
    return $this->sourceId;
  }
  /**
   * The tags of the VM.
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
   * The number of vCPUs the VM has. It is calculated as the number of CPU cores
   * * threads per CPU the VM has.
   *
   * @param int $vcpuCount
   */
  public function setVcpuCount($vcpuCount)
  {
    $this->vcpuCount = $vcpuCount;
  }
  /**
   * @return int
   */
  public function getVcpuCount()
  {
    return $this->vcpuCount;
  }
  /**
   * The virtualization type.
   *
   * Accepted values: VM_VIRTUALIZATION_TYPE_UNSPECIFIED, HVM, PARAVIRTUAL
   *
   * @param self::VIRTUALIZATION_TYPE_* $virtualizationType
   */
  public function setVirtualizationType($virtualizationType)
  {
    $this->virtualizationType = $virtualizationType;
  }
  /**
   * @return self::VIRTUALIZATION_TYPE_*
   */
  public function getVirtualizationType()
  {
    return $this->virtualizationType;
  }
  /**
   * The VM ID in AWS.
   *
   * @param string $vmId
   */
  public function setVmId($vmId)
  {
    $this->vmId = $vmId;
  }
  /**
   * @return string
   */
  public function getVmId()
  {
    return $this->vmId;
  }
  /**
   * The VPC ID the VM belongs to.
   *
   * @param string $vpcId
   */
  public function setVpcId($vpcId)
  {
    $this->vpcId = $vpcId;
  }
  /**
   * @return string
   */
  public function getVpcId()
  {
    return $this->vpcId;
  }
  /**
   * The AWS zone of the VM.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsVmDetails::class, 'Google_Service_VMMigrationService_AwsVmDetails');
