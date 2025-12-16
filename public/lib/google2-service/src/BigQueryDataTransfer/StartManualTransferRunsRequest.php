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

class StartManualTransferRunsRequest extends \Google\Model
{
  /**
   * A run_time timestamp for historical data files or reports that are
   * scheduled to be transferred by the scheduled transfer run.
   * requested_run_time must be a past time and cannot include future time
   * values.
   *
   * @var string
   */
  public $requestedRunTime;
  protected $requestedTimeRangeType = TimeRange::class;
  protected $requestedTimeRangeDataType = '';

  /**
   * A run_time timestamp for historical data files or reports that are
   * scheduled to be transferred by the scheduled transfer run.
   * requested_run_time must be a past time and cannot include future time
   * values.
   *
   * @param string $requestedRunTime
   */
  public function setRequestedRunTime($requestedRunTime)
  {
    $this->requestedRunTime = $requestedRunTime;
  }
  /**
   * @return string
   */
  public function getRequestedRunTime()
  {
    return $this->requestedRunTime;
  }
  /**
   * A time_range start and end timestamp for historical data files or reports
   * that are scheduled to be transferred by the scheduled transfer run.
   * requested_time_range must be a past time and cannot include future time
   * values.
   *
   * @param TimeRange $requestedTimeRange
   */
  public function setRequestedTimeRange(TimeRange $requestedTimeRange)
  {
    $this->requestedTimeRange = $requestedTimeRange;
  }
  /**
   * @return TimeRange
   */
  public function getRequestedTimeRange()
  {
    return $this->requestedTimeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartManualTransferRunsRequest::class, 'Google_Service_BigQueryDataTransfer_StartManualTransferRunsRequest');
