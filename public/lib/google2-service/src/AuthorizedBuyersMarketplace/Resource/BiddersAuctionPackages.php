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

use Google\Service\AuthorizedBuyersMarketplace\ListAuctionPackagesResponse;

/**
 * The "auctionPackages" collection of methods.
 * Typical usage is:
 *  <code>
 *   $authorizedbuyersmarketplaceService = new Google\Service\AuthorizedBuyersMarketplace(...);
 *   $auctionPackages = $authorizedbuyersmarketplaceService->bidders_auctionPackages;
 *  </code>
 */
class BiddersAuctionPackages extends \Google\Service\Resource
{
  /**
   * List the auction packages. Buyers can use the URL path
   * "/v1/buyers/{accountId}/auctionPackages" to list auction packages for the
   * current buyer and its clients. Bidders can use the URL path
   * "/v1/bidders/{accountId}/auctionPackages" to list auction packages for the
   * bidder, its media planners, its buyers, and all their clients.
   * (auctionPackages.listBiddersAuctionPackages)
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
  public function listBiddersAuctionPackages($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAuctionPackagesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BiddersAuctionPackages::class, 'Google_Service_AuthorizedBuyersMarketplace_Resource_BiddersAuctionPackages');
