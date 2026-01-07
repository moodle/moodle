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

class AssetPerformanceData extends \Google\Collection
{
  protected $collection_key = 'dailyResourceUsageAggregations';
  protected $dailyResourceUsageAggregationsType = DailyResourceUsageAggregation::class;
  protected $dailyResourceUsageAggregationsDataType = 'array';

  /**
   * Daily resource usage aggregations. Contains all of the data available for
   * an asset, up to the last 420 days. Aggregations are sorted from oldest to
   * most recent.
   *
   * @param DailyResourceUsageAggregation[] $dailyResourceUsageAggregations
   */
  public function setDailyResourceUsageAggregations($dailyResourceUsageAggregations)
  {
    $this->dailyResourceUsageAggregations = $dailyResourceUsageAggregations;
  }
  /**
   * @return DailyResourceUsageAggregation[]
   */
  public function getDailyResourceUsageAggregations()
  {
    return $this->dailyResourceUsageAggregations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetPerformanceData::class, 'Google_Service_MigrationCenterAPI_AssetPerformanceData');
