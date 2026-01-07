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

class VmwareVmDetails extends \Google\Model
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
   * The boot option is EFI.
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
   * The VM is turned ON.
   */
  public const POWER_STATE_ON = 'ON';
  /**
   * The VM is turned OFF.
   */
  public const POWER_STATE_OFF = 'OFF';
  /**
   * The VM is suspended. This is similar to hibernation or sleep mode.
   */
  public const POWER_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * Output only. The CPU architecture.
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. The VM Boot Option.
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
   * The number of cpus in the VM.
   *
   * @var int
   */
  public $cpuCount;
  /**
   * The descriptive name of the vCenter's datacenter this VM is contained in.
   *
   * @var string
   */
  public $datacenterDescription;
  /**
   * The id of the vCenter's datacenter this VM is contained in.
   *
   * @var string
   */
  public $datacenterId;
  /**
   * The number of disks the VM has.
   *
   * @var int
   */
  public $diskCount;
  /**
   * The display name of the VM. Note that this is not necessarily unique.
   *
   * @var string
   */
  public $displayName;
  /**
   * The VM's OS. See for example https://vdc-repo.vmware.com/vmwb-
   * repository/dcr-public/da47f910-60ac-438b-8b9b-6122f4d14524/16b7274a-bf8b-
   * 4b4c-a05e-746f2aa93c8c/doc/vim.vm.GuestOsDescriptor.GuestOsIdentifier.html
   * for types of strings this might hold.
   *
   * @var string
   */
  public $guestDescription;
  /**
   * The size of the memory of the VM in MB.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * The power state of the VM at the moment list was taken.
   *
   * @var string
   */
  public $powerState;
  /**
   * The unique identifier of the VM in vCenter.
   *
   * @var string
   */
  public $uuid;
  /**
   * The VM's id in the source (note that this is not the MigratingVm's id).
   * This is the moref id of the VM.
   *
   * @var string
   */
  public $vmId;

  /**
   * Output only. The CPU architecture.
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
   * Output only. The VM Boot Option.
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
   * The number of cpus in the VM.
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
   * The descriptive name of the vCenter's datacenter this VM is contained in.
   *
   * @param string $datacenterDescription
   */
  public function setDatacenterDescription($datacenterDescription)
  {
    $this->datacenterDescription = $datacenterDescription;
  }
  /**
   * @return string
   */
  public function getDatacenterDescription()
  {
    return $this->datacenterDescription;
  }
  /**
   * The id of the vCenter's datacenter this VM is contained in.
   *
   * @param string $datacenterId
   */
  public function setDatacenterId($datacenterId)
  {
    $this->datacenterId = $datacenterId;
  }
  /**
   * @return string
   */
  public function getDatacenterId()
  {
    return $this->datacenterId;
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
   * The display name of the VM. Note that this is not necessarily unique.
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
   * The VM's OS. See for example https://vdc-repo.vmware.com/vmwb-
   * repository/dcr-public/da47f910-60ac-438b-8b9b-6122f4d14524/16b7274a-bf8b-
   * 4b4c-a05e-746f2aa93c8c/doc/vim.vm.GuestOsDescriptor.GuestOsIdentifier.html
   * for types of strings this might hold.
   *
   * @param string $guestDescription
   */
  public function setGuestDescription($guestDescription)
  {
    $this->guestDescription = $guestDescription;
  }
  /**
   * @return string
   */
  public function getGuestDescription()
  {
    return $this->guestDescription;
  }
  /**
   * The size of the memory of the VM in MB.
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
   * The power state of the VM at the moment list was taken.
   *
   * Accepted values: POWER_STATE_UNSPECIFIED, ON, OFF, SUSPENDED
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
   * The unique identifier of the VM in vCenter.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
  /**
   * The VM's id in the source (note that this is not the MigratingVm's id).
   * This is the moref id of the VM.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareVmDetails::class, 'Google_Service_VMMigrationService_VmwareVmDetails');
