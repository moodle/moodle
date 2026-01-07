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

namespace Google\Service\Monitoring;

class ServiceLevelObjective extends \Google\Model
{
  /**
   * Undefined period, raises an error.
   */
  public const CALENDAR_PERIOD_CALENDAR_PERIOD_UNSPECIFIED = 'CALENDAR_PERIOD_UNSPECIFIED';
  /**
   * A day.
   */
  public const CALENDAR_PERIOD_DAY = 'DAY';
  /**
   * A week. Weeks begin on Monday, following ISO 8601
   * (https://en.wikipedia.org/wiki/ISO_week_date).
   */
  public const CALENDAR_PERIOD_WEEK = 'WEEK';
  /**
   * A fortnight. The first calendar fortnight of the year begins at the start
   * of week 1 according to ISO 8601
   * (https://en.wikipedia.org/wiki/ISO_week_date).
   */
  public const CALENDAR_PERIOD_FORTNIGHT = 'FORTNIGHT';
  /**
   * A month.
   */
  public const CALENDAR_PERIOD_MONTH = 'MONTH';
  /**
   * A quarter. Quarters start on dates 1-Jan, 1-Apr, 1-Jul, and 1-Oct of each
   * year.
   */
  public const CALENDAR_PERIOD_QUARTER = 'QUARTER';
  /**
   * A half-year. Half-years start on dates 1-Jan and 1-Jul.
   */
  public const CALENDAR_PERIOD_HALF = 'HALF';
  /**
   * A year.
   */
  public const CALENDAR_PERIOD_YEAR = 'YEAR';
  /**
   * A calendar period, semantically "since the start of the current ". At this
   * time, only DAY, WEEK, FORTNIGHT, and MONTH are supported.
   *
   * @var string
   */
  public $calendarPeriod;
  /**
   * Name used for UI elements listing this SLO.
   *
   * @var string
   */
  public $displayName;
  /**
   * The fraction of service that must be good in order for this objective to be
   * met. 0 < goal <= 0.9999.
   *
   * @var 
   */
  public $goal;
  /**
   * Identifier. Resource name for this ServiceLevelObjective. The format is: pr
   * ojects/[PROJECT_ID_OR_NUMBER]/services/[SERVICE_ID]/serviceLevelObjectives/
   * [SLO_NAME]
   *
   * @var string
   */
  public $name;
  /**
   * A rolling time period, semantically "in the past ". Must be an integer
   * multiple of 1 day no larger than 30 days.
   *
   * @var string
   */
  public $rollingPeriod;
  protected $serviceLevelIndicatorType = ServiceLevelIndicator::class;
  protected $serviceLevelIndicatorDataType = '';
  /**
   * Labels which have been used to annotate the service-level objective. Label
   * keys must start with a letter. Label keys and values may contain lowercase
   * letters, numbers, underscores, and dashes. Label keys and values have a
   * maximum length of 63 characters, and must be less than 128 bytes in size.
   * Up to 64 label entries may be stored. For labels which do not have a
   * semantic value, the empty string may be supplied for the label value.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * A calendar period, semantically "since the start of the current ". At this
   * time, only DAY, WEEK, FORTNIGHT, and MONTH are supported.
   *
   * Accepted values: CALENDAR_PERIOD_UNSPECIFIED, DAY, WEEK, FORTNIGHT, MONTH,
   * QUARTER, HALF, YEAR
   *
   * @param self::CALENDAR_PERIOD_* $calendarPeriod
   */
  public function setCalendarPeriod($calendarPeriod)
  {
    $this->calendarPeriod = $calendarPeriod;
  }
  /**
   * @return self::CALENDAR_PERIOD_*
   */
  public function getCalendarPeriod()
  {
    return $this->calendarPeriod;
  }
  /**
   * Name used for UI elements listing this SLO.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  public function setGoal($goal)
  {
    $this->goal = $goal;
  }
  public function getGoal()
  {
    return $this->goal;
  }
  /**
   * Identifier. Resource name for this ServiceLevelObjective. The format is: pr
   * ojects/[PROJECT_ID_OR_NUMBER]/services/[SERVICE_ID]/serviceLevelObjectives/
   * [SLO_NAME]
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A rolling time period, semantically "in the past ". Must be an integer
   * multiple of 1 day no larger than 30 days.
   *
   * @param string $rollingPeriod
   */
  public function setRollingPeriod($rollingPeriod)
  {
    $this->rollingPeriod = $rollingPeriod;
  }
  /**
   * @return string
   */
  public function getRollingPeriod()
  {
    return $this->rollingPeriod;
  }
  /**
   * The definition of good service, used to measure and calculate the quality
   * of the Service's performance with respect to a single aspect of service
   * quality.
   *
   * @param ServiceLevelIndicator $serviceLevelIndicator
   */
  public function setServiceLevelIndicator(ServiceLevelIndicator $serviceLevelIndicator)
  {
    $this->serviceLevelIndicator = $serviceLevelIndicator;
  }
  /**
   * @return ServiceLevelIndicator
   */
  public function getServiceLevelIndicator()
  {
    return $this->serviceLevelIndicator;
  }
  /**
   * Labels which have been used to annotate the service-level objective. Label
   * keys must start with a letter. Label keys and values may contain lowercase
   * letters, numbers, underscores, and dashes. Label keys and values have a
   * maximum length of 63 characters, and must be less than 128 bytes in size.
   * Up to 64 label entries may be stored. For labels which do not have a
   * semantic value, the empty string may be supplied for the label value.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceLevelObjective::class, 'Google_Service_Monitoring_ServiceLevelObjective');
