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

class GoogleChromeManagementV1RuntimeCountersReport extends \Google\Model
{
  /**
   * Number of times that the device has entered into the hibernation state.
   * Currently obtained via the PSR, count from S0->S4.
   *
   * @var string
   */
  public $enterHibernationCount;
  /**
   * Number of times that the device has entered into the power-off state.
   * Currently obtained via the PSR, count from S0->S5.
   *
   * @var string
   */
  public $enterPoweroffCount;
  /**
   * Number of times that the device has entered into the sleep state. Currently
   * obtained via the PSR, count from S0->S3.
   *
   * @var string
   */
  public $enterSleepCount;
  /**
   * Timestamp when the report was collected.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Total lifetime runtime. Currently always S0 runtime from Intel vPro PSR.
   *
   * @var string
   */
  public $uptimeRuntimeDuration;

  /**
   * Number of times that the device has entered into the hibernation state.
   * Currently obtained via the PSR, count from S0->S4.
   *
   * @param string $enterHibernationCount
   */
  public function setEnterHibernationCount($enterHibernationCount)
  {
    $this->enterHibernationCount = $enterHibernationCount;
  }
  /**
   * @return string
   */
  public function getEnterHibernationCount()
  {
    return $this->enterHibernationCount;
  }
  /**
   * Number of times that the device has entered into the power-off state.
   * Currently obtained via the PSR, count from S0->S5.
   *
   * @param string $enterPoweroffCount
   */
  public function setEnterPoweroffCount($enterPoweroffCount)
  {
    $this->enterPoweroffCount = $enterPoweroffCount;
  }
  /**
   * @return string
   */
  public function getEnterPoweroffCount()
  {
    return $this->enterPoweroffCount;
  }
  /**
   * Number of times that the device has entered into the sleep state. Currently
   * obtained via the PSR, count from S0->S3.
   *
   * @param string $enterSleepCount
   */
  public function setEnterSleepCount($enterSleepCount)
  {
    $this->enterSleepCount = $enterSleepCount;
  }
  /**
   * @return string
   */
  public function getEnterSleepCount()
  {
    return $this->enterSleepCount;
  }
  /**
   * Timestamp when the report was collected.
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
   * Total lifetime runtime. Currently always S0 runtime from Intel vPro PSR.
   *
   * @param string $uptimeRuntimeDuration
   */
  public function setUptimeRuntimeDuration($uptimeRuntimeDuration)
  {
    $this->uptimeRuntimeDuration = $uptimeRuntimeDuration;
  }
  /**
   * @return string
   */
  public function getUptimeRuntimeDuration()
  {
    return $this->uptimeRuntimeDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1RuntimeCountersReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1RuntimeCountersReport');
