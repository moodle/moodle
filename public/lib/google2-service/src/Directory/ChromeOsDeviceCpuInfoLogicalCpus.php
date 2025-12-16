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

namespace Google\Service\Directory;

class ChromeOsDeviceCpuInfoLogicalCpus extends \Google\Collection
{
  protected $collection_key = 'cStates';
  protected $cStatesType = ChromeOsDeviceCpuInfoLogicalCpusCStates::class;
  protected $cStatesDataType = 'array';
  /**
   * Current frequency the CPU is running at.
   *
   * @var int
   */
  public $currentScalingFrequencyKhz;
  /**
   * Idle time since last boot.
   *
   * @var string
   */
  public $idleDuration;
  /**
   * Maximum frequency the CPU is allowed to run at, by policy.
   *
   * @var int
   */
  public $maxScalingFrequencyKhz;

  /**
   * C-States indicate the power consumption state of the CPU. For more
   * information look at documentation published by the CPU maker.
   *
   * @param ChromeOsDeviceCpuInfoLogicalCpusCStates[] $cStates
   */
  public function setCStates($cStates)
  {
    $this->cStates = $cStates;
  }
  /**
   * @return ChromeOsDeviceCpuInfoLogicalCpusCStates[]
   */
  public function getCStates()
  {
    return $this->cStates;
  }
  /**
   * Current frequency the CPU is running at.
   *
   * @param int $currentScalingFrequencyKhz
   */
  public function setCurrentScalingFrequencyKhz($currentScalingFrequencyKhz)
  {
    $this->currentScalingFrequencyKhz = $currentScalingFrequencyKhz;
  }
  /**
   * @return int
   */
  public function getCurrentScalingFrequencyKhz()
  {
    return $this->currentScalingFrequencyKhz;
  }
  /**
   * Idle time since last boot.
   *
   * @param string $idleDuration
   */
  public function setIdleDuration($idleDuration)
  {
    $this->idleDuration = $idleDuration;
  }
  /**
   * @return string
   */
  public function getIdleDuration()
  {
    return $this->idleDuration;
  }
  /**
   * Maximum frequency the CPU is allowed to run at, by policy.
   *
   * @param int $maxScalingFrequencyKhz
   */
  public function setMaxScalingFrequencyKhz($maxScalingFrequencyKhz)
  {
    $this->maxScalingFrequencyKhz = $maxScalingFrequencyKhz;
  }
  /**
   * @return int
   */
  public function getMaxScalingFrequencyKhz()
  {
    return $this->maxScalingFrequencyKhz;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceCpuInfoLogicalCpus::class, 'Google_Service_Directory_ChromeOsDeviceCpuInfoLogicalCpus');
