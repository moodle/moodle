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

namespace Google\Service\AIPlatformNotebooks;

class BootDisk extends \Google\Model
{
  /**
   * Disk encryption is not specified.
   */
  public const DISK_ENCRYPTION_DISK_ENCRYPTION_UNSPECIFIED = 'DISK_ENCRYPTION_UNSPECIFIED';
  /**
   * Use Google managed encryption keys to encrypt the boot disk.
   */
  public const DISK_ENCRYPTION_GMEK = 'GMEK';
  /**
   * Use customer managed encryption keys to encrypt the boot disk.
   */
  public const DISK_ENCRYPTION_CMEK = 'CMEK';
  /**
   * Disk type not set.
   */
  public const DISK_TYPE_DISK_TYPE_UNSPECIFIED = 'DISK_TYPE_UNSPECIFIED';
  /**
   * Standard persistent disk type.
   */
  public const DISK_TYPE_PD_STANDARD = 'PD_STANDARD';
  /**
   * SSD persistent disk type.
   */
  public const DISK_TYPE_PD_SSD = 'PD_SSD';
  /**
   * Balanced persistent disk type.
   */
  public const DISK_TYPE_PD_BALANCED = 'PD_BALANCED';
  /**
   * Extreme persistent disk type.
   */
  public const DISK_TYPE_PD_EXTREME = 'PD_EXTREME';
  /**
   * Hyperdisk Balanced persistent disk type.
   */
  public const DISK_TYPE_HYPERDISK_BALANCED = 'HYPERDISK_BALANCED';
  /**
   * Optional. Input only. Disk encryption method used on the boot and data
   * disks, defaults to GMEK.
   *
   * @var string
   */
  public $diskEncryption;
  /**
   * Optional. The size of the boot disk in GB attached to this instance, up to
   * a maximum of 64000 GB (64 TB). If not specified, this defaults to the
   * recommended value of 150GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Optional. Indicates the type of the disk.
   *
   * @var string
   */
  public $diskType;
  /**
   * Optional. Input only. The KMS key used to encrypt the disks, only
   * applicable if disk_encryption is CMEK. Format: `projects/{project_id}/locat
   * ions/{location}/keyRings/{key_ring_id}/cryptoKeys/{key_id}` Learn more
   * about using your own encryption keys.
   *
   * @var string
   */
  public $kmsKey;

  /**
   * Optional. Input only. Disk encryption method used on the boot and data
   * disks, defaults to GMEK.
   *
   * Accepted values: DISK_ENCRYPTION_UNSPECIFIED, GMEK, CMEK
   *
   * @param self::DISK_ENCRYPTION_* $diskEncryption
   */
  public function setDiskEncryption($diskEncryption)
  {
    $this->diskEncryption = $diskEncryption;
  }
  /**
   * @return self::DISK_ENCRYPTION_*
   */
  public function getDiskEncryption()
  {
    return $this->diskEncryption;
  }
  /**
   * Optional. The size of the boot disk in GB attached to this instance, up to
   * a maximum of 64000 GB (64 TB). If not specified, this defaults to the
   * recommended value of 150GB.
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
   * Optional. Indicates the type of the disk.
   *
   * Accepted values: DISK_TYPE_UNSPECIFIED, PD_STANDARD, PD_SSD, PD_BALANCED,
   * PD_EXTREME, HYPERDISK_BALANCED
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
   * Optional. Input only. The KMS key used to encrypt the disks, only
   * applicable if disk_encryption is CMEK. Format: `projects/{project_id}/locat
   * ions/{location}/keyRings/{key_ring_id}/cryptoKeys/{key_id}` Learn more
   * about using your own encryption keys.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BootDisk::class, 'Google_Service_AIPlatformNotebooks_BootDisk');
