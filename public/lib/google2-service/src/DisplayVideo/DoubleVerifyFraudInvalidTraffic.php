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

namespace Google\Service\DisplayVideo;

class DoubleVerifyFraudInvalidTraffic extends \Google\Model
{
  /**
   * This enum is only a placeholder and it doesn't specify any fraud and
   * invalid traffic options.
   */
  public const AVOIDED_FRAUD_OPTION_FRAUD_UNSPECIFIED = 'FRAUD_UNSPECIFIED';
  /**
   * 100% Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_100 = 'AD_IMPRESSION_FRAUD_100';
  /**
   * 50% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_50 = 'AD_IMPRESSION_FRAUD_50';
  /**
   * 25% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_25 = 'AD_IMPRESSION_FRAUD_25';
  /**
   * 10% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_10 = 'AD_IMPRESSION_FRAUD_10';
  /**
   * 8% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_8 = 'AD_IMPRESSION_FRAUD_8';
  /**
   * 6% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_6 = 'AD_IMPRESSION_FRAUD_6';
  /**
   * 4% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_4 = 'AD_IMPRESSION_FRAUD_4';
  /**
   * 2% or Higher Fraud & IVT.
   */
  public const AVOIDED_FRAUD_OPTION_AD_IMPRESSION_FRAUD_2 = 'AD_IMPRESSION_FRAUD_2';
  /**
   * Insufficient Historical Fraud & IVT Stats.
   *
   * @var bool
   */
  public $avoidInsufficientOption;
  /**
   * Avoid Sites and Apps with historical Fraud & IVT.
   *
   * @var string
   */
  public $avoidedFraudOption;

  /**
   * Insufficient Historical Fraud & IVT Stats.
   *
   * @param bool $avoidInsufficientOption
   */
  public function setAvoidInsufficientOption($avoidInsufficientOption)
  {
    $this->avoidInsufficientOption = $avoidInsufficientOption;
  }
  /**
   * @return bool
   */
  public function getAvoidInsufficientOption()
  {
    return $this->avoidInsufficientOption;
  }
  /**
   * Avoid Sites and Apps with historical Fraud & IVT.
   *
   * Accepted values: FRAUD_UNSPECIFIED, AD_IMPRESSION_FRAUD_100,
   * AD_IMPRESSION_FRAUD_50, AD_IMPRESSION_FRAUD_25, AD_IMPRESSION_FRAUD_10,
   * AD_IMPRESSION_FRAUD_8, AD_IMPRESSION_FRAUD_6, AD_IMPRESSION_FRAUD_4,
   * AD_IMPRESSION_FRAUD_2
   *
   * @param self::AVOIDED_FRAUD_OPTION_* $avoidedFraudOption
   */
  public function setAvoidedFraudOption($avoidedFraudOption)
  {
    $this->avoidedFraudOption = $avoidedFraudOption;
  }
  /**
   * @return self::AVOIDED_FRAUD_OPTION_*
   */
  public function getAvoidedFraudOption()
  {
    return $this->avoidedFraudOption;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DoubleVerifyFraudInvalidTraffic::class, 'Google_Service_DisplayVideo_DoubleVerifyFraudInvalidTraffic');
