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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TaskTriggerSpec extends \Google\Model
{
  /**
   * Unspecified trigger type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The task runs one-time shortly after Task Creation.
   */
  public const TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * The task is scheduled to run periodically.
   */
  public const TYPE_RECURRING = 'RECURRING';
  /**
   * Optional. Prevent the task from executing. This does not cancel already
   * running tasks. It is intended to temporarily disable RECURRING tasks.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. Number of retry attempts before aborting. Set to zero to never
   * attempt to retry a failed task.
   *
   * @var int
   */
  public $maxRetries;
  /**
   * Optional. Cron schedule (https://en.wikipedia.org/wiki/Cron) for running
   * tasks periodically. To explicitly set a timezone to the cron tab, apply a
   * prefix in the cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or
   * "TZ=${IANA_TIME_ZONE}". The ${IANA_TIME_ZONE} may only be a valid string
   * from IANA time zone database. For example, CRON_TZ=America/New_York 1 * * *
   * *, or TZ=America/New_York 1 * * * *. This field is required for RECURRING
   * tasks.
   *
   * @var string
   */
  public $schedule;
  /**
   * Optional. The first run of the task will be after this time. If not
   * specified, the task will run shortly after being submitted if ON_DEMAND and
   * based on the schedule if RECURRING.
   *
   * @var string
   */
  public $startTime;
  /**
   * Required. Immutable. Trigger type of the user-specified Task.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Prevent the task from executing. This does not cancel already
   * running tasks. It is intended to temporarily disable RECURRING tasks.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. Number of retry attempts before aborting. Set to zero to never
   * attempt to retry a failed task.
   *
   * @param int $maxRetries
   */
  public function setMaxRetries($maxRetries)
  {
    $this->maxRetries = $maxRetries;
  }
  /**
   * @return int
   */
  public function getMaxRetries()
  {
    return $this->maxRetries;
  }
  /**
   * Optional. Cron schedule (https://en.wikipedia.org/wiki/Cron) for running
   * tasks periodically. To explicitly set a timezone to the cron tab, apply a
   * prefix in the cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or
   * "TZ=${IANA_TIME_ZONE}". The ${IANA_TIME_ZONE} may only be a valid string
   * from IANA time zone database. For example, CRON_TZ=America/New_York 1 * * *
   * *, or TZ=America/New_York 1 * * * *. This field is required for RECURRING
   * tasks.
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
   * Optional. The first run of the task will be after this time. If not
   * specified, the task will run shortly after being submitted if ON_DEMAND and
   * based on the schedule if RECURRING.
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
   * Required. Immutable. Trigger type of the user-specified Task.
   *
   * Accepted values: TYPE_UNSPECIFIED, ON_DEMAND, RECURRING
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskTriggerSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskTriggerSpec');
