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

class DiskMoveRequest extends \Google\Model
{
  /**
   * The URL of the destination zone to move the disk. This can be a full or
   * partial URL. For example, the following are all valid URLs to a zone:
   * - https://www.googleapis.com/compute/v1/projects/project/zones/zone     -
   * projects/project/zones/zone     - zones/zone
   *
   * @var string
   */
  public $destinationZone;
  /**
   * The URL of the target disk to move. This can be a full or partial URL. For
   * example, the following are all valid URLs to a disk:        - https://www.g
   * oogleapis.com/compute/v1/projects/project/zones/zone/disks/disk     -
   * projects/project/zones/zone/disks/disk     - zones/zone/disks/disk
   *
   * @var string
   */
  public $targetDisk;

  /**
   * The URL of the destination zone to move the disk. This can be a full or
   * partial URL. For example, the following are all valid URLs to a zone:
   * - https://www.googleapis.com/compute/v1/projects/project/zones/zone     -
   * projects/project/zones/zone     - zones/zone
   *
   * @param string $destinationZone
   */
  public function setDestinationZone($destinationZone)
  {
    $this->destinationZone = $destinationZone;
  }
  /**
   * @return string
   */
  public function getDestinationZone()
  {
    return $this->destinationZone;
  }
  /**
   * The URL of the target disk to move. This can be a full or partial URL. For
   * example, the following are all valid URLs to a disk:        - https://www.g
   * oogleapis.com/compute/v1/projects/project/zones/zone/disks/disk     -
   * projects/project/zones/zone/disks/disk     - zones/zone/disks/disk
   *
   * @param string $targetDisk
   */
  public function setTargetDisk($targetDisk)
  {
    $this->targetDisk = $targetDisk;
  }
  /**
   * @return string
   */
  public function getTargetDisk()
  {
    return $this->targetDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskMoveRequest::class, 'Google_Service_Compute_DiskMoveRequest');
