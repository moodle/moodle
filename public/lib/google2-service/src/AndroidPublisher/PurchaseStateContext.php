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

class PurchaseStateContext extends \Google\Model
{
  /**
   * Purchase state unspecified. This value should never be set.
   */
  public const PURCHASE_STATE_PURCHASE_STATE_UNSPECIFIED = 'PURCHASE_STATE_UNSPECIFIED';
  /**
   * Purchased successfully.
   */
  public const PURCHASE_STATE_PURCHASED = 'PURCHASED';
  /**
   * Purchase canceled.
   */
  public const PURCHASE_STATE_CANCELLED = 'CANCELLED';
  /**
   * The purchase is in a pending state and has not yet been completed. For more
   * information on handling pending purchases, see
   * https://developer.android.com/google/play/billing/integrate#pending.
   */
  public const PURCHASE_STATE_PENDING = 'PENDING';
  /**
   * Output only. The purchase state of the purchase.
   *
   * @var string
   */
  public $purchaseState;

  /**
   * Output only. The purchase state of the purchase.
   *
   * Accepted values: PURCHASE_STATE_UNSPECIFIED, PURCHASED, CANCELLED, PENDING
   *
   * @param self::PURCHASE_STATE_* $purchaseState
   */
  public function setPurchaseState($purchaseState)
  {
    $this->purchaseState = $purchaseState;
  }
  /**
   * @return self::PURCHASE_STATE_*
   */
  public function getPurchaseState()
  {
    return $this->purchaseState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurchaseStateContext::class, 'Google_Service_AndroidPublisher_PurchaseStateContext');
