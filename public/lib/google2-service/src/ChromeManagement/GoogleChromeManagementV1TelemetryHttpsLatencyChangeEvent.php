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

class GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent extends \Google\Model
{
  /**
   * HTTPS latency state is unspecified.
   */
  public const HTTPS_LATENCY_STATE_HTTPS_LATENCY_STATE_UNSPECIFIED = 'HTTPS_LATENCY_STATE_UNSPECIFIED';
  /**
   * HTTPS latency recovered from a problem.
   */
  public const HTTPS_LATENCY_STATE_RECOVERY = 'RECOVERY';
  /**
   * HTTPS latency problem.
   */
  public const HTTPS_LATENCY_STATE_PROBLEM = 'PROBLEM';
  protected $httpsLatencyRoutineDataType = GoogleChromeManagementV1HttpsLatencyRoutineData::class;
  protected $httpsLatencyRoutineDataDataType = '';
  /**
   * Current HTTPS latency state.
   *
   * @var string
   */
  public $httpsLatencyState;

  /**
   * HTTPS latency routine data that triggered the event.
   *
   * @param GoogleChromeManagementV1HttpsLatencyRoutineData $httpsLatencyRoutineData
   */
  public function setHttpsLatencyRoutineData(GoogleChromeManagementV1HttpsLatencyRoutineData $httpsLatencyRoutineData)
  {
    $this->httpsLatencyRoutineData = $httpsLatencyRoutineData;
  }
  /**
   * @return GoogleChromeManagementV1HttpsLatencyRoutineData
   */
  public function getHttpsLatencyRoutineData()
  {
    return $this->httpsLatencyRoutineData;
  }
  /**
   * Current HTTPS latency state.
   *
   * Accepted values: HTTPS_LATENCY_STATE_UNSPECIFIED, RECOVERY, PROBLEM
   *
   * @param self::HTTPS_LATENCY_STATE_* $httpsLatencyState
   */
  public function setHttpsLatencyState($httpsLatencyState)
  {
    $this->httpsLatencyState = $httpsLatencyState;
  }
  /**
   * @return self::HTTPS_LATENCY_STATE_*
   */
  public function getHttpsLatencyState()
  {
    return $this->httpsLatencyState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryHttpsLatencyChangeEvent');
