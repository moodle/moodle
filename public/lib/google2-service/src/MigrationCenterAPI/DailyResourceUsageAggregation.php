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

class DailyResourceUsageAggregation extends \Google\Model
{
  protected $cpuType = DailyResourceUsageAggregationCPU::class;
  protected $cpuDataType = '';
  protected $dateType = Date::class;
  protected $dateDataType = '';
  protected $diskType = DailyResourceUsageAggregationDisk::class;
  protected $diskDataType = '';
  protected $memoryType = DailyResourceUsageAggregationMemory::class;
  protected $memoryDataType = '';
  protected $networkType = DailyResourceUsageAggregationNetwork::class;
  protected $networkDataType = '';

  /**
   * CPU usage.
   *
   * @param DailyResourceUsageAggregationCPU $cpu
   */
  public function setCpu(DailyResourceUsageAggregationCPU $cpu)
  {
    $this->cpu = $cpu;
  }
  /**
   * @return DailyResourceUsageAggregationCPU
   */
  public function getCpu()
  {
    return $this->cpu;
  }
  /**
   * Aggregation date. Day boundaries are at midnight UTC.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Disk usage.
   *
   * @param DailyResourceUsageAggregationDisk $disk
   */
  public function setDisk(DailyResourceUsageAggregationDisk $disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return DailyResourceUsageAggregationDisk
   */
  public function getDisk()
  {
    return $this->disk;
  }
  /**
   * Memory usage.
   *
   * @param DailyResourceUsageAggregationMemory $memory
   */
  public function setMemory(DailyResourceUsageAggregationMemory $memory)
  {
    $this->memory = $memory;
  }
  /**
   * @return DailyResourceUsageAggregationMemory
   */
  public function getMemory()
  {
    return $this->memory;
  }
  /**
   * Network usage.
   *
   * @param DailyResourceUsageAggregationNetwork $network
   */
  public function setNetwork(DailyResourceUsageAggregationNetwork $network)
  {
    $this->network = $network;
  }
  /**
   * @return DailyResourceUsageAggregationNetwork
   */
  public function getNetwork()
  {
    return $this->network;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DailyResourceUsageAggregation::class, 'Google_Service_MigrationCenterAPI_DailyResourceUsageAggregation');
