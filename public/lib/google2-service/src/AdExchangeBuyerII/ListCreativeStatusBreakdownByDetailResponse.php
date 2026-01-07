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

namespace Google\Service\AdExchangeBuyerII;

class ListCreativeStatusBreakdownByDetailResponse extends \Google\Collection
{
  /**
   * A placeholder for an undefined status. This value will never be returned in
   * responses.
   */
  public const DETAIL_TYPE_DETAIL_TYPE_UNSPECIFIED = 'DETAIL_TYPE_UNSPECIFIED';
  /**
   * Indicates that the detail ID refers to a creative attribute; see
   * [publisher-excludable-creative-
   * attributes](https://developers.google.com/authorized-
   * buyers/rtb/downloads/publisher-excludable-creative-attributes).
   */
  public const DETAIL_TYPE_CREATIVE_ATTRIBUTE = 'CREATIVE_ATTRIBUTE';
  /**
   * Indicates that the detail ID refers to a vendor; see
   * [vendors](https://developers.google.com/authorized-
   * buyers/rtb/downloads/vendors). This namespace is different from that of the
   * `ATP_VENDOR` detail type.
   */
  public const DETAIL_TYPE_VENDOR = 'VENDOR';
  /**
   * Indicates that the detail ID refers to a sensitive category; see [ad-
   * sensitive-categories](https://developers.google.com/authorized-
   * buyers/rtb/downloads/ad-sensitive-categories).
   */
  public const DETAIL_TYPE_SENSITIVE_CATEGORY = 'SENSITIVE_CATEGORY';
  /**
   * Indicates that the detail ID refers to a product category; see [ad-product-
   * categories](https://developers.google.com/authorized-
   * buyers/rtb/downloads/ad-product-categories).
   */
  public const DETAIL_TYPE_PRODUCT_CATEGORY = 'PRODUCT_CATEGORY';
  /**
   * Indicates that the detail ID refers to a disapproval reason; see
   * DisapprovalReason enum in [snippet-status-report-
   * proto](https://developers.google.com/authorized-
   * buyers/rtb/downloads/snippet-status-report-proto).
   */
  public const DETAIL_TYPE_DISAPPROVAL_REASON = 'DISAPPROVAL_REASON';
  /**
   * Indicates that the detail ID refers to a policy topic.
   */
  public const DETAIL_TYPE_POLICY_TOPIC = 'POLICY_TOPIC';
  /**
   * Indicates that the detail ID refers to an ad technology provider (ATP); see
   * [providers] (https://storage.googleapis.com/adx-rtb-
   * dictionaries/providers.csv). This namespace is different from the `VENDOR`
   * detail type; see [ad technology
   * providers](https://support.google.com/admanager/answer/9012903) for more
   * information.
   */
  public const DETAIL_TYPE_ATP_VENDOR = 'ATP_VENDOR';
  /**
   * Indicates that the detail string refers the domain of an unknown vendor.
   */
  public const DETAIL_TYPE_VENDOR_DOMAIN = 'VENDOR_DOMAIN';
  /**
   * Indicates that the detail ID refers an IAB GVL ID which Google did not
   * detect in the latest TCF Vendor List. See [Global Vendor List]
   * (https://vendor-list.consensu.org/v2/vendor-list.json)
   */
  public const DETAIL_TYPE_GVL_ID = 'GVL_ID';
  protected $collection_key = 'filteredBidDetailRows';
  /**
   * The type of detail that the detail IDs represent.
   *
   * @var string
   */
  public $detailType;
  protected $filteredBidDetailRowsType = FilteredBidDetailRow::class;
  protected $filteredBidDetailRowsDataType = 'array';
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * ListCreativeStatusBreakdownByDetailRequest.pageToken field in the
   * subsequent call to the filteredBids.details.list method to retrieve the
   * next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The type of detail that the detail IDs represent.
   *
   * Accepted values: DETAIL_TYPE_UNSPECIFIED, CREATIVE_ATTRIBUTE, VENDOR,
   * SENSITIVE_CATEGORY, PRODUCT_CATEGORY, DISAPPROVAL_REASON, POLICY_TOPIC,
   * ATP_VENDOR, VENDOR_DOMAIN, GVL_ID
   *
   * @param self::DETAIL_TYPE_* $detailType
   */
  public function setDetailType($detailType)
  {
    $this->detailType = $detailType;
  }
  /**
   * @return self::DETAIL_TYPE_*
   */
  public function getDetailType()
  {
    return $this->detailType;
  }
  /**
   * List of rows, with counts of bids with a given creative status aggregated
   * by detail.
   *
   * @param FilteredBidDetailRow[] $filteredBidDetailRows
   */
  public function setFilteredBidDetailRows($filteredBidDetailRows)
  {
    $this->filteredBidDetailRows = $filteredBidDetailRows;
  }
  /**
   * @return FilteredBidDetailRow[]
   */
  public function getFilteredBidDetailRows()
  {
    return $this->filteredBidDetailRows;
  }
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * ListCreativeStatusBreakdownByDetailRequest.pageToken field in the
   * subsequent call to the filteredBids.details.list method to retrieve the
   * next page of results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListCreativeStatusBreakdownByDetailResponse::class, 'Google_Service_AdExchangeBuyerII_ListCreativeStatusBreakdownByDetailResponse');
