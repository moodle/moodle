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

class GroupFindingsResponse extends \Google\Collection
{
  protected $collection_key = 'groupByResults';
  protected $groupByResultsType = GroupResult::class;
  protected $groupByResultsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Time used for executing the groupBy request.
   *
   * @var string
   */
  public $readTime;
  /**
   * The total number of results matching the query.
   *
   * @var int
   */
  public $totalSize;

  /**
   * Group results. There exists an element for each existing unique combination
   * of property/values. The element contains a count for the number of times
   * those specific property/values appear.
   *
   * @param GroupResult[] $groupByResults
   */
  public function setGroupByResults($groupByResults)
  {
    $this->groupByResults = $groupByResults;
  }
  /**
   * @return GroupResult[]
   */
  public function getGroupByResults()
  {
    return $this->groupByResults;
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
   * Time used for executing the groupBy request.
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
   * The total number of results matching the query.
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
class_alias(GroupFindingsResponse::class, 'Google_Service_SecurityCommandCenter_GroupFindingsResponse');
