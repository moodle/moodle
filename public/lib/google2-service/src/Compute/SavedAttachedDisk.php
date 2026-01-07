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

class SavedAttachedDisk extends \Google\Collection
{
  public const INTERFACE_NVME = 'NVME';
  public const INTERFACE_SCSI = 'SCSI';
  /**
   * Attaches this disk in read-only mode. Multiple virtual machines can use a
   * disk in read-only mode at a time.
   */
  public const MODE_READ_ONLY = 'READ_ONLY';
  /**
   * *[Default]* Attaches this disk in read-write mode. Only one virtual machine
   * at a time can be attached to a disk in read-write mode.
   */
  public const MODE_READ_WRITE = 'READ_WRITE';
  public const STORAGE_BYTES_STATUS_UPDATING = 'UPDATING';
  public const STORAGE_BYTES_STATUS_UP_TO_DATE = 'UP_TO_DATE';
  public const TYPE_PERSISTENT = 'PERSISTENT';
  public const TYPE_SCRATCH = 'SCRATCH';
  protected $collection_key = 'licenses';
  /**
   * Specifies whether the disk will be auto-deleted when the instance is
   * deleted (but not when the disk is detached from the instance).
   *
   * @var bool
   */
  public $autoDelete;
  /**
   * Indicates that this is a boot disk. The virtual machine will use the first
   * partition of the disk for its root filesystem.
   *
   * @var bool
   */
  public $boot;
  /**
   * Specifies the name of the disk attached to the source instance.
   *
   * @var string
   */
  public $deviceName;
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * The size of the disk in base-2 GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Output only. [Output Only] URL of the disk type resource. For
   * example:projects/project/zones/zone/diskTypes/pd-standard or pd-ssd
   *
   * @var string
   */
  public $diskType;
  protected $guestOsFeaturesType = GuestOsFeature::class;
  protected $guestOsFeaturesDataType = 'array';
  /**
   * Output only. Specifies zero-based index of the disk that is attached to the
   * source instance.
   *
   * @var int
   */
  public $index;
  /**
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME.
   *
   * @var string
   */
  public $interface;
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#attachedDisk
   * for attached disks.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] Any valid publicly visible licenses.
   *
   * @var string[]
   */
  public $licenses;
  /**
   * The mode in which this disk is attached to the source instance,
   * eitherREAD_WRITE or READ_ONLY.
   *
   * @var string
   */
  public $mode;
  /**
   * Specifies a URL of the disk attached to the source instance.
   *
   * @var string
   */
  public $source;
  /**
   * Output only. [Output Only] A size of the storage used by the disk's
   * snapshot by this machine image.
   *
   * @var string
   */
  public $storageBytes;
  /**
   * Output only. [Output Only] An indicator whether storageBytes is in a stable
   * state or it is being adjusted as a result of shared storage reallocation.
   * This status can either be UPDATING, meaning the size of the snapshot is
   * being updated, or UP_TO_DATE, meaning the size of the snapshot is up-to-
   * date.
   *
   * @var string
   */
  public $storageBytesStatus;
  /**
   * Specifies the type of the attached disk, either SCRATCH orPERSISTENT.
   *
   * @var string
   */
  public $type;

  /**
   * Specifies whether the disk will be auto-deleted when the instance is
   * deleted (but not when the disk is detached from the instance).
   *
   * @param bool $autoDelete
   */
  public function setAutoDelete($autoDelete)
  {
    $this->autoDelete = $autoDelete;
  }
  /**
   * @return bool
   */
  public function getAutoDelete()
  {
    return $this->autoDelete;
  }
  /**
   * Indicates that this is a boot disk. The virtual machine will use the first
   * partition of the disk for its root filesystem.
   *
   * @param bool $boot
   */
  public function setBoot($boot)
  {
    $this->boot = $boot;
  }
  /**
   * @return bool
   */
  public function getBoot()
  {
    return $this->boot;
  }
  /**
   * Specifies the name of the disk attached to the source instance.
   *
   * @param string $deviceName
   */
  public function setDeviceName($deviceName)
  {
    $this->deviceName = $deviceName;
  }
  /**
   * @return string
   */
  public function getDeviceName()
  {
    return $this->deviceName;
  }
  /**
   * The encryption key for the disk.
   *
   * @param CustomerEncryptionKey $diskEncryptionKey
   */
  public function setDiskEncryptionKey(CustomerEncryptionKey $diskEncryptionKey)
  {
    $this->diskEncryptionKey = $diskEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getDiskEncryptionKey()
  {
    return $this->diskEncryptionKey;
  }
  /**
   * The size of the disk in base-2 GB.
   *
   * @param string $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return string
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Output only. [Output Only] URL of the disk type resource. For
   * example:projects/project/zones/zone/diskTypes/pd-standard or pd-ssd
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * A list of features to enable on the guest operating system. Applicable only
   * for bootable images. Read Enabling guest operating system features to see a
   * list of available options.
   *
   * @param GuestOsFeature[] $guestOsFeatures
   */
  public function setGuestOsFeatures($guestOsFeatures)
  {
    $this->guestOsFeatures = $guestOsFeatures;
  }
  /**
   * @return GuestOsFeature[]
   */
  public function getGuestOsFeatures()
  {
    return $this->guestOsFeatures;
  }
  /**
   * Output only. Specifies zero-based index of the disk that is attached to the
   * source instance.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME.
   *
   * Accepted values: NVME, SCSI
   *
   * @param self::INTERFACE_* $interface
   */
  public function setInterface($interface)
  {
    $this->interface = $interface;
  }
  /**
   * @return self::INTERFACE_*
   */
  public function getInterface()
  {
    return $this->interface;
  }
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#attachedDisk
   * for attached disks.
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
   * Output only. [Output Only] Any valid publicly visible licenses.
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
   * The mode in which this disk is attached to the source instance,
   * eitherREAD_WRITE or READ_ONLY.
   *
   * Accepted values: READ_ONLY, READ_WRITE
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
   * Specifies a URL of the disk attached to the source instance.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. [Output Only] A size of the storage used by the disk's
   * snapshot by this machine image.
   *
   * @param string $storageBytes
   */
  public function setStorageBytes($storageBytes)
  {
    $this->storageBytes = $storageBytes;
  }
  /**
   * @return string
   */
  public function getStorageBytes()
  {
    return $this->storageBytes;
  }
  /**
   * Output only. [Output Only] An indicator whether storageBytes is in a stable
   * state or it is being adjusted as a result of shared storage reallocation.
   * This status can either be UPDATING, meaning the size of the snapshot is
   * being updated, or UP_TO_DATE, meaning the size of the snapshot is up-to-
   * date.
   *
   * Accepted values: UPDATING, UP_TO_DATE
   *
   * @param self::STORAGE_BYTES_STATUS_* $storageBytesStatus
   */
  public function setStorageBytesStatus($storageBytesStatus)
  {
    $this->storageBytesStatus = $storageBytesStatus;
  }
  /**
   * @return self::STORAGE_BYTES_STATUS_*
   */
  public function getStorageBytesStatus()
  {
    return $this->storageBytesStatus;
  }
  /**
   * Specifies the type of the attached disk, either SCRATCH orPERSISTENT.
   *
   * Accepted values: PERSISTENT, SCRATCH
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavedAttachedDisk::class, 'Google_Service_Compute_SavedAttachedDisk');
