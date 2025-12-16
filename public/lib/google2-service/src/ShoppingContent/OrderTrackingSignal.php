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

namespace Google\Service\ShoppingContent;

class OrderTrackingSignal extends \Google\Collection
{
  protected $collection_key = 'shippingInfo';
  protected $customerShippingFeeType = PriceAmount::class;
  protected $customerShippingFeeDataType = '';
  /**
   * Required. The delivery postal code, as a continuous string without spaces
   * or dashes, e.g. "95016". This field will be anonymized in returned
   * OrderTrackingSignal creation response.
   *
   * @var string
   */
  public $deliveryPostalCode;
  /**
   * Required. The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) for the
   * shipping destination.
   *
   * @var string
   */
  public $deliveryRegionCode;
  protected $lineItemsType = OrderTrackingSignalLineItemDetails::class;
  protected $lineItemsDataType = 'array';
  /**
   * The Google merchant ID of this order tracking signal. This value is
   * optional. If left unset, the caller's merchant ID is used. You must request
   * access in order to provide data on behalf of another merchant. For more
   * information, see [Submitting Order Tracking Signals](/shopping-
   * content/guides/order-tracking-signals).
   *
   * @var string
   */
  public $merchantId;
  protected $orderCreatedTimeType = DateTime::class;
  protected $orderCreatedTimeDataType = '';
  /**
   * Required. The ID of the order on the merchant side. This field will be
   * hashed in returned OrderTrackingSignal creation response.
   *
   * @var string
   */
  public $orderId;
  /**
   * Output only. The ID that uniquely identifies this order tracking signal.
   *
   * @var string
   */
  public $orderTrackingSignalId;
  protected $shipmentLineItemMappingType = OrderTrackingSignalShipmentLineItemMapping::class;
  protected $shipmentLineItemMappingDataType = 'array';
  protected $shippingInfoType = OrderTrackingSignalShippingInfo::class;
  protected $shippingInfoDataType = 'array';

  /**
   * The shipping fee of the order; this value should be set to zero in the case
   * of free shipping.
   *
   * @param PriceAmount $customerShippingFee
   */
  public function setCustomerShippingFee(PriceAmount $customerShippingFee)
  {
    $this->customerShippingFee = $customerShippingFee;
  }
  /**
   * @return PriceAmount
   */
  public function getCustomerShippingFee()
  {
    return $this->customerShippingFee;
  }
  /**
   * Required. The delivery postal code, as a continuous string without spaces
   * or dashes, e.g. "95016". This field will be anonymized in returned
   * OrderTrackingSignal creation response.
   *
   * @param string $deliveryPostalCode
   */
  public function setDeliveryPostalCode($deliveryPostalCode)
  {
    $this->deliveryPostalCode = $deliveryPostalCode;
  }
  /**
   * @return string
   */
  public function getDeliveryPostalCode()
  {
    return $this->deliveryPostalCode;
  }
  /**
   * Required. The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) for the
   * shipping destination.
   *
   * @param string $deliveryRegionCode
   */
  public function setDeliveryRegionCode($deliveryRegionCode)
  {
    $this->deliveryRegionCode = $deliveryRegionCode;
  }
  /**
   * @return string
   */
  public function getDeliveryRegionCode()
  {
    return $this->deliveryRegionCode;
  }
  /**
   * Information about line items in the order.
   *
   * @param OrderTrackingSignalLineItemDetails[] $lineItems
   */
  public function setLineItems($lineItems)
  {
    $this->lineItems = $lineItems;
  }
  /**
   * @return OrderTrackingSignalLineItemDetails[]
   */
  public function getLineItems()
  {
    return $this->lineItems;
  }
  /**
   * The Google merchant ID of this order tracking signal. This value is
   * optional. If left unset, the caller's merchant ID is used. You must request
   * access in order to provide data on behalf of another merchant. For more
   * information, see [Submitting Order Tracking Signals](/shopping-
   * content/guides/order-tracking-signals).
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * Required. The time when the order was created on the merchant side. Include
   * the year and timezone string, if available.
   *
   * @param DateTime $orderCreatedTime
   */
  public function setOrderCreatedTime(DateTime $orderCreatedTime)
  {
    $this->orderCreatedTime = $orderCreatedTime;
  }
  /**
   * @return DateTime
   */
  public function getOrderCreatedTime()
  {
    return $this->orderCreatedTime;
  }
  /**
   * Required. The ID of the order on the merchant side. This field will be
   * hashed in returned OrderTrackingSignal creation response.
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
   * Output only. The ID that uniquely identifies this order tracking signal.
   *
   * @param string $orderTrackingSignalId
   */
  public function setOrderTrackingSignalId($orderTrackingSignalId)
  {
    $this->orderTrackingSignalId = $orderTrackingSignalId;
  }
  /**
   * @return string
   */
  public function getOrderTrackingSignalId()
  {
    return $this->orderTrackingSignalId;
  }
  /**
   * The mapping of the line items to the shipment information.
   *
   * @param OrderTrackingSignalShipmentLineItemMapping[] $shipmentLineItemMapping
   */
  public function setShipmentLineItemMapping($shipmentLineItemMapping)
  {
    $this->shipmentLineItemMapping = $shipmentLineItemMapping;
  }
  /**
   * @return OrderTrackingSignalShipmentLineItemMapping[]
   */
  public function getShipmentLineItemMapping()
  {
    return $this->shipmentLineItemMapping;
  }
  /**
   * The shipping information for the order.
   *
   * @param OrderTrackingSignalShippingInfo[] $shippingInfo
   */
  public function setShippingInfo($shippingInfo)
  {
    $this->shippingInfo = $shippingInfo;
  }
  /**
   * @return OrderTrackingSignalShippingInfo[]
   */
  public function getShippingInfo()
  {
    return $this->shippingInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderTrackingSignal::class, 'Google_Service_ShoppingContent_OrderTrackingSignal');
