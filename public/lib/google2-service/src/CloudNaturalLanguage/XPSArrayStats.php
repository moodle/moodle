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

class XPSArrayStats extends \Google\Model
{
  protected $commonStatsType = XPSCommonStats::class;
  protected $commonStatsDataType = '';
  protected $memberStatsType = XPSDataStats::class;
  protected $memberStatsDataType = '';

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
   * Stats of all the values of all arrays, as if they were a single long series
   * of data. The type depends on the element type of the array.
   *
   * @param XPSDataStats $memberStats
   */
  public function setMemberStats(XPSDataStats $memberStats)
  {
    $this->memberStats = $memberStats;
  }
  /**
   * @return XPSDataStats
   */
  public function getMemberStats()
  {
    return $this->memberStats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSArrayStats::class, 'Google_Service_CloudNaturalLanguage_XPSArrayStats');
