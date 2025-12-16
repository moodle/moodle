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

class ResourcePolicyInstanceSchedulePolicy extends \Google\Model
{
  /**
   * The expiration time of the schedule. The timestamp is an RFC3339 string.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The start time of the schedule. The timestamp is an RFC3339 string.
   *
   * @var string
   */
  public $startTime;
  /**
   * Specifies the time zone to be used in interpreting Schedule.schedule. The
   * value of this field must be a time zone name from the tz database:
   * https://wikipedia.org/wiki/Tz_database.
   *
   * @var string
   */
  public $timeZone;
  protected $vmStartScheduleType = ResourcePolicyInstanceSchedulePolicySchedule::class;
  protected $vmStartScheduleDataType = '';
  protected $vmStopScheduleType = ResourcePolicyInstanceSchedulePolicySchedule::class;
  protected $vmStopScheduleDataType = '';

  /**
   * The expiration time of the schedule. The timestamp is an RFC3339 string.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The start time of the schedule. The timestamp is an RFC3339 string.
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
   * Specifies the time zone to be used in interpreting Schedule.schedule. The
   * value of this field must be a time zone name from the tz database:
   * https://wikipedia.org/wiki/Tz_database.
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
  /**
   * Specifies the schedule for starting instances.
   *
   * @param ResourcePolicyInstanceSchedulePolicySchedule $vmStartSchedule
   */
  public function setVmStartSchedule(ResourcePolicyInstanceSchedulePolicySchedule $vmStartSchedule)
  {
    $this->vmStartSchedule = $vmStartSchedule;
  }
  /**
   * @return ResourcePolicyInstanceSchedulePolicySchedule
   */
  public function getVmStartSchedule()
  {
    return $this->vmStartSchedule;
  }
  /**
   * Specifies the schedule for stopping instances.
   *
   * @param ResourcePolicyInstanceSchedulePolicySchedule $vmStopSchedule
   */
  public function setVmStopSchedule(ResourcePolicyInstanceSchedulePolicySchedule $vmStopSchedule)
  {
    $this->vmStopSchedule = $vmStopSchedule;
  }
  /**
   * @return ResourcePolicyInstanceSchedulePolicySchedule
   */
  public function getVmStopSchedule()
  {
    return $this->vmStopSchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyInstanceSchedulePolicy::class, 'Google_Service_Compute_ResourcePolicyInstanceSchedulePolicy');
