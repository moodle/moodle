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

namespace Google\Service\Backupdr;

class AttachedDisk extends \Google\Collection
{
  /**
   * Default value, which is unused.
   */
  public const DISK_INTERFACE_DISK_INTERFACE_UNSPECIFIED = 'DISK_INTERFACE_UNSPECIFIED';
  /**
   * SCSI Disk Interface.
   */
  public const DISK_INTERFACE_SCSI = 'SCSI';
  /**
   * NVME Disk Interface.
   */
  public const DISK_INTERFACE_NVME = 'NVME';
  /**
   * NVDIMM Disk Interface.
   */
  public const DISK_INTERFACE_NVDIMM = 'NVDIMM';
  /**
   * ISCSI Disk Interface.
   */
  public const DISK_INTERFACE_ISCSI = 'ISCSI';
  /**
   * Default value, which is unused.
   */
  public const DISK_TYPE_DEPRECATED_DISK_TYPE_UNSPECIFIED = 'DISK_TYPE_UNSPECIFIED';
  /**
   * A scratch disk type.
   */
  public const DISK_TYPE_DEPRECATED_SCRATCH = 'SCRATCH';
  /**
   * A persistent disk type.
   */
  public const DISK_TYPE_DEPRECATED_PERSISTENT = 'PERSISTENT';
  /**
   * Default value, which is unused.
   */
  public const MODE_DISK_MODE_UNSPECIFIED = 'DISK_MODE_UNSPECIFIED';
  /**
   * Attaches this disk in read-write mode. Only one virtual machine at a time
   * can be attached to a disk in read-write mode.
   */
  public const MODE_READ_WRITE = 'READ_WRITE';
  /**
   * Attaches this disk in read-only mode. Multiple virtual machines can use a
   * disk in read-only mode at a time.
   */
  public const MODE_READ_ONLY = 'READ_ONLY';
  /**
   * The disk is locked for administrative reasons. Nobody else can use the
   * disk. This mode is used (for example) when taking a snapshot of a disk to
   * prevent mounting the disk while it is being snapshotted.
   */
  public const MODE_LOCKED = 'LOCKED';
  /**
   * Default Disk state has not been preserved.
   */
  public const SAVED_STATE_DISK_SAVED_STATE_UNSPECIFIED = 'DISK_SAVED_STATE_UNSPECIFIED';
  /**
   * Disk state has been preserved.
   */
  public const SAVED_STATE_PRESERVED = 'PRESERVED';
  /**
   * Default value, which is unused.
   */
  public const TYPE_DISK_TYPE_UNSPECIFIED = 'DISK_TYPE_UNSPECIFIED';
  /**
   * A scratch disk type.
   */
  public const TYPE_SCRATCH = 'SCRATCH';
  /**
   * A persistent disk type.
   */
  public const TYPE_PERSISTENT = 'PERSISTENT';
  protected $collection_key = 'license';
  /**
   * Optional. Specifies whether the disk will be auto-deleted when the instance
   * is deleted (but not when the disk is detached from the instance).
   *
   * @var bool
   */
  public $autoDelete;
  /**
   * Optional. Indicates that this is a boot disk. The virtual machine will use
   * the first partition of the disk for its root filesystem.
   *
   * @var bool
   */
  public $boot;
  /**
   * Optional. This is used as an identifier for the disks. This is the unique
   * name has to provided to modify disk parameters like disk_name and
   * replica_zones (in case of RePDs)
   *
   * @var string
   */
  public $deviceName;
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * Optional. Specifies the disk interface to use for attaching this disk.
   *
   * @var string
   */
  public $diskInterface;
  /**
   * Optional. The size of the disk in GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Optional. Output only. The URI of the disk type resource. For example:
   * projects/project/zones/zone/diskTypes/pd-standard or pd-ssd
   *
   * @var string
   */
  public $diskType;
  /**
   * Specifies the type of the disk.
   *
   * @deprecated
   * @var string
   */
  public $diskTypeDeprecated;
  protected $guestOsFeatureType = GuestOsFeature::class;
  protected $guestOsFeatureDataType = 'array';
  /**
   * Optional. A zero-based index to this disk, where 0 is reserved for the boot
   * disk.
   *
   * @var string
   */
  public $index;
  protected $initializeParamsType = InitializeParams::class;
  protected $initializeParamsDataType = '';
  /**
   * Optional. Type of the resource.
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. Any valid publicly visible licenses.
   *
   * @var string[]
   */
  public $license;
  /**
   * Optional. The mode in which to attach this disk.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. Output only. The state of the disk.
   *
   * @var string
   */
  public $savedState;
  /**
   * Optional. Specifies a valid partial or full URL to an existing Persistent
   * Disk resource.
   *
   * @var string
   */
  public $source;
  /**
   * Optional. Specifies the type of the disk.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Specifies whether the disk will be auto-deleted when the instance
   * is deleted (but not when the disk is detached from the instance).
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
   * Optional. Indicates that this is a boot disk. The virtual machine will use
   * the first partition of the disk for its root filesystem.
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
   * Optional. This is used as an identifier for the disks. This is the unique
   * name has to provided to modify disk parameters like disk_name and
   * replica_zones (in case of RePDs)
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
   * Optional. Encrypts or decrypts a disk using a customer-supplied encryption
   * key.
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
   * Optional. Specifies the disk interface to use for attaching this disk.
   *
   * Accepted values: DISK_INTERFACE_UNSPECIFIED, SCSI, NVME, NVDIMM, ISCSI
   *
   * @param self::DISK_INTERFACE_* $diskInterface
   */
  public function setDiskInterface($diskInterface)
  {
    $this->diskInterface = $diskInterface;
  }
  /**
   * @return self::DISK_INTERFACE_*
   */
  public function getDiskInterface()
  {
    return $this->diskInterface;
  }
  /**
   * Optional. The size of the disk in GB.
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
   * Optional. Output only. The URI of the disk type resource. For example:
   * projects/project/zones/zone/diskTypes/pd-standard or pd-ssd
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
   * Specifies the type of the disk.
   *
   * Accepted values: DISK_TYPE_UNSPECIFIED, SCRATCH, PERSISTENT
   *
   * @deprecated
   * @param self::DISK_TYPE_DEPRECATED_* $diskTypeDeprecated
   */
  public function setDiskTypeDeprecated($diskTypeDeprecated)
  {
    $this->diskTypeDeprecated = $diskTypeDeprecated;
  }
  /**
   * @deprecated
   * @return self::DISK_TYPE_DEPRECATED_*
   */
  public function getDiskTypeDeprecated()
  {
    return $this->diskTypeDeprecated;
  }
  /**
   * Optional. A list of features to enable on the guest operating system.
   * Applicable only for bootable images.
   *
   * @param GuestOsFeature[] $guestOsFeature
   */
  public function setGuestOsFeature($guestOsFeature)
  {
    $this->guestOsFeature = $guestOsFeature;
  }
  /**
   * @return GuestOsFeature[]
   */
  public function getGuestOsFeature()
  {
    return $this->guestOsFeature;
  }
  /**
   * Optional. A zero-based index to this disk, where 0 is reserved for the boot
   * disk.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Optional. Specifies the parameters to initialize this disk.
   *
   * @param InitializeParams $initializeParams
   */
  public function setInitializeParams(InitializeParams $initializeParams)
  {
    $this->initializeParams = $initializeParams;
  }
  /**
   * @return InitializeParams
   */
  public function getInitializeParams()
  {
    return $this->initializeParams;
  }
  /**
   * Optional. Type of the resource.
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
   * Optional. Any valid publicly visible licenses.
   *
   * @param string[] $license
   */
  public function setLicense($license)
  {
    $this->license = $license;
  }
  /**
   * @return string[]
   */
  public function getLicense()
  {
    return $this->license;
  }
  /**
   * Optional. The mode in which to attach this disk.
   *
   * Accepted values: DISK_MODE_UNSPECIFIED, READ_WRITE, READ_ONLY, LOCKED
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
   * Optional. Output only. The state of the disk.
   *
   * Accepted values: DISK_SAVED_STATE_UNSPECIFIED, PRESERVED
   *
   * @param self::SAVED_STATE_* $savedState
   */
  public function setSavedState($savedState)
  {
    $this->savedState = $savedState;
  }
  /**
   * @return self::SAVED_STATE_*
   */
  public function getSavedState()
  {
    return $this->savedState;
  }
  /**
   * Optional. Specifies a valid partial or full URL to an existing Persistent
   * Disk resource.
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
   * Optional. Specifies the type of the disk.
   *
   * Accepted values: DISK_TYPE_UNSPECIFIED, SCRATCH, PERSISTENT
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
class_alias(AttachedDisk::class, 'Google_Service_Backupdr_AttachedDisk');
