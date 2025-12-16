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

namespace Google\Service\PaymentsResellerSubscription\Resource;

use Google\Service\PaymentsResellerSubscription\SubscriptionLineItem;

/**
 * The "lineItems" collection of methods.
 * Typical usage is:
 *  <code>
 *   $paymentsresellersubscriptionService = new Google\Service\PaymentsResellerSubscription(...);
 *   $lineItems = $paymentsresellersubscriptionService->partners_subscriptions_lineItems;
 *  </code>
 */
class PartnersSubscriptionsLineItems extends \Google\Service\Resource
{
  /**
   * Updates a line item of a subscription. It should be autenticated with a
   * service account. (lineItems.patch)
   *
   * @param string $name Identifier. Resource name of the line item. Format:
   * partners/{partner}/subscriptions/{subscription}/lineItems/{lineItem}
   * @param SubscriptionLineItem $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update. Only a
   * limited set of fields can be updated. The allowed fields are the following: -
   * `product_payload.googleHomePayload.googleStructureId`
   * @return SubscriptionLineItem
   * @throws \Google\Service\Exception
   */
  public function patch($name, SubscriptionLineItem $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], SubscriptionLineItem::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartnersSubscriptionsLineItems::class, 'Google_Service_PaymentsResellerSubscription_Resource_PartnersSubscriptionsLineItems');
