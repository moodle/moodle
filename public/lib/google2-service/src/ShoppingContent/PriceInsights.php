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

class PriceInsights extends \Google\Model
{
  /**
   * Effectiveness is unknown.
   */
  public const EFFECTIVENESS_EFFECTIVENESS_UNSPECIFIED = 'EFFECTIVENESS_UNSPECIFIED';
  /**
   * Effectiveness is low.
   */
  public const EFFECTIVENESS_LOW = 'LOW';
  /**
   * Effectiveness is medium.
   */
  public const EFFECTIVENESS_MEDIUM = 'MEDIUM';
  /**
   * Effectiveness is high.
   */
  public const EFFECTIVENESS_HIGH = 'HIGH';
  /**
   * The predicted effectiveness of applying the price suggestion, bucketed.
   *
   * @var string
   */
  public $effectiveness;
  /**
   * The predicted change in clicks as a fraction after introducing the
   * suggested price compared to current active price. For example, 0.05 is a 5%
   * predicted increase in clicks.
   *
   * @var 
   */
  public $predictedClicksChangeFraction;
  /**
   * The predicted change in conversions as a fraction after introducing the
   * suggested price compared to current active price. For example, 0.05 is a 5%
   * predicted increase in conversions).
   *
   * @var 
   */
  public $predictedConversionsChangeFraction;
  /**
   * *Deprecated*: This field is no longer supported and will start returning 0.
   * The predicted change in gross profit as a fraction after introducing the
   * suggested price compared to current active price. For example, 0.05 is a 5%
   * predicted increase in gross profit.
   *
   * @var 
   */
  public $predictedGrossProfitChangeFraction;
  /**
   * The predicted change in impressions as a fraction after introducing the
   * suggested price compared to current active price. For example, 0.05 is a 5%
   * predicted increase in impressions.
   *
   * @var 
   */
  public $predictedImpressionsChangeFraction;
  /**
   * *Deprecated*: This field is no longer supported and will start returning
   * USD for all requests. The predicted monthly gross profit change currency
   * (ISO 4217 code).
   *
   * @var string
   */
  public $predictedMonthlyGrossProfitChangeCurrencyCode;
  /**
   * *Deprecated*: This field is no longer supported and will start returning 0.
   * The predicted change in gross profit in micros (1 millionth of a standard
   * unit, 1 USD = 1000000 micros) after introducing the suggested price for a
   * month compared to current active price.
   *
   * @var string
   */
  public $predictedMonthlyGrossProfitChangeMicros;
  /**
   * The suggested price currency (ISO 4217 code).
   *
   * @var string
   */
  public $suggestedPriceCurrencyCode;
  /**
   * The latest suggested price in micros (1 millionth of a standard unit, 1 USD
   * = 1000000 micros) for the product.
   *
   * @var string
   */
  public $suggestedPriceMicros;

  /**
   * The predicted effectiveness of applying the price suggestion, bucketed.
   *
   * Accepted values: EFFECTIVENESS_UNSPECIFIED, LOW, MEDIUM, HIGH
   *
   * @param self::EFFECTIVENESS_* $effectiveness
   */
  public function setEffectiveness($effectiveness)
  {
    $this->effectiveness = $effectiveness;
  }
  /**
   * @return self::EFFECTIVENESS_*
   */
  public function getEffectiveness()
  {
    return $this->effectiveness;
  }
  public function setPredictedClicksChangeFraction($predictedClicksChangeFraction)
  {
    $this->predictedClicksChangeFraction = $predictedClicksChangeFraction;
  }
  public function getPredictedClicksChangeFraction()
  {
    return $this->predictedClicksChangeFraction;
  }
  public function setPredictedConversionsChangeFraction($predictedConversionsChangeFraction)
  {
    $this->predictedConversionsChangeFraction = $predictedConversionsChangeFraction;
  }
  public function getPredictedConversionsChangeFraction()
  {
    return $this->predictedConversionsChangeFraction;
  }
  public function setPredictedGrossProfitChangeFraction($predictedGrossProfitChangeFraction)
  {
    $this->predictedGrossProfitChangeFraction = $predictedGrossProfitChangeFraction;
  }
  public function getPredictedGrossProfitChangeFraction()
  {
    return $this->predictedGrossProfitChangeFraction;
  }
  public function setPredictedImpressionsChangeFraction($predictedImpressionsChangeFraction)
  {
    $this->predictedImpressionsChangeFraction = $predictedImpressionsChangeFraction;
  }
  public function getPredictedImpressionsChangeFraction()
  {
    return $this->predictedImpressionsChangeFraction;
  }
  /**
   * *Deprecated*: This field is no longer supported and will start returning
   * USD for all requests. The predicted monthly gross profit change currency
   * (ISO 4217 code).
   *
   * @param string $predictedMonthlyGrossProfitChangeCurrencyCode
   */
  public function setPredictedMonthlyGrossProfitChangeCurrencyCode($predictedMonthlyGrossProfitChangeCurrencyCode)
  {
    $this->predictedMonthlyGrossProfitChangeCurrencyCode = $predictedMonthlyGrossProfitChangeCurrencyCode;
  }
  /**
   * @return string
   */
  public function getPredictedMonthlyGrossProfitChangeCurrencyCode()
  {
    return $this->predictedMonthlyGrossProfitChangeCurrencyCode;
  }
  /**
   * *Deprecated*: This field is no longer supported and will start returning 0.
   * The predicted change in gross profit in micros (1 millionth of a standard
   * unit, 1 USD = 1000000 micros) after introducing the suggested price for a
   * month compared to current active price.
   *
   * @param string $predictedMonthlyGrossProfitChangeMicros
   */
  public function setPredictedMonthlyGrossProfitChangeMicros($predictedMonthlyGrossProfitChangeMicros)
  {
    $this->predictedMonthlyGrossProfitChangeMicros = $predictedMonthlyGrossProfitChangeMicros;
  }
  /**
   * @return string
   */
  public function getPredictedMonthlyGrossProfitChangeMicros()
  {
    return $this->predictedMonthlyGrossProfitChangeMicros;
  }
  /**
   * The suggested price currency (ISO 4217 code).
   *
   * @param string $suggestedPriceCurrencyCode
   */
  public function setSuggestedPriceCurrencyCode($suggestedPriceCurrencyCode)
  {
    $this->suggestedPriceCurrencyCode = $suggestedPriceCurrencyCode;
  }
  /**
   * @return string
   */
  public function getSuggestedPriceCurrencyCode()
  {
    return $this->suggestedPriceCurrencyCode;
  }
  /**
   * The latest suggested price in micros (1 millionth of a standard unit, 1 USD
   * = 1000000 micros) for the product.
   *
   * @param string $suggestedPriceMicros
   */
  public function setSuggestedPriceMicros($suggestedPriceMicros)
  {
    $this->suggestedPriceMicros = $suggestedPriceMicros;
  }
  /**
   * @return string
   */
  public function getSuggestedPriceMicros()
  {
    return $this->suggestedPriceMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PriceInsights::class, 'Google_Service_ShoppingContent_PriceInsights');
