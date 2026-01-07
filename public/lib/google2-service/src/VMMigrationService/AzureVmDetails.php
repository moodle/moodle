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

class AzureVmDetails extends \Google\Collection
{
  /**
   * The architecture is unknown.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_UNSPECIFIED = 'VM_ARCHITECTURE_UNSPECIFIED';
  /**
   * The architecture is one of the x86 architectures.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_X86_FAMILY = 'VM_ARCHITECTURE_X86_FAMILY';
  /**
   * The architecture is ARM64.
   */
  public const ARCHITECTURE_VM_ARCHITECTURE_ARM64 = 'VM_ARCHITECTURE_ARM64';
  /**
   * The boot option is unknown.
   */
  public const BOOT_OPTION_BOOT_OPTION_UNSPECIFIED = 'BOOT_OPTION_UNSPECIFIED';
  /**
   * The boot option is UEFI.
   */
  public const BOOT_OPTION_EFI = 'EFI';
  /**
   * The boot option is BIOS.
   */
  public const BOOT_OPTION_BIOS = 'BIOS';
  /**
   * Power state is not specified.
   */
  public const POWER_STATE_POWER_STATE_UNSPECIFIED = 'POWER_STATE_UNSPECIFIED';
  /**
   * The VM is starting.
   */
  public const POWER_STATE_STARTING = 'STARTING';
  /**
   * The VM is running.
   */
  public const POWER_STATE_RUNNING = 'RUNNING';
  /**
   * The VM is stopping.
   */
  public const POWER_STATE_STOPPING = 'STOPPING';
  /**
   * The VM is stopped.
   */
  public const POWER_STATE_STOPPED = 'STOPPED';
  /**
   * The VM is deallocating.
   */
  public const POWER_STATE_DEALLOCATING = 'DEALLOCATING';
  /**
   * The VM is deallocated.
   */
  public const POWER_STATE_DEALLOCATED = 'DEALLOCATED';
  /**
   * The VM's power state is unknown.
   */
  public const POWER_STATE_UNKNOWN = 'UNKNOWN';
  protected $collection_key = 'disks';
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
   * The VM's ComputerName.
   *
   * @var string
   */
  public $computerName;
  /**
   * The number of cpus the VM has.
   *
   * @var int
   */
  public $cpuCount;
  /**
   * The number of disks the VM has, including OS disk.
   *
   * @var int
   */
  public $diskCount;
  protected $disksType = Disk::class;
  protected $disksDataType = 'array';
  /**
   * The memory size of the VM in MB.
   *
   * @var int
   */
  public $memoryMb;
  protected $osDescriptionType = OSDescription::class;
  protected $osDescriptionDataType = '';
  protected $osDiskType = OSDisk::class;
  protected $osDiskDataType = '';
  /**
   * The power state of the VM at the moment list was taken.
   *
   * @var string
   */
  public $powerState;
  /**
   * The tags of the VM.
   *
   * @var string[]
   */
  public $tags;
  /**
   * The VM full path in Azure.
   *
   * @var string
   */
  public $vmId;
  /**
   * VM size as configured in Azure. Determines the VM's hardware spec.
   *
   * @var string
   */
  public $vmSize;

  /**
   * The CPU architecture.
   *
   * Accepted values: VM_ARCHITECTURE_UNSPECIFIED, VM_ARCHITECTURE_X86_FAMILY,
   * VM_ARCHITECTURE_ARM64
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
   * The VM's ComputerName.
   *
   * @param string $computerName
   */
  public function setComputerName($computerName)
  {
    $this->computerName = $computerName;
  }
  /**
   * @return string
   */
  public function getComputerName()
  {
    return $this->computerName;
  }
  /**
   * The number of cpus the VM has.
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
   * The number of disks the VM has, including OS disk.
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
   * Description of the data disks.
   *
   * @param Disk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return Disk[]
   */
  public function getDisks()
  {
    return $this->disks;
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
   * Description of the OS.
   *
   * @param OSDescription $osDescription
   */
  public function setOsDescription(OSDescription $osDescription)
  {
    $this->osDescription = $osDescription;
  }
  /**
   * @return OSDescription
   */
  public function getOsDescription()
  {
    return $this->osDescription;
  }
  /**
   * Description of the OS disk.
   *
   * @param OSDisk $osDisk
   */
  public function setOsDisk(OSDisk $osDisk)
  {
    $this->osDisk = $osDisk;
  }
  /**
   * @return OSDisk
   */
  public function getOsDisk()
  {
    return $this->osDisk;
  }
  /**
   * The power state of the VM at the moment list was taken.
   *
   * Accepted values: POWER_STATE_UNSPECIFIED, STARTING, RUNNING, STOPPING,
   * STOPPED, DEALLOCATING, DEALLOCATED, UNKNOWN
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
   * The VM full path in Azure.
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
   * VM size as configured in Azure. Determines the VM's hardware spec.
   *
   * @param string $vmSize
   */
  public function setVmSize($vmSize)
  {
    $this->vmSize = $vmSize;
  }
  /**
   * @return string
   */
  public function getVmSize()
  {
    return $this->vmSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureVmDetails::class, 'Google_Service_VMMigrationService_AzureVmDetails');
