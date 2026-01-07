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

namespace Google\Service\BigQueryDataTransfer;

class ScheduleOptions extends \Google\Model
{
  /**
   * If true, automatic scheduling of data transfer runs for this configuration
   * will be disabled. The runs can be started on ad-hoc basis using
   * StartManualTransferRuns API. When automatic scheduling is disabled, the
   * TransferConfig.schedule field will be ignored.
   *
   * @var bool
   */
  public $disableAutoScheduling;
  /**
   * Defines time to stop scheduling transfer runs. A transfer run cannot be
   * scheduled at or after the end time. The end time can be changed at any
   * moment. The time when a data transfer can be triggered manually is not
   * limited by this option.
   *
   * @var string
   */
  public $endTime;
  /**
   * Specifies time to start scheduling transfer runs. The first run will be
   * scheduled at or after the start time according to a recurrence pattern
   * defined in the schedule string. The start time can be changed at any
   * moment. The time when a data transfer can be triggered manually is not
   * limited by this option.
   *
   * @var string
   */
  public $startTime;

  /**
   * If true, automatic scheduling of data transfer runs for this configuration
   * will be disabled. The runs can be started on ad-hoc basis using
   * StartManualTransferRuns API. When automatic scheduling is disabled, the
   * TransferConfig.schedule field will be ignored.
   *
   * @param bool $disableAutoScheduling
   */
  public function setDisableAutoScheduling($disableAutoScheduling)
  {
    $this->disableAutoScheduling = $disableAutoScheduling;
  }
  /**
   * @return bool
   */
  public function getDisableAutoScheduling()
  {
    return $this->disableAutoScheduling;
  }
  /**
   * Defines time to stop scheduling transfer runs. A transfer run cannot be
   * scheduled at or after the end time. The end time can be changed at any
   * moment. The time when a data transfer can be triggered manually is not
   * limited by this option.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Specifies time to start scheduling transfer runs. The first run will be
   * scheduled at or after the start time according to a recurrence pattern
   * defined in the schedule string. The start time can be changed at any
   * moment. The time when a data transfer can be triggered manually is not
   * limited by this option.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScheduleOptions::class, 'Google_Service_BigQueryDataTransfer_ScheduleOptions');
