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

class GoogleMapsPlacesV1FuelOptions extends \Google\Collection
{
  protected $collection_key = 'fuelPrices';
  protected $fuelPricesType = GoogleMapsPlacesV1FuelOptionsFuelPrice::class;
  protected $fuelPricesDataType = 'array';

  /**
   * The last known fuel price for each type of fuel this station has. There is
   * one entry per fuel type this station has. Order is not important.
   *
   * @param GoogleMapsPlacesV1FuelOptionsFuelPrice[] $fuelPrices
   */
  public function setFuelPrices($fuelPrices)
  {
    $this->fuelPrices = $fuelPrices;
  }
  /**
   * @return GoogleMapsPlacesV1FuelOptionsFuelPrice[]
   */
  public function getFuelPrices()
  {
    return $this->fuelPrices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1FuelOptions::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1FuelOptions');
