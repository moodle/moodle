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

class OrderTrackingSignalShippingInfo extends \Google\Model
{
  /**
   * The shipping status is not known to merchant.
   */
  public const SHIPPING_STATUS_SHIPPING_STATE_UNSPECIFIED = 'SHIPPING_STATE_UNSPECIFIED';
  /**
   * All items are shipped.
   */
  public const SHIPPING_STATUS_SHIPPED = 'SHIPPED';
  /**
   * The shipment is already delivered.
   */
  public const SHIPPING_STATUS_DELIVERED = 'DELIVERED';
  protected $actualDeliveryTimeType = DateTime::class;
  protected $actualDeliveryTimeDataType = '';
  /**
   * The name of the shipping carrier for the delivery. This field is required
   * if one of the following fields is absent: earliest_delivery_promise_time,
   * latest_delivery_promise_time, and actual_delivery_time.
   *
   * @var string
   */
  public $carrierName;
  /**
   * The service type for fulfillment, e.g., GROUND, FIRST_CLASS, etc.
   *
   * @var string
   */
  public $carrierServiceName;
  protected $earliestDeliveryPromiseTimeType = DateTime::class;
  protected $earliestDeliveryPromiseTimeDataType = '';
  protected $latestDeliveryPromiseTimeType = DateTime::class;
  protected $latestDeliveryPromiseTimeDataType = '';
  /**
   * The origin postal code, as a continuous string without spaces or dashes,
   * e.g. "95016". This field will be anonymized in returned OrderTrackingSignal
   * creation response.
   *
   * @var string
   */
  public $originPostalCode;
  /**
   * The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) for the
   * shipping origin.
   *
   * @var string
   */
  public $originRegionCode;
  /**
   * Required. The shipment ID. This field will be hashed in returned
   * OrderTrackingSignal creation response.
   *
   * @var string
   */
  public $shipmentId;
  protected $shippedTimeType = DateTime::class;
  protected $shippedTimeDataType = '';
  /**
   * The status of the shipment.
   *
   * @var string
   */
  public $shippingStatus;
  /**
   * The tracking ID of the shipment. This field is required if one of the
   * following fields is absent: earliest_delivery_promise_time,
   * latest_delivery_promise_time, and actual_delivery_time.
   *
   * @var string
   */
  public $trackingId;

  /**
   * The time when the shipment was actually delivered. Include the year and
   * timezone string, if available. This field is required, if one of the
   * following fields is absent: tracking_id or carrier_name.
   *
   * @param DateTime $actualDeliveryTime
   */
  public function setActualDeliveryTime(DateTime $actualDeliveryTime)
  {
    $this->actualDeliveryTime = $actualDeliveryTime;
  }
  /**
   * @return DateTime
   */
  public function getActualDeliveryTime()
  {
    return $this->actualDeliveryTime;
  }
  /**
   * The name of the shipping carrier for the delivery. This field is required
   * if one of the following fields is absent: earliest_delivery_promise_time,
   * latest_delivery_promise_time, and actual_delivery_time.
   *
   * @param string $carrierName
   */
  public function setCarrierName($carrierName)
  {
    $this->carrierName = $carrierName;
  }
  /**
   * @return string
   */
  public function getCarrierName()
  {
    return $this->carrierName;
  }
  /**
   * The service type for fulfillment, e.g., GROUND, FIRST_CLASS, etc.
   *
   * @param string $carrierServiceName
   */
  public function setCarrierServiceName($carrierServiceName)
  {
    $this->carrierServiceName = $carrierServiceName;
  }
  /**
   * @return string
   */
  public function getCarrierServiceName()
  {
    return $this->carrierServiceName;
  }
  /**
   * The earliest delivery promised time. Include the year and timezone string,
   * if available. This field is required, if one of the following fields is
   * absent: tracking_id or carrier_name.
   *
   * @param DateTime $earliestDeliveryPromiseTime
   */
  public function setEarliestDeliveryPromiseTime(DateTime $earliestDeliveryPromiseTime)
  {
    $this->earliestDeliveryPromiseTime = $earliestDeliveryPromiseTime;
  }
  /**
   * @return DateTime
   */
  public function getEarliestDeliveryPromiseTime()
  {
    return $this->earliestDeliveryPromiseTime;
  }
  /**
   * The latest delivery promised time. Include the year and timezone string, if
   * available. This field is required, if one of the following fields is
   * absent: tracking_id or carrier_name.
   *
   * @param DateTime $latestDeliveryPromiseTime
   */
  public function setLatestDeliveryPromiseTime(DateTime $latestDeliveryPromiseTime)
  {
    $this->latestDeliveryPromiseTime = $latestDeliveryPromiseTime;
  }
  /**
   * @return DateTime
   */
  public function getLatestDeliveryPromiseTime()
  {
    return $this->latestDeliveryPromiseTime;
  }
  /**
   * The origin postal code, as a continuous string without spaces or dashes,
   * e.g. "95016". This field will be anonymized in returned OrderTrackingSignal
   * creation response.
   *
   * @param string $originPostalCode
   */
  public function setOriginPostalCode($originPostalCode)
  {
    $this->originPostalCode = $originPostalCode;
  }
  /**
   * @return string
   */
  public function getOriginPostalCode()
  {
    return $this->originPostalCode;
  }
  /**
   * The [CLDR territory code]
   * (http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) for the
   * shipping origin.
   *
   * @param string $originRegionCode
   */
  public function setOriginRegionCode($originRegionCode)
  {
    $this->originRegionCode = $originRegionCode;
  }
  /**
   * @return string
   */
  public function getOriginRegionCode()
  {
    return $this->originRegionCode;
  }
  /**
   * Required. The shipment ID. This field will be hashed in returned
   * OrderTrackingSignal creation response.
   *
   * @param string $shipmentId
   */
  public function setShipmentId($shipmentId)
  {
    $this->shipmentId = $shipmentId;
  }
  /**
   * @return string
   */
  public function getShipmentId()
  {
    return $this->shipmentId;
  }
  /**
   * The time when the shipment was shipped. Include the year and timezone
   * string, if available.
   *
   * @param DateTime $shippedTime
   */
  public function setShippedTime(DateTime $shippedTime)
  {
    $this->shippedTime = $shippedTime;
  }
  /**
   * @return DateTime
   */
  public function getShippedTime()
  {
    return $this->shippedTime;
  }
  /**
   * The status of the shipment.
   *
   * Accepted values: SHIPPING_STATE_UNSPECIFIED, SHIPPED, DELIVERED
   *
   * @param self::SHIPPING_STATUS_* $shippingStatus
   */
  public function setShippingStatus($shippingStatus)
  {
    $this->shippingStatus = $shippingStatus;
  }
  /**
   * @return self::SHIPPING_STATUS_*
   */
  public function getShippingStatus()
  {
    return $this->shippingStatus;
  }
  /**
   * The tracking ID of the shipment. This field is required if one of the
   * following fields is absent: earliest_delivery_promise_time,
   * latest_delivery_promise_time, and actual_delivery_time.
   *
   * @param string $trackingId
   */
  public function setTrackingId($trackingId)
  {
    $this->trackingId = $trackingId;
  }
  /**
   * @return string
   */
  public function getTrackingId()
  {
    return $this->trackingId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderTrackingSignalShippingInfo::class, 'Google_Service_ShoppingContent_OrderTrackingSignalShippingInfo');
