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

namespace Google\Service\OracleDatabase;

class DbSystemInitialStorageSizeProperties extends \Google\Collection
{
  /**
   * Unspecified shape type.
   */
  public const SHAPE_TYPE_SHAPE_TYPE_UNSPECIFIED = 'SHAPE_TYPE_UNSPECIFIED';
  /**
   * Standard X86.
   */
  public const SHAPE_TYPE_STANDARD_X86 = 'STANDARD_X86';
  /**
   * Unspecified storage management.
   */
  public const STORAGE_MANAGEMENT_STORAGE_MANAGEMENT_UNSPECIFIED = 'STORAGE_MANAGEMENT_UNSPECIFIED';
  /**
   * Automatic Storage Management.
   */
  public const STORAGE_MANAGEMENT_ASM = 'ASM';
  /**
   * Logical Volume Management.
   */
  public const STORAGE_MANAGEMENT_LVM = 'LVM';
  protected $collection_key = 'storageSizeDetails';
  protected $launchFromBackupStorageSizeDetailsType = StorageSizeDetails::class;
  protected $launchFromBackupStorageSizeDetailsDataType = 'array';
  /**
   * Output only. VM shape platform type
   *
   * @var string
   */
  public $shapeType;
  /**
   * Output only. The storage option used in DB system.
   *
   * @var string
   */
  public $storageManagement;
  protected $storageSizeDetailsType = StorageSizeDetails::class;
  protected $storageSizeDetailsDataType = 'array';

  /**
   * Output only. List of storage disk details available for launches from
   * backup.
   *
   * @param StorageSizeDetails[] $launchFromBackupStorageSizeDetails
   */
  public function setLaunchFromBackupStorageSizeDetails($launchFromBackupStorageSizeDetails)
  {
    $this->launchFromBackupStorageSizeDetails = $launchFromBackupStorageSizeDetails;
  }
  /**
   * @return StorageSizeDetails[]
   */
  public function getLaunchFromBackupStorageSizeDetails()
  {
    return $this->launchFromBackupStorageSizeDetails;
  }
  /**
   * Output only. VM shape platform type
   *
   * Accepted values: SHAPE_TYPE_UNSPECIFIED, STANDARD_X86
   *
   * @param self::SHAPE_TYPE_* $shapeType
   */
  public function setShapeType($shapeType)
  {
    $this->shapeType = $shapeType;
  }
  /**
   * @return self::SHAPE_TYPE_*
   */
  public function getShapeType()
  {
    return $this->shapeType;
  }
  /**
   * Output only. The storage option used in DB system.
   *
   * Accepted values: STORAGE_MANAGEMENT_UNSPECIFIED, ASM, LVM
   *
   * @param self::STORAGE_MANAGEMENT_* $storageManagement
   */
  public function setStorageManagement($storageManagement)
  {
    $this->storageManagement = $storageManagement;
  }
  /**
   * @return self::STORAGE_MANAGEMENT_*
   */
  public function getStorageManagement()
  {
    return $this->storageManagement;
  }
  /**
   * Output only. List of storage disk details.
   *
   * @param StorageSizeDetails[] $storageSizeDetails
   */
  public function setStorageSizeDetails($storageSizeDetails)
  {
    $this->storageSizeDetails = $storageSizeDetails;
  }
  /**
   * @return StorageSizeDetails[]
   */
  public function getStorageSizeDetails()
  {
    return $this->storageSizeDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystemInitialStorageSizeProperties::class, 'Google_Service_OracleDatabase_DbSystemInitialStorageSizeProperties');
