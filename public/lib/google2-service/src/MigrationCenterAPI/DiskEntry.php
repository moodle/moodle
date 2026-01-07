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

class DiskEntry extends \Google\Model
{
  /**
   * Interface type unknown or unspecified.
   */
  public const INTERFACE_TYPE_INTERFACE_TYPE_UNSPECIFIED = 'INTERFACE_TYPE_UNSPECIFIED';
  /**
   * IDE interface type.
   */
  public const INTERFACE_TYPE_IDE = 'IDE';
  /**
   * SATA interface type.
   */
  public const INTERFACE_TYPE_SATA = 'SATA';
  /**
   * SAS interface type.
   */
  public const INTERFACE_TYPE_SAS = 'SAS';
  /**
   * SCSI interface type.
   */
  public const INTERFACE_TYPE_SCSI = 'SCSI';
  /**
   * NVME interface type.
   */
  public const INTERFACE_TYPE_NVME = 'NVME';
  /**
   * FC interface type.
   */
  public const INTERFACE_TYPE_FC = 'FC';
  /**
   * iSCSI interface type.
   */
  public const INTERFACE_TYPE_ISCSI = 'ISCSI';
  /**
   * Disk capacity.
   *
   * @var string
   */
  public $capacityBytes;
  /**
   * Disk label.
   *
   * @var string
   */
  public $diskLabel;
  /**
   * Disk label type (e.g. BIOS/GPT)
   *
   * @var string
   */
  public $diskLabelType;
  /**
   * Disk free space.
   *
   * @var string
   */
  public $freeBytes;
  /**
   * Disk hardware address (e.g. 0:1 for SCSI).
   *
   * @var string
   */
  public $hwAddress;
  /**
   * Disks interface type.
   *
   * @var string
   */
  public $interfaceType;
  protected $partitionsType = DiskPartitionList::class;
  protected $partitionsDataType = '';
  protected $vmwareType = VmwareDiskConfig::class;
  protected $vmwareDataType = '';

  /**
   * Disk capacity.
   *
   * @param string $capacityBytes
   */
  public function setCapacityBytes($capacityBytes)
  {
    $this->capacityBytes = $capacityBytes;
  }
  /**
   * @return string
   */
  public function getCapacityBytes()
  {
    return $this->capacityBytes;
  }
  /**
   * Disk label.
   *
   * @param string $diskLabel
   */
  public function setDiskLabel($diskLabel)
  {
    $this->diskLabel = $diskLabel;
  }
  /**
   * @return string
   */
  public function getDiskLabel()
  {
    return $this->diskLabel;
  }
  /**
   * Disk label type (e.g. BIOS/GPT)
   *
   * @param string $diskLabelType
   */
  public function setDiskLabelType($diskLabelType)
  {
    $this->diskLabelType = $diskLabelType;
  }
  /**
   * @return string
   */
  public function getDiskLabelType()
  {
    return $this->diskLabelType;
  }
  /**
   * Disk free space.
   *
   * @param string $freeBytes
   */
  public function setFreeBytes($freeBytes)
  {
    $this->freeBytes = $freeBytes;
  }
  /**
   * @return string
   */
  public function getFreeBytes()
  {
    return $this->freeBytes;
  }
  /**
   * Disk hardware address (e.g. 0:1 for SCSI).
   *
   * @param string $hwAddress
   */
  public function setHwAddress($hwAddress)
  {
    $this->hwAddress = $hwAddress;
  }
  /**
   * @return string
   */
  public function getHwAddress()
  {
    return $this->hwAddress;
  }
  /**
   * Disks interface type.
   *
   * Accepted values: INTERFACE_TYPE_UNSPECIFIED, IDE, SATA, SAS, SCSI, NVME,
   * FC, ISCSI
   *
   * @param self::INTERFACE_TYPE_* $interfaceType
   */
  public function setInterfaceType($interfaceType)
  {
    $this->interfaceType = $interfaceType;
  }
  /**
   * @return self::INTERFACE_TYPE_*
   */
  public function getInterfaceType()
  {
    return $this->interfaceType;
  }
  /**
   * Partition layout.
   *
   * @param DiskPartitionList $partitions
   */
  public function setPartitions(DiskPartitionList $partitions)
  {
    $this->partitions = $partitions;
  }
  /**
   * @return DiskPartitionList
   */
  public function getPartitions()
  {
    return $this->partitions;
  }
  /**
   * VMware disk details.
   *
   * @param VmwareDiskConfig $vmware
   */
  public function setVmware(VmwareDiskConfig $vmware)
  {
    $this->vmware = $vmware;
  }
  /**
   * @return VmwareDiskConfig
   */
  public function getVmware()
  {
    return $this->vmware;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskEntry::class, 'Google_Service_MigrationCenterAPI_DiskEntry');
