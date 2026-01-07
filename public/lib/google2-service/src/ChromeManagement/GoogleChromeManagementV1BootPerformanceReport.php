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

class GoogleChromeManagementV1BootPerformanceReport extends \Google\Model
{
  /**
   * Shutdown reason is not specified.
   */
  public const SHUTDOWN_REASON_SHUTDOWN_REASON_UNSPECIFIED = 'SHUTDOWN_REASON_UNSPECIFIED';
  /**
   * User initiated.
   */
  public const SHUTDOWN_REASON_USER_REQUEST = 'USER_REQUEST';
  /**
   * System update initiated.
   */
  public const SHUTDOWN_REASON_SYSTEM_UPDATE = 'SYSTEM_UPDATE';
  /**
   * Shutdown due to low battery.
   */
  public const SHUTDOWN_REASON_LOW_BATTERY = 'LOW_BATTERY';
  /**
   * Shutdown due to other reasons.
   */
  public const SHUTDOWN_REASON_OTHER = 'OTHER';
  /**
   * Total time to boot up.
   *
   * @var string
   */
  public $bootUpDuration;
  /**
   * The timestamp when power came on.
   *
   * @var string
   */
  public $bootUpTime;
  /**
   * Timestamp when the report was collected.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Total time since shutdown start to power off.
   *
   * @var string
   */
  public $shutdownDuration;
  /**
   * The shutdown reason.
   *
   * @var string
   */
  public $shutdownReason;
  /**
   * The timestamp when shutdown.
   *
   * @var string
   */
  public $shutdownTime;

  /**
   * Total time to boot up.
   *
   * @param string $bootUpDuration
   */
  public function setBootUpDuration($bootUpDuration)
  {
    $this->bootUpDuration = $bootUpDuration;
  }
  /**
   * @return string
   */
  public function getBootUpDuration()
  {
    return $this->bootUpDuration;
  }
  /**
   * The timestamp when power came on.
   *
   * @param string $bootUpTime
   */
  public function setBootUpTime($bootUpTime)
  {
    $this->bootUpTime = $bootUpTime;
  }
  /**
   * @return string
   */
  public function getBootUpTime()
  {
    return $this->bootUpTime;
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
   * Total time since shutdown start to power off.
   *
   * @param string $shutdownDuration
   */
  public function setShutdownDuration($shutdownDuration)
  {
    $this->shutdownDuration = $shutdownDuration;
  }
  /**
   * @return string
   */
  public function getShutdownDuration()
  {
    return $this->shutdownDuration;
  }
  /**
   * The shutdown reason.
   *
   * Accepted values: SHUTDOWN_REASON_UNSPECIFIED, USER_REQUEST, SYSTEM_UPDATE,
   * LOW_BATTERY, OTHER
   *
   * @param self::SHUTDOWN_REASON_* $shutdownReason
   */
  public function setShutdownReason($shutdownReason)
  {
    $this->shutdownReason = $shutdownReason;
  }
  /**
   * @return self::SHUTDOWN_REASON_*
   */
  public function getShutdownReason()
  {
    return $this->shutdownReason;
  }
  /**
   * The timestamp when shutdown.
   *
   * @param string $shutdownTime
   */
  public function setShutdownTime($shutdownTime)
  {
    $this->shutdownTime = $shutdownTime;
  }
  /**
   * @return string
   */
  public function getShutdownTime()
  {
    return $this->shutdownTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1BootPerformanceReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1BootPerformanceReport');
