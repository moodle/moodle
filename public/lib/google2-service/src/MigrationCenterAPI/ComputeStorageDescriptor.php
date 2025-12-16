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

class ComputeStorageDescriptor extends \Google\Model
{
  /**
   * Unspecified. Fallback to default value based on context.
   */
  public const TYPE_PERSISTENT_DISK_TYPE_UNSPECIFIED = 'PERSISTENT_DISK_TYPE_UNSPECIFIED';
  /**
   * Standard HDD Persistent Disk.
   */
  public const TYPE_PERSISTENT_DISK_TYPE_STANDARD = 'PERSISTENT_DISK_TYPE_STANDARD';
  /**
   * Balanced Persistent Disk.
   */
  public const TYPE_PERSISTENT_DISK_TYPE_BALANCED = 'PERSISTENT_DISK_TYPE_BALANCED';
  /**
   * SSD Persistent Disk.
   */
  public const TYPE_PERSISTENT_DISK_TYPE_SSD = 'PERSISTENT_DISK_TYPE_SSD';
  /**
   * Output only. Disk size in GiB.
   *
   * @var int
   */
  public $sizeGb;
  /**
   * Output only. Disk type backing the storage.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Disk size in GiB.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Output only. Disk type backing the storage.
   *
   * Accepted values: PERSISTENT_DISK_TYPE_UNSPECIFIED,
   * PERSISTENT_DISK_TYPE_STANDARD, PERSISTENT_DISK_TYPE_BALANCED,
   * PERSISTENT_DISK_TYPE_SSD
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
class_alias(ComputeStorageDescriptor::class, 'Google_Service_MigrationCenterAPI_ComputeStorageDescriptor');
