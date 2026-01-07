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

class Warehouse extends \Google\Model
{
  protected $businessDayConfigType = BusinessDayConfig::class;
  protected $businessDayConfigDataType = '';
  protected $cutoffTimeType = WarehouseCutoffTime::class;
  protected $cutoffTimeDataType = '';
  /**
   * Required. The number of days it takes for this warehouse to pack up and
   * ship an item. This is on the warehouse level, but can be overridden on the
   * offer level based on the attributes of an item.
   *
   * @var string
   */
  public $handlingDays;
  /**
   * Required. The name of the warehouse. Must be unique within account.
   *
   * @var string
   */
  public $name;
  protected $shippingAddressType = Address::class;
  protected $shippingAddressDataType = '';

  /**
   * Business days of the warehouse. If not set, will be Monday to Friday by
   * default.
   *
   * @param BusinessDayConfig $businessDayConfig
   */
  public function setBusinessDayConfig(BusinessDayConfig $businessDayConfig)
  {
    $this->businessDayConfig = $businessDayConfig;
  }
  /**
   * @return BusinessDayConfig
   */
  public function getBusinessDayConfig()
  {
    return $this->businessDayConfig;
  }
  /**
   * Required. The latest time of day that an order can be accepted and begin
   * processing. Later orders will be processed in the next day. The time is
   * based on the warehouse postal code.
   *
   * @param WarehouseCutoffTime $cutoffTime
   */
  public function setCutoffTime(WarehouseCutoffTime $cutoffTime)
  {
    $this->cutoffTime = $cutoffTime;
  }
  /**
   * @return WarehouseCutoffTime
   */
  public function getCutoffTime()
  {
    return $this->cutoffTime;
  }
  /**
   * Required. The number of days it takes for this warehouse to pack up and
   * ship an item. This is on the warehouse level, but can be overridden on the
   * offer level based on the attributes of an item.
   *
   * @param string $handlingDays
   */
  public function setHandlingDays($handlingDays)
  {
    $this->handlingDays = $handlingDays;
  }
  /**
   * @return string
   */
  public function getHandlingDays()
  {
    return $this->handlingDays;
  }
  /**
   * Required. The name of the warehouse. Must be unique within account.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Shipping address of the warehouse.
   *
   * @param Address $shippingAddress
   */
  public function setShippingAddress(Address $shippingAddress)
  {
    $this->shippingAddress = $shippingAddress;
  }
  /**
   * @return Address
   */
  public function getShippingAddress()
  {
    return $this->shippingAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Warehouse::class, 'Google_Service_ShoppingContent_Warehouse');
