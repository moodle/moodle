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

class VoidedPurchase extends \Google\Model
{
  /**
   * This kind represents a voided purchase object in the androidpublisher
   * service.
   *
   * @var string
   */
  public $kind;
  /**
   * The order id which uniquely identifies a one-time purchase, subscription
   * purchase, or subscription renewal.
   *
   * @var string
   */
  public $orderId;
  /**
   * The time at which the purchase was made, in milliseconds since the epoch
   * (Jan 1, 1970).
   *
   * @var string
   */
  public $purchaseTimeMillis;
  /**
   * The token which uniquely identifies a one-time purchase or subscription. To
   * uniquely identify subscription renewals use order_id (available starting
   * from version 3 of the API).
   *
   * @var string
   */
  public $purchaseToken;
  /**
   * The voided quantity as the result of a quantity-based partial refund.
   * Voided purchases of quantity-based partial refunds may only be returned
   * when includeQuantityBasedPartialRefund is set to true.
   *
   * @var int
   */
  public $voidedQuantity;
  /**
   * The reason why the purchase was voided, possible values are: 0. Other 1.
   * Remorse 2. Not_received 3. Defective 4. Accidental_purchase 5. Fraud 6.
   * Friendly_fraud 7. Chargeback 8. Unacknowledged_purchase
   *
   * @var int
   */
  public $voidedReason;
  /**
   * The initiator of voided purchase, possible values are: 0. User 1. Developer
   * 2. Google
   *
   * @var int
   */
  public $voidedSource;
  /**
   * The time at which the purchase was canceled/refunded/charged-back, in
   * milliseconds since the epoch (Jan 1, 1970).
   *
   * @var string
   */
  public $voidedTimeMillis;

  /**
   * This kind represents a voided purchase object in the androidpublisher
   * service.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The order id which uniquely identifies a one-time purchase, subscription
   * purchase, or subscription renewal.
   *
   * @param string $orderId
   */
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  /**
   * @return string
   */
  public function getOrderId()
  {
    return $this->orderId;
  }
  /**
   * The time at which the purchase was made, in milliseconds since the epoch
   * (Jan 1, 1970).
   *
   * @param string $purchaseTimeMillis
   */
  public function setPurchaseTimeMillis($purchaseTimeMillis)
  {
    $this->purchaseTimeMillis = $purchaseTimeMillis;
  }
  /**
   * @return string
   */
  public function getPurchaseTimeMillis()
  {
    return $this->purchaseTimeMillis;
  }
  /**
   * The token which uniquely identifies a one-time purchase or subscription. To
   * uniquely identify subscription renewals use order_id (available starting
   * from version 3 of the API).
   *
   * @param string $purchaseToken
   */
  public function setPurchaseToken($purchaseToken)
  {
    $this->purchaseToken = $purchaseToken;
  }
  /**
   * @return string
   */
  public function getPurchaseToken()
  {
    return $this->purchaseToken;
  }
  /**
   * The voided quantity as the result of a quantity-based partial refund.
   * Voided purchases of quantity-based partial refunds may only be returned
   * when includeQuantityBasedPartialRefund is set to true.
   *
   * @param int $voidedQuantity
   */
  public function setVoidedQuantity($voidedQuantity)
  {
    $this->voidedQuantity = $voidedQuantity;
  }
  /**
   * @return int
   */
  public function getVoidedQuantity()
  {
    return $this->voidedQuantity;
  }
  /**
   * The reason why the purchase was voided, possible values are: 0. Other 1.
   * Remorse 2. Not_received 3. Defective 4. Accidental_purchase 5. Fraud 6.
   * Friendly_fraud 7. Chargeback 8. Unacknowledged_purchase
   *
   * @param int $voidedReason
   */
  public function setVoidedReason($voidedReason)
  {
    $this->voidedReason = $voidedReason;
  }
  /**
   * @return int
   */
  public function getVoidedReason()
  {
    return $this->voidedReason;
  }
  /**
   * The initiator of voided purchase, possible values are: 0. User 1. Developer
   * 2. Google
   *
   * @param int $voidedSource
   */
  public function setVoidedSource($voidedSource)
  {
    $this->voidedSource = $voidedSource;
  }
  /**
   * @return int
   */
  public function getVoidedSource()
  {
    return $this->voidedSource;
  }
  /**
   * The time at which the purchase was canceled/refunded/charged-back, in
   * milliseconds since the epoch (Jan 1, 1970).
   *
   * @param string $voidedTimeMillis
   */
  public function setVoidedTimeMillis($voidedTimeMillis)
  {
    $this->voidedTimeMillis = $voidedTimeMillis;
  }
  /**
   * @return string
   */
  public function getVoidedTimeMillis()
  {
    return $this->voidedTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VoidedPurchase::class, 'Google_Service_AndroidPublisher_VoidedPurchase');
