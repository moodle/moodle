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

class RegionalPriceMigrationConfig extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const PRICE_INCREASE_TYPE_PRICE_INCREASE_TYPE_UNSPECIFIED = 'PRICE_INCREASE_TYPE_UNSPECIFIED';
  /**
   * Subscribers must accept the price increase or their subscription is
   * canceled.
   */
  public const PRICE_INCREASE_TYPE_PRICE_INCREASE_TYPE_OPT_IN = 'PRICE_INCREASE_TYPE_OPT_IN';
  /**
   * Subscribers are notified but do not have to accept the price increase. Opt-
   * out price increases not meeting regional, frequency, and amount limits will
   * proceed as opt-in price increase. The first opt-out price increase for each
   * app must be initiated in the Google Play Console.
   */
  public const PRICE_INCREASE_TYPE_PRICE_INCREASE_TYPE_OPT_OUT = 'PRICE_INCREASE_TYPE_OPT_OUT';
  /**
   * Required. Subscribers in all legacy price cohorts before this time will be
   * migrated to the current price. Subscribers in any newer price cohorts are
   * unaffected. Affected subscribers will receive one or more notifications
   * from Google Play about the price change. Price decreases occur at the
   * subscriber's next billing date. Price increases occur at the subscriber's
   * next billing date following a notification period that varies by region and
   * price increase type.
   *
   * @var string
   */
  public $oldestAllowedPriceVersionTime;
  /**
   * Optional. The requested type of price increase
   *
   * @var string
   */
  public $priceIncreaseType;
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g. "US".
   *
   * @var string
   */
  public $regionCode;

  /**
   * Required. Subscribers in all legacy price cohorts before this time will be
   * migrated to the current price. Subscribers in any newer price cohorts are
   * unaffected. Affected subscribers will receive one or more notifications
   * from Google Play about the price change. Price decreases occur at the
   * subscriber's next billing date. Price increases occur at the subscriber's
   * next billing date following a notification period that varies by region and
   * price increase type.
   *
   * @param string $oldestAllowedPriceVersionTime
   */
  public function setOldestAllowedPriceVersionTime($oldestAllowedPriceVersionTime)
  {
    $this->oldestAllowedPriceVersionTime = $oldestAllowedPriceVersionTime;
  }
  /**
   * @return string
   */
  public function getOldestAllowedPriceVersionTime()
  {
    return $this->oldestAllowedPriceVersionTime;
  }
  /**
   * Optional. The requested type of price increase
   *
   * Accepted values: PRICE_INCREASE_TYPE_UNSPECIFIED,
   * PRICE_INCREASE_TYPE_OPT_IN, PRICE_INCREASE_TYPE_OPT_OUT
   *
   * @param self::PRICE_INCREASE_TYPE_* $priceIncreaseType
   */
  public function setPriceIncreaseType($priceIncreaseType)
  {
    $this->priceIncreaseType = $priceIncreaseType;
  }
  /**
   * @return self::PRICE_INCREASE_TYPE_*
   */
  public function getPriceIncreaseType()
  {
    return $this->priceIncreaseType;
  }
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g. "US".
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
class_alias(RegionalPriceMigrationConfig::class, 'Google_Service_AndroidPublisher_RegionalPriceMigrationConfig');
