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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1FuelOptionsFuelPrice extends \Google\Model
{
  /**
   * Unspecified fuel type.
   */
  public const TYPE_FUEL_TYPE_UNSPECIFIED = 'FUEL_TYPE_UNSPECIFIED';
  /**
   * Diesel fuel.
   */
  public const TYPE_DIESEL = 'DIESEL';
  /**
   * Diesel plus fuel.
   */
  public const TYPE_DIESEL_PLUS = 'DIESEL_PLUS';
  /**
   * Regular unleaded.
   */
  public const TYPE_REGULAR_UNLEADED = 'REGULAR_UNLEADED';
  /**
   * Midgrade.
   */
  public const TYPE_MIDGRADE = 'MIDGRADE';
  /**
   * Premium.
   */
  public const TYPE_PREMIUM = 'PREMIUM';
  /**
   * SP 91.
   */
  public const TYPE_SP91 = 'SP91';
  /**
   * SP 91 E10.
   */
  public const TYPE_SP91_E10 = 'SP91_E10';
  /**
   * SP 92.
   */
  public const TYPE_SP92 = 'SP92';
  /**
   * SP 95.
   */
  public const TYPE_SP95 = 'SP95';
  /**
   * SP95 E10.
   */
  public const TYPE_SP95_E10 = 'SP95_E10';
  /**
   * SP 98.
   */
  public const TYPE_SP98 = 'SP98';
  /**
   * SP 99.
   */
  public const TYPE_SP99 = 'SP99';
  /**
   * SP 100.
   */
  public const TYPE_SP100 = 'SP100';
  /**
   * Liquefied Petroleum Gas.
   */
  public const TYPE_LPG = 'LPG';
  /**
   * E 80.
   */
  public const TYPE_E80 = 'E80';
  /**
   * E 85.
   */
  public const TYPE_E85 = 'E85';
  /**
   * E 100.
   */
  public const TYPE_E100 = 'E100';
  /**
   * Methane.
   */
  public const TYPE_METHANE = 'METHANE';
  /**
   * Bio-diesel.
   */
  public const TYPE_BIO_DIESEL = 'BIO_DIESEL';
  /**
   * Truck diesel.
   */
  public const TYPE_TRUCK_DIESEL = 'TRUCK_DIESEL';
  protected $priceType = GoogleTypeMoney::class;
  protected $priceDataType = '';
  /**
   * The type of fuel.
   *
   * @var string
   */
  public $type;
  /**
   * The time the fuel price was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The price of the fuel.
   *
   * @param GoogleTypeMoney $price
   */
  public function setPrice(GoogleTypeMoney $price)
  {
    $this->price = $price;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * The type of fuel.
   *
   * Accepted values: FUEL_TYPE_UNSPECIFIED, DIESEL, DIESEL_PLUS,
   * REGULAR_UNLEADED, MIDGRADE, PREMIUM, SP91, SP91_E10, SP92, SP95, SP95_E10,
   * SP98, SP99, SP100, LPG, E80, E85, E100, METHANE, BIO_DIESEL, TRUCK_DIESEL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The time the fuel price was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1FuelOptionsFuelPrice::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1FuelOptionsFuelPrice');
