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

namespace Google\Service\CloudNaturalLanguage;

class XPSTimestampStats extends \Google\Model
{
  protected $commonStatsType = XPSCommonStats::class;
  protected $commonStatsDataType = '';
  protected $granularStatsType = XPSTimestampStatsGranularStats::class;
  protected $granularStatsDataType = 'map';
  /**
   * @var string
   */
  public $medianTimestampNanos;

  /**
   * @param XPSCommonStats $commonStats
   */
  public function setCommonStats(XPSCommonStats $commonStats)
  {
    $this->commonStats = $commonStats;
  }
  /**
   * @return XPSCommonStats
   */
  public function getCommonStats()
  {
    return $this->commonStats;
  }
  /**
   * The string key is the pre-defined granularity. Currently supported:
   * hour_of_day, day_of_week, month_of_year. Granularities finer that the
   * granularity of timestamp data are not populated (e.g. if timestamps are at
   * day granularity, then hour_of_day is not populated).
   *
   * @param XPSTimestampStatsGranularStats[] $granularStats
   */
  public function setGranularStats($granularStats)
  {
    $this->granularStats = $granularStats;
  }
  /**
   * @return XPSTimestampStatsGranularStats[]
   */
  public function getGranularStats()
  {
    return $this->granularStats;
  }
  /**
   * @param string $medianTimestampNanos
   */
  public function setMedianTimestampNanos($medianTimestampNanos)
  {
    $this->medianTimestampNanos = $medianTimestampNanos;
  }
  /**
   * @return string
   */
  public function getMedianTimestampNanos()
  {
    return $this->medianTimestampNanos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTimestampStats::class, 'Google_Service_CloudNaturalLanguage_XPSTimestampStats');
