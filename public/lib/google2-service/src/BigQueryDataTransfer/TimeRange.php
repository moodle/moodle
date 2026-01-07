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

class TimeRange extends \Google\Model
{
  /**
   * End time of the range of transfer runs. For example,
   * `"2017-05-30T00:00:00+00:00"`. The end_time must not be in the future.
   * Creates transfer runs where run_time is in the range between start_time
   * (inclusive) and end_time (exclusive).
   *
   * @var string
   */
  public $endTime;
  /**
   * Start time of the range of transfer runs. For example,
   * `"2017-05-25T00:00:00+00:00"`. The start_time must be strictly less than
   * the end_time. Creates transfer runs where run_time is in the range between
   * start_time (inclusive) and end_time (exclusive).
   *
   * @var string
   */
  public $startTime;

  /**
   * End time of the range of transfer runs. For example,
   * `"2017-05-30T00:00:00+00:00"`. The end_time must not be in the future.
   * Creates transfer runs where run_time is in the range between start_time
   * (inclusive) and end_time (exclusive).
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
   * Start time of the range of transfer runs. For example,
   * `"2017-05-25T00:00:00+00:00"`. The start_time must be strictly less than
   * the end_time. Creates transfer runs where run_time is in the range between
   * start_time (inclusive) and end_time (exclusive).
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
class_alias(TimeRange::class, 'Google_Service_BigQueryDataTransfer_TimeRange');
