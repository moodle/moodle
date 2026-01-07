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

class GoogleCloudChannelV1RenewalSettings extends \Google\Model
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
   * If false, the plan will be completed at the end date.
   *
   * @var bool
   */
  public $enableRenewal;
  protected $paymentCycleType = GoogleCloudChannelV1Period::class;
  protected $paymentCycleDataType = '';
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1RenewalSettings::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1RenewalSettings');
