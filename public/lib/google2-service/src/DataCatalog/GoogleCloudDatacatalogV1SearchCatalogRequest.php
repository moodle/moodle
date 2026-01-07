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

class GoogleCloudDatacatalogV1SearchCatalogRequest extends \Google\Model
{
  /**
   * Optional. If set, use searchAll permission granted on organizations from
   * `include_org_ids` and projects from `include_project_ids` instead of the
   * fine grained per resource permissions when filtering the search results.
   * The only allowed `order_by` criteria for admin_search mode is `default`.
   * Using this flags guarantees a full recall of the search results.
   *
   * @var bool
   */
  public $adminSearch;
  /**
   * Specifies the order of results. Currently supported case-sensitive values
   * are: * `relevance` that can only be descending * `last_modified_timestamp
   * [asc|desc]` with descending (`desc`) as default * `default` that can only
   * be descending Search queries don't guarantee full recall. Results that
   * match your query might not be returned, even in subsequent result pages.
   * Additionally, returned (and not returned) results can vary if you repeat
   * search queries. If you are experiencing recall issues and you don't have to
   * fetch the results in any specific order, consider setting this parameter to
   * `default`. If this parameter is omitted, it defaults to the descending
   * `relevance`.
   *
   * @var string
   */
  public $orderBy;
  /**
   * Upper bound on the number of results you can get in a single response.
   * Can't be negative or 0, defaults to 10 in this case. The maximum number is
   * 1000. If exceeded, throws an "invalid argument" exception.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. Pagination token that, if specified, returns the next page of
   * search results. If empty, returns the first page. This token is returned in
   * the SearchCatalogResponse.next_page_token field of the response to a
   * previous SearchCatalogRequest call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. The query string with a minimum of 3 characters and specific
   * syntax. For more information, see [Data Catalog search
   * syntax](https://cloud.google.com/data-catalog/docs/how-to/search-
   * reference). An empty query string returns all data assets (in the specified
   * scope) that you have access to. A query string can be a simple `xyz` or
   * qualified by predicates: * `name:x` * `column:y` * `description:z`
   *
   * @var string
   */
  public $query;
  protected $scopeType = GoogleCloudDatacatalogV1SearchCatalogRequestScope::class;
  protected $scopeDataType = '';

  /**
   * Optional. If set, use searchAll permission granted on organizations from
   * `include_org_ids` and projects from `include_project_ids` instead of the
   * fine grained per resource permissions when filtering the search results.
   * The only allowed `order_by` criteria for admin_search mode is `default`.
   * Using this flags guarantees a full recall of the search results.
   *
   * @param bool $adminSearch
   */
  public function setAdminSearch($adminSearch)
  {
    $this->adminSearch = $adminSearch;
  }
  /**
   * @return bool
   */
  public function getAdminSearch()
  {
    return $this->adminSearch;
  }
  /**
   * Specifies the order of results. Currently supported case-sensitive values
   * are: * `relevance` that can only be descending * `last_modified_timestamp
   * [asc|desc]` with descending (`desc`) as default * `default` that can only
   * be descending Search queries don't guarantee full recall. Results that
   * match your query might not be returned, even in subsequent result pages.
   * Additionally, returned (and not returned) results can vary if you repeat
   * search queries. If you are experiencing recall issues and you don't have to
   * fetch the results in any specific order, consider setting this parameter to
   * `default`. If this parameter is omitted, it defaults to the descending
   * `relevance`.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * Upper bound on the number of results you can get in a single response.
   * Can't be negative or 0, defaults to 10 in this case. The maximum number is
   * 1000. If exceeded, throws an "invalid argument" exception.
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
   * Optional. Pagination token that, if specified, returns the next page of
   * search results. If empty, returns the first page. This token is returned in
   * the SearchCatalogResponse.next_page_token field of the response to a
   * previous SearchCatalogRequest call.
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
   * Optional. The query string with a minimum of 3 characters and specific
   * syntax. For more information, see [Data Catalog search
   * syntax](https://cloud.google.com/data-catalog/docs/how-to/search-
   * reference). An empty query string returns all data assets (in the specified
   * scope) that you have access to. A query string can be a simple `xyz` or
   * qualified by predicates: * `name:x` * `column:y` * `description:z`
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
  /**
   * Required. The scope of this search request. The `scope` is invalid if
   * `include_org_ids`, `include_project_ids` are empty AND
   * `include_gcp_public_datasets` is set to `false`. In this case, the request
   * returns an error.
   *
   * @param GoogleCloudDatacatalogV1SearchCatalogRequestScope $scope
   */
  public function setScope(GoogleCloudDatacatalogV1SearchCatalogRequestScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return GoogleCloudDatacatalogV1SearchCatalogRequestScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SearchCatalogRequest::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SearchCatalogRequest');
