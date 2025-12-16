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

class GoogleCloudChannelV1Plan extends \Google\Model
{
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
   * Not used.
   */
  public const PAYMENT_TYPE_PAYMENT_TYPE_UNSPECIFIED = 'PAYMENT_TYPE_UNSPECIFIED';
  /**
   * Prepay. Amount has to be paid before service is rendered.
   */
  public const PAYMENT_TYPE_PREPAY = 'PREPAY';
  /**
   * Postpay. Reseller is charged at the end of the Payment cycle.
   */
  public const PAYMENT_TYPE_POSTPAY = 'POSTPAY';
  /**
   * Reseller Billing account to charge after an offer transaction. Only present
   * for Google Cloud offers.
   *
   * @var string
   */
  public $billingAccount;
  protected $paymentCycleType = GoogleCloudChannelV1Period::class;
  protected $paymentCycleDataType = '';
  /**
   * Describes how a reseller will be billed.
   *
   * @var string
   */
  public $paymentPlan;
  /**
   * Specifies when the payment needs to happen.
   *
   * @var string
   */
  public $paymentType;
  protected $trialPeriodType = GoogleCloudChannelV1Period::class;
  protected $trialPeriodDataType = '';

  /**
   * Reseller Billing account to charge after an offer transaction. Only present
   * for Google Cloud offers.
   *
   * @param string $billingAccount
   */
  public function setBillingAccount($billingAccount)
  {
    $this->billingAccount = $billingAccount;
  }
  /**
   * @return string
   */
  public function getBillingAccount()
  {
    return $this->billingAccount;
  }
  /**
   * Describes how frequently the reseller will be billed, such as once per
   * month.
   *
   * @param GoogleCloudChannelV1Period $paymentCycle
   */
  public function setPaymentCycle(GoogleCloudChannelV1Period $paymentCycle)
  {
    $this->paymentCycle = $paymentCycle;
  }
  /**
   * @return GoogleCloudChannelV1Period
   */
  public function getPaymentCycle()
  {
    return $this->paymentCycle;
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
   * Specifies when the payment needs to happen.
   *
   * Accepted values: PAYMENT_TYPE_UNSPECIFIED, PREPAY, POSTPAY
   *
   * @param self::PAYMENT_TYPE_* $paymentType
   */
  public function setPaymentType($paymentType)
  {
    $this->paymentType = $paymentType;
  }
  /**
   * @return self::PAYMENT_TYPE_*
   */
  public function getPaymentType()
  {
    return $this->paymentType;
  }
  /**
   * Present for Offers with a trial period. For trial-only Offers, a paid
   * service needs to start before the trial period ends for continued service.
   * For Regular Offers with a trial period, the regular pricing goes into
   * effect when trial period ends, or if paid service is started before the end
   * of the trial period.
   *
   * @param GoogleCloudChannelV1Period $trialPeriod
   */
  public function setTrialPeriod(GoogleCloudChannelV1Period $trialPeriod)
  {
    $this->trialPeriod = $trialPeriod;
  }
  /**
   * @return GoogleCloudChannelV1Period
   */
  public function getTrialPeriod()
  {
    return $this->trialPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Plan::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Plan');
