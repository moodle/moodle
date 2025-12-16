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

namespace Google\Service\AuthorizedBuyersMarketplace\Resource;

use Google\Service\AuthorizedBuyersMarketplace\AuctionPackage;
use Google\Service\AuthorizedBuyersMarketplace\ListAuctionPackagesResponse;
use Google\Service\AuthorizedBuyersMarketplace\SubscribeAuctionPackageRequest;
use Google\Service\AuthorizedBuyersMarketplace\SubscribeClientsRequest;
use Google\Service\AuthorizedBuyersMarketplace\UnsubscribeAuctionPackageRequest;
use Google\Service\AuthorizedBuyersMarketplace\UnsubscribeClientsRequest;

/**
 * The "auctionPackages" collection of methods.
 * Typical usage is:
 *  <code>
 *   $authorizedbuyersmarketplaceService = new Google\Service\AuthorizedBuyersMarketplace(...);
 *   $auctionPackages = $authorizedbuyersmarketplaceService->buyers_auctionPackages;
 *  </code>
 */
class BuyersAuctionPackages extends \Google\Service\Resource
{
  /**
   * Gets an auction package given its name. (auctionPackages.get)
   *
   * @param string $name Required. Name of auction package to get. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}`
   * @param array $optParams Optional parameters.
   * @return AuctionPackage
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AuctionPackage::class);
  }
  /**
   * List the auction packages. Buyers can use the URL path
   * "/v1/buyers/{accountId}/auctionPackages" to list auction packages for the
   * current buyer and its clients. Bidders can use the URL path
   * "/v1/bidders/{accountId}/auctionPackages" to list auction packages for the
   * bidder, its media planners, its buyers, and all their clients.
   * (auctionPackages.listBuyersAuctionPackages)
   *
   * @param string $parent Required. Name of the parent buyer that can access the
   * auction package. Format: `buyers/{accountId}`. When used with a bidder
   * account, the auction packages that the bidder, its media planners, its buyers
   * and clients are subscribed to will be listed, in the format
   * `bidders/{accountId}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Optional query string using the [Cloud API
   * list filtering syntax](/authorized-buyers/apis/guides/list-filters). Only
   * supported when parent is bidder. Supported columns for filtering are: *
   * displayName * createTime * updateTime * eligibleSeatIds
   * @opt_param string orderBy Optional. An optional query string to sort auction
   * packages using the [Cloud API sorting
   * syntax](https://cloud.google.com/apis/design/design_patterns#sorting_order).
   * If no sort order is specified, results will be returned in an arbitrary
   * order. Only supported when parent is bidder. Supported columns for sorting
   * are: * displayName * createTime * updateTime
   * @opt_param int pageSize Requested page size. The server may return fewer
   * results than requested. Max allowed page size is 500.
   * @opt_param string pageToken The page token as returned.
   * ListAuctionPackagesResponse.nextPageToken
   * @return ListAuctionPackagesResponse
   * @throws \Google\Service\Exception
   */
  public function listBuyersAuctionPackages($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAuctionPackagesResponse::class);
  }
  /**
   * Subscribe to the auction package for the specified buyer. Once subscribed,
   * the bidder will receive a call out for inventory matching the auction package
   * targeting criteria with the auction package deal ID and the specified buyer.
   * (auctionPackages.subscribe)
   *
   * @param string $name Required. Name of the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}`
   * @param SubscribeAuctionPackageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AuctionPackage
   * @throws \Google\Service\Exception
   */
  public function subscribe($name, SubscribeAuctionPackageRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('subscribe', [$params], AuctionPackage::class);
  }
  /**
   * Subscribe the specified clients of the buyer to the auction package. If a
   * client in the list does not belong to the buyer, an error response will be
   * returned, and all of the following clients in the list will not be
   * subscribed. Subscribing an already subscribed client will have no effect.
   * (auctionPackages.subscribeClients)
   *
   * @param string $auctionPackage Required. Name of the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}`
   * @param SubscribeClientsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AuctionPackage
   * @throws \Google\Service\Exception
   */
  public function subscribeClients($auctionPackage, SubscribeClientsRequest $postBody, $optParams = [])
  {
    $params = ['auctionPackage' => $auctionPackage, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('subscribeClients', [$params], AuctionPackage::class);
  }
  /**
   * Unsubscribe from the auction package for the specified buyer. Once
   * unsubscribed, the bidder will no longer receive a call out for the auction
   * package deal ID and the specified buyer. (auctionPackages.unsubscribe)
   *
   * @param string $name Required. Name of the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}`
   * @param UnsubscribeAuctionPackageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AuctionPackage
   * @throws \Google\Service\Exception
   */
  public function unsubscribe($name, UnsubscribeAuctionPackageRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unsubscribe', [$params], AuctionPackage::class);
  }
  /**
   * Unsubscribe from the auction package for the specified clients of the buyer.
   * Unsubscribing a client that is not subscribed will have no effect.
   * (auctionPackages.unsubscribeClients)
   *
   * @param string $auctionPackage Required. Name of the auction package. Format:
   * `buyers/{accountId}/auctionPackages/{auctionPackageId}`
   * @param UnsubscribeClientsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return AuctionPackage
   * @throws \Google\Service\Exception
   */
  public function unsubscribeClients($auctionPackage, UnsubscribeClientsRequest $postBody, $optParams = [])
  {
    $params = ['auctionPackage' => $auctionPackage, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('unsubscribeClients', [$params], AuctionPackage::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuyersAuctionPackages::class, 'Google_Service_AuthorizedBuyersMarketplace_Resource_BuyersAuctionPackages');
