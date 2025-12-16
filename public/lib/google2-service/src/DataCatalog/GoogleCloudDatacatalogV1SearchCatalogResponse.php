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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SearchCatalogResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * Pagination token that can be used in subsequent calls to retrieve the next
   * page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resultsType = GoogleCloudDatacatalogV1SearchCatalogResult::class;
  protected $resultsDataType = 'array';
  /**
   * The approximate total number of entries matched by the query.
   *
   * @var int
   */
  public $totalSize;
  /**
   * Unreachable locations. Search results don't include data from those
   * locations. To get additional information on an error, repeat the search
   * request and restrict it to specific locations by setting the
   * `SearchCatalogRequest.scope.restricted_locations` parameter.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Pagination token that can be used in subsequent calls to retrieve the next
   * page of results.
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
   * Search results.
   *
   * @param GoogleCloudDatacatalogV1SearchCatalogResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDatacatalogV1SearchCatalogResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * The approximate total number of entries matched by the query.
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
   * Unreachable locations. Search results don't include data from those
   * locations. To get additional information on an error, repeat the search
   * request and restrict it to specific locations by setting the
   * `SearchCatalogRequest.scope.restricted_locations` parameter.
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
class_alias(GoogleCloudDatacatalogV1SearchCatalogResponse::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SearchCatalogResponse');
