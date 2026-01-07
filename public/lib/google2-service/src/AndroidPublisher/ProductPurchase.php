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

class ProductPurchase extends \Google\Model
{
  /**
   * The acknowledgement state of the inapp product. Possible values are: 0. Yet
   * to be acknowledged 1. Acknowledged
   *
   * @var int
   */
  public $acknowledgementState;
  /**
   * The consumption state of the inapp product. Possible values are: 0. Yet to
   * be consumed 1. Consumed
   *
   * @var int
   */
  public $consumptionState;
  /**
   * A developer-specified string that contains supplemental information about
   * an order.
   *
   * @var string
   */
  public $developerPayload;
  /**
   * This kind represents an inappPurchase object in the androidpublisher
   * service.
   *
   * @var string
   */
  public $kind;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedaccountid when the purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalAccountId;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalProfileId;
  /**
   * The order id associated with the purchase of the inapp product.
   *
   * @var string
   */
  public $orderId;
  /**
   * The inapp product SKU. May not be present.
   *
   * @var string
   */
  public $productId;
  /**
   * The purchase state of the order. Possible values are: 0. Purchased 1.
   * Canceled 2. Pending
   *
   * @var int
   */
  public $purchaseState;
  /**
   * The time the product was purchased, in milliseconds since the epoch (Jan 1,
   * 1970).
   *
   * @var string
   */
  public $purchaseTimeMillis;
  /**
   * The purchase token generated to identify this purchase. May not be present.
   *
   * @var string
   */
  public $purchaseToken;
  /**
   * The type of purchase of the inapp product. This field is only set if this
   * purchase was not made using the standard in-app billing flow. Possible
   * values are: 0. Test (i.e. purchased from a license testing account) 1.
   * Promo (i.e. purchased using a promo code). Does not include Play Points
   * purchases. 2. Rewarded (i.e. from watching a video ad instead of paying)
   *
   * @var int
   */
  public $purchaseType;
  /**
   * The quantity associated with the purchase of the inapp product. If not
   * present, the quantity is 1.
   *
   * @var int
   */
  public $quantity;
  /**
   * The quantity eligible for refund, i.e. quantity that hasn't been refunded.
   * The value reflects quantity-based partial refunds and full refunds.
   *
   * @var int
   */
  public $refundableQuantity;
  /**
   * ISO 3166-1 alpha-2 billing region code of the user at the time the product
   * was granted.
   *
   * @var string
   */
  public $regionCode;

  /**
   * The acknowledgement state of the inapp product. Possible values are: 0. Yet
   * to be acknowledged 1. Acknowledged
   *
   * @param int $acknowledgementState
   */
  public function setAcknowledgementState($acknowledgementState)
  {
    $this->acknowledgementState = $acknowledgementState;
  }
  /**
   * @return int
   */
  public function getAcknowledgementState()
  {
    return $this->acknowledgementState;
  }
  /**
   * The consumption state of the inapp product. Possible values are: 0. Yet to
   * be consumed 1. Consumed
   *
   * @param int $consumptionState
   */
  public function setConsumptionState($consumptionState)
  {
    $this->consumptionState = $consumptionState;
  }
  /**
   * @return int
   */
  public function getConsumptionState()
  {
    return $this->consumptionState;
  }
  /**
   * A developer-specified string that contains supplemental information about
   * an order.
   *
   * @param string $developerPayload
   */
  public function setDeveloperPayload($developerPayload)
  {
    $this->developerPayload = $developerPayload;
  }
  /**
   * @return string
   */
  public function getDeveloperPayload()
  {
    return $this->developerPayload;
  }
  /**
   * This kind represents an inappPurchase object in the androidpublisher
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
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedaccountid when the purchase was made.
   *
   * @param string $obfuscatedExternalAccountId
   */
  public function setObfuscatedExternalAccountId($obfuscatedExternalAccountId)
  {
    $this->obfuscatedExternalAccountId = $obfuscatedExternalAccountId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalAccountId()
  {
    return $this->obfuscatedExternalAccountId;
  }
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @param string $obfuscatedExternalProfileId
   */
  public function setObfuscatedExternalProfileId($obfuscatedExternalProfileId)
  {
    $this->obfuscatedExternalProfileId = $obfuscatedExternalProfileId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalProfileId()
  {
    return $this->obfuscatedExternalProfileId;
  }
  /**
   * The order id associated with the purchase of the inapp product.
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
   * The inapp product SKU. May not be present.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The purchase state of the order. Possible values are: 0. Purchased 1.
   * Canceled 2. Pending
   *
   * @param int $purchaseState
   */
  public function setPurchaseState($purchaseState)
  {
    $this->purchaseState = $purchaseState;
  }
  /**
   * @return int
   */
  public function getPurchaseState()
  {
    return $this->purchaseState;
  }
  /**
   * The time the product was purchased, in milliseconds since the epoch (Jan 1,
   * 1970).
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
   * The purchase token generated to identify this purchase. May not be present.
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
   * The type of purchase of the inapp product. This field is only set if this
   * purchase was not made using the standard in-app billing flow. Possible
   * values are: 0. Test (i.e. purchased from a license testing account) 1.
   * Promo (i.e. purchased using a promo code). Does not include Play Points
   * purchases. 2. Rewarded (i.e. from watching a video ad instead of paying)
   *
   * @param int $purchaseType
   */
  public function setPurchaseType($purchaseType)
  {
    $this->purchaseType = $purchaseType;
  }
  /**
   * @return int
   */
  public function getPurchaseType()
  {
    return $this->purchaseType;
  }
  /**
   * The quantity associated with the purchase of the inapp product. If not
   * present, the quantity is 1.
   *
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  /**
   * The quantity eligible for refund, i.e. quantity that hasn't been refunded.
   * The value reflects quantity-based partial refunds and full refunds.
   *
   * @param int $refundableQuantity
   */
  public function setRefundableQuantity($refundableQuantity)
  {
    $this->refundableQuantity = $refundableQuantity;
  }
  /**
   * @return int
   */
  public function getRefundableQuantity()
  {
    return $this->refundableQuantity;
  }
  /**
   * ISO 3166-1 alpha-2 billing region code of the user at the time the product
   * was granted.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductPurchase::class, 'Google_Service_AndroidPublisher_ProductPurchase');
