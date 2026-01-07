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

namespace Google\Service\CCAIPlatform;

class SolverConfig extends \Google\Model
{
  /**
   * Unspecified schedule type. Should not be used.
   */
  public const SCHEDULE_TYPE_SCHEDULE_TYPE_UNSPECIFIED = 'SCHEDULE_TYPE_UNSPECIFIED';
  /**
   * Each `EmployeeSchedule` will include exactly one shift.
   */
  public const SCHEDULE_TYPE_SINGLE_SHIFT = 'SINGLE_SHIFT';
  /**
   * `EmployeeSchedule`s will include several shifts to generate a week-long
   * schedule. The start and end time of events in a particular
   * `EmployeeSchedule` will be identical. All the shifts have the same start
   * and end time.
   */
  public const SCHEDULE_TYPE_WEEKLY_WITH_FIXED_EVENTS = 'WEEKLY_WITH_FIXED_EVENTS';
  /**
   * `EmployeeSchedule`s will include several shifts to generate a week-long
   * schedule. The start and end time of events in a particular
   * `EmployeeSchedule` can vary. All the shifts have the same start and end
   * time. This option may result in longer solve times.
   */
  public const SCHEDULE_TYPE_WEEKLY_WITH_VARIABLE_EVENTS = 'WEEKLY_WITH_VARIABLE_EVENTS';
  /**
   * Optional. Maximum time the solver should spend on the problem. If not set,
   * defaults to 1 minute. The choice of a time limit should depend on the size
   * of the problem. To give an example, when solving a 7-day instance with 2
   * `ShiftTemplates`, each with ~20 possible start times and holding 2 events
   * with ~30 possible start times, and two days off per week, recommended
   * values are: <10s for fast solutions (and likely suboptimal), (10s, 300s)
   * for good quality solutions, and >300s for an exhaustive search. Larger
   * instances may require longer time limits. This value is not a hard limit
   * and it does not account for the communication overhead. The expected
   * latency to solve the problem may slightly exceed this value.
   *
   * @var string
   */
  public $maximumProcessingDuration;
  /**
   * Required. Specifies the type of schedule to generate.
   *
   * @var string
   */
  public $scheduleType;

  /**
   * Optional. Maximum time the solver should spend on the problem. If not set,
   * defaults to 1 minute. The choice of a time limit should depend on the size
   * of the problem. To give an example, when solving a 7-day instance with 2
   * `ShiftTemplates`, each with ~20 possible start times and holding 2 events
   * with ~30 possible start times, and two days off per week, recommended
   * values are: <10s for fast solutions (and likely suboptimal), (10s, 300s)
   * for good quality solutions, and >300s for an exhaustive search. Larger
   * instances may require longer time limits. This value is not a hard limit
   * and it does not account for the communication overhead. The expected
   * latency to solve the problem may slightly exceed this value.
   *
   * @param string $maximumProcessingDuration
   */
  public function setMaximumProcessingDuration($maximumProcessingDuration)
  {
    $this->maximumProcessingDuration = $maximumProcessingDuration;
  }
  /**
   * @return string
   */
  public function getMaximumProcessingDuration()
  {
    return $this->maximumProcessingDuration;
  }
  /**
   * Required. Specifies the type of schedule to generate.
   *
   * Accepted values: SCHEDULE_TYPE_UNSPECIFIED, SINGLE_SHIFT,
   * WEEKLY_WITH_FIXED_EVENTS, WEEKLY_WITH_VARIABLE_EVENTS
   *
   * @param self::SCHEDULE_TYPE_* $scheduleType
   */
  public function setScheduleType($scheduleType)
  {
    $this->scheduleType = $scheduleType;
  }
  /**
   * @return self::SCHEDULE_TYPE_*
   */
  public function getScheduleType()
  {
    return $this->scheduleType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SolverConfig::class, 'Google_Service_CCAIPlatform_SolverConfig');
