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

class InstallmentsBasePlanType extends \Google\Model
{
  /**
   * Unspecified mode.
   */
  public const PRORATION_MODE_SUBSCRIPTION_PRORATION_MODE_UNSPECIFIED = 'SUBSCRIPTION_PRORATION_MODE_UNSPECIFIED';
  /**
   * Users will be charged for their new base plan at the end of their current
   * billing period.
   */
  public const PRORATION_MODE_SUBSCRIPTION_PRORATION_MODE_CHARGE_ON_NEXT_BILLING_DATE = 'SUBSCRIPTION_PRORATION_MODE_CHARGE_ON_NEXT_BILLING_DATE';
  /**
   * Users will be charged for their new base plan immediately and in full. Any
   * remaining period of their existing subscription will be used to extend the
   * duration of the new billing plan.
   */
  public const PRORATION_MODE_SUBSCRIPTION_PRORATION_MODE_CHARGE_FULL_PRICE_IMMEDIATELY = 'SUBSCRIPTION_PRORATION_MODE_CHARGE_FULL_PRICE_IMMEDIATELY';
  /**
   * Unspecified state.
   */
  public const RENEWAL_TYPE_RENEWAL_TYPE_UNSPECIFIED = 'RENEWAL_TYPE_UNSPECIFIED';
  /**
   * Renews periodically for the billing period duration without commitment.
   */
  public const RENEWAL_TYPE_RENEWAL_TYPE_RENEWS_WITHOUT_COMMITMENT = 'RENEWAL_TYPE_RENEWS_WITHOUT_COMMITMENT';
  /**
   * Renews with the commitment of the same duration as the initial one.
   */
  public const RENEWAL_TYPE_RENEWAL_TYPE_RENEWS_WITH_COMMITMENT = 'RENEWAL_TYPE_RENEWS_WITH_COMMITMENT';
  /**
   * Unspecified state.
   */
  public const RESUBSCRIBE_STATE_RESUBSCRIBE_STATE_UNSPECIFIED = 'RESUBSCRIBE_STATE_UNSPECIFIED';
  /**
   * Resubscribe is active.
   */
  public const RESUBSCRIBE_STATE_RESUBSCRIBE_STATE_ACTIVE = 'RESUBSCRIBE_STATE_ACTIVE';
  /**
   * Resubscribe is inactive.
   */
  public const RESUBSCRIBE_STATE_RESUBSCRIBE_STATE_INACTIVE = 'RESUBSCRIBE_STATE_INACTIVE';
  /**
   * Optional. Custom account hold period of the subscription, specified in ISO
   * 8601 format. Acceptable values must be in days and between P0D and P60D. An
   * empty field represents a recommended account hold, calculated as 60 days
   * minus grace period. The sum of gracePeriodDuration and accountHoldDuration
   * must be between P30D and P60D days, inclusive.
   *
   * @var string
   */
  public $accountHoldDuration;
  /**
   * Required. Immutable. Subscription period, specified in ISO 8601 format. For
   * a list of acceptable billing periods, refer to the help center. The
   * duration is immutable after the base plan is created.
   *
   * @var string
   */
  public $billingPeriodDuration;
  /**
   * Required. Immutable. The number of payments the user is committed to. It is
   * immutable after the base plan is created.
   *
   * @var int
   */
  public $committedPaymentsCount;
  /**
   * Grace period of the subscription, specified in ISO 8601 format. Acceptable
   * values must be in days and between P0D and the lesser of 30D and base plan
   * billing period. If not specified, a default value will be used based on the
   * billing period. The sum of gracePeriodDuration and accountHoldDuration must
   * be between P30D and P60D days, inclusive.
   *
   * @var string
   */
  public $gracePeriodDuration;
  /**
   * The proration mode for the base plan determines what happens when a user
   * switches to this plan from another base plan. If unspecified, defaults to
   * CHARGE_ON_NEXT_BILLING_DATE.
   *
   * @var string
   */
  public $prorationMode;
  /**
   * Required. Immutable. Installments base plan renewal type. Determines the
   * behavior at the end of the initial commitment. The renewal type is
   * immutable after the base plan is created.
   *
   * @var string
   */
  public $renewalType;
  /**
   * Whether users should be able to resubscribe to this base plan in Google
   * Play surfaces. Defaults to RESUBSCRIBE_STATE_ACTIVE if not specified.
   *
   * @var string
   */
  public $resubscribeState;

