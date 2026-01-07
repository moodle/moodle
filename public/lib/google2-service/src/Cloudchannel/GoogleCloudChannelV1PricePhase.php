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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1PricePhase extends \Google\Collection
{
  /**
   * Not used.
   */
  public const PERIOD_TYPE_PERIOD_TYPE_UNSPECIFIED = 'PERIOD_TYPE_UNSPECIFIED';
  /**
   * Day.
   */
  public const PERIOD_TYPE_DAY = 'DAY';
  /**
   * Month.
   */
  public const PERIOD_TYPE_MONTH = 'MONTH';
  /**
   * Year.
   */
  public const PERIOD_TYPE_YEAR = 'YEAR';
  protected $collection_key = 'priceTiers';
  /**
   * Defines first period for the phase.
   *
   * @var int
   */
  public $firstPeriod;
  /**
   * Defines first period for the phase.
   *
   * @var int
   */
  public $lastPeriod;
  /**
   * Defines the phase period type.
   *
   * @var string
   */
  public $periodType;
  protected $priceType = GoogleCloudChannelV1Price::class;
  protected $priceDataType = '';
  protected $priceTiersType = GoogleCloudChannelV1PriceTier::class;
  protected $priceTiersDataType = 'array';

  /**
   * Defines first period for the phase.
   *
   * @param int $firstPeriod
   */
  public function setFirstPeriod($firstPeriod)
  {
    $this->firstPeriod = $firstPeriod;
  }
  /**
   * @return int
   */
  public function getFirstPeriod()
  {
    return $this->firstPeriod;
  }
  /**
   * Defines first period for the phase.
   *
   * @param int $lastPeriod
   */
  public function setLastPeriod($lastPeriod)
  {
    $this->lastPeriod = $lastPeriod;
  }
  /**
   * @return int
   */
  public function getLastPeriod()
  {
    return $this->lastPeriod;
  }
  /**
   * Defines the phase period type.
   *
   * Accepted values: PERIOD_TYPE_UNSPECIFIED, DAY, MONTH, YEAR
   *
   * @param self::PERIOD_TYPE_* $periodType
   */
  public function setPeriodType($periodType)
  {
    $this->periodType = $periodType;
  }
  /**
   * @return self::PERIOD_TYPE_*
   */
  public function getPeriodType()
  {
    return $this->periodType;
  }
  /**
   * Price of the phase. Present if there are no price tiers.
   *
   * @param GoogleCloudChannelV1Price $price
   */
  public function setPrice(GoogleCloudChannelV1Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return GoogleCloudChannelV1Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * Price by the resource tiers.
   *
   * @param GoogleCloudChannelV1PriceTier[] $priceTiers
   */
  public function setPriceTiers($priceTiers)
  {
    $this->priceTiers = $priceTiers;
  }
  /**
   * @return GoogleCloudChannelV1PriceTier[]
   */
  public function getPriceTiers()
  {
    return $this->priceTiers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1PricePhase::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1PricePhase');
