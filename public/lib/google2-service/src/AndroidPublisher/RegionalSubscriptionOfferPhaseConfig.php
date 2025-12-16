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

namespace Google\Service\AndroidPublisher;

class RegionalSubscriptionOfferPhaseConfig extends \Google\Model
{
  protected $absoluteDiscountType = Money::class;
  protected $absoluteDiscountDataType = '';
  protected $freeType = RegionalSubscriptionOfferPhaseFreePriceOverride::class;
  protected $freeDataType = '';
  protected $priceType = Money::class;
  protected $priceDataType = '';
  /**
   * Required. Immutable. The region to which this config applies.
   *
   * @var string
   */
  public $regionCode;
  /**
   * The fraction of the base plan price prorated over the phase duration that
   * the user pays for this offer phase. For example, if the base plan price for
   * this region is $12 for a period of 1 year, then a 50% discount for a phase
   * of a duration of 3 months would correspond to a price of $1.50. The
   * discount must be specified as a fraction strictly larger than 0 and
   * strictly smaller than 1. The resulting price will be rounded to the nearest
   * billable unit (e.g. cents for USD). The relative discount is considered
   * invalid if the discounted price ends up being smaller than the minimum
   * price allowed in this region.
   *
   * @var 
   */
  public $relativeDiscount;

  /**
   * The absolute amount of money subtracted from the base plan price prorated
   * over the phase duration that the user pays for this offer phase. For
   * example, if the base plan price for this region is $12 for a period of 1
   * year, then a $1 absolute discount for a phase of a duration of 3 months
   * would correspond to a price of $2. The resulting price may not be smaller
   * than the minimum price allowed for this region.
   *
   * @param Money $absoluteDiscount
   */
  public function setAbsoluteDiscount(Money $absoluteDiscount)
  {
    $this->absoluteDiscount = $absoluteDiscount;
  }
  /**
   * @return Money
   */
  public function getAbsoluteDiscount()
  {
    return $this->absoluteDiscount;
  }
  /**
   * Set to specify this offer is free to obtain.
   *
   * @param RegionalSubscriptionOfferPhaseFreePriceOverride $free
   */
  public function setFree(RegionalSubscriptionOfferPhaseFreePriceOverride $free)
  {
    $this->free = $free;
  }
  /**
   * @return RegionalSubscriptionOfferPhaseFreePriceOverride
   */
  public function getFree()
  {
    return $this->free;
  }
  /**
   * The absolute price the user pays for this offer phase. The price must not
   * be smaller than the minimum price allowed for this region.
   *
   * @param Money $price
   */
  public function setPrice(Money $price)
  {
    $this->price = $price;
  }
  /**
   * @return Money
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * Required. Immutable. The region to which this config applies.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  public function setRelativeDiscount($relativeDiscount)
  {
    $this->relativeDiscount = $relativeDiscount;
  }
  public function getRelativeDiscount()
  {
    return $this->relativeDiscount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionalSubscriptionOfferPhaseConfig::class, 'Google_Service_AndroidPublisher_RegionalSubscriptionOfferPhaseConfig');
