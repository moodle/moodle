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

class Pacing extends \Google\Model
{
  /**
   * Period value is not specified or is unknown in this version.
   */
  public const PACING_PERIOD_PACING_PERIOD_UNSPECIFIED = 'PACING_PERIOD_UNSPECIFIED';
  /**
   * The pacing setting will be applied on daily basis.
   */
  public const PACING_PERIOD_PACING_PERIOD_DAILY = 'PACING_PERIOD_DAILY';
  /**
   * The pacing setting will be applied to the whole flight duration.
   */
  public const PACING_PERIOD_PACING_PERIOD_FLIGHT = 'PACING_PERIOD_FLIGHT';
  /**
   * Pacing mode value is not specified or is unknown in this version.
   */
  public const PACING_TYPE_PACING_TYPE_UNSPECIFIED = 'PACING_TYPE_UNSPECIFIED';
  /**
   * Only applicable to `PACING_PERIOD_FLIGHT` pacing period. Ahead pacing
   * attempts to spend faster than evenly, to make sure the entire budget is
   * spent by the end of the flight.
   */
  public const PACING_TYPE_PACING_TYPE_AHEAD = 'PACING_TYPE_AHEAD';
  /**
   * Spend all of pacing budget amount as quick as possible.
   */
  public const PACING_TYPE_PACING_TYPE_ASAP = 'PACING_TYPE_ASAP';
  /**
   * Spend a consistent budget amount every period of time.
   */
  public const PACING_TYPE_PACING_TYPE_EVEN = 'PACING_TYPE_EVEN';
  /**
   * Maximum number of impressions to serve every day. Applicable when the
   * budget is impression based. Must be greater than 0.
   *
   * @var string
   */
  public $dailyMaxImpressions;
  /**
   * Maximum currency amount to spend every day in micros of advertiser's
   * currency. Applicable when the budget is currency based. Must be greater
   * than 0. For example, for 1.5 standard unit of the currency, set this field
   * to 1500000. The value assigned will be rounded to whole billable units for
   * the relevant currency by the following rules: any positive value less than
   * a single billable unit will be rounded up to one billable unit and any
   * value larger than a single billable unit will be rounded down to the
   * nearest billable value. For example, if the currency's billable unit is
   * 0.01, and this field is set to 10257770, it will round down to 10250000, a
   * value of 10.25. If set to 505, it will round up to 10000, a value of 0.01.
   *
   * @var string
   */
  public $dailyMaxMicros;
  /**
   * Required. The time period in which the pacing budget will be spent. When
   * automatic budget allocation is enabled at the insertion order via
   * automationType, this field is output only and defaults to
   * `PACING_PERIOD_FLIGHT`.
   *
   * @var string
   */
  public $pacingPeriod;
  /**
   * Required. The type of pacing that defines how the budget amount will be
   * spent across the pacing_period. `PACING_TYPE_ASAP` is not compatible with
   * pacing_period `PACING_PERIOD_FLIGHT` for insertion orders.
   *
   * @var string
   */
  public $pacingType;

  /**
   * Maximum number of impressions to serve every day. Applicable when the
   * budget is impression based. Must be greater than 0.
   *
   * @param string $dailyMaxImpressions
   */
  public function setDailyMaxImpressions($dailyMaxImpressions)
  {
    $this->dailyMaxImpressions = $dailyMaxImpressions;
  }
  /**
   * @return string
   */
  public function getDailyMaxImpressions()
  {
    return $this->dailyMaxImpressions;
  }
  /**
   * Maximum currency amount to spend every day in micros of advertiser's
   * currency. Applicable when the budget is currency based. Must be greater
   * than 0. For example, for 1.5 standard unit of the currency, set this field
   * to 1500000. The value assigned will be rounded to whole billable units for
   * the relevant currency by the following rules: any positive value less than
   * a single billable unit will be rounded up to one billable unit and any
   * value larger than a single billable unit will be rounded down to the
   * nearest billable value. For example, if the currency's billable unit is
   * 0.01, and this field is set to 10257770, it will round down to 10250000, a
   * value of 10.25. If set to 505, it will round up to 10000, a value of 0.01.
   *
   * @param string $dailyMaxMicros
   */
  public function setDailyMaxMicros($dailyMaxMicros)
  {
    $this->dailyMaxMicros = $dailyMaxMicros;
  }
  /**
   * @return string
   */
  public function getDailyMaxMicros()
  {
    return $this->dailyMaxMicros;
  }
  /**
   * Required. The time period in which the pacing budget will be spent. When
   * automatic budget allocation is enabled at the insertion order via
   * automationType, this field is output only and defaults to
   * `PACING_PERIOD_FLIGHT`.
   *
   * Accepted values: PACING_PERIOD_UNSPECIFIED, PACING_PERIOD_DAILY,
   * PACING_PERIOD_FLIGHT
   *
   * @param self::PACING_PERIOD_* $pacingPeriod
   */
  public function setPacingPeriod($pacingPeriod)
  {
    $this->pacingPeriod = $pacingPeriod;
  }
  /**
   * @return self::PACING_PERIOD_*
   */
  public function getPacingPeriod()
  {
    return $this->pacingPeriod;
  }
  /**
   * Required. The type of pacing that defines how the budget amount will be
   * spent across the pacing_period. `PACING_TYPE_ASAP` is not compatible with
   * pacing_period `PACING_PERIOD_FLIGHT` for insertion orders.
   *
   * Accepted values: PACING_TYPE_UNSPECIFIED, PACING_TYPE_AHEAD,
   * PACING_TYPE_ASAP, PACING_TYPE_EVEN
   *
   * @param self::PACING_TYPE_* $pacingType
   */
  public function setPacingType($pacingType)
  {
    $this->pacingType = $pacingType;
  }
  /**
   * @return self::PACING_TYPE_*
   */
  public function getPacingType()
  {
    return $this->pacingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pacing::class, 'Google_Service_DisplayVideo_Pacing');
