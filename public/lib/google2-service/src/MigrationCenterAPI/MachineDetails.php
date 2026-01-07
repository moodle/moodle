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

namespace Google\Service\MigrationCenterAPI;

class MachineDetails extends \Google\Model
{
  /**
   * Power state is unknown.
   */
  public const POWER_STATE_POWER_STATE_UNSPECIFIED = 'POWER_STATE_UNSPECIFIED';
  /**
   * The machine is preparing to enter the ACTIVE state. An instance may enter
   * the PENDING state when it launches for the first time, or when it is
   * started after being in the SUSPENDED state.
   */
  public const POWER_STATE_PENDING = 'PENDING';
  /**
   * The machine is active.
   */
  public const POWER_STATE_ACTIVE = 'ACTIVE';
  /**
   * The machine is being turned off.
   */
  public const POWER_STATE_SUSPENDING = 'SUSPENDING';
  /**
   * The machine is off.
   */
  public const POWER_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The machine is being deleted from the hosting platform.
   */
  public const POWER_STATE_DELETING = 'DELETING';
  /**
   * The machine is deleted from the hosting platform.
   */
  public const POWER_STATE_DELETED = 'DELETED';
  protected $architectureType = MachineArchitectureDetails::class;
  protected $architectureDataType = '';
  /**
   * Number of logical CPU cores in the machine. Must be non-negative.
   *
   * @var int
   */
  public $coreCount;
  /**
   * Machine creation time.
   *
   * @var string
   */
  public $createTime;
  protected $diskPartitionsType = DiskPartitionDetails::class;
  protected $diskPartitionsDataType = '';
  protected $disksType = MachineDiskDetails::class;
  protected $disksDataType = '';
  protected $guestOsType = GuestOsDetails::class;
  protected $guestOsDataType = '';
  /**
   * Machine name.
   *
   * @var string
   */
  public $machineName;
  /**
   * The amount of memory in the machine. Must be non-negative.
   *
   * @var int
   */
  public $memoryMb;
  protected $networkType = MachineNetworkDetails::class;
  protected $networkDataType = '';
  protected $platformType = PlatformDetails::class;
  protected $platformDataType = '';
  /**
   * Power state of the machine.
   *
   * @var string
   */
  public $powerState;
  /**
   * Machine unique identifier.
   *
   * @var string
   */
  public $uuid;

  /**
   * Architecture details (vendor, CPU architecture).
   *
   * @param MachineArchitectureDetails $architecture
   */
  public function setArchitecture(MachineArchitectureDetails $architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return MachineArchitectureDetails
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Number of logical CPU cores in the machine. Must be non-negative.
   *
   * @param int $coreCount
   */
  public function setCoreCount($coreCount)
  {
    $this->coreCount = $coreCount;
  }
  /**
   * @return int
   */
  public function getCoreCount()
  {
    return $this->coreCount;
  }
  /**
   * Machine creation time.
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
   * Optional. Disk partitions details. Note: Partitions are not necessarily
   * mounted on local disks and therefore might not have a one-to-one
   * correspondence with local disks.
   *
   * @param DiskPartitionDetails $diskPartitions
   */
  public function setDiskPartitions(DiskPartitionDetails $diskPartitions)
  {
    $this->diskPartitions = $diskPartitions;
  }
  /**
   * @return DiskPartitionDetails
   */
  public function getDiskPartitions()
  {
    return $this->diskPartitions;
  }
  /**
   * Disk details.
   *
   * @param MachineDiskDetails $disks
   */
  public function setDisks(MachineDiskDetails $disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return MachineDiskDetails
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Guest OS information.
   *
   * @param GuestOsDetails $guestOs
   */
  public function setGuestOs(GuestOsDetails $guestOs)
  {
    $this->guestOs = $guestOs;
  }
  /**
   * @return GuestOsDetails
   */
  public function getGuestOs()
  {
    return $this->guestOs;
  }
  /**
   * Machine name.
   *
   * @param string $machineName
   */
  public function setMachineName($machineName)
  {
    $this->machineName = $machineName;
  }
  /**
   * @return string
   */
  public function getMachineName()
  {
    return $this->machineName;
  }
  /**
   * The amount of memory in the machine. Must be non-negative.
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
   * Network details.
   *
   * @param MachineNetworkDetails $network
   */
  public function setNetwork(MachineNetworkDetails $network)
  {
    $this->network = $network;
  }
  /**
   * @return MachineNetworkDetails
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Platform specific information.
   *
   * @param PlatformDetails $platform
   */
  public function setPlatform(PlatformDetails $platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return PlatformDetails
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Power state of the machine.
   *
   * Accepted values: POWER_STATE_UNSPECIFIED, PENDING, ACTIVE, SUSPENDING,
   * SUSPENDED, DELETING, DELETED
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
   * Machine unique identifier.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineDetails::class, 'Google_Service_MigrationCenterAPI_MachineDetails');
