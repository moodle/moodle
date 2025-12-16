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

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1UspsAddress extends \Google\Model
{
  /**
   * City name.
   *
   * @var string
   */
  public $city;
  /**
   * City + state + postal code.
   *
   * @var string
   */
  public $cityStateZipAddressLine;
  /**
   * Firm name.
   *
   * @var string
   */
  public $firm;
  /**
   * First address line.
   *
   * @var string
   */
  public $firstAddressLine;
  /**
   * Second address line.
   *
   * @var string
   */
  public $secondAddressLine;
  /**
   * 2 letter state code.
   *
   * @var string
   */
  public $state;
  /**
   * Puerto Rican urbanization name.
   *
   * @var string
   */
  public $urbanization;
  /**
   * Postal code e.g. 10009.
   *
   * @var string
   */
  public $zipCode;
  /**
   * 4-digit postal code extension e.g. 5023.
   *
   * @var string
   */
  public $zipCodeExtension;

  /**
   * City name.
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
   * City + state + postal code.
   *
   * @param string $cityStateZipAddressLine
   */
  public function setCityStateZipAddressLine($cityStateZipAddressLine)
  {
    $this->cityStateZipAddressLine = $cityStateZipAddressLine;
  }
  /**
   * @return string
   */
  public function getCityStateZipAddressLine()
  {
    return $this->cityStateZipAddressLine;
  }
  /**
   * Firm name.
   *
   * @param string $firm
   */
  public function setFirm($firm)
  {
    $this->firm = $firm;
  }
  /**
   * @return string
   */
  public function getFirm()
  {
    return $this->firm;
  }
  /**
   * First address line.
   *
   * @param string $firstAddressLine
   */
  public function setFirstAddressLine($firstAddressLine)
  {
    $this->firstAddressLine = $firstAddressLine;
  }
  /**
   * @return string
   */
  public function getFirstAddressLine()
  {
    return $this->firstAddressLine;
  }
  /**
   * Second address line.
   *
   * @param string $secondAddressLine
   */
  public function setSecondAddressLine($secondAddressLine)
  {
    $this->secondAddressLine = $secondAddressLine;
  }
  /**
   * @return string
   */
  public function getSecondAddressLine()
  {
    return $this->secondAddressLine;
  }
  /**
   * 2 letter state code.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Puerto Rican urbanization name.
   *
   * @param string $urbanization
   */
  public function setUrbanization($urbanization)
  {
    $this->urbanization = $urbanization;
  }
  /**
   * @return string
   */
  public function getUrbanization()
  {
    return $this->urbanization;
  }
  /**
   * Postal code e.g. 10009.
   *
   * @param string $zipCode
   */
  public function setZipCode($zipCode)
  {
    $this->zipCode = $zipCode;
  }
  /**
   * @return string
   */
  public function getZipCode()
  {
    return $this->zipCode;
  }
  /**
   * 4-digit postal code extension e.g. 5023.
   *
   * @param string $zipCodeExtension
   */
  public function setZipCodeExtension($zipCodeExtension)
  {
    $this->zipCodeExtension = $zipCodeExtension;
  }
  /**
   * @return string
   */
  public function getZipCodeExtension()
  {
    return $this->zipCodeExtension;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1UspsAddress::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1UspsAddress');
