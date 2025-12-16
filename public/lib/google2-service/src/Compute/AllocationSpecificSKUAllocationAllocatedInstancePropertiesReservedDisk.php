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

class AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk extends \Google\Model
{
  public const INTERFACE_NVME = 'NVME';
  public const INTERFACE_SCSI = 'SCSI';
  /**
   * Specifies the size of the disk in base-2 GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME. The default isSCSI. For performance characteristics of
   * SCSI over NVMe, seeLocal SSD performance.
   *
   * @var string
   */
  public $interface;

  /**
   * Specifies the size of the disk in base-2 GB.
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
   * Specifies the disk interface to use for attaching this disk, which is
   * either SCSI or NVME. The default isSCSI. For performance characteristics of
   * SCSI over NVMe, seeLocal SSD performance.
   *
   * Accepted values: NVME, SCSI
   *
   * @param self::INTERFACE_* $interface
   */
  public function setInterface($interface)
  {
    $this->interface = $interface;
  }
  /**
   * @return self::INTERFACE_*
   */
  public function getInterface()
  {
    return $this->interface;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk::class, 'Google_Service_Compute_AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk');
