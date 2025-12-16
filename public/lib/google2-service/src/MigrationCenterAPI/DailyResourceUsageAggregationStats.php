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

namespace Google\Service\MigrationCenterAPI;

class DailyResourceUsageAggregationStats extends \Google\Model
{
  /**
   * Average usage value.
   *
   * @var float
   */
  public $average;
  /**
   * Median usage value.
   *
   * @var float
   */
  public $median;
  /**
   * 95th percentile usage value.
   *
   * @var float
   */
  public $ninteyFifthPercentile;
  /**
   * Peak usage value.
   *
   * @var float
   */
  public $peak;

  /**
   * Average usage value.
   *
   * @param float $average
   */
  public function setAverage($average)
  {
    $this->average = $average;
  }
  /**
   * @return float
   */
  public function getAverage()
  {
    return $this->average;
  }
  /**
   * Median usage value.
   *
   * @param float $median
   */
  public function setMedian($median)
  {
    $this->median = $median;
  }
  /**
   * @return float
   */
  public function getMedian()
  {
    return $this->median;
  }
  /**
   * 95th percentile usage value.
   *
   * @param float $ninteyFifthPercentile
   */
  public function setNinteyFifthPercentile($ninteyFifthPercentile)
  {
    $this->ninteyFifthPercentile = $ninteyFifthPercentile;
  }
  /**
   * @return float
   */
  public function getNinteyFifthPercentile()
  {
    return $this->ninteyFifthPercentile;
  }
  /**
   * Peak usage value.
   *
   * @param float $peak
   */
  public function setPeak($peak)
  {
    $this->peak = $peak;
  }
  /**
   * @return float
   */
  public function getPeak()
  {
    return $this->peak;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DailyResourceUsageAggregationStats::class, 'Google_Service_MigrationCenterAPI_DailyResourceUsageAggregationStats');
