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

namespace Google\Service\YouTube;

class CuepointSchedule extends \Google\Model
{
  public const SCHEDULE_STRATEGY_scheduleStrategyUnspecified = 'scheduleStrategyUnspecified';
  /**
   * Strategy to schedule cuepoints at one time for all viewers.
   */
  public const SCHEDULE_STRATEGY_concurrent = 'concurrent';
  /**
   * Strategy to schedule cuepoints at an increased rate to allow viewers to
   * receive cuepoints when eligible. See go/lcr-non-concurrent-ads for more
   * details.
   */
  public const SCHEDULE_STRATEGY_nonConcurrent = 'nonConcurrent';
  /**
   * This field is semantically required. If it is set false or not set, other
   * fields in this message will be ignored.
   *
   * @var bool
   */
  public $enabled;
  /**
   * If set, automatic cuepoint insertion is paused until this timestamp ("No Ad
   * Zone"). The value is specified in ISO 8601 format.
   *
   * @var string
   */
  public $pauseAdsUntil;
  /**
   * Interval frequency in seconds that api uses to insert cuepoints
   * automatically.
   *
   * @deprecated
   * @var int
   */
  public $repeatIntervalSecs;
  /**
   * The strategy to use when scheduling cuepoints.
   *
   * @deprecated
   * @var string
   */
  public $scheduleStrategy;

  /**
   * This field is semantically required. If it is set false or not set, other
   * fields in this message will be ignored.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * If set, automatic cuepoint insertion is paused until this timestamp ("No Ad
   * Zone"). The value is specified in ISO 8601 format.
   *
   * @param string $pauseAdsUntil
   */
  public function setPauseAdsUntil($pauseAdsUntil)
  {
    $this->pauseAdsUntil = $pauseAdsUntil;
  }
  /**
   * @return string
   */
  public function getPauseAdsUntil()
  {
    return $this->pauseAdsUntil;
  }
  /**
   * Interval frequency in seconds that api uses to insert cuepoints
   * automatically.
   *
   * @deprecated
   * @param int $repeatIntervalSecs
   */
  public function setRepeatIntervalSecs($repeatIntervalSecs)
  {
    $this->repeatIntervalSecs = $repeatIntervalSecs;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getRepeatIntervalSecs()
  {
    return $this->repeatIntervalSecs;
  }
  /**
   * The strategy to use when scheduling cuepoints.
   *
   * Accepted values: scheduleStrategyUnspecified, concurrent, nonConcurrent
   *
   * @deprecated
   * @param self::SCHEDULE_STRATEGY_* $scheduleStrategy
   */
  public function setScheduleStrategy($scheduleStrategy)
  {
    $this->scheduleStrategy = $scheduleStrategy;
  }
  /**
   * @deprecated
   * @return self::SCHEDULE_STRATEGY_*
   */
  public function getScheduleStrategy()
  {
    return $this->scheduleStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CuepointSchedule::class, 'Google_Service_YouTube_CuepointSchedule');
