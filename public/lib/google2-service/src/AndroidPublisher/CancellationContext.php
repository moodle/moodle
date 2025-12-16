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

class CancellationContext extends \Google\Model
{
  /**
   * Cancellation type unspecified.
   */
  public const CANCELLATION_TYPE_CANCELLATION_TYPE_UNSPECIFIED = 'CANCELLATION_TYPE_UNSPECIFIED';
  /**
   * Cancellation requested by the user, and the subscription can be restored.
   * It only stops the subscription's next renewal. For an installment
   * subscription, users still need to finish the commitment period. For more
   * details on renewals and payments, see https://developer.android.com/google/
   * play/billing/subscriptions#installments
   */
  public const CANCELLATION_TYPE_USER_REQUESTED_STOP_RENEWALS = 'USER_REQUESTED_STOP_RENEWALS';
  /**
   * Cancellation requested by the developer, and the subscription cannot be
   * restored. It stops the subscription's next payment. For an installment
   * subscription, users will not need to pay the next payment and finish the
   * commitment period. For more details on renewals and payments, see https://d
   * eveloper.android.com/google/play/billing/subscriptions#installments
   */
  public const CANCELLATION_TYPE_DEVELOPER_REQUESTED_STOP_PAYMENTS = 'DEVELOPER_REQUESTED_STOP_PAYMENTS';
  /**
   * Required. The type of cancellation for the purchased subscription.
   *
   * @var string
   */
  public $cancellationType;

  /**
   * Required. The type of cancellation for the purchased subscription.
   *
   * Accepted values: CANCELLATION_TYPE_UNSPECIFIED,
   * USER_REQUESTED_STOP_RENEWALS, DEVELOPER_REQUESTED_STOP_PAYMENTS
   *
   * @param self::CANCELLATION_TYPE_* $cancellationType
   */
  public function setCancellationType($cancellationType)
  {
    $this->cancellationType = $cancellationType;
  }
  /**
   * @return self::CANCELLATION_TYPE_*
   */
  public function getCancellationType()
  {
    return $this->cancellationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CancellationContext::class, 'Google_Service_AndroidPublisher_CancellationContext');
