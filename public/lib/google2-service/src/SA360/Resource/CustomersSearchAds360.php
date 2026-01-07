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

use Google\Service\SA360\GoogleAdsSearchads360V0ServicesSearchSearchAds360Request;
use Google\Service\SA360\GoogleAdsSearchads360V0ServicesSearchSearchAds360Response;

/**
 * The "searchAds360" collection of methods.
 * Typical usage is:
 *  <code>
 *   $searchads360Service = new Google\Service\SA360(...);
 *   $searchAds360 = $searchads360Service->customers_searchAds360;
 *  </code>
 */
class CustomersSearchAds360 extends \Google\Service\Resource
{
  /**
   * Returns all rows that match the search query. List of thrown errors:
   * [AuthenticationError]() [AuthorizationError]() [HeaderError]()
   * [InternalError]() [QueryError]() [QuotaError]() [RequestError]()
   * (searchAds360.search)
   *
   * @param string $customerId Required. The ID of the customer being queried.
   * @param GoogleAdsSearchads360V0ServicesSearchSearchAds360Request $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleAdsSearchads360V0ServicesSearchSearchAds360Response
   * @throws \Google\Service\Exception
   */
  public function search($customerId, GoogleAdsSearchads360V0ServicesSearchSearchAds360Request $postBody, $optParams = [])
  {
    $params = ['customerId' => $customerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GoogleAdsSearchads360V0ServicesSearchSearchAds360Response::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersSearchAds360::class, 'Google_Service_SA360_Resource_CustomersSearchAds360');
