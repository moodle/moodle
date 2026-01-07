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

namespace Google\Service\CustomSearchAPI;

class SearchSearchInformation extends \Google\Model
{
  /**
   * The time taken for the server to return search results, formatted according
   * to locale style.
   *
   * @var string
   */
  public $formattedSearchTime;
  /**
   * The total number of search results, formatted according to locale style.
   *
   * @var string
   */
  public $formattedTotalResults;
  /**
   * The time taken for the server to return search results.
   *
   * @var 
   */
  public $searchTime;
  /**
   * The total number of search results returned by the query.
   *
   * @var string
   */
  public $totalResults;

  /**
   * The time taken for the server to return search results, formatted according
   * to locale style.
   *
   * @param string $formattedSearchTime
   */
  public function setFormattedSearchTime($formattedSearchTime)
  {
    $this->formattedSearchTime = $formattedSearchTime;
  }
  /**
   * @return string
   */
  public function getFormattedSearchTime()
  {
    return $this->formattedSearchTime;
  }
  /**
   * The total number of search results, formatted according to locale style.
   *
   * @param string $formattedTotalResults
   */
  public function setFormattedTotalResults($formattedTotalResults)
  {
    $this->formattedTotalResults = $formattedTotalResults;
  }
  /**
   * @return string
   */
  public function getFormattedTotalResults()
  {
    return $this->formattedTotalResults;
  }
  public function setSearchTime($searchTime)
  {
    $this->searchTime = $searchTime;
  }
  public function getSearchTime()
  {
    return $this->searchTime;
  }
  /**
   * The total number of search results returned by the query.
   *
   * @param string $totalResults
   */
  public function setTotalResults($totalResults)
  {
    $this->totalResults = $totalResults;
  }
  /**
   * @return string
   */
  public function getTotalResults()
  {
    return $this->totalResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchSearchInformation::class, 'Google_Service_CustomSearchAPI_SearchSearchInformation');
