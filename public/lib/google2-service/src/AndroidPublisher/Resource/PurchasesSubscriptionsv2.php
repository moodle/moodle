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

use Google\Service\AndroidPublisher\CancelSubscriptionPurchaseRequest;
use Google\Service\AndroidPublisher\CancelSubscriptionPurchaseResponse;
use Google\Service\AndroidPublisher\RevokeSubscriptionPurchaseRequest;
use Google\Service\AndroidPublisher\RevokeSubscriptionPurchaseResponse;
use Google\Service\AndroidPublisher\SubscriptionPurchaseV2;

/**
 * The "subscriptionsv2" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $subscriptionsv2 = $androidpublisherService->purchases_subscriptionsv2;
 *  </code>
 */
class PurchasesSubscriptionsv2 extends \Google\Service\Resource
{
  /**
   * Cancel a subscription purchase for the user. (subscriptionsv2.cancel)
   *
   * @param string $packageName Required. The package of the application for which
   * this subscription was purchased (for example, 'com.some.thing').
   * @param string $token Required. The token provided to the user's device when
   * the subscription was purchased.
   * @param CancelSubscriptionPurchaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelSubscriptionPurchaseResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($packageName, $token, CancelSubscriptionPurchaseRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'token' => $token, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelSubscriptionPurchaseResponse::class);
  }
  /**
   * Get metadata about a subscription (subscriptionsv2.get)
   *
   * @param string $packageName The package of the application for which this
   * subscription was purchased (for example, 'com.some.thing').
   * @param string $token Required. The token provided to the user's device when
   * the subscription was purchased.
   * @param array $optParams Optional parameters.
   * @return SubscriptionPurchaseV2
   * @throws \Google\Service\Exception
   */
  public function get($packageName, $token, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'token' => $token];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SubscriptionPurchaseV2::class);
  }
  /**
   * Revoke a subscription purchase for the user. (subscriptionsv2.revoke)
   *
   * @param string $packageName Required. The package of the application for which
   * this subscription was purchased (for example, 'com.some.thing').
   * @param string $token Required. The token provided to the user's device when
   * the subscription was purchased.
   * @param RevokeSubscriptionPurchaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RevokeSubscriptionPurchaseResponse
   * @throws \Google\Service\Exception
   */
  public function revoke($packageName, $token, RevokeSubscriptionPurchaseRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'token' => $token, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('revoke', [$params], RevokeSubscriptionPurchaseResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurchasesSubscriptionsv2::class, 'Google_Service_AndroidPublisher_Resource_PurchasesSubscriptionsv2');
