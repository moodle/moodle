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

class AwsSourceDiskDetails extends \Google\Model
{
  /**
   * Unspecified AWS disk type. Should not be used.
   */
  public const DISK_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * GP2 disk type.
   */
  public const DISK_TYPE_GP2 = 'GP2';
  /**
   * GP3 disk type.
   */
  public const DISK_TYPE_GP3 = 'GP3';
  /**
   * IO1 disk type.
   */
  public const DISK_TYPE_IO1 = 'IO1';
  /**
   * IO2 disk type.
   */
  public const DISK_TYPE_IO2 = 'IO2';
  /**
   * ST1 disk type.
   */
  public const DISK_TYPE_ST1 = 'ST1';
  /**
   * SC1 disk type.
   */
  public const DISK_TYPE_SC1 = 'SC1';
  /**
   * Standard disk type.
   */
  public const DISK_TYPE_STANDARD = 'STANDARD';
  /**
   * Optional. Output only. Disk type.
   *
   * @var string
   */
  public $diskType;
  /**
   * Output only. Size in GiB.
   *
   * @var string
   */
  public $sizeGib;
  /**
   * Optional. Output only. A map of AWS volume tags.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Required. AWS volume ID.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Optional. Output only. Disk type.
   *
   * Accepted values: TYPE_UNSPECIFIED, GP2, GP3, IO1, IO2, ST1, SC1, STANDARD
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
   * Output only. Size in GiB.
   *
   * @param string $sizeGib
   */
  public function setSizeGib($sizeGib)
  {
    $this->sizeGib = $sizeGib;
  }
  /**
   * @return string
   */
  public function getSizeGib()
  {
    return $this->sizeGib;
  }
  /**
   * Optional. Output only. A map of AWS volume tags.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Required. AWS volume ID.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsSourceDiskDetails::class, 'Google_Service_VMMigrationService_AwsSourceDiskDetails');
