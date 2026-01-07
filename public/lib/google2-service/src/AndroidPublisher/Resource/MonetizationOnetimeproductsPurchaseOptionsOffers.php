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

use Google\Service\AndroidPublisher\ActivateOneTimeProductOfferRequest;
use Google\Service\AndroidPublisher\BatchDeleteOneTimeProductOffersRequest;
use Google\Service\AndroidPublisher\BatchGetOneTimeProductOffersRequest;
use Google\Service\AndroidPublisher\BatchGetOneTimeProductOffersResponse;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductOfferStatesRequest;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductOfferStatesResponse;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductOffersRequest;
use Google\Service\AndroidPublisher\BatchUpdateOneTimeProductOffersResponse;
use Google\Service\AndroidPublisher\CancelOneTimeProductOfferRequest;
use Google\Service\AndroidPublisher\DeactivateOneTimeProductOfferRequest;
use Google\Service\AndroidPublisher\ListOneTimeProductOffersResponse;
use Google\Service\AndroidPublisher\OneTimeProductOffer;

/**
 * The "offers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $androidpublisherService = new Google\Service\AndroidPublisher(...);
 *   $offers = $androidpublisherService->monetization_onetimeproducts_purchaseOptions_offers;
 *  </code>
 */
class MonetizationOnetimeproductsPurchaseOptionsOffers extends \Google\Service\Resource
{
  /**
   * Activates a one-time product offer. (offers.activate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to activate.
   * @param string $productId Required. The parent one-time product (ID) of the
   * offer to activate.
   * @param string $purchaseOptionId Required. The parent purchase option (ID) of
   * the offer to activate.
   * @param string $offerId Required. The offer ID of the offer to activate.
   * @param ActivateOneTimeProductOfferRequest $postBody
   * @param array $optParams Optional parameters.
   * @return OneTimeProductOffer
   * @throws \Google\Service\Exception
   */
  public function activate($packageName, $productId, $purchaseOptionId, $offerId, ActivateOneTimeProductOfferRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('activate', [$params], OneTimeProductOffer::class);
  }
  /**
   * Deletes one or more one-time product offers. (offers.batchDelete)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offers to delete. Must be equal to the package_name field on all the
   * OneTimeProductOffer resources.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all offers to delete belong to the same product. If this request
   * spans multiple one-time products, set this field to "-".
   * @param string $purchaseOptionId Required. The parent purchase option (ID) for
   * which the offers should be deleted. May be specified as '-' to update offers
   * from multiple purchase options.
   * @param BatchDeleteOneTimeProductOffersRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function batchDelete($packageName, $productId, $purchaseOptionId, BatchDeleteOneTimeProductOffersRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params]);
  }
  /**
   * Reads one or more one-time product offers. (offers.batchGet)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated offers. Must be equal to the package_name field on all the updated
   * OneTimeProductOffer resources.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all updated offers belong to the same product. If this request
   * spans multiple one-time products, set this field to "-".
   * @param string $purchaseOptionId Required. The parent purchase option (ID) for
   * which the offers should be updated. May be specified as '-' to update offers
   * from multiple purchase options.
   * @param BatchGetOneTimeProductOffersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchGetOneTimeProductOffersResponse
   * @throws \Google\Service\Exception
   */
  public function batchGet($packageName, $productId, $purchaseOptionId, BatchGetOneTimeProductOffersRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchGet', [$params], BatchGetOneTimeProductOffersResponse::class);
  }
  /**
   * Creates or updates one or more one-time product offers. (offers.batchUpdate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated offers. Must be equal to the package_name field on all the updated
   * OneTimeProductOffer resources.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all updated offers belong to the same product. If this request
   * spans multiple one-time products, set this field to "-".
   * @param string $purchaseOptionId Required. The parent purchase option (ID) for
   * which the offers should be updated. May be specified as '-' to update offers
   * from multiple purchase options.
   * @param BatchUpdateOneTimeProductOffersRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdateOneTimeProductOffersResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdate($packageName, $productId, $purchaseOptionId, BatchUpdateOneTimeProductOffersRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', [$params], BatchUpdateOneTimeProductOffersResponse::class);
  }
  /**
   * Updates a batch of one-time product offer states. (offers.batchUpdateStates)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * updated one-time product offers.
   * @param string $productId Required. The product ID of the parent one-time
   * product, if all updated offers belong to the same one-time product. If this
   * batch update spans multiple one-time products, set this field to "-".
   * @param string $purchaseOptionId Required. The purchase option ID of the
   * parent purchase option, if all updated offers belong to the same purchase
   * option. If this batch update spans multiple purchase options, set this field
   * to "-".
   * @param BatchUpdateOneTimeProductOfferStatesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchUpdateOneTimeProductOfferStatesResponse
   * @throws \Google\Service\Exception
   */
  public function batchUpdateStates($packageName, $productId, $purchaseOptionId, BatchUpdateOneTimeProductOfferStatesRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdateStates', [$params], BatchUpdateOneTimeProductOfferStatesResponse::class);
  }
  /**
   * Cancels a one-time product offer. (offers.cancel)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to cancel.
   * @param string $productId Required. The parent one-time product (ID) of the
   * offer to cancel.
   * @param string $purchaseOptionId Required. The parent purchase option (ID) of
   * the offer to cancel.
   * @param string $offerId Required. The offer ID of the offer to cancel.
   * @param CancelOneTimeProductOfferRequest $postBody
   * @param array $optParams Optional parameters.
   * @return OneTimeProductOffer
   * @throws \Google\Service\Exception
   */
  public function cancel($packageName, $productId, $purchaseOptionId, $offerId, CancelOneTimeProductOfferRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], OneTimeProductOffer::class);
  }
  /**
   * Deactivates a one-time product offer. (offers.deactivate)
   *
   * @param string $packageName Required. The parent app (package name) of the
   * offer to deactivate.
   * @param string $productId Required. The parent one-time product (ID) of the
   * offer to deactivate.
   * @param string $purchaseOptionId Required. The parent purchase option (ID) of
   * the offer to deactivate.
   * @param string $offerId Required. The offer ID of the offer to deactivate.
   * @param DeactivateOneTimeProductOfferRequest $postBody
   * @param array $optParams Optional parameters.
   * @return OneTimeProductOffer
   * @throws \Google\Service\Exception
   */
  public function deactivate($packageName, $productId, $purchaseOptionId, $offerId, DeactivateOneTimeProductOfferRequest $postBody, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId, 'offerId' => $offerId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deactivate', [$params], OneTimeProductOffer::class);
  }
  /**
   * Lists all offers under a given app, product, or purchase option.
   * (offers.listMonetizationOnetimeproductsPurchaseOptionsOffers)
   *
   * @param string $packageName Required. The parent app (package name) for which
   * the offers should be read.
   * @param string $productId Required. The parent one-time product (ID) for which
   * the offers should be read. May be specified as '-' to read all offers under
   * an app.
   * @param string $purchaseOptionId Required. The parent purchase option (ID) for
   * which the offers should be read. May be specified as '-' to read all offers
   * under a one-time product or an app. Must be specified as '-' if product_id is
   * specified as '-'.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of offers to return. The
   * service may return fewer than this value. If unspecified, at most 50 offers
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListOneTimeProductsOffers` call. Provide this to retrieve the subsequent
   * page. When paginating, product_id, package_name and purchase_option_id
   * provided to `ListOneTimeProductsOffersRequest` must match the call that
   * provided the page token.
   * @return ListOneTimeProductOffersResponse
   * @throws \Google\Service\Exception
   */
  public function listMonetizationOnetimeproductsPurchaseOptionsOffers($packageName, $productId, $purchaseOptionId, $optParams = [])
  {
    $params = ['packageName' => $packageName, 'productId' => $productId, 'purchaseOptionId' => $purchaseOptionId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOneTimeProductOffersResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonetizationOnetimeproductsPurchaseOptionsOffers::class, 'Google_Service_AndroidPublisher_Resource_MonetizationOnetimeproductsPurchaseOptionsOffers');
