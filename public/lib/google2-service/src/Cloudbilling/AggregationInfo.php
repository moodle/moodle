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

namespace Google\Service\Cloudbilling;

class AggregationInfo extends \Google\Model
{
  public const AGGREGATION_INTERVAL_AGGREGATION_INTERVAL_UNSPECIFIED = 'AGGREGATION_INTERVAL_UNSPECIFIED';
  public const AGGREGATION_INTERVAL_DAILY = 'DAILY';
  public const AGGREGATION_INTERVAL_MONTHLY = 'MONTHLY';
  public const AGGREGATION_LEVEL_AGGREGATION_LEVEL_UNSPECIFIED = 'AGGREGATION_LEVEL_UNSPECIFIED';
  public const AGGREGATION_LEVEL_ACCOUNT = 'ACCOUNT';
  public const AGGREGATION_LEVEL_PROJECT = 'PROJECT';
  /**
   * The number of intervals to aggregate over. Example: If aggregation_level is
   * "DAILY" and aggregation_count is 14, aggregation will be over 14 days.
   *
   * @var int
   */
  public $aggregationCount;
  /**
   * @var string
   */
  public $aggregationInterval;
  /**
   * @var string
   */
  public $aggregationLevel;

  /**
   * The number of intervals to aggregate over. Example: If aggregation_level is
   * "DAILY" and aggregation_count is 14, aggregation will be over 14 days.
   *
   * @param int $aggregationCount
   */
  public function setAggregationCount($aggregationCount)
  {
    $this->aggregationCount = $aggregationCount;
  }
  /**
   * @return int
   */
  public function getAggregationCount()
  {
    return $this->aggregationCount;
  }
  /**
   * @param self::AGGREGATION_INTERVAL_* $aggregationInterval
   */
  public function setAggregationInterval($aggregationInterval)
  {
    $this->aggregationInterval = $aggregationInterval;
  }
  /**
   * @return self::AGGREGATION_INTERVAL_*
   */
  public function getAggregationInterval()
  {
    return $this->aggregationInterval;
  }
  /**
   * @param self::AGGREGATION_LEVEL_* $aggregationLevel
   */
  public function setAggregationLevel($aggregationLevel)
  {
    $this->aggregationLevel = $aggregationLevel;
  }
  /**
   * @return self::AGGREGATION_LEVEL_*
   */
  public function getAggregationLevel()
  {
    return $this->aggregationLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationInfo::class, 'Google_Service_Cloudbilling_AggregationInfo');
