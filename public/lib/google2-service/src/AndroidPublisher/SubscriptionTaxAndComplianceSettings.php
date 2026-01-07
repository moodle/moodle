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

class SubscriptionTaxAndComplianceSettings extends \Google\Model
{
  public const EEA_WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED = 'WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED';
  public const EEA_WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_DIGITAL_CONTENT = 'WITHDRAWAL_RIGHT_DIGITAL_CONTENT';
  public const EEA_WITHDRAWAL_RIGHT_TYPE_WITHDRAWAL_RIGHT_SERVICE = 'WITHDRAWAL_RIGHT_SERVICE';
  /**
   * Digital content or service classification for products distributed to users
   * in the European Economic Area (EEA). The withdrawal regime under EEA
   * consumer laws depends on this classification. Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/10463498) for more information.
   *
   * @var string
   */
  public $eeaWithdrawalRightType;
  /**
   * Whether this subscription is declared as a product representing a tokenized
   * digital asset.
   *
   * @var bool
   */
  public $isTokenizedDigitalAsset;
  /**
   * Product tax category code to assign to the subscription. Product tax
   * category determines the transaction tax rates applied to the subscription.
   * Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/16408159) for more information.
   *
   * @var string
   */
  public $productTaxCategoryCode;
  protected $taxRateInfoByRegionCodeType = RegionalTaxRateInfo::class;
  protected $taxRateInfoByRegionCodeDataType = 'map';

  /**
   * Digital content or service classification for products distributed to users
   * in the European Economic Area (EEA). The withdrawal regime under EEA
   * consumer laws depends on this classification. Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/10463498) for more information.
   *
   * Accepted values: WITHDRAWAL_RIGHT_TYPE_UNSPECIFIED,
   * WITHDRAWAL_RIGHT_DIGITAL_CONTENT, WITHDRAWAL_RIGHT_SERVICE
   *
   * @param self::EEA_WITHDRAWAL_RIGHT_TYPE_* $eeaWithdrawalRightType
   */
  public function setEeaWithdrawalRightType($eeaWithdrawalRightType)
  {
    $this->eeaWithdrawalRightType = $eeaWithdrawalRightType;
  }
  /**
   * @return self::EEA_WITHDRAWAL_RIGHT_TYPE_*
   */
  public function getEeaWithdrawalRightType()
  {
    return $this->eeaWithdrawalRightType;
  }
  /**
   * Whether this subscription is declared as a product representing a tokenized
   * digital asset.
   *
   * @param bool $isTokenizedDigitalAsset
   */
  public function setIsTokenizedDigitalAsset($isTokenizedDigitalAsset)
  {
    $this->isTokenizedDigitalAsset = $isTokenizedDigitalAsset;
  }
  /**
   * @return bool
   */
  public function getIsTokenizedDigitalAsset()
  {
    return $this->isTokenizedDigitalAsset;
  }
  /**
   * Product tax category code to assign to the subscription. Product tax
   * category determines the transaction tax rates applied to the subscription.
   * Refer to the [Help Center
   * article](https://support.google.com/googleplay/android-
   * developer/answer/16408159) for more information.
   *
   * @param string $productTaxCategoryCode
   */
  public function setProductTaxCategoryCode($productTaxCategoryCode)
  {
    $this->productTaxCategoryCode = $productTaxCategoryCode;
  }
  /**
   * @return string
   */
  public function getProductTaxCategoryCode()
  {
    return $this->productTaxCategoryCode;
  }
  /**
   * A mapping from region code to tax rate details. The keys are region codes
   * as defined by Unicode's "CLDR".
   *
   * @param RegionalTaxRateInfo[] $taxRateInfoByRegionCode
   */
  public function setTaxRateInfoByRegionCode($taxRateInfoByRegionCode)
  {
    $this->taxRateInfoByRegionCode = $taxRateInfoByRegionCode;
  }
  /**
   * @return RegionalTaxRateInfo[]
   */
  public function getTaxRateInfoByRegionCode()
  {
    return $this->taxRateInfoByRegionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionTaxAndComplianceSettings::class, 'Google_Service_AndroidPublisher_SubscriptionTaxAndComplianceSettings');
