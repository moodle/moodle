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

namespace Google\Service\Logging;

class ListRecentQueriesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * If there might be more results than appear in this response, then
   * nextPageToken is included. To get the next set of results, call the same
   * method again using the value of nextPageToken as pageToken.
   *
   * @var string
   */
  public $nextPageToken;
  protected $recentQueriesType = RecentQuery::class;
  protected $recentQueriesDataType = 'array';
  /**
   * The unreachable resources. Each resource can be either 1) a saved query if
   * a specific query is unreachable or 2) a location if a specific location is
   * unreachable.
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/recentQueries/[QUERY_ID]"
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global/recentQueries/12345678" "projects/my-
   * project/locations/global"If there are unreachable resources, the response
   * will first return pages that contain recent queries, and then return pages
   * that contain the unreachable resources.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * If there might be more results than appear in this response, then
   * nextPageToken is included. To get the next set of results, call the same
   * method again using the value of nextPageToken as pageToken.
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
   * A list of recent queries.
   *
   * @param RecentQuery[] $recentQueries
   */
  public function setRecentQueries($recentQueries)
  {
    $this->recentQueries = $recentQueries;
  }
  /**
   * @return RecentQuery[]
   */
  public function getRecentQueries()
  {
    return $this->recentQueries;
  }
  /**
   * The unreachable resources. Each resource can be either 1) a saved query if
   * a specific query is unreachable or 2) a location if a specific location is
   * unreachable.
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/recentQueries/[QUERY_ID]"
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]" For example:"projects/my-
   * project/locations/global/recentQueries/12345678" "projects/my-
   * project/locations/global"If there are unreachable resources, the response
   * will first return pages that contain recent queries, and then return pages
   * that contain the unreachable resources.
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
class_alias(ListRecentQueriesResponse::class, 'Google_Service_Logging_ListRecentQueriesResponse');
