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

class FilteredBidDetailRow extends \Google\Model
{
  protected $bidCountType = MetricValue::class;
  protected $bidCountDataType = '';
  /**
   * The ID of the detail, can be numeric or text. The associated value can be
   * looked up in the dictionary file corresponding to the DetailType in the
   * response message.
   *
   * @var string
   */
  public $detail;
  /**
   * Note: this field will be deprecated, use "detail" field instead. When
   * "detail" field represents an integer value, this field is populated as the
   * same integer value "detail" field represents, otherwise this field will be
   * 0. The ID of the detail. The associated value can be looked up in the
   * dictionary file corresponding to the DetailType in the response message.
   *
   * @deprecated
   * @var int
   */
  public $detailId;
  protected $rowDimensionsType = RowDimensions::class;
  protected $rowDimensionsDataType = '';

  /**
   * The number of bids with the specified detail.
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
   * The ID of the detail, can be numeric or text. The associated value can be
   * looked up in the dictionary file corresponding to the DetailType in the
   * response message.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Note: this field will be deprecated, use "detail" field instead. When
   * "detail" field represents an integer value, this field is populated as the
   * same integer value "detail" field represents, otherwise this field will be
   * 0. The ID of the detail. The associated value can be looked up in the
   * dictionary file corresponding to the DetailType in the response message.
   *
   * @deprecated
   * @param int $detailId
   */
  public function setDetailId($detailId)
  {
    $this->detailId = $detailId;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getDetailId()
  {
    return $this->detailId;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilteredBidDetailRow::class, 'Google_Service_AdExchangeBuyerII_FilteredBidDetailRow');
