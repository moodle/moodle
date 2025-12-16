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

namespace Google\Service\AndroidPublisher;

class PageInfo extends \Google\Model
{
  /**
   * Maximum number of results returned in one page. ! The number of results
   * included in the API response.
   *
   * @var int
   */
  public $resultPerPage;
  /**
   * Index of the first result returned in the current page.
   *
   * @var int
   */
  public $startIndex;
  /**
   * Total number of results available on the backend ! The total number of
   * results in the result set.
   *
   * @var int
   */
  public $totalResults;

  /**
   * Maximum number of results returned in one page. ! The number of results
   * included in the API response.
   *
   * @param int $resultPerPage
   */
  public function setResultPerPage($resultPerPage)
  {
    $this->resultPerPage = $resultPerPage;
  }
  /**
   * @return int
   */
  public function getResultPerPage()
  {
    return $this->resultPerPage;
  }
  /**
   * Index of the first result returned in the current page.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * Total number of results available on the backend ! The total number of
   * results in the result set.
   *
   * @param int $totalResults
   */
  public function setTotalResults($totalResults)
  {
    $this->totalResults = $totalResults;
  }
  /**
   * @return int
   */
  public function getTotalResults()
  {
    return $this->totalResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageInfo::class, 'Google_Service_AndroidPublisher_PageInfo');
