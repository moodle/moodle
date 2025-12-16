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

class DbSystemOptions extends \Google\Model
{
  /**
   * The storage management is unspecified.
   */
  public const STORAGE_MANAGEMENT_STORAGE_MANAGEMENT_UNSPECIFIED = 'STORAGE_MANAGEMENT_UNSPECIFIED';
  /**
   * Automatic storage management.
   */
  public const STORAGE_MANAGEMENT_ASM = 'ASM';
  /**
   * Logical Volume management.
   */
  public const STORAGE_MANAGEMENT_LVM = 'LVM';
  /**
   * Optional. The storage option used in DB system.
   *
   * @var string
   */
  public $storageManagement;

  /**
   * Optional. The storage option used in DB system.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystemOptions::class, 'Google_Service_OracleDatabase_DbSystemOptions');
