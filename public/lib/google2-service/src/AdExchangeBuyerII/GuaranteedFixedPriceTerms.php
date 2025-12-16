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

namespace Google\Service\AdExchangeBuyerII;

class GuaranteedFixedPriceTerms extends \Google\Collection
{
  /**
   * An unspecified reservation type.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_UNSPECIFIED = 'RESERVATION_TYPE_UNSPECIFIED';
  /**
   * Non-sponsorship deal.
   */
  public const RESERVATION_TYPE_STANDARD = 'STANDARD';
  /**
   * Sponsorship deals don't have impression goal (guaranteed_looks) and they
   * are served based on the flight dates. For CPM Sponsorship deals,
   * impression_cap is the lifetime impression limit.
   */
  public const RESERVATION_TYPE_SPONSORSHIP = 'SPONSORSHIP';
  protected $collection_key = 'fixedPrices';
  protected $fixedPricesType = PricePerBuyer::class;
  protected $fixedPricesDataType = 'array';
  /**
   * Guaranteed impressions as a percentage. This is the percentage of
   * guaranteed looks that the buyer is guaranteeing to buy.
   *
   * @var string
   */
  public $guaranteedImpressions;
  /**
   * Count of guaranteed looks. Required for deal, optional for product. For CPD
   * deals, buyer changes to guaranteed_looks will be ignored.
   *
   * @var string
   */
  public $guaranteedLooks;
  /**
   * The lifetime impression cap for CPM sponsorship deals. The deal will stop
   * serving when the cap is reached.
   *
   * @var string
   */
  public $impressionCap;
  /**
   * Daily minimum looks for CPD deal types. For CPD deals, buyer should
   * negotiate on this field instead of guaranteed_looks.
   *
   * @var string
   */
  public $minimumDailyLooks;
  /**
   * For sponsorship deals, this is the percentage of the seller's eligible
   * impressions that the deal will serve until the cap is reached.
   *
   * @var string
   */
  public $percentShareOfVoice;
  /**
   * The reservation type for a Programmatic Guaranteed deal. This indicates
   * whether the number of impressions is fixed, or a percent of available
   * impressions. If not specified, the default reservation type is STANDARD.
   *
   * @var string
   */
  public $reservationType;

  /**
   * Fixed price for the specified buyer.
   *
   * @param PricePerBuyer[] $fixedPrices
   */
  public function setFixedPrices($fixedPrices)
  {
    $this->fixedPrices = $fixedPrices;
  }
  /**
   * @return PricePerBuyer[]
   */
  public function getFixedPrices()
  {
    return $this->fixedPrices;
  }
  /**
   * Guaranteed impressions as a percentage. This is the percentage of
   * guaranteed looks that the buyer is guaranteeing to buy.
   *
   * @param string $guaranteedImpressions
   */
  public function setGuaranteedImpressions($guaranteedImpressions)
  {
    $this->guaranteedImpressions = $guaranteedImpressions;
  }
  /**
   * @return string
   */
  public function getGuaranteedImpressions()
  {
    return $this->guaranteedImpressions;
  }
  /**
   * Count of guaranteed looks. Required for deal, optional for product. For CPD
   * deals, buyer changes to guaranteed_looks will be ignored.
   *
   * @param string $guaranteedLooks
   */
  public function setGuaranteedLooks($guaranteedLooks)
  {
    $this->guaranteedLooks = $guaranteedLooks;
  }
  /**
   * @return string
   */
  public function getGuaranteedLooks()
  {
    return $this->guaranteedLooks;
  }
  /**
   * The lifetime impression cap for CPM sponsorship deals. The deal will stop
   * serving when the cap is reached.
   *
   * @param string $impressionCap
   */
  public function setImpressionCap($impressionCap)
  {
    $this->impressionCap = $impressionCap;
  }
  /**
   * @return string
   */
  public function getImpressionCap()
  {
    return $this->impressionCap;
  }
  /**
   * Daily minimum looks for CPD deal types. For CPD deals, buyer should
   * negotiate on this field instead of guaranteed_looks.
   *
   * @param string $minimumDailyLooks
   */
  public function setMinimumDailyLooks($minimumDailyLooks)
  {
    $this->minimumDailyLooks = $minimumDailyLooks;
  }
  /**
   * @return string
   */
  public function getMinimumDailyLooks()
  {
    return $this->minimumDailyLooks;
  }
  /**
   * For sponsorship deals, this is the percentage of the seller's eligible
   * impressions that the deal will serve until the cap is reached.
   *
   * @param string $percentShareOfVoice
   */
  public function setPercentShareOfVoice($percentShareOfVoice)
  {
    $this->percentShareOfVoice = $percentShareOfVoice;
  }
  /**
   * @return string
   */
  public function getPercentShareOfVoice()
  {
    return $this->percentShareOfVoice;
  }
  /**
   * The reservation type for a Programmatic Guaranteed deal. This indicates
   * whether the number of impressions is fixed, or a percent of available
   * impressions. If not specified, the default reservation type is STANDARD.
   *
   * Accepted values: RESERVATION_TYPE_UNSPECIFIED, STANDARD, SPONSORSHIP
   *
   * @param self::RESERVATION_TYPE_* $reservationType
   */
  public function setReservationType($reservationType)
  {
    $this->reservationType = $reservationType;
  }
  /**
   * @return self::RESERVATION_TYPE_*
   */
  public function getReservationType()
  {
    return $this->reservationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuaranteedFixedPriceTerms::class, 'Google_Service_AdExchangeBuyerII_GuaranteedFixedPriceTerms');
