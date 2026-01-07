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

namespace Google\Service\AdSensePlatform;

class Address extends \Google\Model
{
  /**
   * First line of address. Max length 64 bytes or 30 characters.
   *
   * @var string
   */
  public $address1;
  /**
   * Second line of address. Max length 64 bytes or 30 characters.
   *
   * @var string
   */
  public $address2;
  /**
   * City. Max length 60 bytes or 30 characters.
   *
   * @var string
   */
  public $city;
  /**
   * Name of the company. Max length 255 bytes or 34 characters.
   *
   * @var string
   */
  public $company;
  /**
   * Contact name of the company. Max length 128 bytes or 34 characters.
   *
   * @var string
   */
  public $contact;
  /**
   * Fax number with international code (i.e. +441234567890).
   *
   * @var string
   */
  public $fax;
  /**
   * Phone number with international code (i.e. +441234567890).
   *
   * @var string
   */
  public $phone;
  /**
   * Country/Region code. The region is specified as a CLDR region code (e.g.
   * "US", "FR").
   *
   * @var string
   */
  public $regionCode;
  /**
   * State. Max length 60 bytes or 30 characters.
   *
   * @var string
   */
  public $state;
  /**
   * Zip/post code. Max length 10 bytes or 10 characters.
   *
   * @var string
   */
  public $zip;

  /**
   * First line of address. Max length 64 bytes or 30 characters.
   *
   * @param string $address1
   */
  public function setAddress1($address1)
  {
    $this->address1 = $address1;
  }
  /**
   * @return string
   */
  public function getAddress1()
  {
    return $this->address1;
  }
  /**
   * Second line of address. Max length 64 bytes or 30 characters.
   *
   * @param string $address2
   */
  public function setAddress2($address2)
  {
    $this->address2 = $address2;
  }
  /**
   * @return string
   */
  public function getAddress2()
  {
    return $this->address2;
  }
  /**
   * City. Max length 60 bytes or 30 characters.
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
   * Name of the company. Max length 255 bytes or 34 characters.
   *
   * @param string $company
   */
  public function setCompany($company)
  {
    $this->company = $company;
  }
  /**
   * @return string
   */
  public function getCompany()
  {
    return $this->company;
  }
  /**
   * Contact name of the company. Max length 128 bytes or 34 characters.
   *
   * @param string $contact
   */
  public function setContact($contact)
  {
    $this->contact = $contact;
  }
  /**
   * @return string
   */
  public function getContact()
  {
    return $this->contact;
  }
  /**
   * Fax number with international code (i.e. +441234567890).
   *
   * @param string $fax
   */
  public function setFax($fax)
  {
    $this->fax = $fax;
  }
  /**
   * @return string
   */
  public function getFax()
  {
    return $this->fax;
  }
  /**
   * Phone number with international code (i.e. +441234567890).
   *
   * @param string $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }
  /**
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }
  /**
   * Country/Region code. The region is specified as a CLDR region code (e.g.
   * "US", "FR").
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * State. Max length 60 bytes or 30 characters.
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
   * Zip/post code. Max length 10 bytes or 10 characters.
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
class_alias(Address::class, 'Google_Service_AdSensePlatform_Address');
