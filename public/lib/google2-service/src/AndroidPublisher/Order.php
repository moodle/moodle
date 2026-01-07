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

class Order extends \Google\Collection
{
  /**
   * Sales channel unspecified. This value is not used.
   */
  public const SALES_CHANNEL_SALES_CHANNEL_UNSPECIFIED = 'SALES_CHANNEL_UNSPECIFIED';
  /**
   * Standard orders that initiated from in-app.
   */
  public const SALES_CHANNEL_IN_APP = 'IN_APP';
  /**
   * Orders initiated from a PC emulator for in-app purchases.
   */
  public const SALES_CHANNEL_PC_EMULATOR = 'PC_EMULATOR';
  /**
   * Orders initiated from a native PC app for in-app purchases.
   */
  public const SALES_CHANNEL_NATIVE_PC = 'NATIVE_PC';
  /**
   * Orders initiated from the Google Play store.
   */
  public const SALES_CHANNEL_PLAY_STORE = 'PLAY_STORE';
  /**
   * Orders initiated outside the Google Play store.
   */
  public const SALES_CHANNEL_OUTSIDE_PLAY_STORE = 'OUTSIDE_PLAY_STORE';
  /**
   * State unspecified. This value is not used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Order has been created and is waiting to be processed.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Order has been successfully processed.
   */
  public const STATE_PROCESSED = 'PROCESSED';
  /**
   * Order was canceled before being processed.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * Requested refund is waiting to be processed.
   */
  public const STATE_PENDING_REFUND = 'PENDING_REFUND';
  /**
   * Part of the order amount was refunded.
   */
  public const STATE_PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
  /**
   * The full order amount was refunded.
   */
  public const STATE_REFUNDED = 'REFUNDED';
  protected $collection_key = 'lineItems';
  protected $buyerAddressType = BuyerAddress::class;
  protected $buyerAddressDataType = '';
  /**
   * The time when the order was created.
   *
   * @var string
   */
  public $createTime;
  protected $developerRevenueInBuyerCurrencyType = Money::class;
  protected $developerRevenueInBuyerCurrencyDataType = '';
  /**
   * The time of the last event that occurred on the order.
   *
   * @var string
   */
  public $lastEventTime;
  protected $lineItemsType = LineItem::class;
  protected $lineItemsDataType = 'array';
  protected $orderDetailsType = OrderDetails::class;
  protected $orderDetailsDataType = '';
  protected $orderHistoryType = OrderHistory::class;
  protected $orderHistoryDataType = '';
  /**
   * The order ID.
   *
   * @var string
   */
  public $orderId;
  protected $pointsDetailsType = PointsDetails::class;
  protected $pointsDetailsDataType = '';
  /**
   * The token provided to the user's device when the subscription or item was
   * purchased.
   *
   * @var string
   */
  public $purchaseToken;
  /**
   * The originating sales channel of the order.
   *
   * @var string
   */
  public $salesChannel;
  /**
   * The state of the order.
   *
   * @var string
   */
  public $state;
  protected $taxType = Money::class;
  protected $taxDataType = '';
  protected $totalType = Money::class;
  protected $totalDataType = '';

  /**
   * Address information for the customer, for use in tax computation. When
   * Google is the Merchant of Record for the order, only country is shown.
   *
   * @param BuyerAddress $buyerAddress
   */
  public function setBuyerAddress(BuyerAddress $buyerAddress)
  {
    $this->buyerAddress = $buyerAddress;
  }
  /**
   * @return BuyerAddress
   */
  public function getBuyerAddress()
  {
    return $this->buyerAddress;
  }
  /**
   * The time when the order was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Your revenue for this order in the buyer's currency, including deductions
   * of partial refunds, taxes and fees. Google deducts standard transaction and
   * third party fees from each sale, including VAT in some regions.
   *
   * @param Money $developerRevenueInBuyerCurrency
   */
  public function setDeveloperRevenueInBuyerCurrency(Money $developerRevenueInBuyerCurrency)
  {
    $this->developerRevenueInBuyerCurrency = $developerRevenueInBuyerCurrency;
  }
  /**
   * @return Money
   */
  public function getDeveloperRevenueInBuyerCurrency()
  {
    return $this->developerRevenueInBuyerCurrency;
  }
  /**
   * The time of the last event that occurred on the order.
   *
   * @param string $lastEventTime
   */
  public function setLastEventTime($lastEventTime)
  {
    $this->lastEventTime = $lastEventTime;
  }
  /**
   * @return string
   */
  public function getLastEventTime()
  {
    return $this->lastEventTime;
  }
  /**
   * The individual line items making up this order.
   *
   * @param LineItem[] $lineItems
   */
  public function setLineItems($lineItems)
  {
    $this->lineItems = $lineItems;
  }
  /**
   * @return LineItem[]
   */
  public function getLineItems()
  {
    return $this->lineItems;
  }
  /**
   * Detailed information about the order at creation time.
   *
   * @param OrderDetails $orderDetails
   */
  public function setOrderDetails(OrderDetails $orderDetails)
  {
    $this->orderDetails = $orderDetails;
  }
  /**
   * @return OrderDetails
   */
  public function getOrderDetails()
  {
    return $this->orderDetails;
  }
  /**
   * Details about events which modified the order.
   *
   * @param OrderHistory $orderHistory
   */
  public function setOrderHistory(OrderHistory $orderHistory)
  {
    $this->orderHistory = $orderHistory;
  }
  /**
   * @return OrderHistory
   */
  public function getOrderHistory()
  {
    return $this->orderHistory;
  }
  /**
   * The order ID.
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
   * Play points applied to the order, including offer information, discount
   * rate and point values.
   *
   * @param PointsDetails $pointsDetails
   */
  public function setPointsDetails(PointsDetails $pointsDetails)
  {
    $this->pointsDetails = $pointsDetails;
  }
  /**
   * @return PointsDetails
   */
  public function getPointsDetails()
  {
    return $this->pointsDetails;
  }
  /**
   * The token provided to the user's device when the subscription or item was
   * purchased.
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
   * The originating sales channel of the order.
   *
   * Accepted values: SALES_CHANNEL_UNSPECIFIED, IN_APP, PC_EMULATOR, NATIVE_PC,
   * PLAY_STORE, OUTSIDE_PLAY_STORE
   *
   * @param self::SALES_CHANNEL_* $salesChannel
   */
  public function setSalesChannel($salesChannel)
  {
    $this->salesChannel = $salesChannel;
  }
  /**
   * @return self::SALES_CHANNEL_*
   */
  public function getSalesChannel()
  {
    return $this->salesChannel;
  }
  /**
   * The state of the order.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, PROCESSED, CANCELED,
   * PENDING_REFUND, PARTIALLY_REFUNDED, REFUNDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The total tax paid as a part of this order.
   *
   * @param Money $tax
   */
  public function setTax(Money $tax)
  {
    $this->tax = $tax;
  }
  /**
   * @return Money
   */
  public function getTax()
  {
    return $this->tax;
  }
  /**
   * The final amount paid by the customer, taking into account discounts and
   * taxes.
   *
   * @param Money $total
   */
  public function setTotal(Money $total)
  {
    $this->total = $total;
  }
  /**
   * @return Money
   */
  public function getTotal()
  {
    return $this->total;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Order::class, 'Google_Service_AndroidPublisher_Order');
