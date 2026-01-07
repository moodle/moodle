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

class DiskPartitionDetails extends \Google\Model
{
  /**
   * Output only. Total free space of all partitions.
   *
   * @var string
   */
  public $freeSpaceBytes;
  protected $partitionsType = DiskPartitionList::class;
  protected $partitionsDataType = '';
  /**
   * Output only. Total capacity of all partitions.
   *
   * @var string
   */
  public $totalCapacityBytes;

  /**
   * Output only. Total free space of all partitions.
   *
   * @param string $freeSpaceBytes
   */
  public function setFreeSpaceBytes($freeSpaceBytes)
  {
    $this->freeSpaceBytes = $freeSpaceBytes;
  }
  /**
   * @return string
   */
  public function getFreeSpaceBytes()
  {
    return $this->freeSpaceBytes;
  }
  /**
   * Optional. List of partitions.
   *
   * @param DiskPartitionList $partitions
   */
  public function setPartitions(DiskPartitionList $partitions)
  {
    $this->partitions = $partitions;
  }
  /**
   * @return DiskPartitionList
   */
  public function getPartitions()
  {
    return $this->partitions;
  }
  /**
   * Output only. Total capacity of all partitions.
   *
   * @param string $totalCapacityBytes
   */
  public function setTotalCapacityBytes($totalCapacityBytes)
  {
    $this->totalCapacityBytes = $totalCapacityBytes;
  }
  /**
   * @return string
   */
  public function getTotalCapacityBytes()
  {
    return $this->totalCapacityBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskPartitionDetails::class, 'Google_Service_MigrationCenterAPI_DiskPartitionDetails');
