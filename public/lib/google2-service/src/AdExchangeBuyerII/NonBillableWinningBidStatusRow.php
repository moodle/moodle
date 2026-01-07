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

class NonBillableWinningBidStatusRow extends \Google\Model
{
  /**
   * A placeholder for an undefined status. This value will never be returned in
   * responses.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The buyer was not billed because the ad was not rendered by the publisher.
   */
  public const STATUS_AD_NOT_RENDERED = 'AD_NOT_RENDERED';
  /**
   * The buyer was not billed because the impression won by the bid was
   * determined to be invalid.
   */
  public const STATUS_INVALID_IMPRESSION = 'INVALID_IMPRESSION';
  /**
   * A video impression was served but a fatal error was reported from the
   * client during playback.
   */
  public const STATUS_FATAL_VAST_ERROR = 'FATAL_VAST_ERROR';
  /**
   * The buyer was not billed because the ad was outplaced in the mediation
   * waterfall.
   */
  public const STATUS_LOST_IN_MEDIATION = 'LOST_IN_MEDIATION';
  /**
   * The impression was not billed because it exceeded a guaranteed deal
   * delivery goal.
   */
  public const STATUS_OVERDELIVERED_IMPRESSION = 'OVERDELIVERED_IMPRESSION';
  protected $bidCountType = MetricValue::class;
  protected $bidCountDataType = '';
  protected $rowDimensionsType = RowDimensions::class;
  protected $rowDimensionsDataType = '';
  /**
   * The status specifying why the winning bids were not billed.
   *
   * @var string
   */
  public $status;

  /**
   * The number of bids with the specified status.
   *
   * @param MetricValue $bidCount
   */
  public function setBidCount(MetricValue $bidCount)
  {
    $this->bidCount = $bidCount;
  }
  /**
   * @return MetricValue
   */
  public function getBidCount()
  {
    return $this->bidCount;
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
   * The status specifying why the winning bids were not billed.
   *
   * Accepted values: STATUS_UNSPECIFIED, AD_NOT_RENDERED, INVALID_IMPRESSION,
   * FATAL_VAST_ERROR, LOST_IN_MEDIATION, OVERDELIVERED_IMPRESSION
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
class_alias(NonBillableWinningBidStatusRow::class, 'Google_Service_AdExchangeBuyerII_NonBillableWinningBidStatusRow');
