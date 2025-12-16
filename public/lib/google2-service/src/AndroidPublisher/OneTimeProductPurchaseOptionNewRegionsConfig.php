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

class OneTimeProductPurchaseOptionNewRegionsConfig extends \Google\Model
{
  /**
   * Unspecified availability. Must not be used.
   */
  public const AVAILABILITY_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';
  /**
   * The config will be used for any new regions Play may launch in the future.
   */
  public const AVAILABILITY_AVAILABLE = 'AVAILABLE';
  /**
   * The config is not available anymore and will not be used for any new
   * regions Play may launch in the future. This value can only be used if the
   * availability was previously set as AVAILABLE.
   */
  public const AVAILABILITY_NO_LONGER_AVAILABLE = 'NO_LONGER_AVAILABLE';
  /**
   * Required. The regional availability for the new regions config. When set to
   * AVAILABLE, the pricing information will be used for any new regions Play
   * may launch in the future.
   *
   * @var string
   */
  public $availability;
  protected $eurPriceType = Money::class;
  protected $eurPriceDataType = '';
  protected $usdPriceType = Money::class;
  protected $usdPriceDataType = '';

  /**
   * Required. The regional availability for the new regions config. When set to
   * AVAILABLE, the pricing information will be used for any new regions Play
   * may launch in the future.
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
   * Required. Price in EUR to use for any new regions Play may launch in.
   *
   * @param Money $eurPrice
   */
  public function setEurPrice(Money $eurPrice)
  {
    $this->eurPrice = $eurPrice;
  }
  /**
   * @return Money
   */
  public function getEurPrice()
  {
    return $this->eurPrice;
  }
  /**
   * Required. Price in USD to use for any new regions Play may launch in.
   *
   * @param Money $usdPrice
   */
  public function setUsdPrice(Money $usdPrice)
  {
    $this->usdPrice = $usdPrice;
  }
  /**
   * @return Money
   */
  public function getUsdPrice()
  {
    return $this->usdPrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductPurchaseOptionNewRegionsConfig::class, 'Google_Service_AndroidPublisher_OneTimeProductPurchaseOptionNewRegionsConfig');
