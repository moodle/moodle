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

class CarriersCarrier extends \Google\Collection
{
  protected $collection_key = 'services';
  /**
   * The CLDR country code of the carrier (for example, "US"). Always present.
   *
   * @var string
   */
  public $country;
  /**
   * A list of services supported for EDD (Estimated Delivery Date) calculation.
   * This is the list of valid values for
   * WarehouseBasedDeliveryTime.carrierService.
   *
   * @var string[]
   */
  public $eddServices;
  /**
   * The name of the carrier (for example, `"UPS"`). Always present.
   *
   * @var string
   */
  public $name;
  /**
   * A list of supported services (for example, `"ground"`) for that carrier.
   * Contains at least one service. This is the list of valid values for
   * CarrierRate.carrierService.
   *
   * @var string[]
   */
  public $services;

  /**
   * The CLDR country code of the carrier (for example, "US"). Always present.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * A list of services supported for EDD (Estimated Delivery Date) calculation.
   * This is the list of valid values for
   * WarehouseBasedDeliveryTime.carrierService.
   *
   * @param string[] $eddServices
   */
  public function setEddServices($eddServices)
  {
    $this->eddServices = $eddServices;
  }
  /**
   * @return string[]
   */
  public function getEddServices()
  {
    return $this->eddServices;
  }
  /**
   * The name of the carrier (for example, `"UPS"`). Always present.
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
   * A list of supported services (for example, `"ground"`) for that carrier.
   * Contains at least one service. This is the list of valid values for
   * CarrierRate.carrierService.
   *
   * @param string[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return string[]
   */
  public function getServices()
  {
    return $this->services;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CarriersCarrier::class, 'Google_Service_ShoppingContent_CarriersCarrier');
