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

class BidResponseWithoutBidsStatusRow extends \Google\Model
{
  /**
   * A placeholder for an undefined status. This value will never be returned in
   * responses.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The response had no bids.
   */
  public const STATUS_RESPONSES_WITHOUT_BIDS = 'RESPONSES_WITHOUT_BIDS';
  /**
   * The response had no bids for the specified account, though it may have
   * included bids on behalf of other accounts. Applies if: 1. Request is on
   * behalf of a bidder and an account filter is present. 2. Request is on
   * behalf of a child seat.
   */
  public const STATUS_RESPONSES_WITHOUT_BIDS_FOR_ACCOUNT = 'RESPONSES_WITHOUT_BIDS_FOR_ACCOUNT';
  /**
   * The response had no bids for the specified deal, though it may have
   * included bids on other deals on behalf of the account to which the deal
   * belongs. If request is on behalf of a bidder and an account filter is not
   * present, this also includes responses that have bids on behalf of accounts
   * other than the account to which the deal belongs.
   */
  public const STATUS_RESPONSES_WITHOUT_BIDS_FOR_DEAL = 'RESPONSES_WITHOUT_BIDS_FOR_DEAL';
  protected $impressionCountType = MetricValue::class;
  protected $impressionCountDataType = '';
  protected $rowDimensionsType = RowDimensions::class;
  protected $rowDimensionsDataType = '';
  /**
   * The status specifying why the bid responses were considered to have no
   * applicable bids.
   *
   * @var string
   */
  public $status;

  /**
   * The number of impressions for which there was a bid response with the
   * specified status.
   *
   * @param MetricValue $impressionCount
   */
  public function setImpressionCount(MetricValue $impressionCount)
  {
    $this->impressionCount = $impressionCount;
  }
  /**
   * @return MetricValue
   */
  public function getImpressionCount()
  {
    return $this->impressionCount;
  }
  /**
   * The values of all dimensions associated with metric values in this row.
   *
   * @param RowDimensions $rowDimensions
   */
  public function setRowDimensions(RowDimensions $rowDimensions)
  {
    $this->rowDimensions = $rowDimensions;
  }
  /**
   * @return RowDimensions
   */
  public function getRowDimensions()
  {
    return $this->rowDimensions;
  }
  /**
   * The status specifying why the bid responses were considered to have no
   * applicable bids.
   *
   * Accepted values: STATUS_UNSPECIFIED, RESPONSES_WITHOUT_BIDS,
   * RESPONSES_WITHOUT_BIDS_FOR_ACCOUNT, RESPONSES_WITHOUT_BIDS_FOR_DEAL
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BidResponseWithoutBidsStatusRow::class, 'Google_Service_AdExchangeBuyerII_BidResponseWithoutBidsStatusRow');
