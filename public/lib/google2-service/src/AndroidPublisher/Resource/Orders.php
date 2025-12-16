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

use Google\Service\AndroidPublisher\BatchGetOrdersResponse;
use Google\Service\AndroidPublisher\Order;

/**
 * The "orders" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $orders = $androidpublisherService->orders;
 *  </code>
 */
class Orders extends \Google\Service\Resource
{
  /**
   * Get order details for a list of orders. (orders.batchget)
   *
   * @param string $packageName Required. The package name of the application for
   * which this subscription or in-app item was purchased (for example,
   * 'com.some.thing').
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderIds Required. The list of order IDs to retrieve order
   * details for. There must be between 1 and 1000 (inclusive) order IDs per
   * request. If any order ID is not found or does not match the provided package,
   * the entire request will fail with an error. The order IDs must be distinct.
   * @return BatchGetOrdersResponse
   * @throws \Google\Service\Exception
   */
  public function batchget($packageName, $optParams = [])
  {
    $params = ['packageName' => $packageName];
    $params = array_merge($params, $optParams);
    return $this->call('batchget', [$params], BatchGetOrdersResponse::class);
  }
  /**
   * Get order details for a single order. (orders.get)
   *
   * @param string $packageName Required. The package name of the application for
   * which this subscription or in-app item was purchased (for example,
   * 'com.some.thing').
   * @param string $orderId Required. The order ID provided to the user when the
   * subscription or in-app order was purchased.
   * @param array $optParams Optional parameters.
   * @return Order
   * @throws \Google\Service\Exception
   */
  public function get($packageName, $orderId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'orderId' => $orderId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Order::class);
  }
  /**
   * Refunds a user's subscription or in-app purchase order. Orders older than 3
   * years cannot be refunded. (orders.refund)
   *
   * @param string $packageName The package name of the application for which this
   * subscription or in-app item was purchased (for example, 'com.some.thing').
   * @param string $orderId The order ID provided to the user when the
   * subscription or in-app order was purchased.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool revoke Whether to revoke the purchased item. If set to true,
   * access to the subscription or in-app item will be terminated immediately. If
   * the item is a recurring subscription, all future payments will also be
   * terminated. Consumed in-app items need to be handled by developer's app.
   * (optional).
   * @throws \Google\Service\Exception
   */
  public function refund($packageName, $orderId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'orderId' => $orderId];
    $params = array_merge($params, $optParams);
    return $this->call('refund', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Orders::class, 'Google_Service_AndroidPublisher_Resource_Orders');
