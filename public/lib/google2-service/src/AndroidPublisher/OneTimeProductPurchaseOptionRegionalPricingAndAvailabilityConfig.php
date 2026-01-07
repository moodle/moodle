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

class OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig extends \Google\Model
{
  /**
   * Unspecified availability. Must not be used.
   */
  public const AVAILABILITY_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';
  /**
   * The purchase option is available to users.
   */
  public const AVAILABILITY_AVAILABLE = 'AVAILABLE';
  /**
   * The purchase option is no longer available to users. This value can only be
   * used if the availability was previously set as AVAILABLE.
   */
  public const AVAILABILITY_NO_LONGER_AVAILABLE = 'NO_LONGER_AVAILABLE';
  /**
   * The purchase option is initially unavailable, but made available via a
   * released pre-order offer.
   */
  public const AVAILABILITY_AVAILABLE_IF_RELEASED = 'AVAILABLE_IF_RELEASED';
  /**
   * The purchase option is unavailable but offers linked to it (i.e. Play
   * Points offer) are available.
   */
  public const AVAILABILITY_AVAILABLE_FOR_OFFERS_ONLY = 'AVAILABLE_FOR_OFFERS_ONLY';
  /**
   * The availability of the purchase option.
   *
   * @var string
   */
  public $availability;
  protected $priceType = Money::class;
  protected $priceDataType = '';
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g., "US".
   *
   * @var string
   */
  public $regionCode;

  /**
   * The availability of the purchase option.
   *
   * Accepted values: AVAILABILITY_UNSPECIFIED, AVAILABLE, NO_LONGER_AVAILABLE,
   * AVAILABLE_IF_RELEASED, AVAILABLE_FOR_OFFERS_ONLY
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
   * The price of the purchase option in the specified region. Must be set in
   * the currency that is linked to the specified region.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig::class, 'Google_Service_AndroidPublisher_OneTimeProductPurchaseOptionRegionalPricingAndAvailabilityConfig');
