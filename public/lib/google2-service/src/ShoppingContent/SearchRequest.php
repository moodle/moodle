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

namespace Google\Service\ShoppingContent;

class SearchRequest extends \Google\Model
{
  /**
   * Number of ReportRows to retrieve in a single page. Defaults to 1000. Values
   * above 5000 are coerced to 5000.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Token of the page to retrieve. If not specified, the first page of results
   * is returned. In order to request the next page of results, the value
   * obtained from `next_page_token` in the previous response should be used.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. Query that defines performance metrics to retrieve and dimensions
   * according to which the metrics are to be segmented. For details on how to
   * construct your query, see the [Query Language
   * guide](https://developers.google.com/shopping-content/guides/reports/query-
   * language/overview).
   *
   * @var string
   */
  public $query;

  /**
   * Number of ReportRows to retrieve in a single page. Defaults to 1000. Values
   * above 5000 are coerced to 5000.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Token of the page to retrieve. If not specified, the first page of results
   * is returned. In order to request the next page of results, the value
   * obtained from `next_page_token` in the previous response should be used.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Required. Query that defines performance metrics to retrieve and dimensions
   * according to which the metrics are to be segmented. For details on how to
   * construct your query, see the [Query Language
   * guide](https://developers.google.com/shopping-content/guides/reports/query-
   * language/overview).
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchRequest::class, 'Google_Service_ShoppingContent_SearchRequest');
