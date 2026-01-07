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

namespace Google\Service\SecurityCommandCenter;

class ListAssetsResponse extends \Google\Collection
{
  protected $collection_key = 'listAssetsResults';
  protected $listAssetsResultsType = ListAssetsResult::class;
  protected $listAssetsResultsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Time used for executing the list request.
   *
   * @var string
   */
  public $readTime;
  /**
   * The total number of assets matching the query.
   *
   * @var int
   */
  public $totalSize;

  /**
   * Assets matching the list request.
   *
   * @param ListAssetsResult[] $listAssetsResults
   */
  public function setListAssetsResults($listAssetsResults)
  {
    $this->listAssetsResults = $listAssetsResults;
  }
  /**
   * @return ListAssetsResult[]
   */
  public function getListAssetsResults()
  {
    return $this->listAssetsResults;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Time used for executing the list request.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * The total number of assets matching the query.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAssetsResponse::class, 'Google_Service_SecurityCommandCenter_ListAssetsResponse');
