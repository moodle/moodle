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

namespace Google\Service\Directory;

class UserAddress extends \Google\Model
{
  /**
   * Country.
   *
   * @var string
   */
  public $country;
  /**
   * Country code.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Custom type.
   *
   * @var string
   */
  public $customType;
  /**
   * Extended Address.
   *
   * @var string
   */
  public $extendedAddress;
  /**
   * Formatted address.
   *
   * @var string
   */
  public $formatted;
  /**
   * Locality.
   *
   * @var string
   */
  public $locality;
  /**
   * Other parts of address.
   *
   * @var string
   */
  public $poBox;
  /**
   * Postal code.
   *
   * @var string
   */
  public $postalCode;
  /**
   * If this is user's primary address. Only one entry could be marked as
   * primary.
   *
   * @var bool
   */
  public $primary;
  /**
   * Region.
   *
   * @var string
   */
  public $region;
  /**
   * User supplied address was structured. Structured addresses are NOT
   * supported at this time. You might be able to write structured addresses but
   * any values will eventually be clobbered.
   *
   * @var bool
   */
  public $sourceIsStructured;
  /**
   * Street.
   *
   * @var string
   */
  public $streetAddress;
  /**
   * Each entry can have a type which indicates standard values of that entry.
   * For example address could be of home work etc. In addition to the standard
   * type an entry can have a custom type and can take any value. Such type
   * should have the CUSTOM value as type and also have a customType value.
   *
   * @var string
   */
  public $type;

  /**
   * Country.
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
   * Country code.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Custom type.
   *
   * @param string $customType
   */
  public function setCustomType($customType)
  {
    $this->customType = $customType;
  }
  /**
   * @return string
   */
  public function getCustomType()
  {
    return $this->customType;
  }
  /**
   * Extended Address.
   *
   * @param string $extendedAddress
   */
  public function setExtendedAddress($extendedAddress)
  {
    $this->extendedAddress = $extendedAddress;
  }
  /**
   * @return string
   */
  public function getExtendedAddress()
  {
    return $this->extendedAddress;
  }
  /**
   * Formatted address.
   *
   * @param string $formatted
   */
  public function setFormatted($formatted)
  {
    $this->formatted = $formatted;
  }
  /**
   * @return string
   */
  public function getFormatted()
  {
    return $this->formatted;
  }
  /**
   * Locality.
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
   * Other parts of address.
   *
   * @param string $poBox
   */
  public function setPoBox($poBox)
  {
    $this->poBox = $poBox;
  }
  /**
   * @return string
   */
  public function getPoBox()
  {
    return $this->poBox;
  }
  /**
   * Postal code.
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
   * If this is user's primary address. Only one entry could be marked as
   * primary.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Region.
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
   * User supplied address was structured. Structured addresses are NOT
   * supported at this time. You might be able to write structured addresses but
   * any values will eventually be clobbered.
   *
   * @param bool $sourceIsStructured
   */
  public function setSourceIsStructured($sourceIsStructured)
  {
    $this->sourceIsStructured = $sourceIsStructured;
  }
  /**
   * @return bool
   */
  public function getSourceIsStructured()
  {
    return $this->sourceIsStructured;
  }
  /**
   * Street.
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
  /**
   * Each entry can have a type which indicates standard values of that entry.
   * For example address could be of home work etc. In addition to the standard
   * type an entry can have a custom type and can take any value. Such type
   * should have the CUSTOM value as type and also have a customType value.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserAddress::class, 'Google_Service_Directory_UserAddress');
