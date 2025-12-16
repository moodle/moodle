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

class OtherRegionsSubscriptionOfferPhaseConfig extends \Google\Model
{
  protected $absoluteDiscountsType = OtherRegionsSubscriptionOfferPhasePrices::class;
  protected $absoluteDiscountsDataType = '';
  protected $freeType = OtherRegionsSubscriptionOfferPhaseFreePriceOverride::class;
  protected $freeDataType = '';
  protected $otherRegionsPricesType = OtherRegionsSubscriptionOfferPhasePrices::class;
  protected $otherRegionsPricesDataType = '';
  /**
   * The fraction of the base plan price prorated over the phase duration that
   * the user pays for this offer phase. For example, if the base plan price for
   * this region is $12 for a period of 1 year, then a 50% discount for a phase
   * of a duration of 3 months would correspond to a price of $1.50. The
   * discount must be specified as a fraction strictly larger than 0 and
   * strictly smaller than 1. The resulting price will be rounded to the nearest
   * billable unit (e.g. cents for USD). The relative discount is considered
   * invalid if the discounted price ends up being smaller than the minimum
   * price allowed in any new locations Play may launch in.
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
   * than the minimum price allowed for any new locations Play may launch in.
   *
   * @param OtherRegionsSubscriptionOfferPhasePrices $absoluteDiscounts
   */
  public function setAbsoluteDiscounts(OtherRegionsSubscriptionOfferPhasePrices $absoluteDiscounts)
  {
    $this->absoluteDiscounts = $absoluteDiscounts;
  }
  /**
   * @return OtherRegionsSubscriptionOfferPhasePrices
   */
  public function getAbsoluteDiscounts()
  {
    return $this->absoluteDiscounts;
  }
  /**
   * Set to specify this offer is free to obtain.
   *
   * @param OtherRegionsSubscriptionOfferPhaseFreePriceOverride $free
   */
  public function setFree(OtherRegionsSubscriptionOfferPhaseFreePriceOverride $free)
  {
    $this->free = $free;
  }
  /**
   * @return OtherRegionsSubscriptionOfferPhaseFreePriceOverride
   */
  public function getFree()
  {
    return $this->free;
  }
  /**
   * The absolute price the user pays for this offer phase. The price must not
   * be smaller than the minimum price allowed for any new locations Play may
   * launch in.
   *
   * @param OtherRegionsSubscriptionOfferPhasePrices $otherRegionsPrices
   */
  public function setOtherRegionsPrices(OtherRegionsSubscriptionOfferPhasePrices $otherRegionsPrices)
  {
    $this->otherRegionsPrices = $otherRegionsPrices;
  }
  /**
   * @return OtherRegionsSubscriptionOfferPhasePrices
   */
  public function getOtherRegionsPrices()
  {
    return $this->otherRegionsPrices;
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
class_alias(OtherRegionsSubscriptionOfferPhaseConfig::class, 'Google_Service_AndroidPublisher_OtherRegionsSubscriptionOfferPhaseConfig');
