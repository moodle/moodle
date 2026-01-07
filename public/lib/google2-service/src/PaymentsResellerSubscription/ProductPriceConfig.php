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

namespace Google\Service\PaymentsResellerSubscription;

class ProductPriceConfig extends \Google\Model
{
  protected $amountType = Amount::class;
  protected $amountDataType = '';
  /**
   * Output only. 2-letter ISO region code where the product is available in.
   * Ex. "US".
   *
   * @var string
   */
  public $regionCode;

  /**
   * Output only. The price in the region.
   *
   * @param Amount $amount
   */
  public function setAmount(Amount $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return Amount
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * Output only. 2-letter ISO region code where the product is available in.
   * Ex. "US".
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
class_alias(ProductPriceConfig::class, 'Google_Service_PaymentsResellerSubscription_ProductPriceConfig');
