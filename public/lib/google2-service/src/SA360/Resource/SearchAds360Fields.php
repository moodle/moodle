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

namespace Google\Service\SA360\Resource;

use Google\Service\SA360\GoogleAdsSearchads360V0ResourcesSearchAds360Field;
use Google\Service\SA360\GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsRequest;
use Google\Service\SA360\GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse;

/**
 * The "searchAds360Fields" collection of methods.
 * Typical usage is:
 *  <code>
 *   $searchads360Service = new Google\Service\SA360(...);
 *   $searchAds360Fields = $searchads360Service->searchAds360Fields;
 *  </code>
 */
class SearchAds360Fields extends \Google\Service\Resource
{
  /**
   * Returns just the requested field. List of thrown errors:
   * [AuthenticationError]() [AuthorizationError]() [HeaderError]()
   * [InternalError]() [QuotaError]() [RequestError]() (searchAds360Fields.get)
   *
   * @param string $resourceName Required. The resource name of the field to get.
   * @param array $optParams Optional parameters.
   * @return GoogleAdsSearchads360V0ResourcesSearchAds360Field
   * @throws \Google\Service\Exception
   */
  public function get($resourceName, $optParams = [])
  {
    $params = ['resourceName' => $resourceName];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleAdsSearchads360V0ResourcesSearchAds360Field::class);
  }
  /**
   * Returns all fields that match the search [query](/search-
   * ads/reporting/concepts/field-service#use_a_query_to_get_field_details). List
   * of thrown errors: [AuthenticationError]() [AuthorizationError]()
   * [HeaderError]() [InternalError]() [QueryError]() [QuotaError]()
   * [RequestError]() (searchAds360Fields.search)
   *
   * @param GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse
   * @throws \Google\Service\Exception
   */
  public function search(GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleAdsSearchads360V0ServicesSearchSearchAds360FieldsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchAds360Fields::class, 'Google_Service_SA360_Resource_SearchAds360Fields');
