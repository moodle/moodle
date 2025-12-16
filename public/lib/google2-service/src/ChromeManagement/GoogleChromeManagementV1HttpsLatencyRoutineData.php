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

class GoogleChromeManagementV1HttpsLatencyRoutineData extends \Google\Model
{
  /**
   * HTTPS latency problem not specified.
   */
  public const PROBLEM_HTTPS_LATENCY_PROBLEM_UNSPECIFIED = 'HTTPS_LATENCY_PROBLEM_UNSPECIFIED';
  /**
   * One or more DNS resolutions resulted in a failure.
   */
  public const PROBLEM_FAILED_DNS_RESOLUTIONS = 'FAILED_DNS_RESOLUTIONS';
  /**
   * One or more HTTPS requests resulted in a failure.
   */
  public const PROBLEM_FAILED_HTTPS_REQUESTS = 'FAILED_HTTPS_REQUESTS';
  /**
   * Average HTTPS request latency time between 500ms and 1000ms is high.
   */
  public const PROBLEM_HIGH_LATENCY = 'HIGH_LATENCY';
  /**
   * Average HTTPS request latency time greater than 1000ms is very high.
   */
  public const PROBLEM_VERY_HIGH_LATENCY = 'VERY_HIGH_LATENCY';
  /**
   * Output only. HTTPS latency if routine succeeded or failed because of
   * HIGH_LATENCY or VERY_HIGH_LATENCY.
   *
   * @var string
   */
  public $latency;
  /**
   * Output only. HTTPS latency routine problem if a problem occurred.
   *
   * @var string
   */
  public $problem;

  /**
   * Output only. HTTPS latency if routine succeeded or failed because of
   * HIGH_LATENCY or VERY_HIGH_LATENCY.
   *
   * @param string $latency
   */
  public function setLatency($latency)
  {
    $this->latency = $latency;
  }
  /**
   * @return string
   */
  public function getLatency()
  {
    return $this->latency;
  }
  /**
   * Output only. HTTPS latency routine problem if a problem occurred.
   *
   * Accepted values: HTTPS_LATENCY_PROBLEM_UNSPECIFIED, FAILED_DNS_RESOLUTIONS,
   * FAILED_HTTPS_REQUESTS, HIGH_LATENCY, VERY_HIGH_LATENCY
   *
   * @param self::PROBLEM_* $problem
   */
  public function setProblem($problem)
  {
    $this->problem = $problem;
  }
  /**
   * @return self::PROBLEM_*
   */
  public function getProblem()
  {
    return $this->problem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1HttpsLatencyRoutineData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1HttpsLatencyRoutineData');
