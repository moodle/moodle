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

class MachineDiskDetails extends \Google\Model
{
  protected $disksType = DiskEntryList::class;
  protected $disksDataType = '';
  /**
   * Disk total Capacity.
   *
   * @var string
   */
  public $totalCapacityBytes;
  /**
   * Total disk free space.
   *
   * @var string
   */
  public $totalFreeBytes;

  /**
   * List of disks.
   *
   * @param DiskEntryList $disks
   */
  public function setDisks(DiskEntryList $disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return DiskEntryList
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Disk total Capacity.
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
  /**
   * Total disk free space.
   *
   * @param string $totalFreeBytes
   */
  public function setTotalFreeBytes($totalFreeBytes)
  {
    $this->totalFreeBytes = $totalFreeBytes;
  }
  /**
   * @return string
   */
  public function getTotalFreeBytes()
  {
    return $this->totalFreeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineDiskDetails::class, 'Google_Service_MigrationCenterAPI_MachineDiskDetails');
