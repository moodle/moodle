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

class ProductPurchaseV2 extends \Google\Collection
{
  /**
   * Unspecified acknowledgement state.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_UNSPECIFIED = 'ACKNOWLEDGEMENT_STATE_UNSPECIFIED';
  /**
   * The purchase is not acknowledged yet.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_PENDING = 'ACKNOWLEDGEMENT_STATE_PENDING';
  /**
   * The purchase is acknowledged.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED = 'ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED';
  protected $collection_key = 'productLineItem';
  /**
   * Output only. The acknowledgement state of the purchase.
   *
   * @var string
   */
  public $acknowledgementState;
  /**
   * This kind represents a ProductPurchaseV2 object in the androidpublisher
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
   * The order id associated with the purchase of the inapp product. May not be
   * set if there is no order associated with the purchase.
   *
   * @var string
   */
  public $orderId;
  protected $productLineItemType = ProductLineItem::class;
  protected $productLineItemDataType = 'array';
  /**
   * The time when the purchase was successful, i.e., when the PurchaseState has
   * changed to PURCHASED. This field will not be present until the payment is
   * complete. For example, if the user initiated a pending transaction
   * (https://developer.android.com/google/play/billing/integrate#pending), this
   * field will not be populated until the user successfully completes the steps
   * required to complete the transaction.
   *
   * @var string
   */
  public $purchaseCompletionTime;
  protected $purchaseStateContextType = PurchaseStateContext::class;
  protected $purchaseStateContextDataType = '';
  /**
   * ISO 3166-1 alpha-2 billing region code of the user at the time the product
   * was granted.
   *
   * @var string
   */
  public $regionCode;
  protected $testPurchaseContextType = TestPurchaseContext::class;
  protected $testPurchaseContextDataType = '';

  /**
   * Output only. The acknowledgement state of the purchase.
   *
   * Accepted values: ACKNOWLEDGEMENT_STATE_UNSPECIFIED,
   * ACKNOWLEDGEMENT_STATE_PENDING, ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED
   *
   * @param self::ACKNOWLEDGEMENT_STATE_* $acknowledgementState
   */
  public function setAcknowledgementState($acknowledgementState)
  {
    $this->acknowledgementState = $acknowledgementState;
  }
  /**
   * @return self::ACKNOWLEDGEMENT_STATE_*
   */
  public function getAcknowledgementState()
  {
    return $this->acknowledgementState;
  }
  /**
   * This kind represents a ProductPurchaseV2 object in the androidpublisher
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
   * The order id associated with the purchase of the inapp product. May not be
   * set if there is no order associated with the purchase.
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
   * Contains item-level info for a ProductPurchaseV2.
   *
   * @param ProductLineItem[] $productLineItem
   */
  public function setProductLineItem($productLineItem)
  {
    $this->productLineItem = $productLineItem;
  }
  /**
   * @return ProductLineItem[]
   */
  public function getProductLineItem()
  {
    return $this->productLineItem;
  }
  /**
   * The time when the purchase was successful, i.e., when the PurchaseState has
   * changed to PURCHASED. This field will not be present until the payment is
   * complete. For example, if the user initiated a pending transaction
   * (https://developer.android.com/google/play/billing/integrate#pending), this
   * field will not be populated until the user successfully completes the steps
   * required to complete the transaction.
   *
   * @param string $purchaseCompletionTime
   */
  public function setPurchaseCompletionTime($purchaseCompletionTime)
  {
    $this->purchaseCompletionTime = $purchaseCompletionTime;
  }
  /**
   * @return string
   */
  public function getPurchaseCompletionTime()
  {
    return $this->purchaseCompletionTime;
  }
  /**
   * Information about the purchase state of the purchase.
   *
   * @param PurchaseStateContext $purchaseStateContext
   */
  public function setPurchaseStateContext(PurchaseStateContext $purchaseStateContext)
  {
    $this->purchaseStateContext = $purchaseStateContext;
  }
  /**
   * @return PurchaseStateContext
   */
  public function getPurchaseStateContext()
  {
    return $this->purchaseStateContext;
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
  /**
   * Information related to test purchases. This will only be set for test
   * purchases.
   *
   * @param TestPurchaseContext $testPurchaseContext
   */
  public function setTestPurchaseContext(TestPurchaseContext $testPurchaseContext)
  {
    $this->testPurchaseContext = $testPurchaseContext;
  }
  /**
   * @return TestPurchaseContext
   */
  public function getTestPurchaseContext()
  {
    return $this->testPurchaseContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductPurchaseV2::class, 'Google_Service_AndroidPublisher_ProductPurchaseV2');
