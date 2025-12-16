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

namespace Google\Service\DisplayVideo;

class RateDetails extends \Google\Model
{
  /**
   * The rate type is not specified or is unknown in this version.
   */
  public const INVENTORY_SOURCE_RATE_TYPE_INVENTORY_SOURCE_RATE_TYPE_UNSPECIFIED = 'INVENTORY_SOURCE_RATE_TYPE_UNSPECIFIED';
  /**
   * The rate type is CPM (Fixed).
   */
  public const INVENTORY_SOURCE_RATE_TYPE_INVENTORY_SOURCE_RATE_TYPE_CPM_FIXED = 'INVENTORY_SOURCE_RATE_TYPE_CPM_FIXED';
  /**
   * The rate type is CPM (Floor).
   */
  public const INVENTORY_SOURCE_RATE_TYPE_INVENTORY_SOURCE_RATE_TYPE_CPM_FLOOR = 'INVENTORY_SOURCE_RATE_TYPE_CPM_FLOOR';
  /**
   * The rate type is Cost per Day.
   */
  public const INVENTORY_SOURCE_RATE_TYPE_INVENTORY_SOURCE_RATE_TYPE_CPD = 'INVENTORY_SOURCE_RATE_TYPE_CPD';
  /**
   * The rate type is Flat.
   */
  public const INVENTORY_SOURCE_RATE_TYPE_INVENTORY_SOURCE_RATE_TYPE_FLAT = 'INVENTORY_SOURCE_RATE_TYPE_FLAT';
  /**
   * The rate type. Acceptable values are
   * `INVENTORY_SOURCE_RATE_TYPE_CPM_FIXED`,
   * `INVENTORY_SOURCE_RATE_TYPE_CPM_FLOOR`, and
   * `INVENTORY_SOURCE_RATE_TYPE_CPD`.
   *
   * @var string
   */
  public $inventorySourceRateType;
  protected $minimumSpendType = Money::class;
  protected $minimumSpendDataType = '';
  protected $rateType = Money::class;
  protected $rateDataType = '';
  /**
   * Required for guaranteed inventory sources. The number of impressions
   * guaranteed by the seller.
   *
   * @var string
   */
  public $unitsPurchased;

  /**
   * The rate type. Acceptable values are
   * `INVENTORY_SOURCE_RATE_TYPE_CPM_FIXED`,
   * `INVENTORY_SOURCE_RATE_TYPE_CPM_FLOOR`, and
   * `INVENTORY_SOURCE_RATE_TYPE_CPD`.
   *
   * Accepted values: INVENTORY_SOURCE_RATE_TYPE_UNSPECIFIED,
   * INVENTORY_SOURCE_RATE_TYPE_CPM_FIXED, INVENTORY_SOURCE_RATE_TYPE_CPM_FLOOR,
   * INVENTORY_SOURCE_RATE_TYPE_CPD, INVENTORY_SOURCE_RATE_TYPE_FLAT
   *
   * @param self::INVENTORY_SOURCE_RATE_TYPE_* $inventorySourceRateType
   */
  public function setInventorySourceRateType($inventorySourceRateType)
  {
    $this->inventorySourceRateType = $inventorySourceRateType;
  }
  /**
   * @return self::INVENTORY_SOURCE_RATE_TYPE_*
   */
  public function getInventorySourceRateType()
  {
    return $this->inventorySourceRateType;
  }
  /**
   * Output only. The amount that the buyer has committed to spending on the
   * inventory source up front. Only applicable for guaranteed inventory
   * sources.
   *
   * @param Money $minimumSpend
   */
  public function setMinimumSpend(Money $minimumSpend)
  {
    $this->minimumSpend = $minimumSpend;
  }
  /**
   * @return Money
   */
  public function getMinimumSpend()
  {
    return $this->minimumSpend;
  }
  /**
   * The rate for the inventory source.
   *
   * @param Money $rate
   */
  public function setRate(Money $rate)
  {
    $this->rate = $rate;
  }
  /**
   * @return Money
   */
  public function getRate()
  {
    return $this->rate;
  }
  /**
   * Required for guaranteed inventory sources. The number of impressions
   * guaranteed by the seller.
   *
   * @param string $unitsPurchased
   */
  public function setUnitsPurchased($unitsPurchased)
  {
    $this->unitsPurchased = $unitsPurchased;
  }
  /**
   * @return string
   */
  public function getUnitsPurchased()
  {
    return $this->unitsPurchased;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RateDetails::class, 'Google_Service_DisplayVideo_RateDetails');
