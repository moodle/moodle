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

namespace Google\Service\Merchant\Resource;

use Google\Service\Merchant\ListProductReviewsResponse;
use Google\Service\Merchant\MerchantapiEmpty;
use Google\Service\Merchant\ProductReview;

/**
 * The "productReviews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $merchantapiService = new Google\Service\Merchant(...);
 *   $productReviews = $merchantapiService->accounts_productReviews;
 *  </code>
 */
class AccountsProductReviews extends \Google\Service\Resource
{
  /**
   * Deletes a product review. (productReviews.delete)
   *
   * @param string $name Required. The ID of the Product review. Format:
   * accounts/{account}/productReviews/{productReview}
   * @param array $optParams Optional parameters.
   * @return MerchantapiEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], MerchantapiEmpty::class);
  }
  /**
   * Gets a product review. (productReviews.get)
   *
   * @param string $name Required. The ID of the merchant review. Format:
   * accounts/{account}/productReviews/{productReview}
   * @param array $optParams Optional parameters.
   * @return ProductReview
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ProductReview::class);
  }
  /**
   * Inserts a product review. (productReviews.insert)
   *
   * @param string $parent Required. The account where the product review will be
   * inserted. Format: accounts/{account}
   * @param ProductReview $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dataSource Required. Format:
   * `accounts/{account}/dataSources/{datasource}`.
   * @return ProductReview
   * @throws \Google\Service\Exception
   */
  public function insert($parent, ProductReview $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], ProductReview::class);
  }
  /**
   * Lists product reviews. (productReviews.listAccountsProductReviews)
   *
   * @param string $parent Required. The account to list product reviews for.
   * Format: accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of products to return.
   * The service may return fewer than this value.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListProductReviews` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListProductReviews` must match
   * the call that provided the page token.
   * @return ListProductReviewsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsProductReviews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListProductReviewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsProductReviews::class, 'Google_Service_Merchant_Resource_AccountsProductReviews');
