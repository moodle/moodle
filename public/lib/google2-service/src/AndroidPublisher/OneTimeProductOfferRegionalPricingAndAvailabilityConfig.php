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

class OneTimeProductOfferRegionalPricingAndAvailabilityConfig extends \Google\Model
{
  /**
   * Unspecified availability. Must not be used.
   */
  public const AVAILABILITY_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';
  /**
   * The offer is available to users.
   */
  public const AVAILABILITY_AVAILABLE = 'AVAILABLE';
  /**
   * The offer is no longer available to users. This value can only be used if
   * the availability was previously set as AVAILABLE.
   */
  public const AVAILABILITY_NO_LONGER_AVAILABLE = 'NO_LONGER_AVAILABLE';
  protected $absoluteDiscountType = Money::class;
  protected $absoluteDiscountDataType = '';
  /**
   * Required. The availability for this region.
   *
   * @var string
   */
  public $availability;
  protected $noOverrideType = OneTimeProductOfferNoPriceOverrideOptions::class;
  protected $noOverrideDataType = '';
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g., "US".
   *
   * @var string
   */
  public $regionCode;
  /**
   * The fraction of the purchase option price that the user pays for this
   * offer. For example, if the purchase option price for this region is $12,
   * then a 50% discount would correspond to a price of $6. The discount must be
   * specified as a fraction strictly larger than 0 and strictly smaller than 1.
   * The resulting price will be rounded to the nearest billable unit (e.g.
   * cents for USD). The relative discount is considered invalid if the
   * discounted price ends up being smaller than the minimum price allowed in
   * this region.
   *
   * @var 
   */
  public $relativeDiscount;

  /**
   * The absolute value of the discount that is subtracted from the purchase
   * option price. It should be between 0 and the purchase option price.
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
   * Required. The availability for this region.
   *
   * Accepted values: AVAILABILITY_UNSPECIFIED, AVAILABLE, NO_LONGER_AVAILABLE
   *
   * @param self::AVAILABILITY_* $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return self::AVAILABILITY_*
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * The price defined in the purchase option for this region will be used.
   *
   * @param OneTimeProductOfferNoPriceOverrideOptions $noOverride
   */
  public function setNoOverride(OneTimeProductOfferNoPriceOverrideOptions $noOverride)
  {
    $this->noOverride = $noOverride;
  }
  /**
   * @return OneTimeProductOfferNoPriceOverrideOptions
   */
  public function getNoOverride()
  {
    return $this->noOverride;
  }
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g., "US".
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
class_alias(OneTimeProductOfferRegionalPricingAndAvailabilityConfig::class, 'Google_Service_AndroidPublisher_OneTimeProductOfferRegionalPricingAndAvailabilityConfig');
