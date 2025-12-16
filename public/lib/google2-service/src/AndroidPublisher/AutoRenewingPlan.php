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

class AutoRenewingPlan extends \Google\Model
{
  /**
   * If the subscription is currently set to auto-renew, e.g. the user has not
   * canceled the subscription
   *
   * @var bool
   */
  public $autoRenewEnabled;
  protected $installmentDetailsType = InstallmentPlan::class;
  protected $installmentDetailsDataType = '';
  protected $priceChangeDetailsType = SubscriptionItemPriceChangeDetails::class;
  protected $priceChangeDetailsDataType = '';
  protected $priceStepUpConsentDetailsType = PriceStepUpConsentDetails::class;
  protected $priceStepUpConsentDetailsDataType = '';
  protected $recurringPriceType = Money::class;
  protected $recurringPriceDataType = '';

  /**
   * If the subscription is currently set to auto-renew, e.g. the user has not
   * canceled the subscription
   *
   * @param bool $autoRenewEnabled
   */
  public function setAutoRenewEnabled($autoRenewEnabled)
  {
    $this->autoRenewEnabled = $autoRenewEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoRenewEnabled()
  {
    return $this->autoRenewEnabled;
  }
  /**
   * The installment plan commitment and state related info for the auto
   * renewing plan.
   *
   * @param InstallmentPlan $installmentDetails
   */
  public function setInstallmentDetails(InstallmentPlan $installmentDetails)
  {
    $this->installmentDetails = $installmentDetails;
  }
  /**
   * @return InstallmentPlan
   */
  public function getInstallmentDetails()
  {
    return $this->installmentDetails;
  }
  /**
   * The information of the last price change for the item since subscription
   * signup.
   *
   * @param SubscriptionItemPriceChangeDetails $priceChangeDetails
   */
  public function setPriceChangeDetails(SubscriptionItemPriceChangeDetails $priceChangeDetails)
  {
    $this->priceChangeDetails = $priceChangeDetails;
  }
  /**
   * @return SubscriptionItemPriceChangeDetails
   */
  public function getPriceChangeDetails()
  {
    return $this->priceChangeDetails;
  }
  /**
   * The information of the latest price step-up consent.
   *
   * @param PriceStepUpConsentDetails $priceStepUpConsentDetails
   */
  public function setPriceStepUpConsentDetails(PriceStepUpConsentDetails $priceStepUpConsentDetails)
  {
    $this->priceStepUpConsentDetails = $priceStepUpConsentDetails;
  }
  /**
   * @return PriceStepUpConsentDetails
   */
  public function getPriceStepUpConsentDetails()
  {
    return $this->priceStepUpConsentDetails;
  }
  /**
   * The current recurring price of the auto renewing plan. Note that the price
   * does not take into account discounts and does not include taxes for tax-
   * exclusive pricing, please call orders.get API instead if transaction
   * details are needed.
   *
   * @param Money $recurringPrice
   */
  public function setRecurringPrice(Money $recurringPrice)
  {
    $this->recurringPrice = $recurringPrice;
  }
  /**
   * @return Money
   */
  public function getRecurringPrice()
  {
    return $this->recurringPrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoRenewingPlan::class, 'Google_Service_AndroidPublisher_AutoRenewingPlan');
