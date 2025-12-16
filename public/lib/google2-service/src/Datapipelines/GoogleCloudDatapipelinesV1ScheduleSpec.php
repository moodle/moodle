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

namespace Google\Service\Datapipelines;

class GoogleCloudDatapipelinesV1ScheduleSpec extends \Google\Model
{
  /**
   * Output only. When the next Scheduler job is going to run.
   *
   * @var string
   */
  public $nextJobTime;
  /**
   * Unix-cron format of the schedule. This information is retrieved from the
   * linked Cloud Scheduler.
   *
   * @var string
   */
  public $schedule;
  /**
   * Timezone ID. This matches the timezone IDs used by the Cloud Scheduler API.
   * If empty, UTC time is assumed.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Output only. When the next Scheduler job is going to run.
   *
   * @param string $nextJobTime
   */
  public function setNextJobTime($nextJobTime)
  {
    $this->nextJobTime = $nextJobTime;
  }
  /**
   * @return string
   */
  public function getNextJobTime()
  {
    return $this->nextJobTime;
  }
  /**
   * Unix-cron format of the schedule. This information is retrieved from the
   * linked Cloud Scheduler.
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
   * Timezone ID. This matches the timezone IDs used by the Cloud Scheduler API.
   * If empty, UTC time is assumed.
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
class_alias(GoogleCloudDatapipelinesV1ScheduleSpec::class, 'Google_Service_Datapipelines_GoogleCloudDatapipelinesV1ScheduleSpec');
