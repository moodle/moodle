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

namespace Google\Service\Batch;

class AttachedDisk extends \Google\Model
{
  /**
   * Device name that the guest operating system will see. It is used by
   * Runnable.volumes field to mount disks. So please specify the device_name if
   * you want Batch to help mount the disk, and it should match the device_name
   * field in volumes.
   *
   * @var string
   */
  public $deviceName;
  /**
   * Name of an existing PD.
   *
   * @var string
   */
  public $existingDisk;
  protected $newDiskType = Disk::class;
  protected $newDiskDataType = '';

  /**
   * Device name that the guest operating system will see. It is used by
   * Runnable.volumes field to mount disks. So please specify the device_name if
   * you want Batch to help mount the disk, and it should match the device_name
   * field in volumes.
   *
   * @param string $deviceName
   */
  public function setDeviceName($deviceName)
  {
    $this->deviceName = $deviceName;
  }
  /**
   * @return string
   */
  public function getDeviceName()
  {
    return $this->deviceName;
  }
  /**
   * Name of an existing PD.
   *
   * @param string $existingDisk
   */
  public function setExistingDisk($existingDisk)
  {
    $this->existingDisk = $existingDisk;
  }
  /**
   * @return string
   */
  public function getExistingDisk()
  {
    return $this->existingDisk;
  }
  /**
   * @param Disk $newDisk
   */
  public function setNewDisk(Disk $newDisk)
  {
    $this->newDisk = $newDisk;
  }
  /**
   * @return Disk
   */
  public function getNewDisk()
  {
    return $this->newDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttachedDisk::class, 'Google_Service_Batch_AttachedDisk');
