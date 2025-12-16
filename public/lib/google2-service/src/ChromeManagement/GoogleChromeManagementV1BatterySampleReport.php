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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1BatterySampleReport extends \Google\Model
{
  /**
   * Output only. Battery charge percentage.
   *
   * @var int
   */
  public $chargeRate;
  /**
   * Output only. Battery current (mA).
   *
   * @var string
   */
  public $current;
  /**
   * Output only. The battery discharge rate measured in mW. Positive if the
   * battery is being discharged, negative if it's being charged.
   *
   * @var int
   */
  public $dischargeRate;
  /**
   * Output only. Battery remaining capacity (mAmpere-hours).
   *
   * @var string
   */
  public $remainingCapacity;
  /**
   * Output only. Timestamp of when the sample was collected on device
   *
   * @var string
   */
  public $reportTime;
  /**
   * Output only. Battery status read from sysfs. Example: Discharging
   *
   * @var string
   */
  public $status;
  /**
   * Output only. Temperature in Celsius degrees.
   *
   * @var int
   */
  public $temperature;
  /**
   * Output only. Battery voltage (millivolt).
   *
   * @var string
   */
  public $voltage;

  /**
   * Output only. Battery charge percentage.
   *
   * @param int $chargeRate
   */
  public function setChargeRate($chargeRate)
  {
    $this->chargeRate = $chargeRate;
  }
  /**
   * @return int
   */
  public function getChargeRate()
  {
    return $this->chargeRate;
  }
  /**
   * Output only. Battery current (mA).
   *
   * @param string $current
   */
  public function setCurrent($current)
  {
    $this->current = $current;
  }
  /**
   * @return string
   */
  public function getCurrent()
  {
    return $this->current;
  }
  /**
   * Output only. The battery discharge rate measured in mW. Positive if the
   * battery is being discharged, negative if it's being charged.
   *
   * @param int $dischargeRate
   */
  public function setDischargeRate($dischargeRate)
  {
    $this->dischargeRate = $dischargeRate;
  }
  /**
   * @return int
   */
  public function getDischargeRate()
  {
    return $this->dischargeRate;
  }
  /**
   * Output only. Battery remaining capacity (mAmpere-hours).
   *
   * @param string $remainingCapacity
   */
  public function setRemainingCapacity($remainingCapacity)
  {
    $this->remainingCapacity = $remainingCapacity;
  }
  /**
   * @return string
   */
  public function getRemainingCapacity()
  {
    return $this->remainingCapacity;
  }
  /**
   * Output only. Timestamp of when the sample was collected on device
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Output only. Battery status read from sysfs. Example: Discharging
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Temperature in Celsius degrees.
   *
   * @param int $temperature
   */
  public function setTemperature($temperature)
  {
    $this->temperature = $temperature;
  }
  /**
   * @return int
   */
  public function getTemperature()
  {
    return $this->temperature;
  }
  /**
   * Output only. Battery voltage (millivolt).
   *
   * @param string $voltage
   */
  public function setVoltage($voltage)
  {
    $this->voltage = $voltage;
  }
  /**
   * @return string
   */
  public function getVoltage()
  {
    return $this->voltage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1BatterySampleReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1BatterySampleReport');
