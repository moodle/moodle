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

class FilteredBidCreativeRow extends \Google\Model
{
  protected $bidCountType = MetricValue::class;
  protected $bidCountDataType = '';
  /**
   * The ID of the creative.
   *
   * @var string
   */
  public $creativeId;
  protected $rowDimensionsType = RowDimensions::class;
  protected $rowDimensionsDataType = '';

  /**
   * The number of bids with the specified creative.
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
   * The ID of the creative.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
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
class_alias(FilteredBidCreativeRow::class, 'Google_Service_AdExchangeBuyerII_FilteredBidCreativeRow');