  /**
   * Optional. Custom account hold period of the subscription, specified in ISO
   * 8601 format. Acceptable values must be in days and between P0D and P60D. An
   * empty field represents a recommended account hold, calculated as 60 days
   * minus grace period. The sum of gracePeriodDuration and accountHoldDuration
   * must be between P30D and P60D days, inclusive.
   *
   * @param string $accountHoldDuration
   */
  public function setAccountHoldDuration($accountHoldDuration)
  {
    $this->accountHoldDuration = $accountHoldDuration;
  }
  /**
   * @return string
   */
  public function getAccountHoldDuration()
  {
    return $this->accountHoldDuration;
  }
  /**
   * Required. Immutable. Subscription period, specified in ISO 8601 format. For
   * a list of acceptable billing periods, refer to the help center. The
   * duration is immutable after the base plan is created.
   *
   * @param string $billingPeriodDuration
   */
  public function setBillingPeriodDuration($billingPeriodDuration)
  {
    $this->billingPeriodDuration = $billingPeriodDuration;
  }
  /**
   * @return string
   */
  public function getBillingPeriodDuration()
  {
    return $this->billingPeriodDuration;
  }
  /**
   * Required. Immutable. The number of payments the user is committed to. It is
   * immutable after the base plan is created.
   *
   * @param int $committedPaymentsCount
   */
  public function setCommittedPaymentsCount($committedPaymentsCount)
  {
    $this->committedPaymentsCount = $committedPaymentsCount;
  }
  /**
   * @return int
   */
  public function getCommittedPaymentsCount()
  {
    return $this->committedPaymentsCount;
  }
  /**
   * Grace period of the subscription, specified in ISO 8601 format. Acceptable
   * values must be in days and between P0D and the lesser of 30D and base plan
   * billing period. If not specified, a default value will be used based on the
   * billing period. The sum of gracePeriodDuration and accountHoldDuration must
   * be between P30D and P60D days, inclusive.
   *
   * @param string $gracePeriodDuration
   */
  public function setGracePeriodDuration($gracePeriodDuration)
  {
    $this->gracePeriodDuration = $gracePeriodDuration;
  }
  /**
   * @return string
   */
  public function getGracePeriodDuration()
  {
    return $this->gracePeriodDuration;
  }
  /**
   * The proration mode for the base plan determines what happens when a user
   * switches to this plan from another base plan. If unspecified, defaults to
   * CHARGE_ON_NEXT_BILLING_DATE.
   *
   * Accepted values: SUBSCRIPTION_PRORATION_MODE_UNSPECIFIED,
   * SUBSCRIPTION_PRORATION_MODE_CHARGE_ON_NEXT_BILLING_DATE,
   * SUBSCRIPTION_PRORATION_MODE_CHARGE_FULL_PRICE_IMMEDIATELY
   *
   * @param self::PRORATION_MODE_* $prorationMode
   */
  public function setProrationMode($prorationMode)
  {
    $this->prorationMode = $prorationMode;
  }
  /**
   * @return self::PRORATION_MODE_*
   */
  public function getProrationMode()
  {
    return $this->prorationMode;
  }
  /**
   * Required. Immutable. Installments base plan renewal type. Determines the
   * behavior at the end of the initial commitment. The renewal type is
   * immutable after the base plan is created.
   *
   * Accepted values: RENEWAL_TYPE_UNSPECIFIED,
   * RENEWAL_TYPE_RENEWS_WITHOUT_COMMITMENT, RENEWAL_TYPE_RENEWS_WITH_COMMITMENT
   *
   * @param self::RENEWAL_TYPE_* $renewalType
   */
  public function setRenewalType($renewalType)
  {
    $this->renewalType = $renewalType;
  }
  /**
   * @return self::RENEWAL_TYPE_*
   */
  public function getRenewalType()
  {
    return $this->renewalType;
  }
  /**
   * Whether users should be able to resubscribe to this base plan in Google
   * Play surfaces. Defaults to RESUBSCRIBE_STATE_ACTIVE if not specified.
   *
   * Accepted values: RESUBSCRIBE_STATE_UNSPECIFIED, RESUBSCRIBE_STATE_ACTIVE,
   * RESUBSCRIBE_STATE_INACTIVE
   *
   * @param self::RESUBSCRIBE_STATE_* $resubscribeState
   */
  public function setResubscribeState($resubscribeState)
  {
    $this->resubscribeState = $resubscribeState;
  }
  /**
   * @return self::RESUBSCRIBE_STATE_*
   */
  public function getResubscribeState()
  {
    return $this->resubscribeState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstallmentsBasePlanType::class, 'Google_Service_AndroidPublisher_InstallmentsBasePlanType');
