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

namespace Google\Service\AndroidPublisher\Resource;

use Google\Service\AndroidPublisher\InAppProduct;
use Google\Service\AndroidPublisher\InappproductsBatchDeleteRequest;
use Google\Service\AndroidPublisher\InappproductsBatchGetResponse;
use Google\Service\AndroidPublisher\InappproductsBatchUpdateRequest;
use Google\Service\AndroidPublisher\InappproductsBatchUpdateResponse;
use Google\Service\AndroidPublisher\InappproductsListResponse;

/**
 * The "inappproducts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $inappproducts = $androidpublisherService->inappproducts;
 *  </code>
 */
class Inappproducts extends \Google\Service\Resource
{
  /**
   * Deletes in-app products (managed products or subscriptions). Set the
   * latencyTolerance field on nested requests to
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT to achieve maximum update
   * throughput. This method should not be used to delete subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information.
   * (inappproducts.batchDelete)
   *
   * @param string $packageName Package name of the app.
   * @param InappproductsBatchDeleteRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function batchDelete($packageName, InappproductsBatchDeleteRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params]);
  }
  /**
   * Reads multiple in-app products, which can be managed products or
   * subscriptions. This method should not be used to retrieve subscriptions. See
   * [this article](https://android-developers.googleblog.com/2023/06/changes-to-
   * google-play-developer-api-june-2023.html) for more information.
   * (inappproducts.batchGet)
   *
   * @param string $packageName Package name of the app.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sku Unique identifier for the in-app products.
   * @return InappproductsBatchGetResponse
   * @throws \Google\Service\Exception
   */
  public function batchGet($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], InappproductsBatchGetResponse::class);
  }
  /**
   * Updates or inserts one or more in-app products (managed products or
   * subscriptions). Set the latencyTolerance field on nested requests to
   * PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT to achieve maximum update
   * throughput. This method should no longer be used to update subscriptions. See
   * [this article](https://android-developers.googleblog.com/2023/06/changes-to-
   * google-play-developer-api-june-2023.html) for more information.
   * (inappproducts.batchUpdate)
   *
   * @param string $packageName Package name of the app.
   * @param InappproductsBatchUpdateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return InappproductsBatchUpdateResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($packageName, InappproductsBatchUpdateRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], InappproductsBatchUpdateResponse::class);
  }
  /**
   * Deletes an in-app product (a managed product or a subscription). This method
   * should no longer be used to delete subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information.
   * (inappproducts.delete)
   *
   * @param string $packageName Package name of the app.
   * @param string $sku Unique identifier for the in-app product.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product update. Defaults to latency-sensitive.
   * @throws \Google\Service\Exception
   */
  public function delete($packageName, $sku, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'sku' => $sku];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Gets an in-app product, which can be a managed product or a subscription.
   * This method should no longer be used to retrieve subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information. (inappproducts.get)
   *
   * @param string $packageName Package name of the app.
   * @param string $sku Unique identifier for the in-app product.
   * @param array $optParams Optional parameters.
   * @return InAppProduct
   * @throws \Google\Service\Exception
   */
  public function get($packageName, $sku, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'sku' => $sku];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InAppProduct::class);
  }
  /**
   * Creates an in-app product (a managed product or a subscription). This method
   * should no longer be used to create subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information.
   * (inappproducts.insert)
   *
   * @param string $packageName Package name of the app.
   * @param InAppProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool autoConvertMissingPrices If true the prices for all regions
   * targeted by the parent app that don't have a price specified for this in-app
   * product will be auto converted to the target currency based on the default
   * price. Defaults to false.
   * @return InAppProduct
   * @throws \Google\Service\Exception
   */
  public function insert($packageName, InAppProduct $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], InAppProduct::class);
  }
  /**
   * Lists all in-app products - both managed products and subscriptions. If an
   * app has a large number of in-app products, the response may be paginated. In
   * this case the response field `tokenPagination.nextPageToken` will be set and
   * the caller should provide its value as a `token` request parameter to
   * retrieve the next page. This method should no longer be used to retrieve
   * subscriptions. See [this article](https://android-
   * developers.googleblog.com/2023/06/changes-to-google-play-developer-api-
   * june-2023.html) for more information. (inappproducts.listInappproducts)
   *
   * @param string $packageName Package name of the app.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string maxResults Deprecated and ignored. The page size is
   * determined by the server.
   * @opt_param string startIndex Deprecated and ignored. Set the `token`
   * parameter to retrieve the next page.
   * @opt_param string token Pagination token. If empty, list starts at the first
   * product.
   * @return InappproductsListResponse
   * @throws \Google\Service\Exception
   */
  public function listInappproducts($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], InappproductsListResponse::class);
  }
  /**
   * Patches an in-app product (a managed product or a subscription). This method
   * should no longer be used to update subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information.
   * (inappproducts.patch)
   *
   * @param string $packageName Package name of the app.
   * @param string $sku Unique identifier for the in-app product.
   * @param InAppProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool autoConvertMissingPrices If true the prices for all regions
   * targeted by the parent app that don't have a price specified for this in-app
   * product will be auto converted to the target currency based on the default
   * price. Defaults to false.
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product update. Defaults to latency-sensitive.
   * @return InAppProduct
   * @throws \Google\Service\Exception
   */
  public function patch($packageName, $sku, InAppProduct $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'sku' => $sku, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], InAppProduct::class);
  }
  /**
   * Updates an in-app product (a managed product or a subscription). This method
   * should no longer be used to update subscriptions. See [this
   * article](https://android-developers.googleblog.com/2023/06/changes-to-google-
   * play-developer-api-june-2023.html) for more information.
   * (inappproducts.update)
   *
   * @param string $packageName Package name of the app.
   * @param string $sku Unique identifier for the in-app product.
   * @param InAppProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true, and the in-app product with the
   * given package_name and sku doesn't exist, the in-app product will be created.
   * @opt_param bool autoConvertMissingPrices If true the prices for all regions
   * targeted by the parent app that don't have a price specified for this in-app
   * product will be auto converted to the target currency based on the default
   * price. Defaults to false.
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product update. Defaults to latency-sensitive.
   * @return InAppProduct
   * @throws \Google\Service\Exception
   */
  public function update($packageName, $sku, InAppProduct $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'sku' => $sku, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], InAppProduct::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Inappproducts::class, 'Google_Service_AndroidPublisher_Resource_Inappproducts');
