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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1SearchEntriesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resultsType = GoogleCloudDataplexV1SearchEntriesResult::class;
  protected $resultsDataType = 'array';
  /**
   * The estimated total number of matching entries. This number isn't
   * guaranteed to be accurate.
   *
   * @var int
   */
  public $totalSize;
  /**
   * Locations that the service couldn't reach. Search results don't include
   * data from these locations.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
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
   * The results matching the search query.
   *
   * @param GoogleCloudDataplexV1SearchEntriesResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDataplexV1SearchEntriesResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * The estimated total number of matching entries. This number isn't
   * guaranteed to be accurate.
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
  /**
   * Locations that the service couldn't reach. Search results don't include
   * data from these locations.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1SearchEntriesResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1SearchEntriesResponse');
