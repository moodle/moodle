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

namespace Google\Service\ShoppingContent;

class AccountTaxTaxRule extends \Google\Model
{
  /**
   * Country code in which tax is applicable.
   *
   * @var string
   */
  public $country;
  /**
   * Required. State (or province) is which the tax is applicable, described by
   * its location ID (also called criteria ID).
   *
   * @var string
   */
  public $locationId;
  /**
   * Explicit tax rate in percent, represented as a floating point number
   * without the percentage character. Must not be negative.
   *
   * @var string
   */
  public $ratePercent;
  /**
   * If true, shipping charges are also taxed.
   *
   * @var bool
   */
  public $shippingTaxed;
  /**
   * Whether the tax rate is taken from a global tax table or specified
   * explicitly.
   *
   * @var bool
   */
  public $useGlobalRate;

  /**
   * Country code in which tax is applicable.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * Required. State (or province) is which the tax is applicable, described by
   * its location ID (also called criteria ID).
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Explicit tax rate in percent, represented as a floating point number
   * without the percentage character. Must not be negative.
   *
   * @param string $ratePercent
   */
  public function setRatePercent($ratePercent)
  {
    $this->ratePercent = $ratePercent;
  }
  /**
   * @return string
   */
  public function getRatePercent()
  {
    return $this->ratePercent;
  }
  /**
   * If true, shipping charges are also taxed.
   *
   * @param bool $shippingTaxed
   */
  public function setShippingTaxed($shippingTaxed)
  {
    $this->shippingTaxed = $shippingTaxed;
  }
  /**
   * @return bool
   */
  public function getShippingTaxed()
  {
    return $this->shippingTaxed;
  }
  /**
   * Whether the tax rate is taken from a global tax table or specified
   * explicitly.
   *
   * @param bool $useGlobalRate
   */
  public function setUseGlobalRate($useGlobalRate)
  {
    $this->useGlobalRate = $useGlobalRate;
  }
  /**
   * @return bool
   */
  public function getUseGlobalRate()
  {
    return $this->useGlobalRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountTaxTaxRule::class, 'Google_Service_ShoppingContent_AccountTaxTaxRule');
