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

class AttachedDisk extends \Google\Collection
{
  /**
   * Default value indicating Architecture is not set.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * Machines with architecture ARM64
   */
  public const ARCHITECTURE_ARM64 = 'ARM64';
  /**
   * Machines with architecture X86_64
   */
  public const ARCHITECTURE_X86_64 = 'X86_64';
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
  /**
   * *[Default]* Disk state has not been preserved.
   */
  public const SAVED_STATE_DISK_SAVED_STATE_UNSPECIFIED = 'DISK_SAVED_STATE_UNSPECIFIED';
  /**
   * Disk state has been preserved.
   */
  public const SAVED_STATE_PRESERVED = 'PRESERVED';
  public const TYPE_PERSISTENT = 'PERSISTENT';
  public const TYPE_SCRATCH = 'SCRATCH';
  protected $collection_key = 'licenses';
  /**
   * Output only. [Output Only] The architecture of the attached disk. Valid
   * values are ARM64 or X86_64.
   *
   * @var string
   */
  public $architecture;
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
   * Specifies a unique device name of your choice that is reflected into
   * the/dev/disk/by-id/google-* tree of a Linux operating system running within
   * the instance. This name can be used to reference the device for mounting,
   * resizing, and so on, from within the instance.
   *
   * If not specified, the server chooses a default device name to apply to this
   * disk, in the form persistent-disk-x, where x is a number assigned by Google
   * Compute Engine. This field is only applicable for persistent disks.
   *
   * @var string
   */
  public $deviceName;
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * The size of the disk in GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * [Input Only] Whether to force attach the regional disk even if it's
   * currently attached to another instance. If you try to force attach a zonal
   * disk to an instance, you will receive an error.
   *
   * @var bool
   */
  public $forceAttach;
  protected $guestOsFeaturesType = GuestOsFeature::class;
  protected $guestOsFeaturesDataType = 'array';
  /**
   * Output only. [Output Only] A zero-based index to this disk, where 0 is
   * reserved for the boot disk. If you have many disks attached to an instance,
   * each disk would have a unique index number.
   *
   * @var int
   */
  public $index;
  protected $initializeParamsType = AttachedDiskInitializeParams::class;
  protected $initializeParamsDataType = '';
  /**
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME. For most machine types, the default is SCSI. Local
   * SSDs can use either NVME or SCSI. In certain configurations, persistent
   * disks can use NVMe. For more information, seeAbout persistent disks.
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
   * The mode in which to attach this disk, either READ_WRITE orREAD_ONLY. If
   * not specified, the default is to attach the disk in READ_WRITE mode.
   *
   * @var string
   */
  public $mode;
  /**
   * Output only. For LocalSSD disks on VM Instances in STOPPED or SUSPENDED
   * state, this field is set to PRESERVED if the LocalSSD data has been saved
   * to a persistent location by customer request.  (see the discard_local_ssd
   * option on Stop/Suspend). Read-only in the api.
   *
   * @var string
   */
  public $savedState;
  protected $shieldedInstanceInitialStateType = InitialStateConfig::class;
  protected $shieldedInstanceInitialStateDataType = '';
  /**
   * Specifies a valid partial or full URL to an existing Persistent Disk
   * resource. When creating a new instance boot disk, one
   * ofinitializeParams.sourceImage orinitializeParams.sourceSnapshot or
   * disks.source is required.
   *
   * If desired, you can also attach existing non-root persistent disks using
   * this property. This field is only applicable for persistent disks.
   *
   * Note that for InstanceTemplate, specify the disk name for zonal disk, and
   * the URL for regional disk.
   *
   * @var string
   */
  public $source;
  /**
   * Specifies the type of the disk, either SCRATCH orPERSISTENT. If not
   * specified, the default isPERSISTENT.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. [Output Only] The architecture of the attached disk. Valid
   * values are ARM64 or X86_64.
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, ARM64, X86_64
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
   * Specifies a unique device name of your choice that is reflected into
   * the/dev/disk/by-id/google-* tree of a Linux operating system running within
   * the instance. This name can be used to reference the device for mounting,
   * resizing, and so on, from within the instance.
   *
   * If not specified, the server chooses a default device name to apply to this
   * disk, in the form persistent-disk-x, where x is a number assigned by Google
   * Compute Engine. This field is only applicable for persistent disks.
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
   * Encrypts or decrypts a disk using acustomer-supplied encryption key.
   *
   * If you are creating a new disk, this field encrypts the new disk using an
   * encryption key that you provide. If you are attaching an existing disk that
   * is already encrypted, this field decrypts the disk using the customer-
   * supplied encryption key.
   *
   * If you encrypt a disk using a customer-supplied key, you must provide the
   * same key again when you attempt to use this resource at a later time. For
   * example, you must provide the key when you create a snapshot or an image
   * from the disk or when you attach the disk to a virtual machine instance.
   *
   * If you do not provide an encryption key, then the disk will be encrypted
   * using an automatically generated key and you do not need to provide a key
   * to use the disk later.
   *
   * Note:
   *
   * Instance templates do not storecustomer-supplied encryption keys, so you
   * cannot use your own keys to encrypt disks in amanaged instance group.
   *
   * You cannot create VMs that have disks with customer-supplied keys using the
   * bulk insert method.
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
   * The size of the disk in GB.
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
   * [Input Only] Whether to force attach the regional disk even if it's
   * currently attached to another instance. If you try to force attach a zonal
   * disk to an instance, you will receive an error.
   *
   * @param bool $forceAttach
   */
  public function setForceAttach($forceAttach)
  {
    $this->forceAttach = $forceAttach;
  }
  /**
   * @return bool
   */
  public function getForceAttach()
  {
    return $this->forceAttach;
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
   * Output only. [Output Only] A zero-based index to this disk, where 0 is
   * reserved for the boot disk. If you have many disks attached to an instance,
   * each disk would have a unique index number.
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
   * [Input Only] Specifies the parameters for a new disk that will be created
   * alongside the new instance. Use initialization parameters to create boot
   * disks or local SSDs attached to the new instance.
   *
   * This property is mutually exclusive with the source property; you can only
   * define one or the other, but not both.
   *
   * @param AttachedDiskInitializeParams $initializeParams
   */
  public function setInitializeParams(AttachedDiskInitializeParams $initializeParams)
  {
    $this->initializeParams = $initializeParams;
  }
  /**
   * @return AttachedDiskInitializeParams
   */
  public function getInitializeParams()
  {
    return $this->initializeParams;
  }
  /**
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME. For most machine types, the default is SCSI. Local
   * SSDs can use either NVME or SCSI. In certain configurations, persistent
   * disks can use NVMe. For more information, seeAbout persistent disks.
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
   * The mode in which to attach this disk, either READ_WRITE orREAD_ONLY. If
   * not specified, the default is to attach the disk in READ_WRITE mode.
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
   * Output only. For LocalSSD disks on VM Instances in STOPPED or SUSPENDED
   * state, this field is set to PRESERVED if the LocalSSD data has been saved
   * to a persistent location by customer request.  (see the discard_local_ssd
   * option on Stop/Suspend). Read-only in the api.
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
   * Output only. [Output Only] shielded vm initial state stored on disk
   *
   * @param InitialStateConfig $shieldedInstanceInitialState
   */
  public function setShieldedInstanceInitialState(InitialStateConfig $shieldedInstanceInitialState)
  {
    $this->shieldedInstanceInitialState = $shieldedInstanceInitialState;
  }
  /**
   * @return InitialStateConfig
   */
  public function getShieldedInstanceInitialState()
  {
    return $this->shieldedInstanceInitialState;
  }
  /**
   * Specifies a valid partial or full URL to an existing Persistent Disk
   * resource. When creating a new instance boot disk, one
   * ofinitializeParams.sourceImage orinitializeParams.sourceSnapshot or
   * disks.source is required.
   *
   * If desired, you can also attach existing non-root persistent disks using
   * this property. This field is only applicable for persistent disks.
   *
   * Note that for InstanceTemplate, specify the disk name for zonal disk, and
   * the URL for regional disk.
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
   * Specifies the type of the disk, either SCRATCH orPERSISTENT. If not
   * specified, the default isPERSISTENT.
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
class_alias(AttachedDisk::class, 'Google_Service_Compute_AttachedDisk');
