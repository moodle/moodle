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

class WarehouseBasedDeliveryTime extends \Google\Model
{
  /**
   * Required. Carrier, such as `"UPS"` or `"Fedex"`. The list of supported
   * carriers can be retrieved through the `listSupportedCarriers` method.
   *
   * @var string
   */
  public $carrier;
  /**
   * Required. Carrier service, such as `"ground"` or `"2 days"`. The list of
   * supported services for a carrier can be retrieved through the
   * `listSupportedCarriers` method. The name of the service must be in the
   * eddSupportedServices list.
   *
   * @var string
   */
  public $carrierService;
  /**
   * Shipping origin's state.
   *
   * @var string
   */
  public $originAdministrativeArea;
  /**
   * Shipping origin's city.
   *
   * @var string
   */
  public $originCity;
  /**
   * Shipping origin's country represented as a [CLDR territory
   * code](https://github.com/unicode-org/cldr/blob/latest/common/main/en.xml).
   *
   * @var string
   */
  public $originCountry;
  /**
   * Shipping origin.
   *
   * @var string
   */
  public $originPostalCode;
  /**
   * Shipping origin's street address.
   *
   * @var string
   */
  public $originStreetAddress;
  /**
   * The name of the warehouse. Warehouse name need to be matched with name. If
   * warehouseName is set, the below fields will be ignored. The warehouse info
   * will be read from warehouse.
   *
   * @var string
   */
  public $warehouseName;

  /**
   * Required. Carrier, such as `"UPS"` or `"Fedex"`. The list of supported
   * carriers can be retrieved through the `listSupportedCarriers` method.
   *
   * @param string $carrier
   */
  public function setCarrier($carrier)
  {
    $this->carrier = $carrier;
  }
  /**
   * @return string
   */
  public function getCarrier()
  {
    return $this->carrier;
  }
  /**
   * Required. Carrier service, such as `"ground"` or `"2 days"`. The list of
   * supported services for a carrier can be retrieved through the
   * `listSupportedCarriers` method. The name of the service must be in the
   * eddSupportedServices list.
   *
   * @param string $carrierService
   */
  public function setCarrierService($carrierService)
  {
    $this->carrierService = $carrierService;
  }
  /**
   * @return string
   */
  public function getCarrierService()
  {
    return $this->carrierService;
  }
  /**
   * Shipping origin's state.
   *
   * @param string $originAdministrativeArea
   */
  public function setOriginAdministrativeArea($originAdministrativeArea)
  {
    $this->originAdministrativeArea = $originAdministrativeArea;
  }
  /**
   * @return string
   */
  public function getOriginAdministrativeArea()
  {
    return $this->originAdministrativeArea;
  }
  /**
   * Shipping origin's city.
   *
   * @param string $originCity
   */
  public function setOriginCity($originCity)
  {
    $this->originCity = $originCity;
  }
  /**
   * @return string
   */
  public function getOriginCity()
  {
    return $this->originCity;
  }
  /**
   * Shipping origin's country represented as a [CLDR territory
   * code](https://github.com/unicode-org/cldr/blob/latest/common/main/en.xml).
   *
   * @param string $originCountry
   */
  public function setOriginCountry($originCountry)
  {
    $this->originCountry = $originCountry;
  }
  /**
   * @return string
   */
  public function getOriginCountry()
  {
    return $this->originCountry;
  }
  /**
   * Shipping origin.
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
   * Shipping origin's street address.
   *
   * @param string $originStreetAddress
   */
  public function setOriginStreetAddress($originStreetAddress)
  {
    $this->originStreetAddress = $originStreetAddress;
  }
  /**
   * @return string
   */
  public function getOriginStreetAddress()
  {
    return $this->originStreetAddress;
  }
  /**
   * The name of the warehouse. Warehouse name need to be matched with name. If
   * warehouseName is set, the below fields will be ignored. The warehouse info
   * will be read from warehouse.
   *
   * @param string $warehouseName
   */
  public function setWarehouseName($warehouseName)
  {
    $this->warehouseName = $warehouseName;
  }
  /**
   * @return string
   */
  public function getWarehouseName()
  {
    return $this->warehouseName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WarehouseBasedDeliveryTime::class, 'Google_Service_ShoppingContent_WarehouseBasedDeliveryTime');
