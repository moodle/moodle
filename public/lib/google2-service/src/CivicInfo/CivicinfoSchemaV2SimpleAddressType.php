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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2SimpleAddressType extends \Google\Collection
{
  protected $collection_key = 'addressLine';
  /**
   * @var string[]
   */
  public $addressLine;
  /**
   * The city or town for the address.
   *
   * @var string
   */
  public $city;
  /**
   * The street name and number of this address.
   *
   * @var string
   */
  public $line1;
  /**
   * The second line the address, if needed.
   *
   * @var string
   */
  public $line2;
  /**
   * The third line of the address, if needed.
   *
   * @var string
   */
  public $line3;
  /**
   * The name of the location.
   *
   * @var string
   */
  public $locationName;
  /**
   * The US two letter state abbreviation of the address.
   *
   * @var string
   */
  public $state;
  /**
   * The US Postal Zip Code of the address.
   *
   * @var string
   */
  public $zip;

  /**
   * @param string[] $addressLine
   */
  public function setAddressLine($addressLine)
  {
    $this->addressLine = $addressLine;
  }
  /**
   * @return string[]
   */
  public function getAddressLine()
  {
    return $this->addressLine;
  }
  /**
   * The city or town for the address.
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
   * The street name and number of this address.
   *
   * @param string $line1
   */
  public function setLine1($line1)
  {
    $this->line1 = $line1;
  }
  /**
   * @return string
   */
  public function getLine1()
  {
    return $this->line1;
  }
  /**
   * The second line the address, if needed.
   *
   * @param string $line2
   */
  public function setLine2($line2)
  {
    $this->line2 = $line2;
  }
  /**
   * @return string
   */
  public function getLine2()
  {
    return $this->line2;
  }
  /**
   * The third line of the address, if needed.
   *
   * @param string $line3
   */
  public function setLine3($line3)
  {
    $this->line3 = $line3;
  }
  /**
   * @return string
   */
  public function getLine3()
  {
    return $this->line3;
  }
  /**
   * The name of the location.
   *
   * @param string $locationName
   */
  public function setLocationName($locationName)
  {
    $this->locationName = $locationName;
  }
  /**
   * @return string
   */
  public function getLocationName()
  {
    return $this->locationName;
  }
  /**
   * The US two letter state abbreviation of the address.
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
   * The US Postal Zip Code of the address.
   *
   * @param string $zip
   */
  public function setZip($zip)
  {
    $this->zip = $zip;
  }
  /**
   * @return string
   */
  public function getZip()
  {
    return $this->zip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2SimpleAddressType::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2SimpleAddressType');
