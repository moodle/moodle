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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1SearchResourcesRequest extends \Google\Model
{
  /**
   * Optional. An expression that filters the list of search results. A filter
   * expression consists of a field name, a comparison operator, and a value for
   * filtering. The value must be a string, a number, or a boolean. The
   * comparison operator must be `=`. Filters are not case sensitive. The
   * following field names are eligible for filtering: * `resource_type` - The
   * type of resource in the search results. Must be one of the following:
   * `Api`, `ApiOperation`, `Deployment`, `Definition`, `Spec` or `Version`.
   * This field can only be specified once in the filter. Here are is an
   * example: * `resource_type = Api` - The resource_type is _Api_.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The maximum number of search results to return. The service may
   * return fewer than this value. If unspecified at most 10 search results will
   * be returned. If value is negative then `INVALID_ARGUMENT` error is
   * returned. The maximum value is 25; values above 25 will be coerced to 25.
   * While paginating, you can specify a new page size parameter for each page
   * of search results to be listed.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous SearchResources call.
   * Specify this parameter to retrieve the next page of transactions. When
   * paginating, you must specify the `page_token` parameter and all the other
   * parameters except page_size should be specified with the same value which
   * was used in the previous call. If the other fields are set with a different
   * value than the previous call then `INVALID_ARGUMENT` error is returned.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Required. The free text search query. This query can contain keywords which
   * could be related to any detail of the API-Hub resources such display names,
   * descriptions, attributes etc.
   *
   * @var string
   */
  public $query;

  /**
   * Optional. An expression that filters the list of search results. A filter
   * expression consists of a field name, a comparison operator, and a value for
   * filtering. The value must be a string, a number, or a boolean. The
   * comparison operator must be `=`. Filters are not case sensitive. The
   * following field names are eligible for filtering: * `resource_type` - The
   * type of resource in the search results. Must be one of the following:
   * `Api`, `ApiOperation`, `Deployment`, `Definition`, `Spec` or `Version`.
   * This field can only be specified once in the filter. Here are is an
   * example: * `resource_type = Api` - The resource_type is _Api_.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Optional. The maximum number of search results to return. The service may
   * return fewer than this value. If unspecified at most 10 search results will
   * be returned. If value is negative then `INVALID_ARGUMENT` error is
   * returned. The maximum value is 25; values above 25 will be coerced to 25.
   * While paginating, you can specify a new page size parameter for each page
   * of search results to be listed.
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
   * Optional. A page token, received from a previous SearchResources call.
   * Specify this parameter to retrieve the next page of transactions. When
   * paginating, you must specify the `page_token` parameter and all the other
   * parameters except page_size should be specified with the same value which
   * was used in the previous call. If the other fields are set with a different
   * value than the previous call then `INVALID_ARGUMENT` error is returned.
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
   * Required. The free text search query. This query can contain keywords which
   * could be related to any detail of the API-Hub resources such display names,
   * descriptions, attributes etc.
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
class_alias(GoogleCloudApihubV1SearchResourcesRequest::class, 'Google_Service_APIhub_GoogleCloudApihubV1SearchResourcesRequest');
