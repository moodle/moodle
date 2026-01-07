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

namespace Google\Service\FirebaseDynamicLinks;

class DynamicLinkStats extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $linkEventStatsType = DynamicLinkEventStat::class;
  protected $linkEventStatsDataType = 'array';
  protected $warningsType = DynamicLinkWarning::class;
  protected $warningsDataType = 'array';

  /**
   * Dynamic Link event stats.
   *
   * @param DynamicLinkEventStat[] $linkEventStats
   */
  public function setLinkEventStats($linkEventStats)
  {
    $this->linkEventStats = $linkEventStats;
  }
  /**
   * @return DynamicLinkEventStat[]
   */
  public function getLinkEventStats()
  {
    return $this->linkEventStats;
  }
  /**
   * Optional warnings associated this API request.
   *
   * @param DynamicLinkWarning[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return DynamicLinkWarning[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicLinkStats::class, 'Google_Service_FirebaseDynamicLinks_DynamicLinkStats');
