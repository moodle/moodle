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

namespace Google\Service\CloudNaturalLanguage;

class DiskMetric extends \Google\Model
{
  public const DISK_TYPE_UNKNOWN_DISK_TYPE = 'UNKNOWN_DISK_TYPE';
  public const DISK_TYPE_REGIONAL_SSD = 'REGIONAL_SSD';
  public const DISK_TYPE_REGIONAL_STORAGE = 'REGIONAL_STORAGE';
  public const DISK_TYPE_PD_SSD = 'PD_SSD';
  public const DISK_TYPE_PD_STANDARD = 'PD_STANDARD';
  public const DISK_TYPE_STORAGE_SNAPSHOT = 'STORAGE_SNAPSHOT';
  /**
   * Required. Type of Disk, e.g. REGIONAL_SSD.
   *
   * @var string
   */
  public $diskType;
  /**
   * Required. Seconds of physical disk usage, e.g. 3600.
   *
   * @var string
   */
  public $gibSec;

  /**
   * Required. Type of Disk, e.g. REGIONAL_SSD.
   *
   * Accepted values: UNKNOWN_DISK_TYPE, REGIONAL_SSD, REGIONAL_STORAGE, PD_SSD,
   * PD_STANDARD, STORAGE_SNAPSHOT
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
   * Required. Seconds of physical disk usage, e.g. 3600.
   *
   * @param string $gibSec
   */
  public function setGibSec($gibSec)
  {
    $this->gibSec = $gibSec;
  }
  /**
   * @return string
   */
  public function getGibSec()
  {
    return $this->gibSec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskMetric::class, 'Google_Service_CloudNaturalLanguage_DiskMetric');
