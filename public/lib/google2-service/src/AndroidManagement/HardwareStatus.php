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

namespace Google\Service\AndroidManagement;

class HardwareStatus extends \Google\Collection
{
  protected $collection_key = 'skinTemperatures';
  /**
   * Current battery temperatures in Celsius for each battery on the device.
   *
   * @var float[]
   */
  public $batteryTemperatures;
  /**
   * Current CPU temperatures in Celsius for each CPU on the device.
   *
   * @var float[]
   */
  public $cpuTemperatures;
  /**
   * CPU usages in percentage for each core available on the device. Usage is 0
   * for each unplugged core. Empty array implies that CPU usage is not
   * supported in the system.
   *
   * @var float[]
   */
  public $cpuUsages;
  /**
   * The time the measurements were taken.
   *
   * @var string
   */
  public $createTime;
  /**
   * Fan speeds in RPM for each fan on the device. Empty array means that there
   * are no fans or fan speed is not supported on the system.
   *
   * @var float[]
   */
  public $fanSpeeds;
  /**
   * Current GPU temperatures in Celsius for each GPU on the device.
   *
   * @var float[]
   */
  public $gpuTemperatures;
  /**
   * Current device skin temperatures in Celsius.
   *
   * @var float[]
   */
  public $skinTemperatures;

  /**
   * Current battery temperatures in Celsius for each battery on the device.
   *
   * @param float[] $batteryTemperatures
   */
  public function setBatteryTemperatures($batteryTemperatures)
  {
    $this->batteryTemperatures = $batteryTemperatures;
  }
  /**
   * @return float[]
   */
  public function getBatteryTemperatures()
  {
    return $this->batteryTemperatures;
  }
  /**
   * Current CPU temperatures in Celsius for each CPU on the device.
   *
   * @param float[] $cpuTemperatures
   */
  public function setCpuTemperatures($cpuTemperatures)
  {
    $this->cpuTemperatures = $cpuTemperatures;
  }
  /**
   * @return float[]
   */
  public function getCpuTemperatures()
  {
    return $this->cpuTemperatures;
  }
  /**
   * CPU usages in percentage for each core available on the device. Usage is 0
   * for each unplugged core. Empty array implies that CPU usage is not
   * supported in the system.
   *
   * @param float[] $cpuUsages
   */
  public function setCpuUsages($cpuUsages)
  {
    $this->cpuUsages = $cpuUsages;
  }
  /**
   * @return float[]
   */
  public function getCpuUsages()
  {
    return $this->cpuUsages;
  }
  /**
   * The time the measurements were taken.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Fan speeds in RPM for each fan on the device. Empty array means that there
   * are no fans or fan speed is not supported on the system.
   *
   * @param float[] $fanSpeeds
   */
  public function setFanSpeeds($fanSpeeds)
  {
    $this->fanSpeeds = $fanSpeeds;
  }
  /**
   * @return float[]
   */
  public function getFanSpeeds()
  {
    return $this->fanSpeeds;
  }
  /**
   * Current GPU temperatures in Celsius for each GPU on the device.
   *
   * @param float[] $gpuTemperatures
   */
  public function setGpuTemperatures($gpuTemperatures)
  {
    $this->gpuTemperatures = $gpuTemperatures;
  }
  /**
   * @return float[]
   */
  public function getGpuTemperatures()
  {
    return $this->gpuTemperatures;
  }
  /**
   * Current device skin temperatures in Celsius.
   *
   * @param float[] $skinTemperatures
   */
  public function setSkinTemperatures($skinTemperatures)
  {
    $this->skinTemperatures = $skinTemperatures;
  }
  /**
   * @return float[]
   */
  public function getSkinTemperatures()
  {
    return $this->skinTemperatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HardwareStatus::class, 'Google_Service_AndroidManagement_HardwareStatus');
