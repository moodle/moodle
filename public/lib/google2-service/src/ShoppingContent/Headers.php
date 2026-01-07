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

class Headers extends \Google\Collection
{
  protected $collection_key = 'weights';
  protected $locationsType = LocationIdSet::class;
  protected $locationsDataType = 'array';
  /**
   * A list of inclusive number of items upper bounds. The last value can be
   * `"infinity"`. For example `["10", "50", "infinity"]` represents the headers
   * "<= 10 items", "<= 50 items", and "> 50 items". Must be non-empty. Can only
   * be set if all other fields are not set.
   *
   * @var string[]
   */
  public $numberOfItems;
  /**
   * A list of postal group names. The last value can be `"all other
   * locations"`. Example: `["zone 1", "zone 2", "all other locations"]`. The
   * referred postal code groups must match the delivery country of the service.
   * Must be non-empty. Can only be set if all other fields are not set.
   *
   * @var string[]
   */
  public $postalCodeGroupNames;
  protected $pricesType = Price::class;
  protected $pricesDataType = 'array';
  protected $weightsType = Weight::class;
  protected $weightsDataType = 'array';

  /**
   * A list of location ID sets. Must be non-empty. Can only be set if all other
   * fields are not set.
   *
   * @param LocationIdSet[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return LocationIdSet[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * A list of inclusive number of items upper bounds. The last value can be
   * `"infinity"`. For example `["10", "50", "infinity"]` represents the headers
   * "<= 10 items", "<= 50 items", and "> 50 items". Must be non-empty. Can only
   * be set if all other fields are not set.
   *
   * @param string[] $numberOfItems
   */
  public function setNumberOfItems($numberOfItems)
  {
    $this->numberOfItems = $numberOfItems;
  }
  /**
   * @return string[]
   */
  public function getNumberOfItems()
  {
    return $this->numberOfItems;
  }
  /**
   * A list of postal group names. The last value can be `"all other
   * locations"`. Example: `["zone 1", "zone 2", "all other locations"]`. The
   * referred postal code groups must match the delivery country of the service.
   * Must be non-empty. Can only be set if all other fields are not set.
   *
   * @param string[] $postalCodeGroupNames
   */
  public function setPostalCodeGroupNames($postalCodeGroupNames)
  {
    $this->postalCodeGroupNames = $postalCodeGroupNames;
  }
  /**
   * @return string[]
   */
  public function getPostalCodeGroupNames()
  {
    return $this->postalCodeGroupNames;
  }
  /**
   * A list of inclusive order price upper bounds. The last price's value can be
   * `"infinity"`. For example `[{"value": "10", "currency": "USD"}, {"value":
   * "500", "currency": "USD"}, {"value": "infinity", "currency": "USD"}]`
   * represents the headers "<= $10", "<= $500", and "> $500". All prices within
   * a service must have the same currency. Must be non-empty. Can only be set
   * if all other fields are not set.
   *
   * @param Price[] $prices
   */
  public function setPrices($prices)
  {
    $this->prices = $prices;
  }
  /**
   * @return Price[]
   */
  public function getPrices()
  {
    return $this->prices;
  }
  /**
   * A list of inclusive order weight upper bounds. The last weight's value can
   * be `"infinity"`. For example `[{"value": "10", "unit": "kg"}, {"value":
   * "50", "unit": "kg"}, {"value": "infinity", "unit": "kg"}]` represents the
   * headers "<= 10kg", "<= 50kg", and "> 50kg". All weights within a service
   * must have the same unit. Must be non-empty. Can only be set if all other
   * fields are not set.
   *
   * @param Weight[] $weights
   */
  public function setWeights($weights)
  {
    $this->weights = $weights;
  }
  /**
   * @return Weight[]
   */
  public function getWeights()
  {
    return $this->weights;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Headers::class, 'Google_Service_ShoppingContent_Headers');
