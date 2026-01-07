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

use Google\Service\Merchant\ListMerchantReviewsResponse;
use Google\Service\Merchant\MerchantReview;
use Google\Service\Merchant\MerchantapiEmpty;

/**
 * The "merchantReviews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $merchantapiService = new Google\Service\Merchant(...);
 *   $merchantReviews = $merchantapiService->accounts_merchantReviews;
 *  </code>
 */
class AccountsMerchantReviews extends \Google\Service\Resource
{
  /**
   * Deletes merchant review. (merchantReviews.delete)
   *
   * @param string $name Required. The ID of the merchant review. Format:
   * accounts/{account}/merchantReviews/{merchantReview}
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
   * Gets a merchant review. (merchantReviews.get)
   *
   * @param string $name Required. The ID of the merchant review. Format:
   * accounts/{account}/merchantReviews/{merchantReview}
   * @param array $optParams Optional parameters.
   * @return MerchantReview
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MerchantReview::class);
  }
  /**
   * Inserts a review for your Merchant Center account. If the review already
   * exists, then the review is replaced with the new instance.
   * (merchantReviews.insert)
   *
   * @param string $parent Required. The account where the merchant review will be
   * inserted. Format: accounts/{account}
   * @param MerchantReview $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dataSource Required. The data source of the [merchantreview
   * ](https://support.google.com/merchants/answer/7045996?sjid=525358124421758197
   * 6-EU) Format: `accounts/{account}/dataSources/{datasource}`.
   * @return MerchantReview
   * @throws \Google\Service\Exception
   */
  public function insert($parent, MerchantReview $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], MerchantReview::class);
  }
  /**
   * Lists merchant reviews. (merchantReviews.listAccountsMerchantReviews)
   *
   * @param string $parent Required. The account to list merchant reviews for.
   * Format: accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of merchant reviews to
   * return. The service can return fewer than this value. The maximum value is
   * 1000; values above 1000 are coerced to 1000. If unspecified, the maximum
   * number of reviews is returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListMerchantReviews` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListMerchantReviews` must
   * match the call that provided the page token.
   * @return ListMerchantReviewsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsMerchantReviews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMerchantReviewsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsMerchantReviews::class, 'Google_Service_Merchant_Resource_AccountsMerchantReviews');
