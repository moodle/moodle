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

use Google\Service\AndroidPublisher\BatchDeleteOneTimeProductsRequest;
use Google\Service\AndroidPublisher\BatchGetOneTimeProductsResponse;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductsRequest;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductsResponse;
use Google\Service\AndroidPublisher\ListOneTimeProductsResponse;
use Google\Service\AndroidPublisher\OneTimeProduct;

/**
 * The "onetimeproducts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $onetimeproducts = $androidpublisherService->monetization_onetimeproducts;
 *  </code>
 */
class MonetizationOnetimeproducts extends \Google\Service\Resource
{
  /**
   * Deletes one or more one-time products. (onetimeproducts.batchDelete)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the one-time products should be deleted. Must be equal to the package_name
   * field on all the OneTimeProduct resources.
   * @param BatchDeleteOneTimeProductsRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function batchDelete($packageName, BatchDeleteOneTimeProductsRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params]);
  }
  /**
   * Reads one or more one-time products. (onetimeproducts.batchGet)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the products should be retrieved. Must be equal to the package_name field on
   * all requests.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string productIds Required. A list of up to 100 product IDs to
   * retrieve. All IDs must be different.
   * @return BatchGetOneTimeProductsResponse
   * @throws \Google\Service\Exception
   */
  public function batchGet($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], BatchGetOneTimeProductsResponse::class);
  }
  /**
   * Creates or updates one or more one-time products.
   * (onetimeproducts.batchUpdate)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the one-time products should be updated. Must be equal to the package_name
   * field on all the OneTimeProduct resources.
   * @param BatchUpdateOneTimeProductsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdateOneTimeProductsResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($packageName, BatchUpdateOneTimeProductsRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], BatchUpdateOneTimeProductsResponse::class);
  }
  /**
   * Deletes a one-time product. (onetimeproducts.delete)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * one-time product to delete.
   * @param string $productId Required. The one-time product ID of the one-time
   * product to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product update. Defaults to latency-sensitive.
   * @throws \Google\Service\Exception
   */
  public function delete($packageName, $productId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Reads a single one-time product. (onetimeproducts.get)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * product to retrieve.
   * @param string $productId Required. The product ID of the product to retrieve.
   * @param array $optParams Optional parameters.
   * @return OneTimeProduct
   * @throws \Google\Service\Exception
   */
  public function get($packageName, $productId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], OneTimeProduct::class);
  }
  /**
   * Lists all one-time products under a given app.
   * (onetimeproducts.listMonetizationOnetimeproducts)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the one-time product should be read.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of one-time product to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 one-time products will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListOneTimeProducts` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListOneTimeProducts` must
   * match the call that provided the page token.
   * @return ListOneTimeProductsResponse
   * @throws \Google\Service\Exception
   */
  public function listMonetizationOnetimeproducts($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOneTimeProductsResponse::class);
  }
  /**
   * Creates or updates a one-time product. (onetimeproducts.patch)
   *
   * @param string $packageName Required. Immutable. Package name of the parent
   * app.
   * @param string $productId Required. Immutable. Unique product ID of the
   * product. Unique within the parent app. Product IDs must start with a number
   * or lowercase letter, and can contain numbers (0-9), lowercase letters (a-z),
   * underscores (_), and periods (.).
   * @param OneTimeProduct $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the one-time
   * product with the given package_name and product_id doesn't exist, the one-
   * time product will be created. If a new one-time product is created,
   * update_mask is ignored.
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product upsert. Defaults to latency-sensitive.
   * @opt_param string regionsVersion.version Required. A string representing the
   * version of available regions being used for the specified resource. Regional
   * prices and latest supported version for the resource have to be specified
   * according to the information published in [this
   * article](https://support.google.com/googleplay/android-
   * developer/answer/10532353). Each time the supported locations substantially
   * change, the version will be incremented. Using this field will ensure that
   * creating and updating the resource with an older region's version and set of
   * regional prices and currencies will succeed even though a new version is
   * available.
   * @opt_param string updateMask Required. The list of fields to be updated.
   * @return OneTimeProduct
   * @throws \Google\Service\Exception
   */
  public function patch($packageName, $productId, OneTimeProduct $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], OneTimeProduct::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonetizationOnetimeproducts::class, 'Google_Service_AndroidPublisher_Resource_MonetizationOnetimeproducts');
