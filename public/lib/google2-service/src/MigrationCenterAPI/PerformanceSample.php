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

class PerformanceSample extends \Google\Model
{
  protected $cpuType = CpuUsageSample::class;
  protected $cpuDataType = '';
  protected $diskType = DiskUsageSample::class;
  protected $diskDataType = '';
  protected $memoryType = MemoryUsageSample::class;
  protected $memoryDataType = '';
  protected $networkType = NetworkUsageSample::class;
  protected $networkDataType = '';
  /**
   * Time the sample was collected. If omitted, the frame report time will be
   * used.
   *
   * @var string
   */
  public $sampleTime;

  /**
   * CPU usage sample.
   *
   * @param CpuUsageSample $cpu
   */
  public function setCpu(CpuUsageSample $cpu)
  {
    $this->cpu = $cpu;
  }
  /**
   * @return CpuUsageSample
   */
  public function getCpu()
  {
    return $this->cpu;
  }
  /**
   * Disk usage sample.
   *
   * @param DiskUsageSample $disk
   */
  public function setDisk(DiskUsageSample $disk)
  {
    $this->disk = $disk;
  }
  /**
   * @return DiskUsageSample
   */
  public function getDisk()
  {
    return $this->disk;
  }
  /**
   * Memory usage sample.
   *
   * @param MemoryUsageSample $memory
   */
  public function setMemory(MemoryUsageSample $memory)
  {
    $this->memory = $memory;
  }
  /**
   * @return MemoryUsageSample
   */
  public function getMemory()
  {
    return $this->memory;
  }
  /**
   * Network usage sample.
   *
   * @param NetworkUsageSample $network
   */
  public function setNetwork(NetworkUsageSample $network)
  {
    $this->network = $network;
  }
  /**
   * @return NetworkUsageSample
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Time the sample was collected. If omitted, the frame report time will be
   * used.
   *
   * @param string $sampleTime
   */
  public function setSampleTime($sampleTime)
  {
    $this->sampleTime = $sampleTime;
  }
  /**
   * @return string
   */
  public function getSampleTime()
  {
    return $this->sampleTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceSample::class, 'Google_Service_MigrationCenterAPI_PerformanceSample');
