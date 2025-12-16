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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2Condition extends \Google\Collection
{
  protected $collection_key = 'queryTerms';
  protected $activeTimeRangeType = GoogleCloudRetailV2ConditionTimeRange::class;
  protected $activeTimeRangeDataType = 'array';
  /**
   * Used to support browse uses cases. A list (up to 10 entries) of categories
   * or departments. The format should be the same as UserEvent.page_categories;
   *
   * @var string[]
   */
  public $pageCategories;
  protected $queryTermsType = GoogleCloudRetailV2ConditionQueryTerm::class;
  protected $queryTermsDataType = 'array';

  /**
   * Range of time(s) specifying when Condition is active. Condition true if any
   * time range matches.
   *
   * @param GoogleCloudRetailV2ConditionTimeRange[] $activeTimeRange
   */
  public function setActiveTimeRange($activeTimeRange)
  {
    $this->activeTimeRange = $activeTimeRange;
  }
  /**
   * @return GoogleCloudRetailV2ConditionTimeRange[]
   */
  public function getActiveTimeRange()
  {
    return $this->activeTimeRange;
  }
  /**
   * Used to support browse uses cases. A list (up to 10 entries) of categories
   * or departments. The format should be the same as UserEvent.page_categories;
   *
   * @param string[] $pageCategories
   */
  public function setPageCategories($pageCategories)
  {
    $this->pageCategories = $pageCategories;
  }
  /**
   * @return string[]
   */
  public function getPageCategories()
  {
    return $this->pageCategories;
  }
  /**
   * A list (up to 10 entries) of terms to match the query on. If not specified,
   * match all queries. If many query terms are specified, the condition is
   * matched if any of the terms is a match (i.e. using the OR operator).
   *
   * @param GoogleCloudRetailV2ConditionQueryTerm[] $queryTerms
   */
  public function setQueryTerms($queryTerms)
  {
    $this->queryTerms = $queryTerms;
  }
  /**
   * @return GoogleCloudRetailV2ConditionQueryTerm[]
   */
  public function getQueryTerms()
  {
    return $this->queryTerms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2Condition::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2Condition');
