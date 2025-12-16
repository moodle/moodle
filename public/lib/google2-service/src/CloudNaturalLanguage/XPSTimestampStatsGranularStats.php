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

class XPSTimestampStatsGranularStats extends \Google\Model
{
  /**
   * A map from granularity key to example count for that key. E.g. for
   * hour_of_day `13` means 1pm, or for month_of_year `5` means May).
   *
   * @var string[]
   */
  public $buckets;

  /**
   * A map from granularity key to example count for that key. E.g. for
   * hour_of_day `13` means 1pm, or for month_of_year `5` means May).
   *
   * @param string[] $buckets
   */
  public function setBuckets($buckets)
  {
    $this->buckets = $buckets;
  }
  /**
   * @return string[]
   */
  public function getBuckets()
  {
    return $this->buckets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTimestampStatsGranularStats::class, 'Google_Service_CloudNaturalLanguage_XPSTimestampStatsGranularStats');
