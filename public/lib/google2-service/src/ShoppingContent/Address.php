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

class Address extends \Google\Model
{
  /**
   * Required. Top-level administrative subdivision of the country. For example,
   * a state like California ("CA") or a province like Quebec ("QC").
   *
   * @var string
   */
  public $administrativeArea;
  /**
   * Required. City, town or commune. May also include dependent localities or
   * sublocalities (for example, neighborhoods or suburbs).
   *
   * @var string
   */
  public $city;
  /**
   * Required. [CLDR country code](https://github.com/unicode-
   * org/cldr/blob/latest/common/main/en.xml) (for example, "US").
   *
   * @var string
   */
  public $country;
  /**
   * Required. Postal code or ZIP (for example, "94043").
   *
   * @var string
   */
  public $postalCode;
  /**
   * Street-level part of the address. Use `\n` to add a second line.
   *
   * @var string
   */
  public $streetAddress;

  /**
   * Required. Top-level administrative subdivision of the country. For example,
   * a state like California ("CA") or a province like Quebec ("QC").
   *
   * @param string $administrativeArea
   */
  public function setAdministrativeArea($administrativeArea)
  {
    $this->administrativeArea = $administrativeArea;
  }
  /**
   * @return string
   */
  public function getAdministrativeArea()
  {
    return $this->administrativeArea;
  }
  /**
   * Required. City, town or commune. May also include dependent localities or
   * sublocalities (for example, neighborhoods or suburbs).
   *
   * @param string $city
   */
  public function setCity($city)
  {
    $this->city = $city;
  }
  /**
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }
  /**
   * Required. [CLDR country code](https://github.com/unicode-
   * org/cldr/blob/latest/common/main/en.xml) (for example, "US").
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
   * Required. Postal code or ZIP (for example, "94043").
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
   * Street-level part of the address. Use `\n` to add a second line.
   *
   * @param string $streetAddress
   */
  public function setStreetAddress($streetAddress)
  {
    $this->streetAddress = $streetAddress;
  }
  /**
   * @return string
   */
  public function getStreetAddress()
  {
    return $this->streetAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Address::class, 'Google_Service_ShoppingContent_Address');
