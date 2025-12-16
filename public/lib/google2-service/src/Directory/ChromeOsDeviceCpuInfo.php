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

class ChromeOsDeviceCpuInfo extends \Google\Collection
{
  protected $collection_key = 'logicalCpus';
  /**
   * The CPU architecture.
   *
   * @var string
   */
  public $architecture;
  protected $logicalCpusType = ChromeOsDeviceCpuInfoLogicalCpus::class;
  protected $logicalCpusDataType = 'array';
  /**
   * The max CPU clock speed in kHz.
   *
   * @var int
   */
  public $maxClockSpeedKhz;
  /**
   * The CPU model name.
   *
   * @var string
   */
  public $model;

  /**
   * The CPU architecture.
   *
   * @param string $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return string
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Information for the Logical CPUs
   *
   * @param ChromeOsDeviceCpuInfoLogicalCpus[] $logicalCpus
   */
  public function setLogicalCpus($logicalCpus)
  {
    $this->logicalCpus = $logicalCpus;
  }
  /**
   * @return ChromeOsDeviceCpuInfoLogicalCpus[]
   */
  public function getLogicalCpus()
  {
    return $this->logicalCpus;
  }
  /**
   * The max CPU clock speed in kHz.
   *
   * @param int $maxClockSpeedKhz
   */
  public function setMaxClockSpeedKhz($maxClockSpeedKhz)
  {
    $this->maxClockSpeedKhz = $maxClockSpeedKhz;
  }
  /**
   * @return int
   */
  public function getMaxClockSpeedKhz()
  {
    return $this->maxClockSpeedKhz;
  }
  /**
   * The CPU model name.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceCpuInfo::class, 'Google_Service_Directory_ChromeOsDeviceCpuInfo');
