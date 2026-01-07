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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaCondition extends \Google\Collection
{
  protected $collection_key = 'queryTerms';
  protected $activeTimeRangeType = GoogleCloudDiscoveryengineV1betaConditionTimeRange::class;
  protected $activeTimeRangeDataType = 'array';
  /**
   * Optional. Query regex to match the whole search query. Cannot be set when
   * Condition.query_terms is set. Only supported for Basic Site Search
   * promotion serving controls.
   *
   * @var string
   */
  public $queryRegex;
  protected $queryTermsType = GoogleCloudDiscoveryengineV1betaConditionQueryTerm::class;
  protected $queryTermsDataType = 'array';

  /**
   * Range of time(s) specifying when condition is active. Maximum of 10 time
   * ranges.
   *
   * @param GoogleCloudDiscoveryengineV1betaConditionTimeRange[] $activeTimeRange
   */
  public function setActiveTimeRange($activeTimeRange)
  {
    $this->activeTimeRange = $activeTimeRange;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaConditionTimeRange[]
   */
  public function getActiveTimeRange()
  {
    return $this->activeTimeRange;
  }
  /**
   * Optional. Query regex to match the whole search query. Cannot be set when
   * Condition.query_terms is set. Only supported for Basic Site Search
   * promotion serving controls.
   *
   * @param string $queryRegex
   */
  public function setQueryRegex($queryRegex)
  {
    $this->queryRegex = $queryRegex;
  }
  /**
   * @return string
   */
  public function getQueryRegex()
  {
    return $this->queryRegex;
  }
  /**
   * Search only A list of terms to match the query on. Cannot be set when
   * Condition.query_regex is set. Maximum of 10 query terms.
   *
   * @param GoogleCloudDiscoveryengineV1betaConditionQueryTerm[] $queryTerms
   */
  public function setQueryTerms($queryTerms)
  {
    $this->queryTerms = $queryTerms;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaConditionQueryTerm[]
   */
  public function getQueryTerms()
  {
    return $this->queryTerms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaCondition::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaCondition');
