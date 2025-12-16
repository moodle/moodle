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

class AccountAddress extends \Google\Model
{
  /**
   * CLDR country code (for example, "US"). All MCA sub-accounts inherit the
   * country of their parent MCA by default, however the country can be updated
   * for individual sub-accounts.
   *
   * @var string
   */
  public $country;
  /**
   * City, town or commune. May also include dependent localities or
   * sublocalities (for example, neighborhoods or suburbs).
   *
   * @var string
   */
  public $locality;
  /**
   * Postal code or ZIP (for example, "94043").
   *
   * @var string
   */
  public $postalCode;
  /**
   * Top-level administrative subdivision of the country. For example, a state
   * like California ("CA") or a province like Quebec ("QC").
   *
   * @var string
   */
  public $region;
  /**
   * Street-level part of the address. Use `\n` to add a second line.
   *
   * @var string
   */
  public $streetAddress;

  /**
   * CLDR country code (for example, "US"). All MCA sub-accounts inherit the
   * country of their parent MCA by default, however the country can be updated
   * for individual sub-accounts.
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
   * City, town or commune. May also include dependent localities or
   * sublocalities (for example, neighborhoods or suburbs).
   *
   * @param string $locality
   */
  public function setLocality($locality)
  {
    $this->locality = $locality;
  }
  /**
   * @return string
   */
  public function getLocality()
  {
    return $this->locality;
  }
  /**
   * Postal code or ZIP (for example, "94043").
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
   * Top-level administrative subdivision of the country. For example, a state
   * like California ("CA") or a province like Quebec ("QC").
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
class_alias(AccountAddress::class, 'Google_Service_ShoppingContent_AccountAddress');
