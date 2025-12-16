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

use Google\Service\AndroidPublisher\BatchDeletePurchaseOptionsRequest;
use Google\Service\AndroidPublisher\BatchUpdatePurchaseOptionStatesRequest;
use Google\Service\AndroidPublisher\BatchUpdatePurchaseOptionStatesResponse;

/**
 * The "purchaseOptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $purchaseOptions = $androidpublisherService->monetization_onetimeproducts_purchaseOptions;
 *  </code>
 */
class MonetizationOnetimeproductsPurchaseOptions extends \Google\Service\Resource
{
  /**
   * Deletes purchase options across one or multiple one-time products. By default
   * this operation will fail if there are any existing offers under the deleted
   * purchase options. Use the force parameter to override the default behavior.
   * (purchaseOptions.batchDelete)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * purchase options to delete.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all purchase options to delete belong to the same one-time
   * product. If this batch delete spans multiple one-time products, set this
   * field to "-".
   * @param BatchDeletePurchaseOptionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function batchDelete($packageName, $productId, BatchDeletePurchaseOptionsRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params]);
  }
  /**
   * Activates or deactivates purchase options across one or multiple one-time
   * products. (purchaseOptions.batchUpdateStates)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated purchase options.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all updated purchase options belong to the same one-time product.
   * If this batch update spans multiple one-time products, set this field to "-".
   * @param BatchUpdatePurchaseOptionStatesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdatePurchaseOptionStatesResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdateStates($packageName, $productId, BatchUpdatePurchaseOptionStatesRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdateStates', [$params], BatchUpdatePurchaseOptionStatesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonetizationOnetimeproductsPurchaseOptions::class, 'Google_Service_AndroidPublisher_Resource_MonetizationOnetimeproductsPurchaseOptions');
