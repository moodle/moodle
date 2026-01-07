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

class SubscriptionUpgradeDowngradeDetails extends \Google\Model
{
  /**
   * Billing cycle spec is not specified.
   */
  public const BILLING_CYCLE_SPEC_BILLING_CYCLE_SPEC_UNSPECIFIED = 'BILLING_CYCLE_SPEC_UNSPECIFIED';
  /**
   * The billing cycle of the new subscription starts immediately but aligns
   * with the previous subscription it upgrades or downgrades from. First cycle
   * of the new subscription will be prorated.
   */
  public const BILLING_CYCLE_SPEC_BILLING_CYCLE_SPEC_ALIGN_WITH_PREVIOUS_SUBSCRIPTION = 'BILLING_CYCLE_SPEC_ALIGN_WITH_PREVIOUS_SUBSCRIPTION';
  /**
   * The billing cycle of the new subscription starts immediately.
   */
  public const BILLING_CYCLE_SPEC_BILLING_CYCLE_SPEC_START_IMMEDIATELY = 'BILLING_CYCLE_SPEC_START_IMMEDIATELY';
  /**
   * The billing cycle starts at the end of the previous subscription's billing
   * cycle and aligns with the previous subscription's billing cycle.
   */
  public const BILLING_CYCLE_SPEC_BILLING_CYCLE_SPEC_DEFERRED_TO_NEXT_RECURRENCE = 'BILLING_CYCLE_SPEC_DEFERRED_TO_NEXT_RECURRENCE';
  /**
   * Required. Specifies the billing cycle spec for the new upgraded/downgraded
   * subscription.
   *
   * @var string
   */
  public $billingCycleSpec;
  /**
   * Required. The previous subscription id to be replaced. The format can be
   * one of the following: 1. `subscription_id`: the old subscription id under
   * the same partner_id. 2.
   * `partners/{partner_id}/subscriptions/{subscription_id}`. A different
   * partner_id is allowed. But they must be under the same partner group.
   *
   * @var string
   */
  public $previousSubscriptionId;

  /**
   * Required. Specifies the billing cycle spec for the new upgraded/downgraded
   * subscription.
   *
   * Accepted values: BILLING_CYCLE_SPEC_UNSPECIFIED,
   * BILLING_CYCLE_SPEC_ALIGN_WITH_PREVIOUS_SUBSCRIPTION,
   * BILLING_CYCLE_SPEC_START_IMMEDIATELY,
   * BILLING_CYCLE_SPEC_DEFERRED_TO_NEXT_RECURRENCE
   *
   * @param self::BILLING_CYCLE_SPEC_* $billingCycleSpec
   */
  public function setBillingCycleSpec($billingCycleSpec)
  {
    $this->billingCycleSpec = $billingCycleSpec;
  }
  /**
   * @return self::BILLING_CYCLE_SPEC_*
   */
  public function getBillingCycleSpec()
  {
    return $this->billingCycleSpec;
  }
  /**
   * Required. The previous subscription id to be replaced. The format can be
   * one of the following: 1. `subscription_id`: the old subscription id under
   * the same partner_id. 2.
   * `partners/{partner_id}/subscriptions/{subscription_id}`. A different
   * partner_id is allowed. But they must be under the same partner group.
   *
   * @param string $previousSubscriptionId
   */
  public function setPreviousSubscriptionId($previousSubscriptionId)
  {
    $this->previousSubscriptionId = $previousSubscriptionId;
  }
  /**
   * @return string
   */
  public function getPreviousSubscriptionId()
  {
    return $this->previousSubscriptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionUpgradeDowngradeDetails::class, 'Google_Service_PaymentsResellerSubscription_SubscriptionUpgradeDowngradeDetails');
