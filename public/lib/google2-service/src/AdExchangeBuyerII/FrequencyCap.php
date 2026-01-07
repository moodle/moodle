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

namespace Google\Service\AdExchangeBuyerII;

class FrequencyCap extends \Google\Model
{
  /**
   * A placeholder for an undefined time unit type. This just indicates the
   * variable with this value hasn't been initialized.
   */
  public const TIME_UNIT_TYPE_TIME_UNIT_TYPE_UNSPECIFIED = 'TIME_UNIT_TYPE_UNSPECIFIED';
  /**
   * Minute
   */
  public const TIME_UNIT_TYPE_MINUTE = 'MINUTE';
  /**
   * Hour
   */
  public const TIME_UNIT_TYPE_HOUR = 'HOUR';
  /**
   * Day
   */
  public const TIME_UNIT_TYPE_DAY = 'DAY';
  /**
   * Week
   */
  public const TIME_UNIT_TYPE_WEEK = 'WEEK';
  /**
   * Month
   */
  public const TIME_UNIT_TYPE_MONTH = 'MONTH';
  /**
   * Lifetime
   */
  public const TIME_UNIT_TYPE_LIFETIME = 'LIFETIME';
  /**
   * Pod
   */
  public const TIME_UNIT_TYPE_POD = 'POD';
  /**
   * Stream
   */
  public const TIME_UNIT_TYPE_STREAM = 'STREAM';
  /**
   * The maximum number of impressions that can be served to a user within the
   * specified time period.
   *
   * @var int
   */
  public $maxImpressions;
  /**
   * The amount of time, in the units specified by time_unit_type. Defines the
   * amount of time over which impressions per user are counted and capped.
   *
   * @var int
   */
  public $numTimeUnits;
  /**
   * The time unit. Along with num_time_units defines the amount of time over
   * which impressions per user are counted and capped.
   *
   * @var string
   */
  public $timeUnitType;

  /**
   * The maximum number of impressions that can be served to a user within the
   * specified time period.
   *
   * @param int $maxImpressions
   */
  public function setMaxImpressions($maxImpressions)
  {
    $this->maxImpressions = $maxImpressions;
  }
  /**
   * @return int
   */
  public function getMaxImpressions()
  {
    return $this->maxImpressions;
  }
  /**
   * The amount of time, in the units specified by time_unit_type. Defines the
   * amount of time over which impressions per user are counted and capped.
   *
   * @param int $numTimeUnits
   */
  public function setNumTimeUnits($numTimeUnits)
  {
    $this->numTimeUnits = $numTimeUnits;
  }
  /**
   * @return int
   */
  public function getNumTimeUnits()
  {
    return $this->numTimeUnits;
  }
  /**
   * The time unit. Along with num_time_units defines the amount of time over
   * which impressions per user are counted and capped.
   *
   * Accepted values: TIME_UNIT_TYPE_UNSPECIFIED, MINUTE, HOUR, DAY, WEEK,
   * MONTH, LIFETIME, POD, STREAM
   *
   * @param self::TIME_UNIT_TYPE_* $timeUnitType
   */
  public function setTimeUnitType($timeUnitType)
  {
    $this->timeUnitType = $timeUnitType;
  }
  /**
   * @return self::TIME_UNIT_TYPE_*
   */
  public function getTimeUnitType()
  {
    return $this->timeUnitType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FrequencyCap::class, 'Google_Service_AdExchangeBuyerII_FrequencyCap');
