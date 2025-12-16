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

class OrderTrackingSignalShipmentLineItemMapping extends \Google\Model
{
  /**
   * Required. The line item ID.
   *
   * @var string
   */
  public $lineItemId;
  /**
   * The line item quantity in the shipment.
   *
   * @var string
   */
  public $quantity;
  /**
   * Required. The shipment ID. This field will be hashed in returned
   * OrderTrackingSignal creation response.
   *
   * @var string
   */
  public $shipmentId;

  /**
   * Required. The line item ID.
   *
   * @param string $lineItemId
   */
  public function setLineItemId($lineItemId)
  {
    $this->lineItemId = $lineItemId;
  }
  /**
   * @return string
   */
  public function getLineItemId()
  {
    return $this->lineItemId;
  }
  /**
   * The line item quantity in the shipment.
   *
   * @param string $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return string
   */
  public function getQuantity()
  {
    return $this->quantity;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderTrackingSignalShipmentLineItemMapping::class, 'Google_Service_ShoppingContent_OrderTrackingSignalShipmentLineItemMapping');
