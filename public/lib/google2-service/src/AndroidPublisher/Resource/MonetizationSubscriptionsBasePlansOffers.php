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

use Google\Service\AndroidPublisher\ActivateSubscriptionOfferRequest;
use Google\Service\AndroidPublisher\BatchGetSubscriptionOffersRequest;
use Google\Service\AndroidPublisher\BatchGetSubscriptionOffersResponse;
use Google\Service\AndroidPublisher\BatchUpdateSubscriptionOfferStatesRequest;
use Google\Service\AndroidPublisher\BatchUpdateSubscriptionOfferStatesResponse;
use Google\Service\AndroidPublisher\BatchUpdateSubscriptionOffersRequest;
use Google\Service\AndroidPublisher\BatchUpdateSubscriptionOffersResponse;
use Google\Service\AndroidPublisher\DeactivateSubscriptionOfferRequest;
use Google\Service\AndroidPublisher\ListSubscriptionOffersResponse;
use Google\Service\AndroidPublisher\SubscriptionOffer;

/**
 * The "offers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $offers = $androidpublisherService->monetization_subscriptions_basePlans_offers;
 *  </code>
 */
class MonetizationSubscriptionsBasePlansOffers extends \Google\Service\Resource
{
  /**
   * Activates a subscription offer. Once activated, subscription offers will be
   * available to new subscribers. (offers.activate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to activate.
   * @param string $productId Required. The parent subscription (ID) of the offer
   * to activate.
   * @param string $basePlanId Required. The parent base plan (ID) of the offer to
   * activate.
   * @param string $offerId Required. The unique offer ID of the offer to
   * activate.
   * @param ActivateSubscriptionOfferRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SubscriptionOffer
   * @throws \Google\Service\Exception
   */
  public function activate($packageName, $productId, $basePlanId, $offerId, ActivateSubscriptionOfferRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('activate', [$params], SubscriptionOffer::class);
  }
  /**
   * Reads one or more subscription offers. (offers.batchGet)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the subscriptions should be created or updated. Must be equal to the
   * package_name field on all the requests.
   * @param string $productId Required. The product ID of the parent subscription,
   * if all updated offers belong to the same subscription. If this request spans
   * multiple subscriptions, set this field to "-". Must be set.
   * @param string $basePlanId Required. The parent base plan (ID) for which the
   * offers should be read. May be specified as '-' to read offers from multiple
   * base plans.
   * @param BatchGetSubscriptionOffersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchGetSubscriptionOffersResponse
   * @throws \Google\Service\Exception
   */
  public function batchGet($packageName, $productId, $basePlanId, BatchGetSubscriptionOffersRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], BatchGetSubscriptionOffersResponse::class);
  }
  /**
   * Updates a batch of subscription offers. Set the latencyTolerance field on
   * nested requests to PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT to
   * achieve maximum update throughput. (offers.batchUpdate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated subscription offers. Must be equal to the package_name field on all
   * the updated SubscriptionOffer resources.
   * @param string $productId Required. The product ID of the parent subscription,
   * if all updated offers belong to the same subscription. If this request spans
   * multiple subscriptions, set this field to "-". Must be set.
   * @param string $basePlanId Required. The parent base plan (ID) for which the
   * offers should be updated. May be specified as '-' to update offers from
   * multiple base plans.
   * @param BatchUpdateSubscriptionOffersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdateSubscriptionOffersResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($packageName, $productId, $basePlanId, BatchUpdateSubscriptionOffersRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], BatchUpdateSubscriptionOffersResponse::class);
  }
  /**
   * Updates a batch of subscription offer states. Set the latencyTolerance field
   * on nested requests to PRODUCT_UPDATE_LATENCY_TOLERANCE_LATENCY_TOLERANT to
   * achieve maximum update throughput. (offers.batchUpdateStates)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated subscription offers. Must be equal to the package_name field on all
   * the updated SubscriptionOffer resources.
   * @param string $productId Required. The product ID of the parent subscription,
   * if all updated offers belong to the same subscription. If this request spans
   * multiple subscriptions, set this field to "-". Must be set.
   * @param string $basePlanId Required. The parent base plan (ID) for which the
   * offers should be updated. May be specified as '-' to update offers from
   * multiple base plans.
   * @param BatchUpdateSubscriptionOfferStatesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdateSubscriptionOfferStatesResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdateStates($packageName, $productId, $basePlanId, BatchUpdateSubscriptionOfferStatesRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdateStates', [$params], BatchUpdateSubscriptionOfferStatesResponse::class);
  }
  /**
   * Creates a new subscription offer. Only auto-renewing base plans can have
   * subscription offers. The offer state will be DRAFT until it is activated.
   * (offers.create)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the offer should be created. Must be equal to the package_name field on the
   * Subscription resource.
   * @param string $productId Required. The parent subscription (ID) for which the
   * offer should be created. Must be equal to the product_id field on the
   * SubscriptionOffer resource.
   * @param string $basePlanId Required. The parent base plan (ID) for which the
   * offer should be created. Must be equal to the base_plan_id field on the
   * SubscriptionOffer resource.
   * @param SubscriptionOffer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string offerId Required. The ID to use for the offer. For the
   * requirements on this format, see the documentation of the offer_id field on
   * the SubscriptionOffer resource.
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
   * @return SubscriptionOffer
   * @throws \Google\Service\Exception
   */
  public function create($packageName, $productId, $basePlanId, SubscriptionOffer $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], SubscriptionOffer::class);
  }
  /**
   * Deactivates a subscription offer. Once deactivated, existing subscribers will
   * maintain their subscription, but the offer will become unavailable to new
   * subscribers. (offers.deactivate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to deactivate.
   * @param string $productId Required. The parent subscription (ID) of the offer
   * to deactivate.
   * @param string $basePlanId Required. The parent base plan (ID) of the offer to
   * deactivate.
   * @param string $offerId Required. The unique offer ID of the offer to
   * deactivate.
   * @param DeactivateSubscriptionOfferRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SubscriptionOffer
   * @throws \Google\Service\Exception
   */
  public function deactivate($packageName, $productId, $basePlanId, $offerId, DeactivateSubscriptionOfferRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deactivate', [$params], SubscriptionOffer::class);
  }
  /**
   * Deletes a subscription offer. Can only be done for draft offers. This action
   * is irreversible. (offers.delete)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to delete.
   * @param string $productId Required. The parent subscription (ID) of the offer
   * to delete.
   * @param string $basePlanId Required. The parent base plan (ID) of the offer to
   * delete.
   * @param string $offerId Required. The unique offer ID of the offer to delete.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($packageName, $productId, $basePlanId, $offerId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'offerId' => $offerId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Reads a single offer (offers.get)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to get.
   * @param string $productId Required. The parent subscription (ID) of the offer
   * to get.
   * @param string $basePlanId Required. The parent base plan (ID) of the offer to
   * get.
   * @param string $offerId Required. The unique offer ID of the offer to get.
   * @param array $optParams Optional parameters.
   * @return SubscriptionOffer
   * @throws \Google\Service\Exception
   */
  public function get($packageName, $productId, $basePlanId, $offerId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'offerId' => $offerId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], SubscriptionOffer::class);
  }
  /**
   * Lists all offers under a given subscription.
   * (offers.listMonetizationSubscriptionsBasePlansOffers)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the subscriptions should be read.
   * @param string $productId Required. The parent subscription (ID) for which the
   * offers should be read. May be specified as '-' to read all offers under an
   * app.
   * @param string $basePlanId Required. The parent base plan (ID) for which the
   * offers should be read. May be specified as '-' to read all offers under a
   * subscription or an app. Must be specified as '-' if product_id is specified
   * as '-'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of subscriptions to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * subscriptions will be returned. The maximum value is 1000; values above 1000
   * will be coerced to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSubscriptionsOffers` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListSubscriptionOffers`
   * must match the call that provided the page token.
   * @return ListSubscriptionOffersResponse
   * @throws \Google\Service\Exception
   */
  public function listMonetizationSubscriptionsBasePlansOffers($packageName, $productId, $basePlanId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSubscriptionOffersResponse::class);
  }
  /**
   * Updates an existing subscription offer. (offers.patch)
   *
   * @param string $packageName Required. Immutable. The package name of the app
   * the parent subscription belongs to.
   * @param string $productId Required. Immutable. The ID of the parent
   * subscription this offer belongs to.
   * @param string $basePlanId Required. Immutable. The ID of the base plan to
   * which this offer is an extension.
   * @param string $offerId Required. Immutable. Unique ID of this subscription
   * offer. Must be unique within the base plan.
   * @param SubscriptionOffer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the subscription
   * offer with the given package_name, product_id, base_plan_id and offer_id
   * doesn't exist, an offer will be created. If a new offer is created,
   * update_mask is ignored.
   * @opt_param string latencyTolerance Optional. The latency tolerance for the
   * propagation of this product update. Defaults to latency-sensitive.
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
   * @return SubscriptionOffer
   * @throws \Google\Service\Exception
   */
  public function patch($packageName, $productId, $basePlanId, $offerId, SubscriptionOffer $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'basePlanId' => $basePlanId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], SubscriptionOffer::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonetizationSubscriptionsBasePlansOffers::class, 'Google_Service_AndroidPublisher_Resource_MonetizationSubscriptionsBasePlansOffers');
