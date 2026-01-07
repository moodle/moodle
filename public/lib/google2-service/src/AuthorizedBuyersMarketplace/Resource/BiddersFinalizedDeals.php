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

use Google\Service\AuthorizedBuyersMarketplace\FinalizedDeal;
use Google\Service\AuthorizedBuyersMarketplace\ListFinalizedDealsResponse;
use Google\Service\AuthorizedBuyersMarketplace\SetReadyToServeRequest;

/**
 * The "finalizedDeals" collection of methods.
 * Typical usage is:
 *  <code>
 *   $authorizedbuyersmarketplaceService = new Google\Service\AuthorizedBuyersMarketplace(...);
 *   $finalizedDeals = $authorizedbuyersmarketplaceService->bidders_finalizedDeals;
 *  </code>
 */
class BiddersFinalizedDeals extends \Google\Service\Resource
{
  /**
   * Lists finalized deals. Use the URL path
   * "/v1/buyers/{accountId}/finalizedDeals" to list finalized deals for the
   * current buyer and its clients. Bidders can use the URL path
   * "/v1/bidders/{accountId}/finalizedDeals" to list finalized deals for the
   * bidder, its buyers and all their clients.
   * (finalizedDeals.listBiddersFinalizedDeals)
   *
   * @param string $parent Required. The buyer to list the finalized deals for, in
   * the format: `buyers/{accountId}`. When used to list finalized deals for a
   * bidder, its buyers and clients, in the format `bidders/{accountId}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional query string using the [Cloud API list
   * filtering syntax](https://developers.google.com/authorized-
   * buyers/apis/guides/list-filters) Supported columns for filtering are: *
   * deal.displayName * deal.dealType * deal.createTime * deal.updateTime *
   * deal.flightStartTime * deal.flightEndTime * deal.eligibleSeatIds *
   * dealServingStatus
   * @opt_param string orderBy An optional query string to sort finalized deals
   * using the [Cloud API sorting
   * syntax](https://cloud.google.com/apis/design/design_patterns#sorting_order).
   * If no sort order is specified, results will be returned in an arbitrary
   * order. Supported columns for sorting are: * deal.displayName *
   * deal.createTime * deal.updateTime * deal.flightStartTime * deal.flightEndTime
   * * rtbMetrics.bidRequests7Days * rtbMetrics.bids7Days *
   * rtbMetrics.adImpressions7Days * rtbMetrics.bidRate7Days *
   * rtbMetrics.filteredBidRate7Days * rtbMetrics.mustBidRateCurrentMonth
   * @opt_param int pageSize Requested page size. The server may return fewer
   * results than requested. If requested more than 500, the server will return
   * 500 results per page. If unspecified, the server will pick a default page
   * size of 100.
   * @opt_param string pageToken The page token as returned from
   * ListFinalizedDealsResponse.
   * @return ListFinalizedDealsResponse
   * @throws \Google\Service\Exception
   */
  public function listBiddersFinalizedDeals($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFinalizedDealsResponse::class);
  }
  /**
   * Sets the given finalized deal as ready to serve. By default, deals are set as
   * ready to serve as soon as they're finalized. If you want to opt out of the
   * default behavior, and manually indicate that deals are ready to serve, ask
   * your Technical Account Manager to add you to the allowlist. If you choose to
   * use this method, finalized deals belonging to the bidder and its child seats
   * don't start serving until after you call `setReadyToServe`, and after the
   * deals become active. For example, you can use this method to delay receiving
   * bid requests until your creative is ready. In addition, bidders can use the
   * URL path "/v1/bidders/{accountId}/finalizedDeals/{dealId}" to set ready to
   * serve for the finalized deals belong to itself, its child seats and all their
   * clients. This method only applies to programmatic guaranteed deals.
   * (finalizedDeals.setReadyToServe)
   *
   * @param string $deal Required. Format:
   * `buyers/{accountId}/finalizedDeals/{dealId}` or
   * `bidders/{accountId}/finalizedDeals/{dealId}`
   * @param SetReadyToServeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FinalizedDeal
   * @throws \Google\Service\Exception
   */
  public function setReadyToServe($deal, SetReadyToServeRequest $postBody, $optParams = [])
  {
    $params = ['deal' => $deal, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setReadyToServe', [$params], FinalizedDeal::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BiddersFinalizedDeals::class, 'Google_Service_AuthorizedBuyersMarketplace_Resource_BiddersFinalizedDeals');
