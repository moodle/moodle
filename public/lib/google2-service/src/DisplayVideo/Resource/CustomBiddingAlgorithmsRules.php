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

namespace Google\Service\DisplayVideo\Resource;

use Google\Service\DisplayVideo\CustomBiddingAlgorithmRules;
use Google\Service\DisplayVideo\ListCustomBiddingAlgorithmRulesResponse;

/**
 * The "rules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $displayvideoService = new Google\Service\DisplayVideo(...);
 *   $rules = $displayvideoService->customBiddingAlgorithms_rules;
 *  </code>
 */
class CustomBiddingAlgorithmsRules extends \Google\Service\Resource
{
  /**
   * Creates a new rules resource. Returns the newly created rules resource if
   * successful. Requests creating a custom bidding rules resource under an
   * algorithm assigned to a line item will return an error. (rules.create)
   *
   * @param string $customBiddingAlgorithmId Required. The ID of the custom
   * bidding algorithm that owns the rules resource.
   * @param CustomBiddingAlgorithmRules $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId The ID of the advertiser that owns the parent
   * custom bidding algorithm.
   * @opt_param string partnerId The ID of the partner that owns the parent custom
   * bidding algorithm. Only this partner will have write access to this rules
   * resource.
   * @return CustomBiddingAlgorithmRules
   * @throws \Google\Service\Exception
   */
  public function create($customBiddingAlgorithmId, CustomBiddingAlgorithmRules $postBody, $optParams = [])
  {
    $params = ['customBiddingAlgorithmId' => $customBiddingAlgorithmId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CustomBiddingAlgorithmRules::class);
  }
  /**
   * Retrieves a rules resource. (rules.get)
   *
   * @param string $customBiddingAlgorithmId Required. The ID of the custom
   * bidding algorithm that owns the rules resource.
   * @param string $customBiddingAlgorithmRulesId Required. The ID of the rules
   * resource to fetch.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId The ID of the advertiser that owns the parent
   * custom bidding algorithm.
   * @opt_param string partnerId The ID of the partner that owns the parent custom
   * bidding algorithm.
   * @return CustomBiddingAlgorithmRules
   * @throws \Google\Service\Exception
   */
  public function get($customBiddingAlgorithmId, $customBiddingAlgorithmRulesId, $optParams = [])
  {
    $params = ['customBiddingAlgorithmId' => $customBiddingAlgorithmId, 'customBiddingAlgorithmRulesId' => $customBiddingAlgorithmRulesId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CustomBiddingAlgorithmRules::class);
  }
  /**
   * Lists rules resources that belong to the given algorithm. The order is
   * defined by the order_by parameter. (rules.listCustomBiddingAlgorithmsRules)
   *
   * @param string $customBiddingAlgorithmId Required. The ID of the custom
   * bidding algorithm that owns the rules resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string advertiserId The ID of the advertiser that owns the parent
   * custom bidding algorithm.
   * @opt_param string orderBy Field by which to sort the list. Acceptable values
   * are: * `createTime desc` (default) The default sorting order is descending.
   * To specify ascending order for a field, the suffix "desc" should be removed.
   * Example: `createTime`.
   * @opt_param int pageSize Requested page size. Must be between `1` and `200`.
   * If unspecified will default to `100`. Returns error code `INVALID_ARGUMENT`
   * if an invalid value is specified.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of next_page_token returned from
   * the previous call to `ListCustomBiddingAlgorithmRules` method. If not
   * specified, the first page of results will be returned.
   * @opt_param string partnerId The ID of the partner that owns the parent custom
   * bidding algorithm.
   * @return ListCustomBiddingAlgorithmRulesResponse
   * @throws \Google\Service\Exception
   */
  public function listCustomBiddingAlgorithmsRules($customBiddingAlgorithmId, $optParams = [])
  {
    $params = ['customBiddingAlgorithmId' => $customBiddingAlgorithmId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCustomBiddingAlgorithmRulesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomBiddingAlgorithmsRules::class, 'Google_Service_DisplayVideo_Resource_CustomBiddingAlgorithmsRules');
