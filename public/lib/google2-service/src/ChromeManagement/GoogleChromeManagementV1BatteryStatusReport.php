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

class GoogleChromeManagementV1BatteryStatusReport extends \Google\Collection
{
  /**
   * Health unknown.
   */
  public const BATTERY_HEALTH_BATTERY_HEALTH_UNSPECIFIED = 'BATTERY_HEALTH_UNSPECIFIED';
  /**
   * Battery is healthy, full charge capacity / design capacity > 80%
   */
  public const BATTERY_HEALTH_BATTERY_HEALTH_NORMAL = 'BATTERY_HEALTH_NORMAL';
  /**
   * Battery is moderately unhealthy and suggested to be replaced soon, full
   * charge capacity / design capacity 75% - 80%
   */
  public const BATTERY_HEALTH_BATTERY_REPLACE_SOON = 'BATTERY_REPLACE_SOON';
  /**
   * Battery is unhealthy and suggested to be replaced, full charge capacity /
   * design capacity < 75%
   */
  public const BATTERY_HEALTH_BATTERY_REPLACE_NOW = 'BATTERY_REPLACE_NOW';
  protected $collection_key = 'sample';
  /**
   * Output only. Battery health.
   *
   * @var string
   */
  public $batteryHealth;
  /**
   * Output only. Cycle count.
   *
   * @var int
   */
  public $cycleCount;
  /**
   * Output only. Full charge capacity (mAmpere-hours).
   *
   * @var string
   */
  public $fullChargeCapacity;
  /**
   * Output only. Timestamp of when the sample was collected on device
   *
   * @var string
   */
  public $reportTime;
  protected $sampleType = GoogleChromeManagementV1BatterySampleReport::class;
  protected $sampleDataType = 'array';
  /**
   * Output only. Battery serial number.
   *
   * @var string
   */
  public $serialNumber;

  /**
   * Output only. Battery health.
   *
   * Accepted values: BATTERY_HEALTH_UNSPECIFIED, BATTERY_HEALTH_NORMAL,
   * BATTERY_REPLACE_SOON, BATTERY_REPLACE_NOW
   *
   * @param self::BATTERY_HEALTH_* $batteryHealth
   */
  public function setBatteryHealth($batteryHealth)
  {
    $this->batteryHealth = $batteryHealth;
  }
  /**
   * @return self::BATTERY_HEALTH_*
   */
  public function getBatteryHealth()
  {
    return $this->batteryHealth;
  }
  /**
   * Output only. Cycle count.
   *
   * @param int $cycleCount
   */
  public function setCycleCount($cycleCount)
  {
    $this->cycleCount = $cycleCount;
  }
  /**
   * @return int
   */
  public function getCycleCount()
  {
    return $this->cycleCount;
  }
  /**
   * Output only. Full charge capacity (mAmpere-hours).
   *
   * @param string $fullChargeCapacity
   */
  public function setFullChargeCapacity($fullChargeCapacity)
  {
    $this->fullChargeCapacity = $fullChargeCapacity;
  }
  /**
   * @return string
   */
  public function getFullChargeCapacity()
  {
    return $this->fullChargeCapacity;
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
   * Output only. Sampling data for the battery sorted in a decreasing order of
   * report_time.
   *
   * @param GoogleChromeManagementV1BatterySampleReport[] $sample
   */
  public function setSample($sample)
  {
    $this->sample = $sample;
  }
  /**
   * @return GoogleChromeManagementV1BatterySampleReport[]
   */
  public function getSample()
  {
    return $this->sample;
  }
  /**
   * Output only. Battery serial number.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1BatteryStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1BatteryStatusReport');
