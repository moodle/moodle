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

namespace Google\Service\DisplayVideo;

class FrequencyCap extends \Google\Model
{
  /**
   * Time unit value is not specified or is unknown in this version.
   */
  public const TIME_UNIT_TIME_UNIT_UNSPECIFIED = 'TIME_UNIT_UNSPECIFIED';
  /**
   * The frequency cap will be applied to the whole life time of the line item.
   *
   * @deprecated
   */
  public const TIME_UNIT_TIME_UNIT_LIFETIME = 'TIME_UNIT_LIFETIME';
  /**
   * The frequency cap will be applied to a number of months.
   */
  public const TIME_UNIT_TIME_UNIT_MONTHS = 'TIME_UNIT_MONTHS';
  /**
   * The frequency cap will be applied to a number of weeks.
   */
  public const TIME_UNIT_TIME_UNIT_WEEKS = 'TIME_UNIT_WEEKS';
  /**
   * The frequency cap will be applied to a number of days.
   */
  public const TIME_UNIT_TIME_UNIT_DAYS = 'TIME_UNIT_DAYS';
  /**
   * The frequency cap will be applied to a number of hours.
   */
  public const TIME_UNIT_TIME_UNIT_HOURS = 'TIME_UNIT_HOURS';
  /**
   * The frequency cap will be applied to a number of minutes.
   */
  public const TIME_UNIT_TIME_UNIT_MINUTES = 'TIME_UNIT_MINUTES';
  /**
   * The maximum number of times a user may be shown the same ad during this
   * period. Must be greater than 0. Required when unlimited is `false` and
   * max_views is not set.
   *
   * @var int
   */
  public $maxImpressions;
  /**
   * Optional. The maximum number of times a user may click-through or fully
   * view an ad during this period until it is no longer served to them. Must be
   * greater than 0. Only applicable to YouTube and Partners resources. Required
   * when unlimited is `false` and max_impressions is not set.
   *
   * @var int
   */
  public $maxViews;
  /**
   * The time unit in which the frequency cap will be applied. Required when
   * unlimited is `false`.
   *
   * @var string
   */
  public $timeUnit;
  /**
   * The number of time_unit the frequency cap will last. Required when
   * unlimited is `false`. The following restrictions apply based on the value
   * of time_unit: * `TIME_UNIT_MONTHS` - must be 1 * `TIME_UNIT_WEEKS` - must
   * be between 1 and 4 * `TIME_UNIT_DAYS` - must be between 1 and 6 *
   * `TIME_UNIT_HOURS` - must be between 1 and 23 * `TIME_UNIT_MINUTES` - must
   * be between 1 and 59
   *
   * @var int
   */
  public $timeUnitCount;
  /**
   * Whether unlimited frequency capping is applied. When this field is set to
   * `true`, the remaining frequency cap fields are not applicable.
   *
   * @var bool
   */
  public $unlimited;

  /**
   * The maximum number of times a user may be shown the same ad during this
   * period. Must be greater than 0. Required when unlimited is `false` and
   * max_views is not set.
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
   * Optional. The maximum number of times a user may click-through or fully
   * view an ad during this period until it is no longer served to them. Must be
   * greater than 0. Only applicable to YouTube and Partners resources. Required
   * when unlimited is `false` and max_impressions is not set.
   *
   * @param int $maxViews
   */
  public function setMaxViews($maxViews)
  {
    $this->maxViews = $maxViews;
  }
  /**
   * @return int
   */
  public function getMaxViews()
  {
    return $this->maxViews;
  }
  /**
   * The time unit in which the frequency cap will be applied. Required when
   * unlimited is `false`.
   *
   * Accepted values: TIME_UNIT_UNSPECIFIED, TIME_UNIT_LIFETIME,
   * TIME_UNIT_MONTHS, TIME_UNIT_WEEKS, TIME_UNIT_DAYS, TIME_UNIT_HOURS,
   * TIME_UNIT_MINUTES
   *
   * @param self::TIME_UNIT_* $timeUnit
   */
  public function setTimeUnit($timeUnit)
  {
    $this->timeUnit = $timeUnit;
  }
  /**
   * @return self::TIME_UNIT_*
   */
  public function getTimeUnit()
  {
    return $this->timeUnit;
  }
  /**
   * The number of time_unit the frequency cap will last. Required when
   * unlimited is `false`. The following restrictions apply based on the value
   * of time_unit: * `TIME_UNIT_MONTHS` - must be 1 * `TIME_UNIT_WEEKS` - must
   * be between 1 and 4 * `TIME_UNIT_DAYS` - must be between 1 and 6 *
   * `TIME_UNIT_HOURS` - must be between 1 and 23 * `TIME_UNIT_MINUTES` - must
   * be between 1 and 59
   *
   * @param int $timeUnitCount
   */
  public function setTimeUnitCount($timeUnitCount)
  {
    $this->timeUnitCount = $timeUnitCount;
  }
  /**
   * @return int
   */
  public function getTimeUnitCount()
  {
    return $this->timeUnitCount;
  }
  /**
   * Whether unlimited frequency capping is applied. When this field is set to
   * `true`, the remaining frequency cap fields are not applicable.
   *
   * @param bool $unlimited
   */
  public function setUnlimited($unlimited)
  {
    $this->unlimited = $unlimited;
  }
  /**
   * @return bool
   */
  public function getUnlimited()
  {
    return $this->unlimited;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FrequencyCap::class, 'Google_Service_DisplayVideo_FrequencyCap');
