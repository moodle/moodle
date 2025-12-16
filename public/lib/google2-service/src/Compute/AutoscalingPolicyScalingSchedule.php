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

namespace Google\Service\Compute;

class AutoscalingPolicyScalingSchedule extends \Google\Model
{
  /**
   * A description of a scaling schedule.
   *
   * @var string
   */
  public $description;
  /**
   * A boolean value that specifies whether a scaling schedule can influence
   * autoscaler recommendations. If set to true, then a scaling schedule has no
   * effect. This field is optional, and its value is false by default.
   *
   * @var bool
   */
  public $disabled;
  /**
   * The duration of time intervals, in seconds, for which this scaling schedule
   * is to run. The minimum allowed value is 300. This field is required.
   *
   * @var int
   */
  public $durationSec;
  /**
   * The minimum number of VM instances that the autoscaler will recommend in
   * time intervals starting according to schedule. This field is required.
   *
   * @var int
   */
  public $minRequiredReplicas;
  /**
   * The start timestamps of time intervals when this scaling schedule is to
   * provide a scaling signal. This field uses the extended cron format (with an
   * optional year field). The expression can describe a single timestamp if the
   * optional year is set, in which case the scaling schedule runs once. The
   * schedule is interpreted with respect to time_zone. This field is required.
   * Note: These timestamps only describe when autoscaler starts providing the
   * scaling signal. The VMs need additional time to become serving.
   *
   * @var string
   */
  public $schedule;
  /**
   * The time zone to use when interpreting the schedule. The value of this
   * field must be a time zone name from the tz database:
   * https://en.wikipedia.org/wiki/Tz_database. This field is assigned a default
   * value of "UTC" if left empty.
   *
   * @var string
   */
  public $timeZone;

  /**
   * A description of a scaling schedule.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A boolean value that specifies whether a scaling schedule can influence
   * autoscaler recommendations. If set to true, then a scaling schedule has no
   * effect. This field is optional, and its value is false by default.
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
   * The duration of time intervals, in seconds, for which this scaling schedule
   * is to run. The minimum allowed value is 300. This field is required.
   *
   * @param int $durationSec
   */
  public function setDurationSec($durationSec)
  {
    $this->durationSec = $durationSec;
  }
  /**
   * @return int
   */
  public function getDurationSec()
  {
    return $this->durationSec;
  }
  /**
   * The minimum number of VM instances that the autoscaler will recommend in
   * time intervals starting according to schedule. This field is required.
   *
   * @param int $minRequiredReplicas
   */
  public function setMinRequiredReplicas($minRequiredReplicas)
  {
    $this->minRequiredReplicas = $minRequiredReplicas;
  }
  /**
   * @return int
   */
  public function getMinRequiredReplicas()
  {
    return $this->minRequiredReplicas;
  }
  /**
   * The start timestamps of time intervals when this scaling schedule is to
   * provide a scaling signal. This field uses the extended cron format (with an
   * optional year field). The expression can describe a single timestamp if the
   * optional year is set, in which case the scaling schedule runs once. The
   * schedule is interpreted with respect to time_zone. This field is required.
   * Note: These timestamps only describe when autoscaler starts providing the
   * scaling signal. The VMs need additional time to become serving.
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
   * The time zone to use when interpreting the schedule. The value of this
   * field must be a time zone name from the tz database:
   * https://en.wikipedia.org/wiki/Tz_database. This field is assigned a default
   * value of "UTC" if left empty.
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
class_alias(AutoscalingPolicyScalingSchedule::class, 'Google_Service_Compute_AutoscalingPolicyScalingSchedule');
