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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1RenewalSettings extends \Google\Model
{
  /**
   * Not used.
   */
  public const PAYMENT_OPTION_PAYMENT_OPTION_UNSPECIFIED = 'PAYMENT_OPTION_UNSPECIFIED';
  /**
   * Paid in yearly installments.
   */
  public const PAYMENT_OPTION_ANNUAL = 'ANNUAL';
  /**
   * Paid in monthly installments.
   */
  public const PAYMENT_OPTION_MONTHLY = 'MONTHLY';
  /**
   * Not used.
   */
  public const PAYMENT_PLAN_PAYMENT_PLAN_UNSPECIFIED = 'PAYMENT_PLAN_UNSPECIFIED';
  /**
   * Commitment.
   */
  public const PAYMENT_PLAN_COMMITMENT = 'COMMITMENT';
  /**
   * No commitment.
   */
  public const PAYMENT_PLAN_FLEXIBLE = 'FLEXIBLE';
  /**
   * Free.
   */
  public const PAYMENT_PLAN_FREE = 'FREE';
  /**
   * Trial.
   */
  public const PAYMENT_PLAN_TRIAL = 'TRIAL';
  /**
   * Price and ordering not available through API.
   */
  public const PAYMENT_PLAN_OFFLINE = 'OFFLINE';
  /**
   * If true, disables commitment-based offer on renewal and switches to
   * flexible or pay as you go. Deprecated: Use `payment_plan` instead.
   *
   * @deprecated
   * @var bool
   */
  public $disableCommitment;
  /**
   * If false, the plan will be completed at the end date.
   *
   * @var bool
   */
  public $enableRenewal;
  protected $paymentCycleType = GoogleCloudChannelV1alpha1Period::class;
  protected $paymentCycleDataType = '';
  /**
   * Set if enable_renewal=true. Deprecated: Use `payment_cycle` instead.
   *
   * @deprecated
   * @var string
   */
  public $paymentOption;
  /**
   * Describes how a reseller will be billed.
   *
   * @var string
   */
  public $paymentPlan;
  /**
   * If true and enable_renewal = true, the unit (for example seats or licenses)
   * will be set to the number of active units at renewal time.
   *
   * @var bool
   */
  public $resizeUnitCount;
  /**
   * Output only. The offer resource name that the entitlement will renew on at
   * the end date. Takes the form: accounts/{account_id}/offers/{offer_id}.
   *
   * @var string
   */
  public $scheduledRenewalOffer;

  /**
   * If true, disables commitment-based offer on renewal and switches to
   * flexible or pay as you go. Deprecated: Use `payment_plan` instead.
   *
   * @deprecated
   * @param bool $disableCommitment
   */
  public function setDisableCommitment($disableCommitment)
  {
    $this->disableCommitment = $disableCommitment;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDisableCommitment()
  {
    return $this->disableCommitment;
  }
  /**
   * If false, the plan will be completed at the end date.
   *
   * @param bool $enableRenewal
   */
  public function setEnableRenewal($enableRenewal)
  {
    $this->enableRenewal = $enableRenewal;
  }
  /**
   * @return bool
   */
  public function getEnableRenewal()
  {
    return $this->enableRenewal;
  }
  /**
   * Describes how frequently the reseller will be billed, such as once per
   * month.
   *
   * @param GoogleCloudChannelV1alpha1Period $paymentCycle
   */
  public function setPaymentCycle(GoogleCloudChannelV1alpha1Period $paymentCycle)
  {
    $this->paymentCycle = $paymentCycle;
  }
  /**
   * @return GoogleCloudChannelV1alpha1Period
   */
  public function getPaymentCycle()
  {
    return $this->paymentCycle;
  }
  /**
   * Set if enable_renewal=true. Deprecated: Use `payment_cycle` instead.
   *
   * Accepted values: PAYMENT_OPTION_UNSPECIFIED, ANNUAL, MONTHLY
   *
   * @deprecated
   * @param self::PAYMENT_OPTION_* $paymentOption
   */
  public function setPaymentOption($paymentOption)
  {
    $this->paymentOption = $paymentOption;
  }
  /**
   * @deprecated
   * @return self::PAYMENT_OPTION_*
   */
  public function getPaymentOption()
  {
    return $this->paymentOption;
  }
  /**
   * Describes how a reseller will be billed.
   *
   * Accepted values: PAYMENT_PLAN_UNSPECIFIED, COMMITMENT, FLEXIBLE, FREE,
   * TRIAL, OFFLINE
   *
   * @param self::PAYMENT_PLAN_* $paymentPlan
   */
  public function setPaymentPlan($paymentPlan)
  {
    $this->paymentPlan = $paymentPlan;
  }
  /**
   * @return self::PAYMENT_PLAN_*
   */
  public function getPaymentPlan()
  {
    return $this->paymentPlan;
  }
  /**
   * If true and enable_renewal = true, the unit (for example seats or licenses)
   * will be set to the number of active units at renewal time.
   *
   * @param bool $resizeUnitCount
   */
  public function setResizeUnitCount($resizeUnitCount)
  {
    $this->resizeUnitCount = $resizeUnitCount;
  }
  /**
   * @return bool
   */
  public function getResizeUnitCount()
  {
    return $this->resizeUnitCount;
  }
  /**
   * Output only. The offer resource name that the entitlement will renew on at
   * the end date. Takes the form: accounts/{account_id}/offers/{offer_id}.
   *
   * @param string $scheduledRenewalOffer
   */
  public function setScheduledRenewalOffer($scheduledRenewalOffer)
  {
    $this->scheduledRenewalOffer = $scheduledRenewalOffer;
  }
  /**
   * @return string
   */
  public function getScheduledRenewalOffer()
  {
    return $this->scheduledRenewalOffer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1RenewalSettings::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1RenewalSettings');
