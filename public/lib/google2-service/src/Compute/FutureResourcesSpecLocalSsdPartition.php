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

class FutureResourcesSpecLocalSsdPartition extends \Google\Model
{
  public const DISK_INTERFACE_NVME = 'NVME';
  public const DISK_INTERFACE_SCSI = 'SCSI';
  /**
   * Disk interface. Defaults to SCSI.
   *
   * @var string
   */
  public $diskInterface;
  /**
   * The size of the disk in GB.
   *
   * @var string
   */
  public $diskSizeGb;

  /**
   * Disk interface. Defaults to SCSI.
   *
   * Accepted values: NVME, SCSI
   *
   * @param self::DISK_INTERFACE_* $diskInterface
   */
  public function setDiskInterface($diskInterface)
  {
    $this->diskInterface = $diskInterface;
  }
  /**
   * @return self::DISK_INTERFACE_*
   */
  public function getDiskInterface()
  {
    return $this->diskInterface;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpecLocalSsdPartition::class, 'Google_Service_Compute_FutureResourcesSpecLocalSsdPartition');
