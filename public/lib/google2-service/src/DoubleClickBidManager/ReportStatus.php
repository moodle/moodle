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

namespace Google\Service\DoubleClickBidManager;

class ReportStatus extends \Google\Model
{
  /**
   * Default value when format is not specified or is unknown in this version.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * CSV.
   */
  public const FORMAT_CSV = 'CSV';
  /**
   * Excel.
   */
  public const FORMAT_XLSX = 'XLSX';
  /**
   * Default value when state is not specified or is unknown in this version.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The report is queued to run.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * The report is currently running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The report has finished running successfully.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The report has finished running in failure.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The timestamp of when report generation finished successfully
   * or in failure. This field will not be set unless state is `DONE` or
   * `FAILED`.
   *
   * @var string
   */
  public $finishTime;
  /**
   * The format of the generated report file.
   *
   * @var string
   */
  public $format;
  /**
   * Output only. The state of the report generation.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The timestamp of when report generation finished successfully
   * or in failure. This field will not be set unless state is `DONE` or
   * `FAILED`.
   *
   * @param string $finishTime
   */
  public function setFinishTime($finishTime)
  {
    $this->finishTime = $finishTime;
  }
  /**
   * @return string
   */
  public function getFinishTime()
  {
    return $this->finishTime;
  }
  /**
   * The format of the generated report file.
   *
   * Accepted values: FORMAT_UNSPECIFIED, CSV, XLSX
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Output only. The state of the report generation.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, RUNNING, DONE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportStatus::class, 'Google_Service_DoubleClickBidManager_ReportStatus');
