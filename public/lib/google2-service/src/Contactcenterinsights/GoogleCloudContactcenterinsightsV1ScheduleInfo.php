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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1ScheduleInfo extends \Google\Model
{
  /**
   * End time of the schedule. If not specified, will keep scheduling new
   * pipelines for execution util the schedule is no longer active or deleted.
   *
   * @var string
   */
  public $endTime;
  /**
   * The groc expression. Format: `every number [synchronized]` Time units can
   * be: minutes, hours Synchronized is optional and indicates that the schedule
   * should be synchronized to the start of the interval: every 5 minutes
   * synchronized means 00:00, 00:05 ... Otherwise the start time is random
   * within the interval. Example: `every 5 minutes` could be 00:02, 00:07,
   * 00:12, ...
   *
   * @var string
   */
  public $schedule;
  /**
   * Start time of the schedule. If not specified, will start as soon as the
   * schedule is created.
   *
   * @var string
   */
  public $startTime;
  /**
   * The timezone to use for the groc expression. If not specified, defaults to
   * UTC.
   *
   * @var string
   */
  public $timeZone;

  /**
   * End time of the schedule. If not specified, will keep scheduling new
   * pipelines for execution util the schedule is no longer active or deleted.
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
   * The groc expression. Format: `every number [synchronized]` Time units can
   * be: minutes, hours Synchronized is optional and indicates that the schedule
   * should be synchronized to the start of the interval: every 5 minutes
   * synchronized means 00:00, 00:05 ... Otherwise the start time is random
   * within the interval. Example: `every 5 minutes` could be 00:02, 00:07,
   * 00:12, ...
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Start time of the schedule. If not specified, will start as soon as the
   * schedule is created.
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
  /**
   * The timezone to use for the groc expression. If not specified, defaults to
   * UTC.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ScheduleInfo::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ScheduleInfo');
