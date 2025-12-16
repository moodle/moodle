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

class DiskPartition extends \Google\Model
{
  /**
   * Partition capacity.
   *
   * @var string
   */
  public $capacityBytes;
  /**
   * Partition file system.
   *
   * @var string
   */
  public $fileSystem;
  /**
   * Partition free space.
   *
   * @var string
   */
  public $freeBytes;
  /**
   * Mount point (Linux/Windows) or drive letter (Windows).
   *
   * @var string
   */
  public $mountPoint;
  protected $subPartitionsType = DiskPartitionList::class;
  protected $subPartitionsDataType = '';
  /**
   * Partition type.
   *
   * @var string
   */
  public $type;
  /**
   * Partition UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * Partition capacity.
   *
   * @param string $capacityBytes
   */
  public function setCapacityBytes($capacityBytes)
  {
    $this->capacityBytes = $capacityBytes;
  }
  /**
   * @return string
   */
  public function getCapacityBytes()
  {
    return $this->capacityBytes;
  }
  /**
   * Partition file system.
   *
   * @param string $fileSystem
   */
  public function setFileSystem($fileSystem)
  {
    $this->fileSystem = $fileSystem;
  }
  /**
   * @return string
   */
  public function getFileSystem()
  {
    return $this->fileSystem;
  }
  /**
   * Partition free space.
   *
   * @param string $freeBytes
   */
  public function setFreeBytes($freeBytes)
  {
    $this->freeBytes = $freeBytes;
  }
  /**
   * @return string
   */
  public function getFreeBytes()
  {
    return $this->freeBytes;
  }
  /**
   * Mount point (Linux/Windows) or drive letter (Windows).
   *
   * @param string $mountPoint
   */
  public function setMountPoint($mountPoint)
  {
    $this->mountPoint = $mountPoint;
  }
  /**
   * @return string
   */
  public function getMountPoint()
  {
    return $this->mountPoint;
  }
  /**
   * Sub-partitions.
   *
   * @param DiskPartitionList $subPartitions
   */
  public function setSubPartitions(DiskPartitionList $subPartitions)
  {
    $this->subPartitions = $subPartitions;
  }
  /**
   * @return DiskPartitionList
   */
  public function getSubPartitions()
  {
    return $this->subPartitions;
  }
  /**
   * Partition type.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Partition UUID.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskPartition::class, 'Google_Service_MigrationCenterAPI_DiskPartition');
