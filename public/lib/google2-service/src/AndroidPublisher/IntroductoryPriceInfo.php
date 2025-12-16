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

class IntroductoryPriceInfo extends \Google\Model
{
  /**
   * Introductory price of the subscription, not including tax. The currency is
   * the same as price_currency_code. Price is expressed in micro-units, where
   * 1,000,000 micro-units represents one unit of the currency. For example, if
   * the subscription price is €1.99, price_amount_micros is 1990000.
   *
   * @var string
   */
  public $introductoryPriceAmountMicros;
  /**
   * ISO 4217 currency code for the introductory subscription price. For
   * example, if the price is specified in British pounds sterling,
   * price_currency_code is "GBP".
   *
   * @var string
   */
  public $introductoryPriceCurrencyCode;
  /**
   * The number of billing period to offer introductory pricing.
   *
   * @var int
   */
  public $introductoryPriceCycles;
  /**
   * Introductory price period, specified in ISO 8601 format. Common values are
   * (but not limited to) "P1W" (one week), "P1M" (one month), "P3M" (three
   * months), "P6M" (six months), and "P1Y" (one year).
   *
   * @var string
   */
  public $introductoryPricePeriod;

  /**
   * Introductory price of the subscription, not including tax. The currency is
   * the same as price_currency_code. Price is expressed in micro-units, where
   * 1,000,000 micro-units represents one unit of the currency. For example, if
   * the subscription price is €1.99, price_amount_micros is 1990000.
   *
   * @param string $introductoryPriceAmountMicros
   */
  public function setIntroductoryPriceAmountMicros($introductoryPriceAmountMicros)
  {
    $this->introductoryPriceAmountMicros = $introductoryPriceAmountMicros;
  }
  /**
   * @return string
   */
  public function getIntroductoryPriceAmountMicros()
  {
    return $this->introductoryPriceAmountMicros;
  }
  /**
   * ISO 4217 currency code for the introductory subscription price. For
   * example, if the price is specified in British pounds sterling,
   * price_currency_code is "GBP".
   *
   * @param string $introductoryPriceCurrencyCode
   */
  public function setIntroductoryPriceCurrencyCode($introductoryPriceCurrencyCode)
  {
    $this->introductoryPriceCurrencyCode = $introductoryPriceCurrencyCode;
  }
  /**
   * @return string
   */
  public function getIntroductoryPriceCurrencyCode()
  {
    return $this->introductoryPriceCurrencyCode;
  }
  /**
   * The number of billing period to offer introductory pricing.
   *
   * @param int $introductoryPriceCycles
   */
  public function setIntroductoryPriceCycles($introductoryPriceCycles)
  {
    $this->introductoryPriceCycles = $introductoryPriceCycles;
  }
  /**
   * @return int
   */
  public function getIntroductoryPriceCycles()
  {
    return $this->introductoryPriceCycles;
  }
  /**
   * Introductory price period, specified in ISO 8601 format. Common values are
   * (but not limited to) "P1W" (one week), "P1M" (one month), "P3M" (three
   * months), "P6M" (six months), and "P1Y" (one year).
   *
   * @param string $introductoryPricePeriod
   */
  public function setIntroductoryPricePeriod($introductoryPricePeriod)
  {
    $this->introductoryPricePeriod = $introductoryPricePeriod;
  }
  /**
   * @return string
   */
  public function getIntroductoryPricePeriod()
  {
    return $this->introductoryPricePeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IntroductoryPriceInfo::class, 'Google_Service_AndroidPublisher_IntroductoryPriceInfo');
