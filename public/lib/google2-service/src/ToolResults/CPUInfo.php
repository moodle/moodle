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

namespace Google\Service\ToolResults;

class CPUInfo extends \Google\Model
{
  /**
   * description of the device processor ie '1.8 GHz hexa core 64-bit ARMv8-A'
   *
   * @var string
   */
  public $cpuProcessor;
  /**
   * the CPU clock speed in GHz
   *
   * @var float
   */
  public $cpuSpeedInGhz;
  /**
   * the number of CPU cores
   *
   * @var int
   */
  public $numberOfCores;

  /**
   * description of the device processor ie '1.8 GHz hexa core 64-bit ARMv8-A'
   *
   * @param string $cpuProcessor
   */
  public function setCpuProcessor($cpuProcessor)
  {
    $this->cpuProcessor = $cpuProcessor;
  }
  /**
   * @return string
   */
  public function getCpuProcessor()
  {
    return $this->cpuProcessor;
  }
  /**
   * the CPU clock speed in GHz
   *
   * @param float $cpuSpeedInGhz
   */
  public function setCpuSpeedInGhz($cpuSpeedInGhz)
  {
    $this->cpuSpeedInGhz = $cpuSpeedInGhz;
  }
  /**
   * @return float
   */
  public function getCpuSpeedInGhz()
  {
    return $this->cpuSpeedInGhz;
  }
  /**
   * the number of CPU cores
   *
   * @param int $numberOfCores
   */
  public function setNumberOfCores($numberOfCores)
  {
    $this->numberOfCores = $numberOfCores;
  }
  /**
   * @return int
   */
  public function getNumberOfCores()
  {
    return $this->numberOfCores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CPUInfo::class, 'Google_Service_ToolResults_CPUInfo');
