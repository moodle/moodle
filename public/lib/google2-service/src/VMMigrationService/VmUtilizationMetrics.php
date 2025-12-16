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

namespace Google\Service\VMMigrationService;

class VmUtilizationMetrics extends \Google\Model
{
  /**
   * Average CPU usage, percent.
   *
   * @var int
   */
  public $cpuAveragePercent;
  /**
   * Max CPU usage, percent.
   *
   * @var int
   */
  public $cpuMaxPercent;
  /**
   * Average disk IO rate, in kilobytes per second.
   *
   * @var string
   */
  public $diskIoRateAverageKbps;
  /**
   * Max disk IO rate, in kilobytes per second.
   *
   * @var string
   */
  public $diskIoRateMaxKbps;
  /**
   * Average memory usage, percent.
   *
   * @var int
   */
  public $memoryAveragePercent;
  /**
   * Max memory usage, percent.
   *
   * @var int
   */
  public $memoryMaxPercent;
  /**
   * Average network throughput (combined transmit-rates and receive-rates), in
   * kilobytes per second.
   *
   * @var string
   */
  public $networkThroughputAverageKbps;
  /**
   * Max network throughput (combined transmit-rates and receive-rates), in
   * kilobytes per second.
   *
   * @var string
   */
  public $networkThroughputMaxKbps;

  /**
   * Average CPU usage, percent.
   *
   * @param int $cpuAveragePercent
   */
  public function setCpuAveragePercent($cpuAveragePercent)
  {
    $this->cpuAveragePercent = $cpuAveragePercent;
  }
  /**
   * @return int
   */
  public function getCpuAveragePercent()
  {
    return $this->cpuAveragePercent;
  }
  /**
   * Max CPU usage, percent.
   *
   * @param int $cpuMaxPercent
   */
  public function setCpuMaxPercent($cpuMaxPercent)
  {
    $this->cpuMaxPercent = $cpuMaxPercent;
  }
  /**
   * @return int
   */
  public function getCpuMaxPercent()
  {
    return $this->cpuMaxPercent;
  }
  /**
   * Average disk IO rate, in kilobytes per second.
   *
   * @param string $diskIoRateAverageKbps
   */
  public function setDiskIoRateAverageKbps($diskIoRateAverageKbps)
  {
    $this->diskIoRateAverageKbps = $diskIoRateAverageKbps;
  }
  /**
   * @return string
   */
  public function getDiskIoRateAverageKbps()
  {
    return $this->diskIoRateAverageKbps;
  }
  /**
   * Max disk IO rate, in kilobytes per second.
   *
   * @param string $diskIoRateMaxKbps
   */
  public function setDiskIoRateMaxKbps($diskIoRateMaxKbps)
  {
    $this->diskIoRateMaxKbps = $diskIoRateMaxKbps;
  }
  /**
   * @return string
   */
  public function getDiskIoRateMaxKbps()
  {
    return $this->diskIoRateMaxKbps;
  }
  /**
   * Average memory usage, percent.
   *
   * @param int $memoryAveragePercent
   */
  public function setMemoryAveragePercent($memoryAveragePercent)
  {
    $this->memoryAveragePercent = $memoryAveragePercent;
  }
  /**
   * @return int
   */
  public function getMemoryAveragePercent()
  {
    return $this->memoryAveragePercent;
  }
  /**
   * Max memory usage, percent.
   *
   * @param int $memoryMaxPercent
   */
  public function setMemoryMaxPercent($memoryMaxPercent)
  {
    $this->memoryMaxPercent = $memoryMaxPercent;
  }
  /**
   * @return int
   */
  public function getMemoryMaxPercent()
  {
    return $this->memoryMaxPercent;
  }
  /**
   * Average network throughput (combined transmit-rates and receive-rates), in
   * kilobytes per second.
   *
   * @param string $networkThroughputAverageKbps
   */
  public function setNetworkThroughputAverageKbps($networkThroughputAverageKbps)
  {
    $this->networkThroughputAverageKbps = $networkThroughputAverageKbps;
  }
  /**
   * @return string
   */
  public function getNetworkThroughputAverageKbps()
  {
    return $this->networkThroughputAverageKbps;
  }
  /**
   * Max network throughput (combined transmit-rates and receive-rates), in
   * kilobytes per second.
   *
   * @param string $networkThroughputMaxKbps
   */
  public function setNetworkThroughputMaxKbps($networkThroughputMaxKbps)
  {
    $this->networkThroughputMaxKbps = $networkThroughputMaxKbps;
  }
  /**
   * @return string
   */
  public function getNetworkThroughputMaxKbps()
  {
    return $this->networkThroughputMaxKbps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmUtilizationMetrics::class, 'Google_Service_VMMigrationService_VmUtilizationMetrics');
