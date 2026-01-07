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

class TimeBasedSchedule extends \Google\Model
{
  /**
   * Defines time to stop scheduling transfer runs. A transfer run cannot be
   * scheduled at or after the end time. The end time can be changed at any
   * moment.
   *
   * @var string
   */
  public $endTime;
  /**
   * Data transfer schedule. If the data source does not support a custom
   * schedule, this should be empty. If it is empty, the default value for the
   * data source will be used. The specified times are in UTC. Examples of valid
   * format: `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`,
   * and `first sunday of quarter 00:00`. See more explanation about the format
   * here: https://cloud.google.com/appengine/docs/flexible/python/scheduling-
   * jobs-with-cron-yaml#the_schedule_format NOTE: The minimum interval time
   * between recurring transfers depends on the data source; refer to the
   * documentation for your data source.
   *
   * @var string
   */
  public $schedule;
  /**
   * Specifies time to start scheduling transfer runs. The first run will be
   * scheduled at or after the start time according to a recurrence pattern
   * defined in the schedule string. The start time can be changed at any
   * moment.
   *
   * @var string
   */
  public $startTime;

  /**
   * Defines time to stop scheduling transfer runs. A transfer run cannot be
   * scheduled at or after the end time. The end time can be changed at any
   * moment.
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
   * Data transfer schedule. If the data source does not support a custom
   * schedule, this should be empty. If it is empty, the default value for the
   * data source will be used. The specified times are in UTC. Examples of valid
   * format: `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`,
   * and `first sunday of quarter 00:00`. See more explanation about the format
   * here: https://cloud.google.com/appengine/docs/flexible/python/scheduling-
   * jobs-with-cron-yaml#the_schedule_format NOTE: The minimum interval time
   * between recurring transfers depends on the data source; refer to the
   * documentation for your data source.
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
   * Specifies time to start scheduling transfer runs. The first run will be
   * scheduled at or after the start time according to a recurrence pattern
   * defined in the schedule string. The start time can be changed at any
   * moment.
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
class_alias(TimeBasedSchedule::class, 'Google_Service_BigQueryDataTransfer_TimeBasedSchedule');
