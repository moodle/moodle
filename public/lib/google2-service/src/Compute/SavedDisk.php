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

class SavedDisk extends \Google\Model
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
  public const STORAGE_BYTES_STATUS_UPDATING = 'UPDATING';
  public const STORAGE_BYTES_STATUS_UP_TO_DATE = 'UP_TO_DATE';
  /**
   * Output only. [Output Only] The architecture of the attached disk.
   *
   * @var string
   */
  public $architecture;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#savedDisk
   * for attached disks.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. Specifies a URL of the disk attached to the source instance.
   *
   * @var string
   */
  public $sourceDisk;
  /**
   * Output only. [Output Only] Size of the individual disk snapshot used by
   * this machine image.
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
   * Output only. [Output Only] The architecture of the attached disk.
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
   * Output only. [Output Only] Type of the resource. Always compute#savedDisk
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
   * Output only. Specifies a URL of the disk attached to the source instance.
   *
   * @param string $sourceDisk
   */
  public function setSourceDisk($sourceDisk)
  {
    $this->sourceDisk = $sourceDisk;
  }
  /**
   * @return string
   */
  public function getSourceDisk()
  {
    return $this->sourceDisk;
  }
  /**
   * Output only. [Output Only] Size of the individual disk snapshot used by
   * this machine image.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavedDisk::class, 'Google_Service_Compute_SavedDisk');
