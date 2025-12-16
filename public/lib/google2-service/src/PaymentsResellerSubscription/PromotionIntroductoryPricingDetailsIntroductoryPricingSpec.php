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

class PromotionIntroductoryPricingDetailsIntroductoryPricingSpec extends \Google\Model
{
  protected $discountAmountType = Amount::class;
  protected $discountAmountDataType = '';
  /**
   * Output only. The discount percentage in micros. For example, 50,000
   * represents 5%.
   *
   * @var string
   */
  public $discountRatioMicros;
  /**
   * Output only. The duration of an introductory offer in billing cycles.
   *
   * @var int
   */
  public $recurrenceCount;
  /**
   * Output only. 2-letter ISO region code where the product is available in.
   * Ex. "US".
   *
   * @var string
   */
  public $regionCode;

  /**
   * Output only. The discount amount. The value is positive.
   *
   * @param Amount $discountAmount
   */
  public function setDiscountAmount(Amount $discountAmount)
  {
    $this->discountAmount = $discountAmount;
  }
  /**
   * @return Amount
   */
  public function getDiscountAmount()
  {
    return $this->discountAmount;
  }
  /**
   * Output only. The discount percentage in micros. For example, 50,000
   * represents 5%.
   *
   * @param string $discountRatioMicros
   */
  public function setDiscountRatioMicros($discountRatioMicros)
  {
    $this->discountRatioMicros = $discountRatioMicros;
  }
  /**
   * @return string
   */
  public function getDiscountRatioMicros()
  {
    return $this->discountRatioMicros;
  }
  /**
   * Output only. The duration of an introductory offer in billing cycles.
   *
   * @param int $recurrenceCount
   */
  public function setRecurrenceCount($recurrenceCount)
  {
    $this->recurrenceCount = $recurrenceCount;
  }
  /**
   * @return int
   */
  public function getRecurrenceCount()
  {
    return $this->recurrenceCount;
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
class_alias(PromotionIntroductoryPricingDetailsIntroductoryPricingSpec::class, 'Google_Service_PaymentsResellerSubscription_PromotionIntroductoryPricingDetailsIntroductoryPricingSpec');
