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

class SubscriptionCancellationDetails extends \Google\Model
{
  /**
   * Reason is unspecified. Should not be used.
   */
  public const REASON_CANCELLATION_REASON_UNSPECIFIED = 'CANCELLATION_REASON_UNSPECIFIED';
  /**
   * Fraudualant transaction.
   */
  public const REASON_CANCELLATION_REASON_FRAUD = 'CANCELLATION_REASON_FRAUD';
  /**
   * Buyer's remorse.
   */
  public const REASON_CANCELLATION_REASON_REMORSE = 'CANCELLATION_REASON_REMORSE';
  /**
   * Accidential purchase.
   */
  public const REASON_CANCELLATION_REASON_ACCIDENTAL_PURCHASE = 'CANCELLATION_REASON_ACCIDENTAL_PURCHASE';
  /**
   * Payment is past due.
   */
  public const REASON_CANCELLATION_REASON_PAST_DUE = 'CANCELLATION_REASON_PAST_DUE';
  /**
   * Used for notification only, do not use in Cancel API. User account closed.
   */
  public const REASON_CANCELLATION_REASON_ACCOUNT_CLOSED = 'CANCELLATION_REASON_ACCOUNT_CLOSED';
  /**
   * Used for notification only, do not use in Cancel API. Cancellation due to
   * upgrade or downgrade.
   */
  public const REASON_CANCELLATION_REASON_UPGRADE_DOWNGRADE = 'CANCELLATION_REASON_UPGRADE_DOWNGRADE';
  /**
   * Cancellation due to user delinquency
   */
  public const REASON_CANCELLATION_REASON_USER_DELINQUENCY = 'CANCELLATION_REASON_USER_DELINQUENCY';
  /**
   * Used for notification only, do not use in Cancel API. Cancellation due to
   * an unrecoverable system error.
   */
  public const REASON_CANCELLATION_REASON_SYSTEM_ERROR = 'CANCELLATION_REASON_SYSTEM_ERROR';
  /**
   * Used for notification only, do not use in Cancel API. The subscription is
   * cancelled by Google automatically since it is no longer valid.
   */
  public const REASON_CANCELLATION_REASON_SYSTEM_CANCEL = 'CANCELLATION_REASON_SYSTEM_CANCEL';
  /**
   * Cancellation due to a billing system switch.
   */
  public const REASON_CANCELLATION_REASON_BILLING_SYSTEM_SWITCH = 'CANCELLATION_REASON_BILLING_SYSTEM_SWITCH';
  /**
   * Other reason.
   */
  public const REASON_CANCELLATION_REASON_OTHER = 'CANCELLATION_REASON_OTHER';
  /**
   * Output only. The reason of the cancellation.
   *
   * @var string
   */
  public $reason;

  /**
   * Output only. The reason of the cancellation.
   *
   * Accepted values: CANCELLATION_REASON_UNSPECIFIED,
   * CANCELLATION_REASON_FRAUD, CANCELLATION_REASON_REMORSE,
   * CANCELLATION_REASON_ACCIDENTAL_PURCHASE, CANCELLATION_REASON_PAST_DUE,
   * CANCELLATION_REASON_ACCOUNT_CLOSED, CANCELLATION_REASON_UPGRADE_DOWNGRADE,
   * CANCELLATION_REASON_USER_DELINQUENCY, CANCELLATION_REASON_SYSTEM_ERROR,
   * CANCELLATION_REASON_SYSTEM_CANCEL,
   * CANCELLATION_REASON_BILLING_SYSTEM_SWITCH, CANCELLATION_REASON_OTHER
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionCancellationDetails::class, 'Google_Service_PaymentsResellerSubscription_SubscriptionCancellationDetails');
