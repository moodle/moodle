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

namespace Google\Service\ShoppingContent\Resource;

use Google\Service\ShoppingContent\RequestReviewShoppingAdsRequest;
use Google\Service\ShoppingContent\ShoppingAdsProgramStatus;

/**
 * The "shoppingadsprogram" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $shoppingadsprogram = $contentService->shoppingadsprogram;
 *  </code>
 */
class Shoppingadsprogram extends \Google\Service\Resource
{
  /**
   * Retrieves the status and review eligibility for the Shopping Ads program.
   * Returns errors and warnings if they require action to resolve, will become
   * disapprovals, or impact impressions. Use `accountstatuses` to view all issues
   * for an account. (shoppingadsprogram.get)
   *
   * @param string $merchantId Required. The ID of the account.
   * @param array $optParams Optional parameters.
   * @return ShoppingAdsProgramStatus
   * @throws \Google\Service\Exception
   */
  public function get($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ShoppingAdsProgramStatus::class);
  }
  /**
   * Requests a review of Shopping ads in a specific region. This method
   * deprecated. Use the `MerchantSupportService` to view product and account
   * issues and request a review. (shoppingadsprogram.requestreview)
   *
   * @param string $merchantId Required. The ID of the account.
   * @param RequestReviewShoppingAdsRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function requestreview($merchantId, RequestReviewShoppingAdsRequest $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('requestreview', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Shoppingadsprogram::class, 'Google_Service_ShoppingContent_Resource_Shoppingadsprogram');
