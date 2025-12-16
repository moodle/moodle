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

class ProductShipping extends \Google\Model
{
  /**
   * The CLDR territory code of the country to which an item will ship.
   *
   * @var string
   */
  public $country;
  /**
   * The location where the shipping is applicable, represented by a location
   * group name.
   *
   * @var string
   */
  public $locationGroupName;
  /**
   * The numeric ID of a location that the shipping rate applies to as defined
   * in the Google Ads API.
   *
   * @var string
   */
  public $locationId;
  /**
   * Maximum handling time (inclusive) between when the order is received and
   * shipped in business days. 0 means that the order is shipped on the same day
   * as it's received if it happens before the cut-off time. Both
   * maxHandlingTime and maxTransitTime are required if providing shipping
   * speeds.
   *
   * @var string
   */
  public $maxHandlingTime;
  /**
   * Maximum transit time (inclusive) between when the order has shipped and
   * when it's delivered in business days. 0 means that the order is delivered
   * on the same day as it ships. Both maxHandlingTime and maxTransitTime are
   * required if providing shipping speeds.
   *
   * @var string
   */
  public $maxTransitTime;
  /**
   * Minimum handling time (inclusive) between when the order is received and
   * shipped in business days. 0 means that the order is shipped on the same day
   * as it's received if it happens before the cut-off time. minHandlingTime can
   * only be present together with maxHandlingTime; but it's not required if
   * maxHandlingTime is present.
   *
   * @var string
   */
  public $minHandlingTime;
  /**
   * Minimum transit time (inclusive) between when the order has shipped and
   * when it's delivered in business days. 0 means that the order is delivered
   * on the same day as it ships. minTransitTime can only be present together
   * with maxTransitTime; but it's not required if maxTransitTime is present.
   *
   * @var string
   */
  public $minTransitTime;
  /**
   * The postal code range that the shipping rate applies to, represented by a
   * postal code, a postal code prefix followed by a * wildcard, a range between
   * two postal codes or two postal code prefixes of equal length.
   *
   * @var string
   */
  public $postalCode;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * The geographic region to which a shipping rate applies.
   *
   * @var string
   */
  public $region;
  /**
   * A free-form description of the service class or delivery speed.
   *
   * @var string
   */
  public $service;

  /**
   * The CLDR territory code of the country to which an item will ship.
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
   * The location where the shipping is applicable, represented by a location
   * group name.
   *
   * @param string $locationGroupName
   */
  public function setLocationGroupName($locationGroupName)
  {
    $this->locationGroupName = $locationGroupName;
  }
  /**
   * @return string
   */
  public function getLocationGroupName()
  {
    return $this->locationGroupName;
  }
  /**
   * The numeric ID of a location that the shipping rate applies to as defined
   * in the Google Ads API.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Maximum handling time (inclusive) between when the order is received and
   * shipped in business days. 0 means that the order is shipped on the same day
   * as it's received if it happens before the cut-off time. Both
   * maxHandlingTime and maxTransitTime are required if providing shipping
   * speeds.
   *
   * @param string $maxHandlingTime
   */
  public function setMaxHandlingTime($maxHandlingTime)
  {
    $this->maxHandlingTime = $maxHandlingTime;
  }
  /**
   * @return string
   */
  public function getMaxHandlingTime()
  {
    return $this->maxHandlingTime;
  }
  /**
   * Maximum transit time (inclusive) between when the order has shipped and
   * when it's delivered in business days. 0 means that the order is delivered
   * on the same day as it ships. Both maxHandlingTime and maxTransitTime are
   * required if providing shipping speeds.
   *
   * @param string $maxTransitTime
   */
  public function setMaxTransitTime($maxTransitTime)
  {
    $this->maxTransitTime = $maxTransitTime;
  }
  /**
   * @return string
   */
  public function getMaxTransitTime()
  {
    return $this->maxTransitTime;
  }
  /**
   * Minimum handling time (inclusive) between when the order is received and
   * shipped in business days. 0 means that the order is shipped on the same day
   * as it's received if it happens before the cut-off time. minHandlingTime can
   * only be present together with maxHandlingTime; but it's not required if
   * maxHandlingTime is present.
   *
   * @param string $minHandlingTime
   */
  public function setMinHandlingTime($minHandlingTime)
  {
    $this->minHandlingTime = $minHandlingTime;
  }
  /**
   * @return string
   */
  public function getMinHandlingTime()
  {
    return $this->minHandlingTime;
  }
  /**
   * Minimum transit time (inclusive) between when the order has shipped and
   * when it's delivered in business days. 0 means that the order is delivered
   * on the same day as it ships. minTransitTime can only be present together
   * with maxTransitTime; but it's not required if maxTransitTime is present.
   *
   * @param string $minTransitTime
   */
  public function setMinTransitTime($minTransitTime)
  {
    $this->minTransitTime = $minTransitTime;
  }
  /**
   * @return string
   */
  public function getMinTransitTime()
  {
    return $this->minTransitTime;
  }
  /**
   * The postal code range that the shipping rate applies to, represented by a
   * postal code, a postal code prefix followed by a * wildcard, a range between
   * two postal codes or two postal code prefixes of equal length.
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  /**
   * Fixed shipping price, represented as a number.
   *
   * @param Price $price
   */
  public function setPrice(Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * The geographic region to which a shipping rate applies.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * A free-form description of the service class or delivery speed.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductShipping::class, 'Google_Service_ShoppingContent_ProductShipping');
