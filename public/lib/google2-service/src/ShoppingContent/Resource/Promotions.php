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

use Google\Service\ShoppingContent\ListPromotionResponse;
use Google\Service\ShoppingContent\Promotion;

/**
 * The "promotions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Google\Service\ShoppingContent(...);
 *   $promotions = $contentService->promotions;
 *  </code>
 */
class Promotions extends \Google\Service\Resource
{
  /**
   * Inserts a promotion for your Merchant Center account. If the promotion
   * already exists, then it updates the promotion instead. To [end or delete]
   * (https://developers.google.com/shopping-
   * content/guides/promotions#end_a_promotion) a promotion update the time period
   * of the promotion to a time that has already passed. (promotions.create)
   *
   * @param string $merchantId Required. The ID of the account that contains the
   * collection.
   * @param Promotion $postBody
   * @param array $optParams Optional parameters.
   * @return Promotion
   * @throws \Google\Service\Exception
   */
  public function create($merchantId, Promotion $postBody, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Promotion::class);
  }
  /**
   * Retrieves a promotion from your Merchant Center account. (promotions.get)
   *
   * @param string $merchantId Required. The ID of the account that contains the
   * collection.
   * @param string $id Required. REST ID of the promotion to retrieve.
   * @param array $optParams Optional parameters.
   * @return Promotion
   * @throws \Google\Service\Exception
   */
  public function get($merchantId, $id, $optParams = [])
  {
    $params = ['merchantId' => $merchantId, 'id' => $id];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Promotion::class);
  }
  /**
   * List all promotions from your Merchant Center account.
   * (promotions.listPromotions)
   *
   * @param string $merchantId Required. The ID of the account that contains the
   * collection.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string countryCode [CLDR country
   * code](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) (for
   * example, "US"), used as a filter on promotions target country.
   * @opt_param string languageCode The two-letter ISO 639-1 language code
   * associated with the promotions, used as a filter.
   * @opt_param int pageSize The maximum number of promotions to return. The
   * service may return fewer than this value. If unspecified, at most 50 labels
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListPromotion` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListPromotion` must match the
   * call that provided the page token.
   * @return ListPromotionResponse
   * @throws \Google\Service\Exception
   */
  public function listPromotions($merchantId, $optParams = [])
  {
    $params = ['merchantId' => $merchantId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPromotionResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Promotions::class, 'Google_Service_ShoppingContent_Resource_Promotions');
