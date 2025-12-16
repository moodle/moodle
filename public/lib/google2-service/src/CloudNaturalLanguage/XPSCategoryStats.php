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

class XPSCategoryStats extends \Google\Collection
{
  protected $collection_key = 'topCategoryStats';
  protected $commonStatsType = XPSCommonStats::class;
  protected $commonStatsDataType = '';
  protected $topCategoryStatsType = XPSCategoryStatsSingleCategoryStats::class;
  protected $topCategoryStatsDataType = 'array';

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
   * The statistics of the top 20 CATEGORY values, ordered by
   * CategoryStats.SingleCategoryStats.count.
   *
   * @param XPSCategoryStatsSingleCategoryStats[] $topCategoryStats
   */
  public function setTopCategoryStats($topCategoryStats)
  {
    $this->topCategoryStats = $topCategoryStats;
  }
  /**
   * @return XPSCategoryStatsSingleCategoryStats[]
   */
  public function getTopCategoryStats()
  {
    return $this->topCategoryStats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSCategoryStats::class, 'Google_Service_CloudNaturalLanguage_XPSCategoryStats');
