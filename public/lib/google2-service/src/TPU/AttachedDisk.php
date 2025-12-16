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

namespace Google\Service\TPU;

class AttachedDisk extends \Google\Model
{
  /**
   * The disk mode is not known/set.
   */
  public const MODE_DISK_MODE_UNSPECIFIED = 'DISK_MODE_UNSPECIFIED';
  /**
   * Attaches the disk in read-write mode. Only one TPU node can attach a disk
   * in read-write mode at a time.
   */
  public const MODE_READ_WRITE = 'READ_WRITE';
  /**
   * Attaches the disk in read-only mode. Multiple TPU nodes can attach a disk
   * in read-only mode at a time.
   */
  public const MODE_READ_ONLY = 'READ_ONLY';
  /**
   * The mode in which to attach this disk. If not specified, the default is
   * READ_WRITE mode. Only applicable to data_disks.
   *
   * @var string
   */
  public $mode;
  /**
   * Specifies the full path to an existing disk. For example: "projects/my-
   * project/zones/us-central1-c/disks/my-disk".
   *
   * @var string
   */
  public $sourceDisk;

  /**
   * The mode in which to attach this disk. If not specified, the default is
   * READ_WRITE mode. Only applicable to data_disks.
   *
   * Accepted values: DISK_MODE_UNSPECIFIED, READ_WRITE, READ_ONLY
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Specifies the full path to an existing disk. For example: "projects/my-
   * project/zones/us-central1-c/disks/my-disk".
   *
   * @param string $sourceDisk
   */
  public function setSourceDisk($sourceDisk)
  {
    $this->sourceDisk = $sourceDisk;
  }
  /**
   * @return string
   */
  public function getSourceDisk()
  {
    return $this->sourceDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttachedDisk::class, 'Google_Service_TPU_AttachedDisk');
