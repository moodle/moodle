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

use Google\Service\AndroidPublisher\ProductPurchaseV2;

/**
 * The "productsv2" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $productsv2 = $androidpublisherService->purchases_productsv2;
 *  </code>
 */
class PurchasesProductsv2 extends \Google\Service\Resource
{
  /**
   * Checks the purchase and consumption status of an inapp item.
   * (productsv2.getproductpurchasev2)
   *
   * @param string $packageName The package name of the application the inapp
   * product was sold in (for example, 'com.some.thing').
   * @param string $token The token provided to the user's device when the inapp
   * product was purchased.
   * @param array $optParams Optional parameters.
   * @return ProductPurchaseV2
   * @throws \Google\Service\Exception
   */
  public function getproductpurchasev2($packageName, $token, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'token' => $token];
    $params = array_merge($params, $optParams);
    return $this->call('getproductpurchasev2', [$params], ProductPurchaseV2::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurchasesProductsv2::class, 'Google_Service_AndroidPublisher_Resource_PurchasesProductsv2');
