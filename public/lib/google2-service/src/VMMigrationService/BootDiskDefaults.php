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

class BootDiskDefaults extends \Google\Model
{
  /**
   * An unspecified disk type. Will be used as STANDARD.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED = 'COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED';
  /**
   * A Standard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_STANDARD = 'COMPUTE_ENGINE_DISK_TYPE_STANDARD';
  /**
   * SSD hard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_SSD = 'COMPUTE_ENGINE_DISK_TYPE_SSD';
  /**
   * An alternative to SSD persistent disks that balance performance and cost.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_BALANCED';
  /**
   * Hyperdisk balanced disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED';
  /**
   * Optional. Specifies a unique device name of your choice that is reflected
   * into the /dev/disk/by-id/google-* tree of a Linux operating system running
   * within the instance. If not specified, the server chooses a default device
   * name to apply to this disk, in the form persistent-disk-x, where x is a
   * number assigned by Google Compute Engine. This field is only applicable for
   * persistent disks.
   *
   * @var string
   */
  public $deviceName;
  /**
   * Optional. The name of the disk.
   *
   * @var string
   */
  public $diskName;
  /**
   * Optional. The type of disk provisioning to use for the VM.
   *
   * @var string
   */
  public $diskType;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  protected $imageType = DiskImageDefaults::class;
  protected $imageDataType = '';

  /**
   * Optional. Specifies a unique device name of your choice that is reflected
   * into the /dev/disk/by-id/google-* tree of a Linux operating system running
   * within the instance. If not specified, the server chooses a default device
   * name to apply to this disk, in the form persistent-disk-x, where x is a
   * number assigned by Google Compute Engine. This field is only applicable for
   * persistent disks.
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
   * Optional. The name of the disk.
   *
   * @param string $diskName
   */
  public function setDiskName($diskName)
  {
    $this->diskName = $diskName;
  }
  /**
   * @return string
   */
  public function getDiskName()
  {
    return $this->diskName;
  }
  /**
   * Optional. The type of disk provisioning to use for the VM.
   *
   * Accepted values: COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED,
   * COMPUTE_ENGINE_DISK_TYPE_STANDARD, COMPUTE_ENGINE_DISK_TYPE_SSD,
   * COMPUTE_ENGINE_DISK_TYPE_BALANCED,
   * COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED
   *
   * @param self::DISK_TYPE_* $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return self::DISK_TYPE_*
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Optional. The encryption to apply to the boot disk.
   *
   * @param Encryption $encryption
   */
  public function setEncryption(Encryption $encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return Encryption
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * The image to use when creating the disk.
   *
   * @param DiskImageDefaults $image
   */
  public function setImage(DiskImageDefaults $image)
  {
    $this->image = $image;
  }
  /**
   * @return DiskImageDefaults
   */
  public function getImage()
  {
    return $this->image;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BootDiskDefaults::class, 'Google_Service_VMMigrationService_BootDiskDefaults');
