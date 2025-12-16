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

class LocalDisk extends \Google\Model
{
  /**
   * Specifies the number of such disks.
   *
   * @var int
   */
  public $diskCount;
  /**
   * Specifies the size of the disk in base-2 GB.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Specifies the desired disk type on the node. This disk type must be a local
   * storage type (e.g.: local-ssd). Note that for nodeTemplates, this should be
   * the name of the disk type and not its URL.
   *
   * @var string
   */
  public $diskType;

  /**
   * Specifies the number of such disks.
   *
   * @param int $diskCount
   */
  public function setDiskCount($diskCount)
  {
    $this->diskCount = $diskCount;
  }
  /**
   * @return int
   */
  public function getDiskCount()
  {
    return $this->diskCount;
  }
  /**
   * Specifies the size of the disk in base-2 GB.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Specifies the desired disk type on the node. This disk type must be a local
   * storage type (e.g.: local-ssd). Note that for nodeTemplates, this should be
   * the name of the disk type and not its URL.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocalDisk::class, 'Google_Service_Compute_LocalDisk');
